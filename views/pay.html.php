<div class="moolamojo-payment">
	<?php echo $content?>
	
	<?php if($accept_paypal and $paypal_id): 
		$paypal_host = "www.paypal.com";
		$paypal_sandbox = get_option('moolamojo_paypal_sandbox');
		if($paypal_sandbox == '1') $paypal_host = 'www.sandbox.paypal.com';// generate Paypal button ?>
	<form action="https://<?php echo $paypal_host?>/cgi-bin/webscr" method="post">
	<p align="center">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="<?php echo $paypal_id?>">
		<input type="hidden" name="item_name" value="<?php echo $item_name?>">
		<input type="hidden" name="item_number" value="<?php echo $item_id?>">
		<input type="hidden" name="amount" value="<?php echo number_format($amount,  2,".","")?>">
		<input type="hidden" name="return" value="<?php echo $return_url;?>">
		<?php if(get_option('moolamojo_use_pdt') != 1):?><input type="hidden" name="notify_url" value="<?php echo $notify_url?>"><?php endif;?>
		<input type="hidden" name="no_shipping" value="1">
		<input type="hidden" name="no_note" value="1">
		<input type="hidden" name="currency_code" value="<?php echo MOOLA_REAL_CURRENCY;?>">
		<input type="hidden" name="lc" value="US">
		<input type="hidden" name="bn" value="PP-BuyNowBF">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</p>
	<input type="hidden" name="charset" value="utf-8">
	</form> 
	<?php endif;?>
	
	<?php if($accept_stripe):?>
	  <form method="post">
		  <script src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
		          data-key="<?php echo $stripe['publishable_key']?>"
		          data-amount="<?php echo ($amount*100)?>" data-description="<?php echo $item_name?>" data-currency="<?php echo MOOLA_REAL_CURRENCY?>"></script>
		<input type="hidden" name="moolamojo_stripe_pay" value="1">
		<input type="hidden" name="item_id" value="<?php echo $item_id?>">
		</form>
	<?php endif;?>
	
	<?php if($accept_other_payment_methods):?>
		<div><?php echo $other_payment_methods?></div>
	<?php endif;?>
</div>