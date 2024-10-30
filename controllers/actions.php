<?php
if(!defined('ABSPATH')) exit;

// manage and process adding / taking moolas for actions
class MoolaMojoActions {
   // this will add all necessary add_action calls on init()
   static function add_actions() {
      add_action('user_register', array(__CLASS__, 'user_register'));
      add_action('wp_login', array(__CLASS__, 'wp_login'), 10, 2);
      add_action('publish_post', array(__CLASS__, 'publish_post'), 10, 2);
      add_action('comment_post', array(__CLASS__, 'comment_post'), 10, 2);
      add_action('transition_comment_status', array(__CLASS__, 'change_comment_status'), 10, 3);
      add_action('moolamojo-link-click', array(__CLASS__, 'link_click'));
      
      // WooCommerce
		add_action('woocommerce_order_status_completed', array('MoolaMojoWooCom', 'completed_order'));
		add_action('woocommerce_thankyou', array('MoolaMojoWooCom', 'thankyou'));

   }   
   
   static function manage() {
      $actions = get_option('moolamojo_actions');
      
      if(empty($actions)) {
         // actions not yet initialized in the DB? Do it now with zeros      
         $actions = array(
            'user_register'=> array('description' => __('When user registers in the site', 'moola'), 'points' => 0, 'action'=>'reward'),
            'wp_login'=> array('description' => __('When user logs in (assigned no more than once per day)', 'moola'), 'points' => 0, 'action'=>'reward'),
            'publish_post'=> array('description' => __('When user publishes a post (or post is approved)', 'moola'), 'points' => 0, 'action'=>'reward'),
            'comment_post'=> array('description' => __('When user posts a comment', 'moola'), 'points' => 0, 'action'=>'reward'),
            'link_click' => array('description' => sprintf(__('When user clicks on a link made with %s shortcode', 'moola'), '[moolamojo-link]'), 'points' => 0, 'action'=>'reward'),
         );
         
         update_option('moolamojo_actions', $actions);
      }  
      
      if(!empty($_POST['save_actions']) and check_admin_referer('moolamojo_actions')) {
         // fill the action & point for each action in the array
         $actions['user_register']['action'] = ($_POST['user_register_action'] != 'charge') ? 'reward' : 'charge';
         $actions['user_register']['points'] = intval($_POST['user_register_points']);
         $actions['wp_login']['action'] = ($_POST['wp_login_action'] == 'reward') ? 'reward' : 'charge';
         $actions['wp_login']['points'] = intval($_POST['wp_login_points']);
         $actions['publish_post']['action'] = ($_POST['publish_post_action'] != 'charge') ? 'reward' : 'charge';
         $actions['publish_post']['points'] = intval($_POST['publish_post_points']);
         $actions['comment_post']['action'] = ($_POST['comment_post_action'] != 'charge') ? 'reward' : 'charge';
         $actions['comment_post']['points'] = intval($_POST['comment_post_points']);
         $actions['link_click']['action'] = ($_POST['link_click_action'] != 'charge') ? 'reward' : 'charge';
         $actions['link_click']['points'] = intval($_POST['link_click_points']);
         
         update_option('moolamojo_actions', $actions);
      }
      
      include(MOOLA_PATH . '/views/actions.html.php');      
   } // end manage actions
   
   // below are specific function calls to handle the WP actions
   // let their names match exactly the action hook names. Not only this is good for readability but might help 
   // for some more automated way of coding this in the future
   static function user_register($user_id) {
      self :: transfer_moola('user_register', $user_id, '_registration');
   }
   
   static function wp_login($user_login, $user) {
      // already logged in today?
      $last_login_date = get_user_meta($user->ID, 'moolamojo_last_login', true);
      if($last_login_date == date('Y-m-d', current_time('timestamp'))) return false;      
      
      update_user_meta($user->ID, 'moolamojo_last_login', date('Y-m-d', current_time('timestamp')));
      self :: transfer_moola('wp_login', $user->ID, '_login');
   }
   
   static function publish_post($ID, $post) {
      $user_id = $post->post_author;
      self :: transfer_moola('publish_post', $user_id, 'posts', $ID);
   }
   
   static function comment_post($comment_id, $comment_approved) {      
      if( 1 === $comment_approved ) {
         $comment = get_comment($comment_id);
         if(empty($comment->user_id)) return false;
   		self :: transfer_moola('comment_post', $comment->user_id, 'comments', $comment_id);
   	}      
   }
   
   static function change_comment_status($new_status, $old_status, $comment) {      
      if($old_status != $new_status) {
           if($new_status == 'approved') {
               if(empty($comment->user_id)) return false;
   		      self :: transfer_moola('comment_post', $comment->user_id, 'comments', $comment->comment_ID);
           }
       } 
   }
   
   // clicked link from the [moolamojo-link] shortcode
   static function link_click($url) {
      global $wpdb, $user_ID;
      if(!is_user_logged_in()) return false;
      self :: transfer_moola('link_click', $user_ID, '_link_click');
   }
   
   // this generic method will read the action settings and reward or substract moola accordingly
   static function transfer_moola($action, $user_id, $action_table = '', $action_id = 0) {
       global $wpdb;
       
       $actions = get_option('moolamojo_actions');
       if(empty($actions[$action])) return false;
       
       $points = $actions[$action]['points'];
       
       // update user balance
       if($actions[$action]['action'] != 'reward') $points = 0 - $points;
       MoolaMojoUser :: update_balance($user_id, $points);
       
       // insert in transactions
       $wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_TRANSACTIONS." SET 
          user_id=%d, datetime=%s, amount_moola=%d, action=%s, action_table=%s, action_id=%d",
          $user_id, current_time('mysql'), $points, $actions[$action]['action'], $action_table, $action_id));
          
       return $wpdb->insert_id;   
   } // end transfer_moola
   
   // almost the same as above but called when user purchases moola so we don't need to verify that the action exists in $actions
   // or use the points from there
   static function purchase_moola($user_id, $package_id, $points, $action = '_purchase') {
      global $wpdb;
      
       MoolaMojoUser :: update_balance($user_id, $points);
              
        $wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_TRANSACTIONS." SET 
          user_id=%d, datetime=%s, amount_moola=%d, action=%s, action_table=%s, action_id=%d",
          $user_id, current_time('mysql'), $points, $action, 'packages', $package_id));
   } 
   
   // similar to above methods but used when catching do_action from other plugins
   // @param $reward - boolean, when true = reward, when false = charge
   // @param $action - action key
   // @param $points - integer unsigned, the amount of points to reward of charge   
   static function catch_action($reward, $points, $action = '', $user_id = 0, $action_table = '', $action_id = 0) {
      global $wpdb, $user_ID;
      if(empty($action)) $action = "external";
      if(empty($user_id)) $user_id = $user_ID;
      if(empty($user_ID)) return false;
      
      // $points should arrive as unsigned. The $action defines whether it goes with + or - sign in the DB 
      // If a plugin mistakenly sent a negative number, let's fix it
      if($points < 0) $points = abs($points);
      
      // if there are no points, don't go further
      if($points == 0) return true;
      
      // update user balance
       if(!$reward) $points = 0 - $points;
       try {
       	MoolaMojoUser :: update_balance($user_id, $points);
       }
		 catch(Exception $e) {
		 	wp_die($e->getMessage());
		 }       
       
         // insert in transactions
       $wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_TRANSACTIONS." SET 
          user_id=%d, datetime=%s, amount_moola=%d, action=%s, action_table=%s, action_id=%d",
          $user_id, current_time('mysql'), $points, $action, $action_table, $action_id));   
          
       return true;      
   } // end catch_action
}