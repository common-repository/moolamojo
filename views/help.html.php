<div class="wrap moolamojo-wrap">
	<h1><?php _e('MoolaMojo User Manual', 'moola');?></h1>
	
	<p><?php _e('MoolaMojo is a plugin for virtual currency in WordPress. It lets you:', 'moola');?></p>
	<ul>
		<li><?php _e('Award currency for various user actions like logging in, posting a comment, publishing a post, etc','moola');?></li>
		<li><?php _e('Sell products or services for virtual currency. Just generate shortcode for a button - it is super easy!', 'moola');?></li>
		<li><?php _e('Define user level based on the currency they own.', 'moola');?></li>
		<li><?php _e('The easiest possible integration with other plugins lets developers charge or award MoolaMojo virtual currency for their actions. We will provide a page with integrated plugins soon!', 'moola');?></li>		
	</ul>
	
	<h2><?php _e('Setting it up', 'moola');?></h2>
	
	<p><?php printf(__('After installing the plugin go to the <a href="%s">Settings</a> page for initial configuration:', 'moola'), 'admin.php?page=moolamojo');?></p>
	
	<ul>
		<li><?php _e('Select the name of your currency. We suggest several names but you can use your own.', 'moola');?></li>
		<li><?php _e('You can allow users to purchase virtual currency with real money. If you select this, after saving you will see the options to setup your payment settings.', 'moola');?></li>
		<li><?php _e('Allow users to check out virtual currency for real money. This will let users send you check out requests which you can handle manually.', 'moola');?></li>
	</ul>
	
	<h2><?php _e('Shortcodes', 'moola');?></h2>
	<p><input type="text" value='[moolamojo-balance]' onclick="this.select()" readonly="readonly"> <?php _e('- displays the currently logged in user credit balance. You can pass attribute "user_id" to show the balance of other user.', 'moola');?></p>
	<p><input type="text" value='[moolamojo-link url="https://namaste-lms.org/moolamojo" text="click here" new_window="1"]' onclick="this.select()" readonly="readonly" size="30"> <?php _e('- Generates clickable link which can reward credits for clicking on it.', 'moola');?></p>
	
	<h2><?php _e('Manage Actions', 'moola');?></h2>
	
	<p><?php printf(__('<a href="%s">This page</a> lets you setup the virtual currency which will be awarded when user completes various actions. More actions may and will be added in the future versions of the plugin.', 'moola'), 'admin.php?page=moolamojo_actions');?></p>
	
	<h2><?php _e("Selling Products and Services", 'moola');?></h2>
	
	<p><?php printf(__('Selling products is as easy as creating a shortcode for "Buy" button! When user purchases the product they will be charged the associated points. If you choose to store the order you will be able to see it in your Orders page and fulfill it. But this is not all. The plugin will fire action that can be used for automated handling the order via any custom plugin or theme function. For more information check our <a href="%s" target="_blank">online documentation</a>, section "Catch button actions".', 'moola'), 'https://namaste-lms.org/moolamojo/developers.php')?></p>
	
	<h2><?php _e('Manage Levels', 'moola');?></h2>
	
	<p><?php _e('You can create any number of user levels and have a level assigned to user when they reach certain virtual currency balance. Levels can be reversible - this defines whether the user will lose their level when their balance falls below the required amount, or a level once earned is never lost.', 'moola');?></p>
	
	<p><?php _e('There is also a shortcode which can be used to display the user level.', 'moola');?></p>
	
	<h2><?php _e('Integration To WooCommerce', 'moola');?></h2>
	
	<p><?php printf(__('This plugin has built-in integration to WooCommerce which allows you to sell virtual currency packages as WooCommerce products. <a href="%s" target="_blank">Learn more here</a>.', 'moola'), 'http://blog.calendarscripts.info/woocommerce-integration-in-moolamojo/');?> </p>
</div>