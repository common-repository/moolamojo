<div class="wrap moolamojo-wrap">
   <h1><?php _e('Manage User Levels', 'moola')?></h1>
   
   <p><?php _e('You can create any number of user levels and have a level assigned to user when they reach certain virtual currency balance. Levels can be reversible - this defines whether the user will lose their level when their balance falls below the required amount, or a level once earned is never lost.', 'moola');?>
   
   <p><?php printf(__('Use the following shortcode to display the user level: %s. If you pass a number to the argument user_login, the shortcode will display the level of that user. When left empty, it displays the level of the currently logged user.', 'moola'), '<input type="text" size="30" onclick="this.select();" readonly="readonly" value="[moolamojo-user-level user_id=0]">');?></p>
   
   <form method="post">
      <p><?php _e('Level name:', 'moola');?> <input type="text" name="name"> <?php printf(__('Required %s:', 'moola'), MOOLA_CURRENCY);?>  
      <input type="text" name="required_moola" size="8"> <input type="checkbox" name="is_reversible" value="1"> <?php _e('Reversible', 'moola');?>
      <input type="submit" name="add" value="<?php _e('Add Level', 'moola');?>" class="button-primary"></p>
      <?php wp_nonce_field('moolamojo_levels');?>
   </form>
   
   <?php foreach($levels as $level):?>
      <form method="post">
         <p><?php _e('Level name:', 'moola');?> <input type="text" name="name" value="<?php echo stripslashes($level->name);?>"> <?php printf(__('Required %s:', 'moola'), MOOLA_CURRENCY);?>  
         <input type="text" name="required_moola" size="8" value="<?php echo $level->required_moola?>"> 
         <input type="checkbox" name="is_reversible" value="1" <?php if($level->is_reversible) echo 'checked'?>> <?php _e('Reversible', 'moola');?>
         <input type="submit" name="save" value="<?php _e('Save', 'moola');?>" class="button-primary">
         <input type="button" value="<?php _e('Delete', 'moola');?>" onclick="moolaConfirmDelete(this.form);" class="button"></p>
         <?php wp_nonce_field('moolamojo_levels');?>
         <input type="hidden" name="id" value="<?php echo $level->id?>">
         <input type="hidden" name="del" value="0">
      </form>
   <?php endforeach;?>
</div>

<script type="text/javascript">
function moolaConfirmDelete(frm) {
   if(confirm("<?php _e('Are you sure?','moola');?>")) {
   	frm.del.value=1;
   	frm.submit();
   }
}
</script>