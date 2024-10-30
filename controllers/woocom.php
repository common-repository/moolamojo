<?php
// WooCommerce integration: sell MoolaMojo Packages as WooCommerce Products
class MoolaMojoWooCom {
	static function completed_order($order_id) {
		global $wpdb;
		
		update_option('moola_ww_last_order_id', $order_id);
		
		// select line items
		$items = $wpdb->get_results($wpdb->prepare("SELECT tI.*, tM.meta_value as product_id 
				FROM {$wpdb->prefix}woocommerce_order_items tI JOIN {$wpdb->prefix}woocommerce_order_itemmeta tM
				ON tM.order_item_id = tI.order_item_id AND tM.meta_key='_product_id'
				WHERE tI.order_id = %d AND tI.order_item_type = 'line_item'", $order_id));
		$package_ids = array(); // package IDs to process
		$package_redirect = ""; // do we redirect anywhere?
		
		// now for each $item select the product, and check in the meta whether it's moolamojo currency package
		foreach($items as $item) {
			$product = get_post($item->product_id);
			update_option('moola_ww_last_product_title', $product->post_title);		
			// get meta
			$atts = get_post_meta($product->ID, '_product_attributes', true);
			
			foreach($atts as $key=>$att) {		
				
				if($att['name'] == 'moolamojo' and !empty($att['value'])) {
					if(is_numeric($att['value'])) $package_ids[] = $package_id = $att['value'];
					else {
						$pids = explode("|", $att['value']);
						foreach($pids as $pid) $package_ids[] = $pid;
					}
				}
			
				if($att['name'] == 'moolamojo-redirect' and empty($moola_redirect)) $moola_redirect = $att['value'];
			}
		}	// end foreach item	
		
		if(!empty($package_ids)) {
		   // select order  meta
			$user_id = get_post_meta($order_id, "_customer_user", true);
			
			if(empty($user_id)) {
				$password = wp_generate_password( 12, true );
				$user_email = get_post_meta($order_id, "_billing_email", true);
				
				// email exists?
				$user = get_user_by('email', $user_email);
				if(empty($user->ID)) {
					$user_id = wp_create_user( $user_email, $password, $user_email );
					wp_update_user( array ('ID' => $user_id) ) ;
				}
				else $user_id = $user->ID;
			}
		}
		
		// if there are quiz ids we'll activate them but first need to ensure there is user ID
		if(!empty($package_ids)) {
			// now insert payments for this user ID and the given quiz IDs
			foreach($package_ids as $package_id) {
				$package = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".MOOLA_PACKAGES." WHERE id=%d", $package_id));				
				
				$wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_TRANSACTIONS." SET 
						user_id=%d, datetime=%s, amount_cash=%f, action='purchase', action_table='_woocommerce', 
						action_id=%d, paycode=%s", 
						$user_id, current_time('mysql'), $package->price, $package_id, $order_id.'-'.$package_id));
							
				// paid	
				do_action('moolamojo-paid', $user_id, $package->price, "package", $package_id);				
							
			  // activate currency package
	         MoolaMojoActions :: purchase_moola($user_id, $package->id, $package->moola);
			}
		}
		
		
		// any redirect defined?
		// if(!empty($quiz_redirect)) watupro_redirect($quiz_redirect);
		if(!empty($moola_redirect)) update_option('moolamojo-woocom-redirect', $moola_redirect);
	}
	
	// this will handle redirects
	static function thankyou($order_id)  {
	   $redirect = get_option('moolamojo-woocom-redirect');
	   if(!empty($redirect)) {
	      update_option('moolamojo-woocom-redirect', '');
	      wp_redirect($redirect);
	   }
	}
}