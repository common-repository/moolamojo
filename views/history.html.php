<div class="wrap">
	<h1><?php _e('Transactions History', 'moola');?></h1>
		
	<div class="wp-admin inside">
		<table class="widefat">
			<thead>
			<tr><th><?php _e('Username', 'moola');?></th><th><?php _e('Date / time', 'moola');?></th>
				<th><?php printf(__('Amount %s', 'moola'), MOOLA_CURRENCY);?></th>
				<th><?php printf(__('Amount %s', 'moola'), MOOLA_REAL_CURRENCY);?></th>
				<th><?php _e('Action', 'moola');?></th>
				<th><?php _e('Notes', 'moola');?></th></tr>
			</thead>
			<tbody>	
			<?php foreach($items as $item):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>">
					<td><?php echo $item->display_name?></td>
					<td><?php echo date_i18n($date_format.' '.$time_format, strtotime($item->datetime));?></td>
					<td><?php echo $item->amount_moola;?></td>
					<td><?php echo $item->amount_cash;?></td>
					<td><?php switch($item->action):
						case 'reward':
							_e('earned', 'moola');
						break;
						case '_manual':
							_e('manually assigned', 'moola');
						break;	
						case '_adjust':
							_e('adjusted by admin', 'moola');
						break;		
						case '_purchase':
							_e('purchased package', 'moola');
						break;			
						default:
							echo stripslashes($item->action);
						break;
					endswitch;?></td>
					<td><?php switch($item->action_table):
						case 'posts':
							_e('for publishing a post', 'moola');
						break;
						case '_login': _e('for logging in', 'moola'); break;
						case '_registration': _e('for registration', 'moola'); break;
						default:
							//
						break;
					endswitch;?></td>
				</tr>	
			<?php endforeach;?>
			</tbody>	
		</table>
		
		<p align="center"><?php if($offset >0):?> <a href="admin.php?page=moolamojo_history&offset=<?php echo $offset - $limit?>"><?php _e('[previous page]', 'moola');?></a><?php endif;?>
		
		<?php if($offset + $limit < $count):?> <a href="admin.php?page=moolamojo_history&offset=<?php echo $offset + $limit?>"><?php _e('[next page]', 'moola');?></a><?php endif;?></p>
	
		<?php if(count($items)):?>
			<p><input type="checkbox" onclick="this.checked ? jQuery('#moolaCleanupHistory').show() : jQuery('#moolaCleanupHistory').hide()"> <?php _e('Show me a button to cleanup the whole history.', 'moola');?></p>
			<form method="post" id="moolaCleanupHistory" style="display: none;">
				<p style="color:red;"><?php _e('Warning: this action cannot be reversed!', 'moola');?> 
				<input type="submit" value="<?php _e('Yes, clear the translation history log', 'moola');?>" class="button button-primary"></p>
				<input type="hidden" name="clear_history" value="1">
				<?php wp_nonce_field('moolamojo_clear_history');?>
			</form>
		<?php endif;?>
	</div>
</div>