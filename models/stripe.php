<?php
// stripe integration model
class MoolaMojoStripe {
	static function load() {
		require_once(MOOLA_PATH.'/lib/stripe/init.php');
 
		$stripe = array(
		  'secret_key'      => get_option('moolamojo_stripe_secret'),
		  'publishable_key' => get_option('moolamojo_stripe_public')
		);
		 
		\Stripe\Stripe::setApiKey($stripe['secret_key']);
		
		return $stripe;
	}
	
	static function pay() {
		global $wpdb, $user_ID, $user_email;
		require_once(MOOLA_PATH.'/lib/stripe/init.php');
		
		self :: load();
			
		$token  = $_POST['stripeToken'];
		
      // select package
      $package = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".MOOLA_PACKAGES." WHERE id=%d", intval($_POST['item_id'])));
      if(empty($package->id)) return false;
      $fee = $package->price;
      
      $currency = MOOLA_REAL_CURRENCY;
		 
		try {
			 $customer = \Stripe\Customer::create(array(
		      'email' => $user_email,
		      'card'  => $token
		    ));				
			
			  $charge = \Stripe\Charge::create(array(
			      'customer' => $customer->id,
			      'amount'   => $fee*100,
			      'currency' => $currency
			  ));
		} 
		catch (Exception $e) {
			wp_die($e->getMessage());
		}	  
		 
		// enter the transaction
		$wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_TRANSACTIONS." SET 
						user_id=%d, datetime=%s, amount_cash=%f, action='purchase', action_table='_stripe', action_id=%d", 
						$user_ID, current_time('mysql'), $fee, $package->id));
						
		do_action('moolamojo-paid', $user_ID, $fee, "package", $package->id);				
						
		// activate currency package
      MoolaMojoActions :: purchase_moola($user_ID, $package->id, $package->moola);
	}
}