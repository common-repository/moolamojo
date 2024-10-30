<div class="wrap">
   <h1><?php _e('Manage Currency Packages', 'moola');?></h1>
   
  <p><?php printf(__('Here you can create packages of virtual currency that user can purchase for real money. You can use the shortcode %s to display automatically generated table with the packages. There are also individual "buy now" button shortcodes for each package if you prefer to craft your own page for selling them.', 'moola'),
   "<input type=\"text\" value='[moolamojo-packages text=\"".__('%d of %s for %s', 'moola')."\" button_text=\"".__('Buy now!', 'moola')."\"]' onclick=\"this.select();\" readonly=\"readonly\" size='50'>");?></p>
  
   <form method="post" onsubmit="return moolaMojoValidate(this);">
      <p><?php _e('Amount virtual currency:', 'moola');?> <input type="text" name="moola" size="8"> <?php printf(__('Price in %s:', 'moola'), MOOLA_REAL_CURRENCY);?>
      <input type="text" size="6" name="price">
      <input type="submit" name="add" value="<?php _e('Add Package', 'moola');?>" class="button-primary"></p>
      <?php wp_nonce_field('moola_packages');?>
   </form>
   
   <?php foreach($packages as $package):?>
       <form method="post" onsubmit="return moolaMojoValidate(this);">
         <p><?php _e('Amount virtual currency:', 'moola');?> <input type="text" name="moola" size="8" value="<?php echo $package->moola?>"> <?php printf(__('Price in %s:', 'moola'), MOOLA_REAL_CURRENCY);?>
         <input type="text" size="6" name="price" value="<?php echo $package->price?>">
         <input type="submit" name="save" value="<?php _e('Save', 'moola');?>" class="button button-primary">
         <input type="button" value="<?php _e('Delete', 'moola');?>" class="button" onclick="moolaMojoConfirmDelete(this.form);">
         <input type="text" value='[moolamojo-package id=<?php echo $package->id?> button_text="<?php _e('Buy now!', 'moola')?>"]' readonly="readonly" onclick="this.select();" size="40">
			<?php printf(__('Package ID: %d', 'moola'), $package->id);?>         
         </p>
         <?php wp_nonce_field('moola_packages');?>
         <input type="hidden" name="id" value="<?php echo $package->id?>">
         <input type="hidden" name="del" value="0">
       </form>
   <?php endforeach;?>
   
   <?php if(count($packages)):?>
      <h3><?php printf(__('Manually award %s to user', 'moola'), MOOLA_CURRENCY);?></h3>
      <p><?php _e('This allows you to assign some virtual currency to any user in your site without them to purchase the package.', 'moola');?></p>
      <?php if(!empty($award_msg)):?>
         <p><b><?php echo $award_msg?></b></p>
      <?php endif;?>
      <form method="post">
         <p><?php _e('WP user login or email address:', 'moola');?> <input type="text" name="user_name">
         <?php _e('Select package:', 'moola');?> <select name="package_id">
            <?php foreach($packages as $package):?>
               <option value="<?php echo $package->id?>"><?php printf(__('%1$s %2$s', 'moola'), MOOLA_CURRENCY, $package->moola);?></option>
            <?php endforeach;?>
         </select>
         <input type="submit" name="award" value="<?php _e('Award currency', 'moola');?>" class="button-primary"></p>
         <?php wp_nonce_field('moola_packages');?>
      </form>
   <?php endif;?>
</div>

<script type="text/javascript">
function moolaMojoValidate(frm) {
   if(frm.moola.value == '' || isNaN(frm.moola.value)) {
      alert("<?php _e('Please enter numeric amount of virtual currency to be sold.', 'moola')?>");
      frm.moola.focus();
      return false;      
   }
   
   if(frm.price.value == '' || isNaN(frm.price.value)) {
      alert("<?php _e('Please enter numeric price of the package (real currency).', 'moola')?>");
      frm.price.focus();
      return false;      
   }
   
   return true;
}

function moolaMojoConfirmDelete(frm) {
   if(confirm("<?php _e('Are you sure?', 'moola')?>")) {
      frm.del.value=1;
      frm.submit();
   } 
}
</script>