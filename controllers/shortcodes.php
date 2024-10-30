<?php
if(!defined('ABSPATH')) exit;

class MoolaMojoShortcodes {
   // displays a page with all available currency packages and buy buttons
   static function packages($atts) {
      global $wpdb, $post;
      
      ob_start();
      
      if(!empty($_POST['moolamojo_buy_package'])) {
         return self :: package_payment();
      }
      
      $text = empty($atts['text']) ? __('%d of %s for %s', 'moola') : esc_attr($atts['text']);
      $button_text = empty($atts['button_text']) ? __('Buy now!', 'moola') : esc_attr($atts['button_text']);
      
      $packages = $wpdb->get_results("SELECT * FROM ".MOOLA_PACKAGES." ORDER BY moola");
      
     
      include(MOOLA_PATH . '/views/packages-table.html.php');
      $content = ob_get_clean();
      return $content;
   } // end packages()
   
   static function package($atts) {
      global $wpdb, $post;
      
      ob_start();
      
      if(!empty($_POST['moolamojo_buy_package'])) {
         return self :: package_payment();
      }
      
      $text = empty($atts['text']) ? __('%d %s for %s', 'moola') : esc_attr($atts['text']);
      $button_text = empty($atts['button_text']) ? __('Buy now!', 'moola') : esc_attr($atts['button_text']);
      
      $package = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".MOOLA_PACKAGES." WHERE id=%d", intval($atts['id'])));
      
      include(MOOLA_PATH . '/views/package-buy-button.html.php');
      $content = ob_get_clean();
      return $content;
   } // end packages()
   
   // displays the payment options for package    
   static function package_payment() {
      global $wpdb, $post, $user_ID;
      
      // select package and prepare texts for the payment page
      $package = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".MOOLA_PACKAGES." WHERE id=%d", intval($_POST['package_id']))); 
      $content = '<h1>'.__('Purchasing a package of virtual currency', 'moola').'</h1>
      <p>'.sprintf(__('You are about to purchase %d of %s for %s%s', 'moola'), $package->moola, MOOLA_CURRENCY, MOOLA_REAL_CURRENCY, $package->price).'</p>';
      $content .= '<p>'.__('Payment options:', 'moola').'</p>';
      $item_name = sprintf(__('%d of %s', 'moola'), $package->moola, MOOLA_CURRENCY);
      $item_id = $package->id;
      $amount = $package->price;
      
      $accept_paypal = get_option('moolamojo_accept_paypal');
		$paypal_id = get_option('moolamojo_paypal_id');
      
      // return URL
		$paypal_return = get_option('moolamojo_paypal_return');			
		if(empty($paypal_return)) $paypal_return =  get_permalink($post->ID);
		$protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
		if(!strstr($paypal_return, 'http')) $paypal_return = $protocol.'://'.$paypal_return;        
				 
      $return_url = (get_option('moolamojo_use_pdt') == 1) ? esc_url(add_query_arg(array('moolamojo_pdt' => 1, 'post_id'=>$post->ID), trim($paypal_return))) : trim($paypal_return);
      $notify_url = site_url('?moolamojo=paypal&item_id='.$package->id.'&user_id='.$user_ID.'&item_type=package');
      
      $accept_other_payment_methods = get_option('moolamojo_accept_other_payment_methods');
   	if($accept_other_payment_methods) {
			$other_payment_methods = stripslashes(get_option('moolamojo_other_payment_methods'));
			$other_payment_methods = str_replace('{{item-id}}', $package->id, $other_payment_methods);
			$other_payment_methods = str_replace('{{item-name}}', $item_name, $other_payment_methods);
			$other_payment_methods = str_replace('{{user-id}}', $user_ID, $other_payment_methods);
			$other_payment_methods = str_replace('{{amount}}', $amount, $other_payment_methods);
			$other_payment_methods = str_replace('{{item-type}}', 'package', $other_payment_methods);
			$other_payment_methods = do_shortcode($other_payment_methods);
		}
		
		$accept_stripe = get_option('moolamojo_accept_stripe');
		$stripe = MoolaMojoStripe::load();
      
      include(MOOLA_PATH . '/views/pay.html.php');
      $content = ob_get_clean();
      return $content;
   }
   
   // shows user's balance of virtual currency
   static function balance($atts) {
      global $user_ID;
      
      $user_id = (empty($atts['user_id']) or !is_numeric($atts['user_id'])) ? $user_ID : intval($atts['user_id']);
      $balance = get_user_meta($user_id, 'moolamojo_balance', true);
      
      return $balance;
   }
   
