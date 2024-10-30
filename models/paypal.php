<?php
// handle paypal IPN, PDT etc
class MoolaMojoPaypal {
   static $pdt_mode = false;	
	static $pdt_response = '';	
	
	static function parse_request($wp) {		
		// only process requests with "moolamojo=paypal"
	   if (array_key_exists('moolamojo', $wp->query_vars) 
	            && $wp->query_vars['moolamojo'] == 'paypal') {	            
	        self::paypal_ipn($wp);
	   }	
	}	
	
	// process paypal IPN for signing up in class
	// a lot of redundancy with namaste-lms/models/payment.php!
	static function paypal_ipn($wp = null) {
		global $wpdb;
		echo "<!-- MOOLAMOJO paypal IPN -->";
		
	   $paypal_email = get_option("moolamojo_paypal_id");
	   $pdt_mode = get_option('moolamojo_use_pdt');	   
	   if(!empty($_GET['tx']) and !empty($_GET['moolamojo_pdt']) and get_option('moolamojo_use_pdt')==1) {	   	
			// PDT			
			$req = 'cmd=_notify-synch';
			$tx_token = strtoupper($_GET['tx']);
			$auth_token = get_option('moolamojo_pdt_token');
			$req .= "&tx=$tx_token&at=$auth_token";
			$pdt_mode = true;
			$success_responce = "SUCCESS";
		}
		else {	
			// IPN		
			$req = 'cmd=_notify-validate';
			foreach ($_POST as $key => $value) { 
			  $value = urlencode(stripslashes($value)); 
			  $req .= "&$key=$value";
			}
			$success_responce = "VERIFIED";
		}	
		
		self :: $pdt_mode = $pdt_mode;	
		
			$paypal_host = "ipnpb.paypal.com";
		if($paypal_sandbox == '1') $paypal_host = 'ipnpb.sandbox.paypal.com';
		
		// post back to PayPal system to validate
		$paypal_host = "https://".$paypal_host;
		
		// wp_remote_post
		$response = wp_remote_post($paypal_host, array(
			    'method'      => 'POST',
			    'timeout'     => 45,
			    'redirection' => 5,
			    'httpversion' => '1.0',
			    'blocking'    => true,
			    'headers'     => array(),
			    'body'        => $req,
			    'cookies'     => array()
		    ));
		
		if ( is_wp_error( $response ) ) {
		    $error_message = $response->get_error_message();
			 return self::log_and_exit("Can't connect to Paypal: $error_message");
		} 
		
		if (strstr ($response['body'], $success_responce) or $paypal_sandbox == '1') self :: paypal_ipn_verify($response['body']);
		else return self::log_and_exit("Paypal result is not VERIFIED: ".$response['body']);			
		exit;
	}  // end paypal_ipn		
	
	static function paypal_ipn_verify($pp_response) {	
		 global $wpdb, $user_ID, $post;
		echo "<!-- MOOLAMOJO paypal IPN -->";  		
				
		// when we are in PDT mode let's assign all lines as POST variables
		if(self :: $pdt_mode) {		   
			 $lines = explode("\n", $pp_response);	
				if (strcmp ($lines[0], "SUCCESS") == 0) {
				for ($i=1; $i<count($lines);$i++){
					if(strstr($lines[$i], '=')) list($key,$val) = explode("=", $lines[$i]);
					$_POST[urldecode($key)] = urldecode($val);
				}
			 }
			 
			 $_GET['user_id'] = $user_ID;
			 self :: $pdt_response = $pp_response;
		} // end PDT mode transfer from lines to $_POST	 		
		
	   $paypal_email = get_option("moolamojo_paypal_id");		
	
   	// check the payment_status is Completed
      // check that txn_id has not been previously processed
      // check that receiver_email is your Primary PayPal email
      // process payment
	   $payment_completed = false;
	   $txn_id_okay = false;
	   $receiver_okay = false;
	   $payment_currency_okay = false;
	   $payment_amount_okay = false;
	   
	   if($_POST['payment_status']=="Completed") {
	   	$payment_completed = true;
	   } 
	   else self::log_and_exit("Payment status: $_POST[payment_status]");
	   
	   // check txn_id
	   $txn_exists = $wpdb->get_var($wpdb->prepare("SELECT paycode FROM ".MOOLA_TRANSACTIONS."
		   WHERE action_table='_paypal' AND paycode=%s", sanitize_text_field($_POST['txn_id'])));
		if(empty($txn_id)) $txn_id_okay = true; 
		else {
			// in PDT mode just redirect to the post because existing txn_id isn't a problem.
			// but of course we shouldn't insert second payment
			if( self :: $pdt_mode) namaste_redirect(get_permalink($post->ID));
			self :: log_and_exit("TXN ID exists: $txn_id");
		}  
		
		// check receiver email
		if($_POST['business'] == $paypal_email) {
			$receiver_okay = true;
		}
		else self::log_and_exit("Business email is wrong: $_POST[business]");
		
		// check payment currency
		if($_POST['mc_currency'] == get_option("moolamojo_real_currency")) {
			$payment_currency_okay = true;
		}
		else self::log_and_exit("Currency is $_POST[mc_currency]"); 
		
		// check amount
		if(empty($_GET['item_id'])) $_GET['item_id'] = @$_GET['item_number']; // in case of PDT
		$package = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".MOOLA_PACKAGES." WHERE id=%d", $_GET['item_id']));
		$fee = $package->price;
		
		if($_POST['mc_gross']>=$fee) {
			$payment_amount_okay = true;
		}
		else self::log_and_exit("Wrong amount: $_POST[mc_gross] when price is $fee"); 
		
		// everything OK, insert payment and activate the package of moola
		if($payment_completed and $txn_id_okay and $receiver_okay and $payment_currency_okay 
				and $payment_amount_okay) {						
				$wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_TRANSACTIONS." SET 
						user_id=%d, datetime=%s, amount_cash=%f, action='purchase', action_table='_paypal', 
						action_id=%d, paycode=%s", 
						$_GET['user_id'], current_time('mysql'), $fee, $package->id, sanitize_text_field($_POST['txn_id'])));
				
			
			// paid	
			do_action('moolamojo-paid', $_GET['user_id'], $fee, "package", $package->id);				
						
		  // activate currency package
         MoolaMojoActions :: purchase_moola($_GET['user_id'], $package->id, $package->moola);
			
			if(!self :: $pdt_mode) exit;
			else moola_redirect(esc_url(add_query_arg(array('moolamojo_cart_paid' => 1), get_permalink($_GET['post_id']))));
		}
		exit;
	} // end paypal_ipn_verify
	
	// log paypal errors
	static function log_and_exit($msg) {
		// log
		$msg = "Paypal payment attempt failed at ".date(get_option('date_format').' '.get_option('time_format')).": ".$msg;
		$errorlog=get_option("moolamojo_errorlog");
		$errorlog = $msg."\n".$errorlog;
		update_option("moolamojo_errorlog",$errorlog);
		
		// if we are in Paypal PDT mode just echo and don't exit
		if(self :: $pdt_mode) {
			echo $msg;
			if(get_option('moolamojo_debug_mode')) echo "<br>Full response: ".self :: $pdt_response;
			return true;
		}
		// throw exception as there's no need to continue
		exit;
	}
}