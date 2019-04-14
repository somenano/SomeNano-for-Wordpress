=== SomeNano for Wordpress ===
Contributors: pwlk
Donate link: http://www.pwlk.net 
Tags: nano, cryptocurrency, paywall, pay, wall, donation, donations, payment, money
Requires at least: 4.6
Tested up to: 5.1.1
Stable tag: 4.3
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Accept Nano Cryptocurrency on your Wordpress site. Set up a paywall so content on posts or pages is hidden until the user pays.

== Description ==

SomeNano for Wordpress is a Plugin that will allow you to accept Nano cryptocurrency as payment on your Wordpress site.

Some different ways that you can accept payment is...

* Paywall on a blog post - require a user to make a payment to view the full content of a blog post.
* Paywall on a page - require a user to make a payment to view some or all of the content on a page.

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

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

== Screenshots ==

1. A paywall will appear on a post or page.  All content below the paywall will not be visible until the payment has been made.
2. Once paid, the paywall goes away.  You can show a message thanking the user in place of the paywall, or just leave it blank.
3. A simple shortcode is added to a post or page.  Attributes can be customized in the shortcode to override the defaults.
4. Settings page allows default values to be set.

== Changelog ==

0.1.0
* Initial beta release