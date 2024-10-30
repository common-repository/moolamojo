<div class="wrap">
   <h1><?php _e('Manage Actions', 'moola')?></h1>
   
   <p><?php printf(__('You can reward virtual currency for various actions that users do in your site. If you want other actions to be added (including actions from popular WP plugins), <a href="%s" target="_blank">please request in the forum</a>. Rewarding points from custom plugins is also super easy. <a href="%s" target="_blank">Learn here</a>.', 'moola'), '#', '#' );?></p>
   
   <form method="post">
      <table class="widefat">
         <tr><th><?php _e('Action', 'moola');?></th><th><?php printf(__('Amount of %s', 'moola'), MOOLA_CURRENCY);?></th></tr>
         <?php foreach($actions as $key=>$action):
            $class = ('alternate' == @$class) ? '' : 'alternate';?>
            <tr class="<?php echo $class?>"><td><?php printf(__('%s reward:', 'moola'), $action['description']);?></td>
            <td><input type="hidden" name="<?php echo $key?>_action" value="reward"> <input type="text" name="<?php echo $key?>_points" size="6" value="<?php echo $action['points']?>"></td></tr>
         <?php endforeach;?> 
      <?php wp_nonce_field('moolamojo_actions');?>
      </table>
      <p><input type="submit" name="save_actions" value="<?php _e('Save Actions', 'moola');?>" align="center" class="button-primary"></p>
   </form>
   
   <p><strong>Actions from other plugins and integration API are coming soon!</strong></p>
</div>