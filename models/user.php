<?php
// handles user-related events
class MoolaMojoUser {
	// update the balance of $user_id with $points moola. 
	// @param $points - unsigned int
	static function update_balance($user_id, $points) {
		if(!is_numeric($points)) $points=0;		
		
		$balance = get_user_meta($user_id, 'moolamojo_balance', true);
		$balance = intval($balance);
      $balance += $points;  
      
		// balance negative? throw exception
		if($balance < 0) throw new Exception(__('Not enough balance', 'moolamojo'));      
           
      update_user_meta($user_id, 'moolamojo_balance', $balance);
      
      do_action('moolamojo-balance-updated', $user_id, $balance);
      MoolaMojoLevels :: update($user_id, $balance);
	}
	
	// add custom column to the users table
	static function add_custom_column($columns) {		
		$columns['moolamojo_moola'] = sprintf(__('%s balance', 'moola'), MOOLA_CURRENCY);
	 	return $columns;		
	}
	
	static function manage_custom_column($empty='', $column_name = '' , $id = 0) {		
	  if( $column_name == 'moolamojo_moola' ) {
			if(!empty($_GET['moolamojo_cleanup_balance']) and $id == $_GET['moolamojo_cleanup_balance']) {
				update_user_meta($_GET['moolamojo_cleanup_balance'], 'moolamojo_balance', 0);
			}	
	  	
			// get the number of points
	  		$points = get_user_meta($id, 'moolamojo_balance', true);
	  		if(!$points) $points = 0;
	  		
	  		return $points .'<br><a href="admin.php?page=moolamojo_adjust_balance&user_id='.$id.'">'.__('Adjust balance', 'moola').'</a>';
	  }
		return $empty;
	}
	
	static function sortable_balance_column( $columns ) {
		$columns['moolamojo_moola'] = 'moolamojo_moola';
			return $columns;
	}
	
	// sort users by balance
	static function sort_by_balance( $query ) {

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! function_exists( 'get_current_screen' ) ) return;

			$screen = get_current_screen();
			if ( $screen === NULL || $screen->id != 'users' ) return;

			if ( isset( $query->query_vars['orderby'] ) ) {
				global $wpdb;

				$order = 'ASC';
				if ( isset( $query->query_vars['order'] ) )
					$order = $query->query_vars['order'];

					$query->query_from  .= " LEFT JOIN {$wpdb->usermeta} tMoolaMeta ON ({$wpdb->users}.ID = tMoolaMeta.user_id AND tMoolaMeta.meta_key = 'moolamojo_balance')";
					$query->query_orderby = "ORDER BY tMoolaMeta.meta_value+0 {$order} ";
			}
		return $query;
	} // end sort by balance
	
	// manually adjust user's balance from admin
	static function adjust() {
		global $wpdb;
		
		if(!empty($_POST['ok']) and check_admin_referer('moolamojo_adjust')) {
			$wpdb->query($wpdb->prepare("INSERT INTO ".MOOLA_TRANSACTIONS." SET
				user_id=%d, amount_moola=%d, action='_adjust', action_table='_admin'", intval($_GET['user_id']), intval($_POST['moola']) ));
				
			self :: update_balance($_GET['user_id'], $_POST['moola']);	
			
			$success = true;
		}
		
		// select user and their balance
		$user = get_userdata($_GET['user_id']);
		$moola = get_user_meta($user->ID, 'moolamojo_balance', true);
		if(empty($moola)) $moola = 0;
		
		$currency = get_option('moolamojo_currency');
		
		include(MOOLA_PATH . '/views/adjust.html.php');
	} // end adjust()
}