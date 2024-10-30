<?php 
class MoolaMojoOrders {
   static function manage() {
      global $wpdb;
      
      $offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
      $limit = 50;
      
      // for this version hardcode these:
      $ob = empty($_GET['ob']) ? 'id' : $_GET['ob'];
      if(!in_array($ob, array('id', 'tU.user_login', 'product_name', 'datetime', 'status'))) $ob = 'id';
      $dir = empty($_GET['dir']) ? 'DESC' : $_GET['dir'];
      if($dir != 'DESC' and $dir != 'ASC') $dir = 'DESC';
      $odir = ($dir == 'DESC') ? 'ASC' : 'DESC';

      // delete order      
      if(!empty($_POST['del']) and check_admin_referer('moola_orders')) {
         $wpdb->query($wpdb->prepare("DELETE FROM ".MOOLA_ORDERS." WHERE id=%d", intval($_POST['id'])));
         moola_redirect("admin.php?page=moolamojo_orders&offset=".$offset);
      }
      
      // update order status
      if(!empty($_POST['change_status']) and check_admin_referer('moola_orders')) {
         if(!in_array($_POST['status'], array('pending', 'paid', 'completed'))) $_POST['status'] = 'completed';
         $wpdb->query($wpdb->prepare("UPDATE ".MOOLA_ORDERS." SET status=%s WHERE id=%d", 
           $_POST['status'], intval($_POST['id'])));
         moola_redirect("admin.php?page=moolamojo_orders&offset=".$offset);
      }
      
      // select orders
      $orders = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS tOr.*, tU.display_name as display_name, tU.user_login as user_login 
         FROM ".MOOLA_ORDERS." tOr LEFT JOIN {$wpdb->users} tU ON tU.ID = tOr.user_id
         ORDER BY $ob $dir LIMIT $offset, $limit");
      $count = $wpdb->get_var("SELECT FOUND_ROWS()");   
      
      $dateformat = get_option('date_format'). ' ' .get_option('time_format');   
      include(MOOLA_PATH . '/views/orders.html.php');
   } // end manage orders
}