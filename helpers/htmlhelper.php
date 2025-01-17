<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// safe redirect
function moola_redirect($url) {
	echo "<meta http-equiv='refresh' content='0;url=$url' />"; 
	exit;
}

// function to conditionally add DB fields
function moolamojo_add_db_fields($fields, $table) {
		global $wpdb;
		
		// check fields
		$table_fields = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
		$table_field_names = array();
		foreach($table_fields as $f) $table_field_names[] = $f->Field;		
		$fields_to_add=array();
		
		foreach($fields as $field) {
			 if(!in_array($field['name'], $table_field_names)) {
			 	  $fields_to_add[] = $field;
			 } 
		}
		
		// now if there are fields to add, run the query
		if(!empty($fields_to_add)) {
			 $sql = "ALTER TABLE `$table` ";
			 
			 foreach($fields_to_add as $cnt => $field) {
			 	 if($cnt > 0) $sql .= ", ";
			 	 $sql .= "ADD $field[name] $field[type]";
			 } 
			 
			 $wpdb->query($sql);
		}
}

// strip tags when user is not allowed to use unfiltered HTML
// keep some safe tags on
function moolamojo_strip_tags($content) {
   if(!current_user_can('unfiltered_html') and WATUPRO_UNFILTERED_HTML != 1) {
		$content = strip_tags($content, '<b><i><em><u><a><p><br><div><span><hr><font><img><strong>');
	}
	
	return $content;
}