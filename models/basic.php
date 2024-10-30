<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// main model containing general config and UI functions
class MoolaMojo {
   static function install($update = false) {
   	global $wpdb;	
   	$wpdb -> show_errors();
   	
   	if(!$update) self::init();
   	
   	// currency packages
   	if($wpdb->get_var("SHOW TABLES LIKE '".MOOLA_PACKAGES."'") != MOOLA_PACKAGES) {        
			$sql = "CREATE TABLE `" . MOOLA_PACKAGES . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `price` DECIMAL(10,2) NOT NULL,
              `moola` INT UNSIGNED NOT NULL DEFAULT 0					 		  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	  // transactions - purchases of currency packages, cashout requests, deduction and addion of currencies for actions
	  // currency packages
   	if($wpdb->get_var("SHOW TABLES LIKE '".MOOLA_TRANSACTIONS."'") != MOOLA_TRANSACTIONS) {        
			$sql = "CREATE TABLE `" . MOOLA_TRANSACTIONS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
              `datetime` DATETIME,
              `amount_moola` INT NOT NULL DEFAULT 0,
              `amount_cash` DECIMAL(10,2) /* only for purchases and cash out */,
              `action` VARCHAR(255) NOT NULL DEFAULT '' /* textual descritpion of what happened */,
              `action_table` VARCHAR(255) NOT NULL DEFAULT '' /* table name when the action is related to another DB table */,
              `action_id` INT UNSIGNED NOT NULL DEFAULT 0 				 		  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	  // products or services ordered by virtual currency
   	if($wpdb->get_var("SHOW TABLES LIKE '".MOOLA_ORDERS."'") != MOOLA_ORDERS) {        
			$sql = "CREATE TABLE `" . MOOLA_ORDERS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,              
              `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
              `amount` INT UNSIGNED NOT NULL DEFAULT 0,
              `product_name` VARCHAR(255) NOT NULL DEFAULT '',
              `description` TEXT,
              `datetime` DATETIME,
              `status` VARCHAR(100) NOT NULL DEFAULT 'paid' /* paid, pending, completed */ 					 		  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	  // stored products/services for future usage and reference
	  if($wpdb->get_var("SHOW TABLES LIKE '".MOOLA_PRODUCTS."'") != MOOLA_PRODUCTS) {        
			$sql = "CREATE TABLE `" . MOOLA_PRODUCTS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,              
              `name` VARCHAR(255) NOT NULL DEFAULT '',
              `price` INT UNSIGNED NOT NULL DEFAULT 0,
              `button_text` VARCHAR(255) NOT NULL DEFAULT '',
              `button_classes` VARCHAR(255) NOT NULL DEFAULT '',
              `button_confirmation` VARCHAR(255) NOT NULL DEFAULT '',
              `button_store` TINYINT UNSIGNED NOT NULL DEFAULT 0,
              `button_redirect_url` VARCHAR(255) NOT NULL DEFAULT '',
              `button_num_clicks` TINYINT UNSIGNED NOT NULL DEFAULT 0,
              `button_form_tags` TINYINT UNSIGNED NOT NULL DEFAULT 0,
              `button_name` VARCHAR(255) NOT NULL DEFAULT ''
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	   // possible user levels unlocked by user's balance
   	if($wpdb->get_var("SHOW TABLES LIKE '".MOOLA_LEVELS."'") != MOOLA_LEVELS) {        
			$sql = "CREATE TABLE `" . MOOLA_LEVELS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,              
              `name` VARCHAR(255) NOT NULL DEFAULT '',
              `required_moola` INT UNSIGNED NOT NULL DEFAULT 0,
              `is_reversible` TINYINT UNSIGNED NOT NULL DEFAULT 0             				 		  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
   	
      moolamojo_add_db_fields(array(   
         array("name"=>"paycode", "type"=>"VARCHAR(100) NOT NULL DEFAULT ''"),
      ),
      MOOLA_TRANSACTIONS);
      
      // fix amount_moola this column should be signed
      $moola_version = get_option('moola_version');
      if($moola_version < 0.09) {
      	$wpdb->query("ALTER TABLE ".MOOLA_TRANSACTIONS." CHANGE `amount_moola` `amount_moola` INT NOT NULL DEFAULT 0");
      } 
   	
	  	$currency = get_option('moolamojo_currency');
	   if(empty($currency)) update_option('moolamojo_currency', 'moola');		
	  	  	  
	   update_option('moola_version', 0.1);
	   // exit;
   }
   
   // main menu
   static function menu() {
		$manage_caps = current_user_can('manage_options') ? 'manage_options' : 'moola_manage';   	
   	
   	add_menu_page(__('Moola Mojo', 'moola'), __('Moola Mojo', 'moola'), $manage_caps, "moolamojo", array('MoolaMojo', 'options'));  
   	add_submenu_page('moolamojo', __('Settings', 'moola'), __('Settings', 'moola'), $manage_caps, "moolamojo", array('MoolaMojo', 'options'));
   	if(get_option('moolamojo_allow_purchase')) {
   	   add_submenu_page('moolamojo', __('Currency Packages', 'moola'), __('Currency Packages', 'moola'), $manage_caps, "moolamojo_packages", array('MoolaMojoPackages', 'manage'));
   	}
   	add_submenu_page('moolamojo', __('Actions', 'moola'), __('Actions', 'moola'), $manage_caps, "moolamojo_actions", array('MoolaMojoActions', 'manage'));
   	add_submenu_page('moolamojo', __('Sell products', 'moola'), __('Sell products', 'moola'), $manage_caps, "moolamojo_button_generator", array('MoolaMojoButtons', 'generate'));
   	add_submenu_page('moolamojo', __('Orders', 'moola'), __('Orders', 'moola'), $manage_caps, "moolamojo_orders", array('MoolaMojoOrders', 'manage'));
   	add_submenu_page('moolamojo', __('Levels', 'moola'), __('Levels', 'moola'), $manage_caps, "moolamojo_levels", array('MoolaMojoLevels', 'manage'));
   	add_submenu_page('moolamojo', __('Transactions', 'moola'), __('Transactions', 'moola'), $manage_caps, "moolamojo_history", array('MoolaMojoHistory', 'view'));
   	add_submenu_page('moolamojo', __('Help', 'moola'), __('Help', 'moola'), $manage_caps, "moolamojo_help", array('MoolaMojo', 'help'));
   	add_submenu_page(null, __('Manually Adjust Balance', 'moola'), __('Manually Adjust Balance', 'moola'), $manage_caps, "moolamojo_adjust_balance", array('MoolaMojoUser', 'adjust'));
	}
	
	// admin CSS and JS
	static function admin_scripts() {
		wp_enqueue_script('jquery');
		
		// moola's own admin Javascript
		wp_register_script(
				'moola-admin',
				MOOLA_URL.'js/admin.js',
				false,
				'0.0.1',
				false
		);
		wp_enqueue_script("moola-admin");
	
		 wp_enqueue_style(
		'moola-style',
		MOOLA_URL.'css/main.css',
		array(),
		'0.0.1');
		
		$translation_array = array(
			'ajax_url' => admin_url('admin-ajax.php'),
		);	
		wp_localize_script( 'moola-admin', 'moola_i18n', $translation_array );	
	}	
	
	// front-end CSS and JS
	static function scripts() {
	   wp_enqueue_script('jquery');
	}

	// initialization
	static function init() {
		global $wpdb;
		load_plugin_textdomain( 'moola', false, MOOLA_RELATIVE_PATH."/languages/" );
		// start session only on front-end and MoolaMojo admin pages
		if (isset($_GET['page']) and !session_id() and (strstr($_GET['page'], 'moolamojo') or !is_admin()) ) {
				@session_start();
		}
		
		// define table names 
      define('MOOLA_PACKAGES', $wpdb->prefix.'moolamojo_packages');
      define('MOOLA_TRANSACTIONS', $wpdb->prefix.'moolamojo_transactions');	
      define('MOOLA_ORDERS', $wpdb->prefix.'moolamojo_orders');
      define('MOOLA_LEVELS', $wpdb->prefix.'moolamojo_levels');
      define('MOOLA_PRODUCTS', $wpdb->prefix.'moolamojo_products');
		
		define('MOOLA_CURRENCY', get_option('moolamojo_currency'));
		define('MOOLA_REAL_CURRENCY', get_option('moolamojo_real_currency'));
				
		// shortcodes
		add_shortcode('moolamojo-packages', array('MoolaMojoShortcodes', 'packages'));
		add_shortcode('moolamojo-package', array('MoolaMojoShortcodes', 'package'));
		add_shortcode('moolamojo-balance', array('MoolaMojoShortcodes', 'balance'));
		add_shortcode('moolamojo-button', array('MoolaMojoShortcodes', 'button'));
		add_shortcode('moolamojo-user-level', array('MoolaMojoShortcodes', 'user_level'));
		add_shortcode('moolamojo-link', array('MoolaMojoShortcodes', 'link'));
		
		MoolaMojoActions :: add_actions();
		
		// handle Stripe payments
		if(!empty($_POST['moolamojo_stripe_pay'])) {
   		require_once(MOOLA_PATH.'/lib/stripe/init.php');
   		MoolaMojoStripe :: pay();
		} 
		
		// handle Paypal PDT & IPN
		// Paypal IPN
		add_filter('query_vars', array(__CLASS__, "query_vars"));
		add_action('parse_request', array("MoolaMojoPaypal", "parse_request"));
		if(!empty($_GET['moolamojo_pdt'])) MoolaMojoPaypal::paypal_ipn();
		
		if(!empty($_POST['moolamojo_submitted'])) {
		   $_POST['moolamojo_result'] = MoolaMojoButtons :: submit();
		}
		
		// actions
		add_action( 'wp_loaded', array(__CLASS__, "wp_loaded") );
		add_action('moolamojo_transaction', array('MoolaMojoActions', 'catch_action'), 10, 6);
		add_filter('manage_users_columns', array('MoolaMojoUser', 'add_custom_column'));
		add_action('manage_users_custom_column', array('MoolaMojoUser','manage_custom_column'), 10, 3);
		add_filter( 'manage_users_sortable_columns',        array( 'MoolaMojoUser', 'sortable_balance_column' ) );
		add_action( 'pre_user_query',  array( 'MoolaMojoUser', 'sort_by_balance' ) );
				
		// run activate
		$version = get_option('moola_version');
		if(empty($version) or $version < 0.1) self :: install(true);
		
		add_shortcode( 'wp_caption', 'menumoda_add_description' );
		add_shortcode( 'caption',    'menumoda_add_description' );


	}
	
	// handle Namaste vars in the request
	static function query_vars($vars) {
		$new_vars = array('moolamojo');
		$vars = array_merge($new_vars, $vars);
	   return $vars;
	} 	
		
	// manage general options
	static function options() {
		if(!empty($_POST['ok']) and check_admin_referer('moola_settings')) {
			$allow_checkout = empty($_POST['allow_checkout']) ? 0 : 1;
			$allow_purchase = empty($_POST['allow_purchase']) ? 0 : 1;
			if(empty($_POST['currency']) or $_POST['currency'] == 'other') $_POST['currency'] = sanitize_text_field($_POST['custom_currency']);
			if(empty($_POST['real_currency']) and !empty($_POST['custom_real_currency'])) $_POST['real_currency'] = $_POST['custom_real_currency'];
			if(empty($_POST['real_currency'])) $_POST['real_currency'] = 'USD';
			update_option('moolamojo_currency', sanitize_text_field($_POST['currency']));
			update_option('moolamojo_real_currency', sanitize_text_field($_POST['real_currency']));
			update_option('moolamojo_allow_checkout', $allow_checkout);
			update_option('moolamojo_allow_purchase', $allow_purchase);
			update_option('moolamojo_exchange_rate', floatval($_POST['exchange_rate']));
		}		
		
		if(!empty($_POST['moola_payment_options']) and check_admin_referer('moola_payment_options')) {
			update_option('moolamojo_accept_other_payment_methods', intval(@$_POST['accept_other_payment_methods']));
			update_option('moolamojo_other_payment_methods', moolamojo_strip_tags($_POST['other_payment_methods']));
			if(empty($_POST['currency'])) $_POST['currency'] = sanitize_text_field($_POST['custom_currency']);
			update_option('moolamojo_real_currency', sanitize_text_field($_POST['currency']));
			update_option('moolamojo_accept_paypal', intval(@$_POST['accept_paypal']));
			update_option('moolamojo_paypal_sandbox', intval(@$_POST['paypal_sandbox']));
			update_option('moolamojo_paypal_id', sanitize_text_field($_POST['paypal_id']));
			update_option('moolamojo_paypal_return', esc_url_raw($_POST['paypal_return']));
			$use_pdt = empty($_POST['use_pdt']) ? 0 : 1;
			update_option('moolamojo_use_pdt', $use_pdt);
			update_option('moolamojo_pdt_token', sanitize_text_field($_POST['pdt_token']));
			
			update_option('moolamojo_accept_stripe', intval(@$_POST['accept_stripe']));
			update_option('moolamojo_stripe_public', sanitize_text_field($_POST['stripe_public']));
			update_option('moolamojo_stripe_secret', sanitize_text_field($_POST['stripe_secret']));
			
			do_action('moolamojo-saved-options-payments');
		} 
		
		$currencies = array('moola', 'coins', 'points', '$'); 
		$currency = get_option('moolamojo_currency');
		
		$real_currency = get_option('moolamojo_real_currency');
		$real_currencies=array('USD'=>'$', "EUR"=>"&euro;", "GBP"=>"&pound;", "JPY"=>"&yen;", "AUD"=>"AUD",
	   "CAD"=>"CAD", "CHF"=>"CHF", "CZK"=>"CZK", "DKK"=>"DKK", "HKD"=>"HKD", "HUF"=>"HUF",
	   "ILS"=>"ILS", "INR"=>"INR", "MXN"=>"MXN", "NOK"=>"NOK", "NZD"=>"NZD", "PLN"=>"PLN", "SEK"=>"SEK",
	   "SGD"=>"SGD", "ZAR"=>"ZAR");		
	   $real_currency_keys = array_keys($real_currencies);        
		
		$allow_checkout = get_option('moolamojo_allow_checkout');
		$allow_purchase = get_option('moolamojo_allow_purchase');
		
      $accept_other_payment_methods = get_option('moolamojo_accept_other_payment_methods');
		$accept_paypal = get_option('moolamojo_accept_paypal');
		$accept_stripe = get_option('moolamojo_accept_stripe');    
		$use_pdt = get_option('moolamojo_use_pdt');     		
		
		require(MOOLA_PATH."/views/options.html.php");
	}	
	
	static function help() {
		require(MOOLA_PATH."/views/help.html.php");
	}	
	
	// called on wp_loaded
	static function wp_loaded() {
	   // parse pressing the moolamojo payment button
	   if(!empty($_POST['moolamojo_name'])) MoolaMojoButtons :: submit();
	   
	   // redirect when the [moolamojo-link] shortcode is used
	   if(!empty($_GET['moolamojo_goto'])) {
	      $url = esc_url_raw($_GET['moolamojo_goto']);
	      do_action('moolamojo-link-click', $url);
	      moola_redirect($url);
	   }
	} // end wp_loaded
}