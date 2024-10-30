<?php
if(!defined('ABSPATH')) exit;
 
class MoolaMojoPackages {
   static function manage() {
      global $wpdb;
      
      if(!empty($_POST['add']) and check_admin_referer('moola_packages')) {
         $wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_PACKAGES." SET
            price=%f, moola=%d", floatval($_POST['price']), intval($_POST['moola'])));
         moola_redirect("admin.php?page=moolamojo_packages");  
      }
      
      if(!empty($_POST['save']) and check_admin_referer('moola_packages')) {
         $wpdb->query($wpdb->prepare("UPDATE ".MOOLA_PACKAGES." SET
            price=%f, moola=%d WHERE id=%d", 
            floatval($_POST['price']), intval($_POST['moola']), intval($_POST['id'])));
      }
      
      if(!empty($_POST['del']) and check_admin_referer('moola_packages')) {
         $wpdb->query($wpdb->prepare("DELETE FROM ".MOOLA_PACKAGES." WHERE id=%d", intval($_POST['id'])));
         moola_redirect("admin.php?page=moolamojo_packages");  
      }
      
      if(!empty($_POST['award']) and !empty($_POST['user_name']) and check_admin_referer('moola_packages')) {
         // find user
         if(strstr($_POST['user_name'], '@')) {
            $user = get_user_by('email', sanitize_email($_POST['user_name']));
         }
         else $user = get_user_by('login', sanitize_text_field($_POST['user_name']));
         
         if(empty($user->ID)) $award_msg = __('User not found.', 'moola');
         else {            
            // find package & award
            $package = $wpdb->get_row($wpdb->prepare("SELECT id, moola FROM " . MOOLA_PACKAGES . " 
               WHERE id=%d", intval($_POST['package_id'])));
            MoolaMojoActions :: purchase_moola($user->ID, $package->id, $package->moola, '_manual');
            $award_msg = sprintf(__('The amount of %s %d has been manually awarded to %s.', 'moola'), MOOLA_CURRENCY, $package->moola, sanitize_text_field($_POST['user_name']));
         }
      }
      
      // select packages
      $packages = $wpdb->get_results("SELECT * FROM ".MOOLA_PACKAGES." ORDER BY moola");
      
      include(MOOLA_PATH . '/views/packages.html.php');
            
   } // end manage
}