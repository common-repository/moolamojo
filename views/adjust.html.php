<div class="wrap">
	<h1><?php printf(__('Manually Adjust %s Balance', 'moola'),$currency);?></h1>
	
	<h3><?php printf(__('User: %1$s. Current balance: %2$s %3$s.', 'moola'), $user->display_name, $moola, $currency);?></h3>
	<p> <a href="users.php"><?php _e('Back to users', 'moola');?></a></p>
	
	<?php if(!empty($success)): echo '<p><b>'.sprintf(__('User balance adjusted with %1$s %2$s', 'moola'), esc_attr($_POST['moola']), $currency).'</b></p>'; endif;?>	
	
	<form method="post">
		<p><?php printf(__('Adjust balance with %s of %s', 'moola'), '<input type="text" name="moola" size="6">', $currency);?> <input type="submit" value="<?php _e('Adjust Balance', 'moola');?>" class="button button-primary"></p>
		<p><?php printf(__('To add %1$s enter positive number, to substrat %1$s enter negative number.', 'moola'), $currency, $currency);?></p>
		<?php wp_nonce_field('moolamojo_adjust');?>
		<input type="hidden" name="ok" value="1">
	</form>
</div>