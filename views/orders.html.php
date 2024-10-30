<div class="wrap moolamojo-wrap">
   <h1><?php _e('Manage Orders', 'moola');?></h1>
   
   <?php if(!count($orders)):?>
      <p><?php _e('There are no orders at this time.', 'moola');?></p>
   <?php return true; 
   endif;?>
   
   <p>
      <b><?php _e('Order statuses:', 'moola');?></b>
      <ol>
         <li><?php _e('<b>Pending:</b> the order is not paid successfully for some reason.', 'moola');?></li>
         <li><?php _e('<b>Paid:</b> by default all orders made through MoolaMojo are stored with this status. It means the customer has paid the required points but their product or service is not yet delivered.', 'moola');?></li>
         <li><?php _e('<b>Completed:</b> the status when you have delivered the product or service to the customer.', 'moola');?></li>
      </ol>   
   </p>
   
   <table class="widefat">
      <tr><th><a href="admin.php?page=moolamojo_orders&ob=id&dir=<?php echo $odir?>&offset=<?php echo $offset?>"><?php _e('ID', 'moola');?></a></th>
         <th><a href="admin.php?page=moolamojo_orders&ob=user_login&dir=<?php echo $odir?>&offset=<?php echo $offset?>"><?php _e('Customer', 'moola');?></a></th>
         <th><a href="admin.php?page=moolamojo_orders&ob=product_name&dir=<?php echo $odir?>&offset=<?php echo $offset?>"><?php _e('Product/service', 'moola');?></a></th>
         <th><a href="admin.php?page=moolamojo_orders&ob=datetime&dir=<?php echo $odir?>&offset=<?php echo $offset?>"><?php _e('Date/time', 'moola');?></a></th>
         <th><a href="admin.php?page=moolamojo_orders&ob=status&dir=<?php echo $odir?>&offset=<?php echo $offset?>"><?php _e('Status', 'moola');?></a></th>
         <th><?php _e('Actions', 'moola');?></th></tr>
      <?php foreach($orders as $order):
         $class = ('alternate' == @$class) ? '' : 'alternate';?>
           <tr class="<?php echo $class?>">
               <td><?php echo $order->id?></td>
               <td><?php printf(__('%1$s (%2$s)','moola'), $order->display_name, $order->user_login);?></td>
               <td><?php echo stripslashes($order->product_name);
                  if(!empty($order->description)): echo wpautop(stripslashes($order->description)); endif;?></td>
               <td><?php echo date_i18n($dateformat, strtotime($order->datetime));?></td>   
               <td><form method="post">
                  <select name="status" onchange="this.form.submit();">
                     <option value="pending"><?php _e('Pending', 'moola');?></option>
                     <option value="paid" <?php if($order->status == 'paid') echo 'selected'?>><?php _e('Paid', 'moola');?></option>
                     <option value="completed" <?php if($order->status == 'completed') echo 'selected'?>><?php _e('Completed', 'moola');?></option>
                  </select>
                  <?php wp_nonce_field('moola_orders');?>
                 <input type="hidden" name="change_status" value="1">
                 <input type="hidden" name="id" value="<?php echo $order->id?>">               
               </form></td>
               <td><form method="post">
                  <input type="button" value="<?php _e('Delete', 'moola');?>" onclick="moolaConfirmDelete(this.form);">
                  <?php wp_nonce_field('moola_orders');?>    
                  <input type="hidden" name="del" value="0">
                  <input type="hidden" name="id" value="<?php echo $order->id?>">           
               </form></td>
           </tr>
      <?php endforeach;?>   
   </table>
   
   <p align="center"><?php if($offset > 0):?><a href="admin.php?page=moolamojo_orders&ob=<?php echo $ob?>&dir=<?php echo $dir?>&offset=<?php echo $offset-$limit?>"><?php _e('Previous page', 'moola');?></a><?php endif;?>
   &nbsp;
   <?php if($count > $offset + $limit):?>
      <a href="admin.php?page=moolamojo_orders&ob=<?php echo $ob?>&dir=<?php echo $dir?>&offset=<?php echo $offset+$limit?>"><?php _e('Next page', 'moola');?></a>
   <?php endif;?></p>
</div>

<script type="text/javascript">
function moolaConfirmDelete(frm) {
   if(confirm("<?php _e('Are you sure?', 'moola');?>")) {
      frm.del.value=1;
      frm.submit();
   }
}
</script>