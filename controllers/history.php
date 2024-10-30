<?php
if(!defined('ABSPATH')) exit;

// transactions history
class MoolaMojoHistory {
	// view history of translactions
   static function view() {
   	global $wpdb;
   	
   	// clear?
   	if(!empty($_POST['clear_history']) and check_admin_referer('moolamojo_clear_history')) {
   		$wpdb->query("DELETE FROM ".MOOLA_TRANSACTIONS);
   	}
   	
   	// these vars will be configurable in a future release
   	$ob = "tT.id";
		$dir = "DESC"; 
		
		$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
		$limit = 50;
		
   	$items = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS tU.display_name, tT.* 
   		FROM ".MOOLA_TRANSACTIONS." tT JOIN {$wpdb->users} tU ON tU.ID = tT.user_id 
   		ORDER BY $ob $dir LIMIT $offset, $limit");
   	$count = $wpdb->get_var("SELECT FOUND_ROWS()");	
   		
   	$date_format = get_option('date_format');
   	$time_format = get_option('time_format');	
   	
   	include(MOOLA_PATH . '/views/history.html.php');
   }
}