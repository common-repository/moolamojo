<h2 class="nav-tab-wrapper">
	<a class='nav-tab' href='admin.php?page=moolamojo_button_generator'><?php _e('Generate Buttons', 'moola')?></a>
	<a class='nav-tab nav-tab-active' href='admin.php?page=moolamojo_button_generator&list_products=1'><?php _e('Existing Products/Services', 'moola')?></a>	
</h2>

<div class="wrap">
	<h1><?php _e('Manage Products and Services', 'moola');?></h1>
	
	<?php if($count):?>
		<table class="widefat">
			<tr><th><?php _e('Product/Service Name', 'moola');?></th><th><?php printf(__('Price in %s', 'moola'), MOOLA_CURRENCY)?></th>
				<th><?php _e('Action', 'moola');?></th></tr>
			<?php foreach($products as $product):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>">
					<td><?php echo stripslashes($product->name);?></td>
					<td><?php echo $product->price;?></td>
					<td><a href="admin.php?page=moolamojo_button_generator&id=<?php echo $product->id?>"><?php _e('Edit', 'moola');?></a>
					| <a href="#" onclick="if(confirm('<?php _e("Are you sure?", 'moola');?>')) window.location='<?php echo wp_nonce_url("admin.php?page=moolamojo_button_generator&list_products=1&del=1&id=".$product->id."offset=".$offset, 'moolamojo_products');?>';"><?php _e('Delete', 'moola');?></a></td>
				</tr>
			<?php endforeach;?>	
		</table>
		<p><?php if($offset > 0):?><a href="admin.php?page=moolamojo_button_generator&list_products=1&offset=<?php echo $offset - $page_limit?>"><?php _e('previous page','moola');?></a><?php endif;?>
		&nbsp;
		<?php if($count > $offset + $page_limit):?>
			<a href="admin.php?page=moolamojo_button_generator&list_products=1&offset=<?php echo $offset + $page_limit?>"><?php _e('previous page','moola');?></a>		
		<?php endif;?>		
		</p>
	<?php else:?>
		<p><?php _e('You have not stored any products or services yet.', 'moola');?></p>
	<?php endif;?>
</div>