<?php
class MoolaMojoButtons {
   // the shortcode generator for the "sell stuff" button
   static function generate() {
   	global $wpdb; 
   	
		// returns the list of products
		if(!empty($_GET['list_products'])) return self :: list_products();   	
   	
		if(!empty($_POST['add_product']) and check_admin_referer('moolamojo_product')) { 
		  // add new product in the DB
		   self :: prepare_vars();
		   
		   // if name exists don't store
		   $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".MOOLA_PRODUCTS." WHERE name=%s", sanitize_text_field($_POST['product_name'])));
		   $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".MOOLA_PRODUCTS." WHERE name=%s", sanitize_text_field($_POST['product_name'])));
		   
			if(!$exists) {
				$wpdb->query($wpdb->prepare("INSERT INTO " . MOOLA_PRODUCTS." SET 
				 	name=%s, price=%d, button_text=%s, button_classes=%s, button_confirmation=%s,
				 	button_store=%d, button_redirect_url=%s, button_num_clicks=%d, button_form_tags = %d, button_name=%s",
				 	sanitize_text_field($_POST['product_name']), intval($_POST['charge']), sanitize_text_field($_POST['button_text']), 
				 	sanitize_text_field($_POST['classes']), sanitize_text_field($_POST['confirmation_required']),
				 	intval($_POST['store_order']), esc_url_raw($_POST['redirect_url']), intval($_POST['num_clicks']), intval($_POST['generate_form_tags']),
				 	sanitize_text_field($_POST['button_name'])));

			}
		}   	
		
		if(!empty($_POST['save_product']) and check_admin_referer('moolamojo_product')) { 
		  // save existing product
		  self :: prepare_vars();
		 	$wpdb->query($wpdb->prepare("UPDATE " . MOOLA_PRODUCTS." SET 
			 	name=%s, price=%d, button_text=%s, button_classes=%s, button_confirmation=%s,
			 	button_store=%d, button_redirect_url=%s, button_num_clicks=%d, button_form_tags = %d, button_name=%s
			 	WHERE id=%d",
			 	sanitize_text_field($_POST['product_name']), intval($_POST['charge']), sanitize_text_field($_POST['button_text']), sanitize_text_field($_POST['classes']), 
			 	sanitize_text_field($_POST['confirmation_required']), intval($_POST['store_order']), esc_url_raw($_POST['redirect_url']), intval($_POST['num_clicks']), 
			 	intval($_POST['generate_form_tags']), sanitize_text_field($_POST['button_name']), intval($_GET['id'])));
		}
		
		// existing product loaded?
		if(!empty($_GET['id'])) {
			$product = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . MOOLA_PRODUCTS . " WHERE id=%d", intval($_GET['id'])));
			// assign product vars to POST vars so we can pre-fill. Even if we changed something, it's already written to the DB
			// by the query above
			$_POST['charge']= $product->price;
			$_POST['button_text'] = $product->button_text;
			$_POST['product_name'] = $product->name;
			$_POST['classes'] = $product->button_classes;
			$_POST['confirmation_required'] = $product->button_confirmation;
			$_POST['store_order'] = $product->button_store ? 'true' : 'false';
			$_POST['redirect_url'] = $product->button_redirect_url;
			$_POST['num_clicks'] = $product->button_num_clicks;
			$_POST['generate_form_tags'] = $product->button_form_tags ? 'true' : 'false'; 
			$_POST['button_name'] = $product->button_name;
		}
   	
      $shortcode = '[moolamojo-button';
      
      if(!empty($_POST['charge'])) $shortcode .= ' charge="'.intval($_POST['charge']).'"';
      if(!empty($_POST['button_text'])) $shortcode .= ' button_text="'.esc_attr($_POST['button_text']).'"';
      if(!empty($_POST['product_name'])) $shortcode .= ' product_name="'.esc_attr($_POST['product_name']).'"';      
      if(!empty($_POST['classes'])) $shortcode .= ' classes="'.esc_attr($_POST['classes']).'"';
      if(!empty($_POST['confirmation_required'])) $shortcode .= ' confirmation_required="'.esc_attr($_POST['confirmation_required']).'"';
      if(!empty($_POST['description_type'])) $shortcode .= ' description_type="'.esc_attr($_POST['description_type']).'"';
      if(!empty($_POST['store_order'])) $shortcode .= ' store_order="'.intval($_POST['store_order']).'"';
      if(!empty($_POST['redirect_url'])) $shortcode .= ' redirect_url="'.esc_url_raw($_POST['redirect_url']).'"';
      if(!empty($_POST['num_clicks'])) $shortcode .= ' num_clicks="'.intval($_POST['num_clicks']).'"';
      if(!empty($_POST['generate_form_tags'])) $shortcode .= ' generate_form_tags="'.intval($_POST['generate_form_tags']).'"';
      if(!empty($_POST['button_name'])) $shortcode .= ' button_name="'.esc_attr($_POST['button_name']).'"';
      if(!empty($_POST['custom_action'])) $shortcode .= ' custom_action="'.esc_attr($_POST['custom_action']).'"';
      
      $shortcode .=']';      
      
      include(MOOLA_PATH . '/views/button-generator.html.php');
   }
   
   // list the stored products in the DB
   static function list_products() {
   	global $wpdb;
   	
   	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
   	$page_limit = 20;
   	
   	if(!empty($_GET['del']) and check_admin_referer('moolamojo_products')) {
   		$wpdb->query($wpdb->prepare("DELETE FROM ".MOOLA_PRODUCTS." WHERE id=%d", intval($_GET['id'])));
   	}
   	
   	$products = $wpdb->get_results($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM ".MOOLA_PRODUCTS."
   		ORDER BY name LIMIT %d, %d", $offset, $page_limit));
   	$count = $wpdb->get_var("SELECT FOUND_ROWS()");	
   		
   	include(MOOLA_PATH .'/views/products.html.php');	
   }
   
   // prepare & sanitize some POST vars for DB input
   static function prepare_vars() {
   	$_POST['product_name'] = sanitize_text_field($_POST['product_name']);
   	$_POST['charge'] = intval($_POST['charge']);
   	$_POST['button_text'] = sanitize_text_field($_POST['button_text']);
   	$_POST['classes'] = sanitize_text_field($_POST['classes']);
   	$_POST['confirmation_required'] = sanitize_text_field($_POST['confirmation_required']);
   	$_POST['store_order'] = (@$_POST['store_order'] == 'true') ? 1 : 0;
   	$_POST['redirect_url'] = esc_url_raw($_POST['redirect_url']);
   	$_POST['num_clicks'] = intval($_POST['num_clicks']);
   	$_POST['generate_form_tags'] = (@$_POST['generate_form_tags'] == 'true') ? 1 : 0;
   	$_POST['button_name'] = sanitize_text_field($_POST['button_name']);
   }
   
   // handle when the button is submitted
   static function submit() {
      global $user_ID, $wpdb;
      if(!is_user_logged_in()) return false;      
      
      // find the name of the clicked submit button
      // from it we'll figure out $unique_name which is the name of the field with serialized attributes
      foreach($_POST as $key => $val) {
         if(preg_match("/^moolamojo_submit/", $key)) {
            $unique_name = str_replace('moolamojo_submit_', '', $key);
         }
      }
      
      if(empty($unique_name)) return false;
      
      $atts = unserialize(base64_decode($_POST[$unique_name]));
        
      // check for confirmation if required
      if(!empty($atts['confirmation_required']) 
         and $atts['confirmation_required'] == 'checkbox'
         and empty($_POST[$unique_name.'_chk_confirm'])) return false;
         
      // maximum times used?
      if(!empty($atts['num_clicks'])) {
         $num_orders = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".MOOLA_ORDERS." 
            WHERE product_name=%s AND user_id=%d", sanitize_text_field($atts['product_name']), $user_ID));
         if($num_orders >= $atts['num_clicks']) return __("You can't buy more of this.", 'moola');   
      }   
         
      // check user's balance
      $charge = intval($atts['charge']);
      $balance = get_user_meta($user_ID, 'moolamojo_balance', true);
      if($charge > $balance) return __('You have not enough currency to pay for this.', 'moola');
      
      // charge the moola
      $action_table = empty($atts['action_table']) ? '' : sanitize_text_field($atts['action_table']);
      $action_id = empty($atts['action_id']) ? 0 : intval($action_id);
      $balance -= $charge;
      update_user_meta($user_ID, 'moolamojo_balance', $balance);
      
       // insert in transactions
       $wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_TRANSACTIONS." SET 
          user_id=%d, datetime=%s, amount_moola=%d, action=%s, action_table=%s, action_id=%d",
          $user_ID, current_time('mysql'), $charge, 'spend', $action_table, $action_id));
      
      // insert order if required
      if(!empty($atts['store_order']) and $atts['store_order'] == 'true') {
         // prepare description
         $description = '';
         if($atts['description_type'] == 'text') $description = moolamojo_strip_tags($atts['description']);    
         if($atts['description_type'] == 'form_fields') {
            $form_fields = explode(',', $atts['description']);
            foreach($form_fields as $cnt => $form_field) {
               $form_field = trim($form_field);
               if(!empty($_POST[$form_field])) {
                  if($cnt > 0) $description .= ', ';
                  $description .= $_POST[$form_field];
               }
            }
         } // end handling form fields description
         
         $wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_ORDERS." 
            SET user_id=%d, amount=%d, product_name=%s, description=%s, datetime=%s, status='paid'",
            $user_ID, intval($atts['charge']), sanitize_text_field($atts['product_name']), $description, current_time('mysql')));
      }
      
      // call actions
      do_action('moolamojo-submitted', $atts);
      if(!empty($atts['custom_action'])) do_action(sanitize_text_field($atts['custom_action']), $atts);
      
      // redirect      
      if(!empty($atts['redirect_url'])) moola_redirect(esc_url_raw($atts['redirect_url']));     
   }
}