   // list of accepted arguments (params):
   // button_text - the button value
   // generate_form_tags - whether to generate form tags around the button. Defaults to yes. Otherwise exists in a form
   // product_name - the name of product or service you are charging for
   // classes - CSS classes for the button
   // confirmation_required = no/javascript/checkbox (when checkbox, attr checkbox_text is accepted)
   // action = what action to call on submit (besides the internal action we call for charing the points and inserting the order)
   // description_type = text/form_fields, defaults to text. Then "description" attribute required. When form_fields, reads from the form
   // store_order - true/false, defaults to true. Whether to store the order in the internal orders database
   // redirect_url - URL to redirect to after "payment". Defaults to current URL
   // text_after_payment - text to show above the button if user has just purchased. Possible only if we redirect to the same page
   // num_clicks - 0 or number, defaults to 0 = unlimited. How many times same user can buy this.
   // button_name - optional. If you are using several buttons on the same page you must provide different name for each of them. 
   static function button($atts) {
      global $post, $wpdb, $user_ID;
      
      // moolamojo buttons are only for logged in users in this version
      // to-do: go-to URL in case of non-logged in
      if(!is_user_logged_in()) return '';
      
      // normalize params
      $atts['charge']  = empty($atts['charge']) ?  1 :  intval($atts['charge']);    
      $button_text = empty($atts['button_text']) ? __('Buy now!', 'moola') : $atts['button_text'];
      $atts['product_name'] = empty($atts['product_name']) ? __('Unnamed', 'moola') : $atts['product_name'];
      $classes = empty($atts['classes']) ? '' : $atts['classes'];
      $atts['confirmation_required'] = (empty($atts['confirmation_required']) or !in_array($atts['confirmation_required'], array('none', 'js', 'checkbox'))) 
         ? 'js' : $atts['confirmation_required']; 
      $atts['description_type'] = (empty($atts['description_type']) or !in_array($atts['description_type'], array('text', 'form_fields'))) 
         ? 'text' : $atts['description_type'];
      $atts['description'] = empty($atts['description']) ? '' : $atts['description'];   
      $atts['store_order'] = (empty($atts['store_order']) or $atts['store_order'] = 'yes') ? true : false;
      $atts['redirect_url'] = empty($atts['redirect_url']) ? get_permalink($post->ID) : $atts['redirect_url'];
      $atts['num_clicks']  = empty($atts['num_clicks']) ?  0 : intval($atts['num_clicks']);  
      $generate_form_tags = (empty($atts['generate_form_tags']) or $atts['generate_form_tags'] == 'true') ? true : false;      
      $unique_name = empty($atts['button_name']) ?  'moolamojo_atts' : 'moolamojo_'.$atts['button_name'];
      $atts['custom_action']  = empty($atts['custom_action']) ?  '' :  $atts['custom_action'];    
      
      // JS confirmation if required
      $onclick = '';
      if($atts['confirmation_required'] == 'js') {
         $onclick = 'onclick="return confirm('."'".__('Are you sure?', 'moola')."'".');"';
      }
      
      // prepare the button
      $button = '';
      
      if(!empty($atts['num_clicks']) and !empty($user_ID)) {
         $num_orders = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".MOOLA_ORDERS." 
            WHERE product_name=%s AND user_id=%d", $atts['product_name'], $user_ID));
         if($num_orders >= $atts['num_clicks']) return __("N/a", 'moola');   
      }  
      
      if($generate_form_tags) $button .= '<form method="post" action="'.get_permalink(@$post->ID).'">'."\n";
      
      // checkbox valdation if required
      if($atts['confirmation_required'] == 'checkbox') {
         $button .= '<input type="checkbox" name="'.$unique_name.'_chk_confirm" value="1"> '.__('Confirm', 'moola').'<br>';
         $onclick = 'onclick="return this.form.'.$unique_name.'_chk_confirm.checked;"';
      }
      
      if(!empty($_POST['moolamojo_submit_'.$unique_name]) and !empty($_POST['moolamojo_result'])) {
         $button .= '<p><b>'.esc_attr($_POST['moolamojo_result']).'</b></p>';
      }
      
      $button .= '<input type="hidden" name="'.$unique_name.'" value="'.base64_encode(serialize($atts)).'">'."\n";
      $button .= '<input type="hidden" name="moolamojo_submitted" value="1">';  
      $button .= '<input type="submit" value="'.$button_text.'" class="'.$classes.'" name="moolamojo_submit_'.$unique_name.'" '.$onclick.'>';    
      if($generate_form_tags) $button .= '</form>';
      
      return $button;
   }
   
   // displays the level of an user 
   static function user_level($atts) {
      global $user_ID;
      
      $user_id = empty($atts['user_id']) ? $user_ID : intval($atts['user_id']);
      $default_text = empty($atts['default_text']) ? __('N/a', 'moola') : $atts['default_text'];
      if(empty($user_id)) return $default_text;
            
      $level = MoolaMojoLevels :: get_level($user_id);
      if(!empty($level->id)) return stripslashes($level->name);
      
      return $default_text;
   }
   
   // generates clickable link that fires the click action
   static function link($atts) {
      $url = empty($atts['url']) ? site_url() : $atts['url'];
      $text = empty($atts['text']) ? $url : $atts['text'];
      $target = empty($atts['new_window']) ? '_self' : '_blank';
      
      // generate target URL
      $url = add_query_arg('moolamojo_goto', $url, home_url());
      
      return '<a href="'.$url.'" target="'.$target.'">'.$text.'</a>';
   }
}