<h2 class="nav-tab-wrapper">
	<a class='nav-tab nav-tab-active' href='admin.php?page=moolamojo_button_generator'><?php _e('Generate Buttons', 'moola')?></a>
	<a class='nav-tab' href='admin.php?page=moolamojo_button_generator&list_products=1'><?php _e('Existing Products/Services', 'moola')?></a>	
</h2>

<div class="wrap">
   <h1><?php _e('Sell Products or Services', 'moola');?></h1>
   
   <p><?php _e('Use the shortcode generator to generate buttons that let you charge virtual currency for any kind of products or services.', 'moola')?>
   <br><?php _e('The orders made will be visible in the Orders page. Paying with the button will also call a default + optional custom action to allow integration with other plugins. Read more here.', 'moola');?></p>
   
   <p><?php _e('All shortcode attributes are optional', 'moola')?></p>
   
   <form method="post">
      <p><label><?php _e('Amount of virtual currency to charge:', 'moola')?></label> <input type="text" name="charge" value="<?php echo empty($_POST['charge']) ? '' : intval($_POST['charge'])?>" size="6"></p>
      <p><label><?php _e('Button text (value):', 'moola')?></label> <input type="text" name="button_text" value="<?php echo empty($_POST['button_text']) ? '' : esc_attr(stripslashes($_POST['button_text']))?>"></p>
      <p><label><?php _e('Product / service name:', 'moola')?></label> <input type="text" name="product_name" value="<?php echo empty($_POST['product_name']) ? '' : esc_attr(stripslashes($_POST['product_name']))?>"></p>
      <p><label><?php _e('CSS classes', 'moola')?></label> <input type="text" name="classes" value="<?php echo empty($_POST['classes']) ? '' : esc_attr($_POST['classes'])?>"></p>
      <p><label><?php _e('Confirmation required', 'moola')?></label> <select name="confirmation_required">
         <option value="none"><?php _e('None', 'moola')?></option>
         <option value="js" <?php if(!empty($_POST['confirmation_required']) and $_POST['confirmation_required'] == 'js') echo 'selected'?>><?php _e('Javasript Confirm', 'moola')?></option>
         <option value="checkbox" <?php if(!empty($_POST['confirmation_required']) and $_POST['confirmation_required'] == 'checkbox') echo 'selected'?>><?php _e('HTML checkbox element', 'moola')?></option>
      </select></p>
      
        <p><label><?php _e('Store in Orders database', 'moola')?></label> <select name="store_order">
         <option value="true"><?php _e('Yes', 'moola')?></option>
         <option value="false" <?php if(!empty($_POST['store_order']) and $_POST['store_order'] == 'false') echo 'selected'?>><?php _e('No', 'moola')?></option>
      </select></p>
      
      <p><label><?php _e('URL to redirect after processing:', 'moola')?></label> <input type="text" name="redirect_url" value="<?php echo empty($_POST['redirect_url']) ? '' : esc_url_raw($_POST['redirect_url'])?>" size="30"></p>
      
      <p><label><?php _e('Same user can buy the same product or service this many times:', 'moola')?></label> <input type="text" name="num_clicks" value="<?php echo empty($_POST['num_clicks']) ? '' : intval($_POST['num_clicks'])?>" size="4"> 
      <?php _e('Enter 0 for unlimited. We will check orders by product / service name.', 'moola')?></p>  
      
      <p><label><?php _e('Generate form tags', 'moola')?></label> <select name="generate_form_tags">
         <option value="true"><?php _e('Yes', 'moola')?></option>
         <option value="false" <?php if(!empty($_POST['generate_form_tags']) and $_POST['generate_form_tags'] == 'false') echo 'selected'?>><?php _e('No', 'moola')?></option>
      </select><br>
      <?php _e('Set this to "No" ONLY if you want to embed the button into existing POST form on your site. Otherwise let us generate the form tags or the button will simply not work.', 'moola');?></p>
      
      <p><label><?php _e('Button name:', 'moola')?></label> <input type="text" name="button_name" value="<?php echo empty($_POST['button_name']) ? '' : esc_attr($_POST['button_name'])?>"><br>
      <?php _e('Unique name is required if you want to use several buttons in the same form.', 'moola');?></p>
      
      <?php if(empty($product->id)):?>
      	<p><input type="checkbox" name="add_product" value="1"> <?php _e('Store this product or service for reference and reusing the button code.', 'moola');?></p>
      <?php else:?>
      	<input type="hidden" name="save_product" value="1"> 
      <?php endif;?>
      
      <p><input type="submit" name="generate" value="<?php _e('Generate Shortcode', 'moola');?>" class="button-primary"></p>
      <?php wp_nonce_field('moolamojo_product');?>
   </form>
   
   <?php if(!empty($_POST['generate'])):?>
      <p><textarea readonly="readonly" onclick="this.select()" rows="3" cols="60">[moolamojo-button <?php
      if(!empty($_POST['charge'])): echo 'charge="'.floatval($_POST['charge']).'" '; endif; 
      if(!empty($_POST['button_text'])): echo 'button_text="'.esc_attr($_POST['button_text']).'" '; endif;
      if(!empty($_POST['product_name'])): echo 'product_name="'.esc_attr($_POST['product_name']).'" '; endif;
      if(!empty($_POST['classes'])): echo 'classes="'.esc_attr($_POST['classes']).'" '; endif;
      if(!empty($_POST['confirmation_required'])): echo 'confirmation_required="'.esc_attr($_POST['confirmation_required']).'" '; endif;
      if(!empty($_POST['store_order'])): echo 'store_order="'.esc_attr($_POST['store_order']).'" '; endif;
      if(!empty($_POST['redirect_url'])): echo 'redirect_url="'.esc_url_raw($_POST['redirect_url']).'" '; endif;
      if(!empty($_POST['num_clicks'])): echo 'num_clicks="'.intval($_POST['num_clicks']).'" '; endif;
      if(!empty($_POST['generate_form_tags'])): echo 'generate_form_tags="'.esc_attr($_POST['generate_form_tags']).'" '; endif;
      if(!empty($_POST['button_name'])): echo 'button_name="'.esc_attr($_POST['button_name']).'" '; endif;
      ?>]</textarea></p>
   <?php endif;?>
</div>