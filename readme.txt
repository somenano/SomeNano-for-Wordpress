=== SomeNano for WordPress ===
Contributors: pwlk
Donate link: https://somenano.com
Tags: nano, cryptocurrency, paywall, pay, wall, donation, donations, payment, money
Requires at least: 4.6
Tested up to: 5.1.1
Stable tag: 4.3
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Accept Nano Cryptocurrency on your WordPress site. Set up a paywall so content on posts or pages is hidden until the user pays an amount of Nano.

== Description ==

SomeNano for WordPress is a Plugin that will allow you to accept Nano cryptocurrency as payment on your WordPress site.

Some different ways that you can accept payment is...

* Paywall on a blog post - require a user to make a payment to view the full content of a blog post.
* Paywall on a page - require a user to make a payment to view some or all of the content on a page.
* Paywall for an image - require a user to make a payment to view a premium image, perhaps an image without a watermark.

Demo site with example usage can be found here: [wordpress.somenano.com](https://wordpress.somenano.com).

Shortcodes:

* `[somenano_paywall]`: Insert a paywall in your post/page.  All content below paywall will not be visible until payment is made.
* `[somenano_paywall_numpaid]`: Insert number of payments made on that post/page.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/somenano-wordpress` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->SomeNano screen to configure the plugin

You must set a default Nano account for this plugin to work.  It will display an error until you enter a Nano account.

== Frequently Asked Questions ==

= How do I use the Paywall? =

Type the shortcode [somenano_paywall] anywhere in a post or page.  All content after the shortcode will not be shown to the user until the user sends payment.  After the payment is sent, the paywall will be gone forever for a user that was logged into the site, and for users not logged in, will be gone as long as the saved cookie remains in their browser.

Shortcode Attributes - the following attributes can be given in the shortcode and override the default values in Settings->SomeNano.

* `account`: Nano account to receive payment
* `currency`: Type of currency to convert to Nano for payment
* `amount`: The amount in set currency to charge for the paywall.  Will be converted to Nano for payment
* `preface`: Text to show on paywall to inform the user what to do
* `paid_note`: Text that will be shown instead of the paywall after a payment is made

Example: 

* Override default values: `[somenano_paywall currency="usd" amount="0.10"]`
* Use default values: `[somenano_paywall]`

= My payment goes through but the paywall stays =

This is likely due to a plugin conflict, probably a caching plugin.  Disable caching plugins (and possibly others) to verify that is the problem.  Many caching plugins let you set exceptions, you should do this for pages or posts that have a paywall.  Read more about about caching plugin conflicts here: [Caching Plugins and SomeNano for Wordpress](https://somenano.com/index.php/2019/04/24/caching-plugins-and-somenano-for-wordpress/)

== Screenshots ==

1. Demonstration of how a user makes a payment.  This demo uses the VANO wallet browser extension.
2. A paywall will appear on a post or page.  All content below the paywall will not be visible until the payment has been made.
3. Once paid, the paywall goes away.  You can show a message thanking the user in place of the paywall, or just leave it blank.
4. A simple shortcode is added to a post or page.  Attributes can be customized in the shortcode to override the defaults.
5. Settings page allows default values to be set.
6. View all payments made to the posts and pages on your site.

== Changelog ==

0.1.2
* New admin screen for viewing history and metrics of payments
* Minor style fixes
* Demo gif added

0.1.1
* Added database table in the settings to view paid transactions. Will be prettied up in later releases.

0.1.0
* Initial beta release

== Upgrade Notice ==

= 0.1.2 =
New admin screen for viewing history and metrics of payments and minor style fixes

= 0.1.1 =
Added database table in the settings to view paid transactions.

= 0.1.0 =
Initial release