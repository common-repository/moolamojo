<div class="wrap">
	<h1><?php _e('Moola Mojo Options', 'moola');?></h1>
	
	<form method="post">
	<div class="wp-admin moolamojo-form">
		<p><label><?php _e('Currency name:', 'moola');?></label> <select name="currency" onchange="this.value == 'other' ? jQuery('#moolaOtherCurrency').show() : jQuery('#moolaOtherCurrency').hide();">
			<?php foreach($currencies as $cur):
				if($cur == $currency) $selected = ' selected';
				else $selected = '';?>
				<option value="<?php echo $cur?>"<?php echo $selected?>><?php echo $cur?></option>
			<?php endforeach;?>
			<option value="other" <?php if(!in_array($currency, $currencies)) echo 'selected'?>><?php _e('Other', 'moola');?></option>
		</select>
		<input id="moolaOtherCurrency" type="text" size="10" name="custom_currency" value="<?php if(!in_array($currency, $currencies)) echo $currency?>" style='display:<?php echo in_array($currency, $currencies) ? 'none' : 'inline';?>'></p>
		
		<p><input type="checkbox" name="allow_purchase" value="1" <?php if(!empty($allow_purchase)) echo 'checked'?>> <?php _e('Allow users to purchase packages of virtual currency with real money', 'moola');?>		
		</p>
		
		<p><input type="checkbox" name="allow_checkout" value="1" <?php if(!empty($allow_checkout)) echo 'checked'?> onclick="this.checked ? jQuery('#mmCheckoutInfo').show() : jQuery('#mmCheckoutInfo').hide();"> <?php _e('Allow users to checkout virtual currency for real money', 'moola');?></p>
		
		<div id="mmCheckoutInfo" style='display:<?php echo $allow_checkout ? 'block' : 'none';?>'>
			<p><label><?php _e('Set exchange rate: 1 virtual currency = ', 'moola');?></label>
			<input type="text" name="exchange_rate" value="<?php echo get_option('moolamojo_exchange_rate');?>" size="4">  <select name="real_currency" onchange="this.value ? jQuery('#moolaCustomCurrency').hide() : jQuery('#moolaCustomCurrency').show(); ">
			<?php foreach($real_currencies as $key => $val):
            if($key == $real_currency) $selected='selected';
            else $selected='';?>
        		<option <?php echo $selected?> value='<?php echo $key?>'><?php echo $val?></option>
         <?php endforeach; ?>
			<option value="" <?php if(!in_array($real_currency, $real_currency_keys)) echo 'selected'?>><?php _e('Custom', 'moola')?></option>
			</select>
			<input type="text" id="moolaCustomCurrency" name="custom_real_currency" style='display:<?php echo in_array($real_currency, $real_currency_keys) ? 'none' : 'inline';?>' value="<?php echo $real_currency?>"></p>
		</div>
		
		<p><input type="submit" name="ok" value="<?php _e('Save Options', 'moola');?>" class="button button-primary"></p>
	</div>
	<?php wp_nonce_field('moola_settings');?>
	</form>
	
	
	<form method="post" class="namaste-form">
		<div class="wp-admin moolamojo-form" style='display:<?php echo $allow_purchase ? 'block' : 'none';?>'>
			<h2><?php _e('Payment Settings', 'moola')?></h2>
					
			<p><label><?php _e('Payment currency:', 'moola')?></label> <select name="currency" onchange="this.value ? jQuery('#moolaCustomCurrency2').hide() : jQuery('#moolaCustomCurrency2').show(); ">
			<?php foreach($real_currencies as $key => $val):
            if($key == $real_currency) $selected='selected';
            else $selected='';?>
        		<option <?php echo $selected?> value='<?php echo $key?>'><?php echo $val?></option>
         <?php endforeach; ?>
			<option value="" <?php if(!in_array($real_currency, $real_currency_keys)) echo 'selected'?>><?php _e('Custom', 'moola')?></option>
			</select>
			<input type="text" id="moolaCustomCurrency2" name="custom_currency" style='display:<?php echo in_array($real_currency, $real_currency_keys) ? 'none' : 'inline';?>" value="<?php echo $real_currency?>'></p>
			
			<p><?php _e('Here you can specify payment methods that you will accept to sell packages of virtual currency.', 'moola')?></p>
			
			<p><input type="checkbox" name="accept_paypal" value="1" <?php if($accept_paypal) echo 'checked'?> onclick="this.checked?jQuery('#paypalDiv').show():jQuery('#paypalDiv').hide()"> <?php _e('Accept PayPal', 'moola')?></p>
			
			<div id="paypalDiv" style='display:<?php echo $accept_paypal?'block':'none'?>;'>
				<p><input type="checkbox" name="paypal_sandbox" value="1" <?php if(get_option('moolamojo_paypal_sandbox')=='1') echo 'checked'?>> <?php _e('Use Paypal in sandbox mode', 'moola')?></p>
				<p><label><?php _e('Your Paypal ID:', 'moola')?></label> <input type="text" name="paypal_id" value="<?php echo get_option('moolamojo_paypal_id')?>"></p>
				<p><label><?php _e('After payment go to:', 'moola')?></label> <input type="text" name="paypal_return" value="<?php echo get_option('moolamojo_paypal_return');?>" size="40"> <br />
				<?php _e('When left blank it goes to the course page. If you enter specific full URL, the user will be returned to that URL.', 'moola')?> </p>
				
				<p><b><?php _e('Note: Paypal IPN will not work if your site is behind a "htaccess" login box or running on localhost. Your site must be accessible from the internet for the IPN to work. In cases when IPN cannot work you need to use Paypal PDT.', 'moola')?></b></p>
			
			<p><input type="checkbox" name="use_pdt" value="1" <?php if($use_pdt == 1) echo 'checked'?> onclick="this.checked ? jQuery('#paypalPDTToken').show() : jQuery('#paypalPDTToken').hide();"> <?php printf(__('Use Paypal PDT instead of IPN (<a href="%s" target="_blank">Why and how</a>)', 'moola'), 'http://blog.calendarscripts.info/watupro-intelligence-module-using-paypal-data-transfer-pdt-instead-of-ipn/');?></p>
			
			<div id="paypalPDTToken" style='display:<?php echo ($use_pdt == 1) ? 'block' : 'none';?>'>
				<p><label><?php _e('Paypal PDT Token:', 'namaste');?></label> <input type="text" name="pdt_token" value="<?php echo get_option('moolamojo_pdt_token');?>" size="60"></p>
			</div>
			</div>
			
			<p><input type="checkbox" name="accept_stripe" value="1" <?php if($accept_stripe) echo 'checked'?> onclick="this.checked?jQuery('#stripeDiv').show():jQuery('#stripeDiv').hide()"> <?php _e('Accept Stripe', 'moola')?></p>
			
			<div id="stripeDiv" style='display:<?php echo $accept_stripe?'block':'none'?>;'>
				<p><label><?php _e('Your Public Key:', 'moola')?></label> <input type="text" name="stripe_public" value="<?php echo get_option('moolamojo_stripe_public')?>"></p>
				<p><label><?php _e('Your Secret Key:', 'moola')?></label> <input type="text" name="stripe_secret" value="<?php echo get_option('moolamojo_stripe_secret')?>"></p>
			</div>
			
			<p><input type="checkbox" name="accept_other_payment_methods" value="1" <?php if($accept_other_payment_methods) echo 'checked'?> onclick="this.checked?jQuery('#otherPayments').show():jQuery('#otherPayments').hide()"> <?php _e('Accept other payment methods', 'moola')?> 
				<span class="moola_help"><?php _e('This option lets you paste your own button HTML code or other manual instructions, for example bank wire. These payments will have to be processed manually unless you can build your own script to verify them.','moola')?></span></p>
				
			<div id="otherPayments" style='display:<?php echo $accept_other_payment_methods?'block':'none'?>;'>
				<p><?php _e('Enter text or HTML code for payment button(s). You can use the following variables: {{item-id}}, {{item-name}}, {{user-id}}, {{amount}}.', 'moola')?></p>
				<textarea name="other_payment_methods" rows="8" cols="80"><?php echo stripslashes(get_option('moolamojo_other_payment_methods'))?></textarea>
			
			</div>	
			
			<?php echo do_action('moolamojo-options-payments');?>
			
			<p><input type="submit" value="<?php _e('Save payment settings', 'moola')?>" class="button button-primary"></p>
			
			<?php if(!empty($payment_errors)):?>
				<p><a href="#" onclick="jQuery('#namasteErrorlog').toggle();return false;"><?php _e('View payments errorlog', 'moola')?></a></p>
				<div id="namasteErrorlog" style="display:none;"><?php echo nl2br($payment_errors)?></div>
			<?php endif;?>	
		</div>
		
		<input type="hidden" name="moola_payment_options" value="1">
		<?php echo wp_nonce_field('moola_payment_options');?>	
	</form>
</div>