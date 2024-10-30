<?php
/*
Plugin Name: MoolaMojo
Plugin URI: https://namaste-lms.org/moolamojo
Description: Virtual currency that just works
Author: Kiboko Labs
Version: 0.7.5
Author URI: http://kibokolabs.com
License: GPLv2 or later
Text-domain: moola
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'MOOLA_PATH', dirname( __FILE__ ) );
define( 'MOOLA_RELATIVE_PATH', dirname( plugin_basename( __FILE__ )));
define( 'MOOLA_URL', plugin_dir_url( __FILE__ ));

// require controllers and models
require_once(MOOLA_PATH.'/models/basic.php');
require_once(MOOLA_PATH.'/controllers/packages.php');
require_once(MOOLA_PATH.'/controllers/actions.php');
require_once(MOOLA_PATH.'/controllers/shortcodes.php');
require_once(MOOLA_PATH.'/controllers/buttons.php');
require_once(MOOLA_PATH.'/helpers/htmlhelper.php');
require_once(MOOLA_PATH.'/models/stripe.php');
require_once(MOOLA_PATH.'/models/paypal.php');
require_once(MOOLA_PATH.'/controllers/levels.php');
require_once(MOOLA_PATH.'/models/user.php');
require_once(MOOLA_PATH.'/controllers/orders.php');
require_once(MOOLA_PATH.'/controllers/history.php');
require_once(MOOLA_PATH.'/controllers/woocom.php');

add_action('init', array("MoolaMojo", "init"));

register_activation_hook(__FILE__, array("MoolaMojo", "install"));
add_action('admin_menu', array("MoolaMojo", "menu"));
add_action('admin_enqueue_scripts', array("MoolaMojo", "admin_scripts"));

// show the things on the front-end
add_action( 'wp_enqueue_scripts', array("MoolaMojo", "scripts"));

// other actions
add_action('wp_ajax_moola_ajax', 'moola_ajax');
add_action('wp_ajax_nopriv_moola_ajax', 'moola_ajax');