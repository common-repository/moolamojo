=== MoolaMojo ===
Contributors: prasunsen
Tags: virtual currency, virtual credits, points, shopping cart
Requires at least: 4.0
Tested up to: 5.8
Stable tag: trunk
License: GPL2

MoolaMojo is a virtual credits system which lets you reward users for actions and sell products or services for virtual currency.

/*** License

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    ***/

== Description ==

MoolaMojo is a virtual credits system which lets you reward users for actions and sell products or services for virtual currency.

Enhance your site with incentives and gamification:

###Features###

- Reward virtual currency for various user actions
- Sell virtual currency packages for real money
- Charge virtual currency for products or services
- Manage orders like in a regular online store
- Assign user levels based on points they collect
- Super easy integration with custom plugins and functions
- Engage your users, get more traffic and earn real revenue

Full documentation, tour and integration guides are available at [https://namaste-lms.org/moolamojo](https://namaste-lms.org/moolamojo "Official MoolaMojo Site")

###Integrated Plugins###

MoolaMojo has built-in integrations for several popular plugins:

- Integration to [WooCommerce](https://wordpress.org/plugins/woocommerce/ "WooCommerce") lets you sell virtual currency packages as WooCommerce products so you can use all payment processors and options provided by WooCommerce
- Integration to [Watu PRO](http://calendarscripts.info/watupro/ "Watu PRO") allows you to charge virtual credits to access paid quizzes and to award credits for completing quizzes.
- Integration to [Namaste! LMS](https://namaste-lms.org/ "Namaste! LMS") lets you charge MoolaMojo credits for accessing paid courses and groups. You can also transfer earned points from the LMS to user's MoolaMojo virtual money balance.
- Integration to [Konnichiwa!](https://wordpress.org/plugins/konnichiwa/ "Konnichiwa!") lets you sell access to membership plans for virtual money. 

== Installation ==

1. Unzip the contents and upload the entire `moolamojo` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "MoolaMojo" in your menu and manage the plugin

== Frequently Asked Questions ==

None yet, please ask in the forum

== Screenshots ==

1. Manage settings and set currency name
2. Reward virtual credits for performing various actions in WordPress
3. Sell packages of virtual credits
4. Generate shortcodes for buttons to sell products or services for virtual credits
5. Example of how it may look on the front-end

== Changelog ==

= Version 0.7.4 =

- Added the missing [moolamojo-package] shortcode.
- Replaced CURL with WP HTTP API.
- Added option to cleanup transaction history log.
- Added option to manually adjust user's balance from your main Users page.


= Version 0.7 =

- First public release
- Updated the documentation with shortcodes information and link to the online Integration guide
- Handled wrong action calls from other plugins - if they send negative amount of points we should turn them into absolute number and defined whether to enter with + or - sign based on the $reward parameter. Changed the "amount_moola" field to signed it to handle this.
- You can now store products/services in the system for future usage and reference
- Added sortable MoolaMojo balance column in WP Users page
- Added page with transactions history
- Don't store transaction if points are zero
- Integration to WooCommerce
- Ensure sessions will start only on the front-end and in MoolaMojo admin pages to avoid conflicts with the WP plugin and theme editors