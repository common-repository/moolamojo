<?php
class MoolaMojoLevels {
   static function manage() {
      global $wpdb;

      // sanitize input      
      if(!empty($_POST['add']) or !empty($_POST['save'])) {
         $name = sanitize_text_field($_POST['name']);
         $required_moola = intval($_POST['required_moola']);
         $is_reversible = empty($_POST['is_reversible']) ?  0 : 1;
      }
      
      if(!empty($_POST['add']) and check_admin_referer('moolamojo_levels')) {
         $wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_LEVELS." SET
            name=%s, required_moola=%d, is_reversible=%d", $name, $required_moola, $is_reversible));            
         moola_redirect("admin.php?page=moolamojo_levels");   
      }
      
      if(!empty($_POST['save']) and check_admin_referer('moolamojo_levels')) {
         $wpdb->query($wpdb->prepare("UPDATE ".MOOLA_LEVELS." SET
            name=%s, required_moola=%d, is_reversible=%d WHERE id=%d", 
            $name, $required_moola, $is_reversible, intval($_POST['id'])));         
      }
      
      if(!empty($_POST['del']) and check_admin_referer('moolamojo_levels')) {
         $wpdb->query($wpdb->prepare("DELETE FROM ".MOOLA_LEVELS." WHERE id=%d", intval($_POST['id'])));
         moola_redirect("admin.php?page=moolamojo_levels");    
      }
      
      $levels = $wpdb->get_results("SELECT * FROM ".MOOLA_LEVELS." ORDER BY required_moola DESC");
      
      include(MOOLA_PATH . '/views/levels.html.php');
   } // end manage
   
   // update user's level accordingly to the balance
   static function update($user_id, $balance = null) {
   	global $wpdb;
   	if($balance === null) $balance = get_user_meta($user_id, 'moolamojo_balance', true); 
   	
   	$current_level = self :: get_level($user_id);
   	
   	// shall we go to higher level? If yes, find it
   	$new_level = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".MOOLA_LEVELS."
   		WHERE id!=%d AND required_moola <= %d AND required_moola > %d
   		ORDER BY required_moola DESC LIMIT 1",
   		$current_level->id, $balance, $current_level->required_moola));

   	// else check if the current user level is reversible. If yes, check whether we should go to lower level
   	if(empty($new_level->id) and !empty($current_level->is_reversible)) {   	
         // shall we reverse the current level?
         if($balance < $current_level->required_moola) {
            update_user_meta($user_id, 'moolamojo_level', 0); // reverse to no level, don't fire action
            $new_level = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".MOOLA_LEVELS."
         		WHERE id!=%d AND required_moola <= %d AND required_moola < %d
         		ORDER BY required_moola DESC LIMIT 1",
         		$current_level->id, $balance, $current_level->required_moola));  		
         }
   	}

   	// finally assign the new level only if new level is found
   	if(!empty($new_level->id)) {
   		update_user_meta($user_id, 'moolamojo_level', $new_level->id);
   		do_action('moolamojo-changed-level', $user_id, $new_level->id);
   	}
   } // update
   
   // get current user level if any
   static function get_level($user_id) {
   	global $wpdb;
   	
   	$current_level_id = get_user_meta($user_id, 'moolamojo_level', true);
   	if(empty($current_level_id)) return (object)array("id"=>0, "required_moola"=>0);
   	
   	$level = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".MOOLA_LEVELS."
   		WHERE id=%d", $current_level_id));
   		
   	return $level;	
   } // end get_level
}