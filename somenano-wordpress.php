<?php
/**
 * Plugin Name: SomeNano for Wordpress
 * Plugin URI: https://wordpress.org/plugins/somenano/
 * Description: Accept Nano cryptocurrency as payment for users to view content on your Wordpress site
 * Version: 0.1.2
 * Author: SomeNano
 * Author URI: https://somenano.com
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( plugin_dir_path( __FILE__ ) . 'somenano-options.php' );
require_once( plugin_dir_path( __FILE__ ) . 'somenano-install.php' );
require_once( plugin_dir_path( __FILE__ ) . 'somenano-defaults.php' );
require_once( plugin_dir_path( __FILE__ ) . 'somenano-log.php' );

register_activation_hook( __FILE__, 'somenano_install' );
register_activation_hook( __FILE__, 'somenano_install_data' );
add_action( 'init', 'somenano_post_handler' );
add_action( 'the_content', 'somenano_content_handler' );

global $payment_success;
$payment_success = false;

global $payment_posted;
$payment_posted = false;

function somenano_enqueue_scripts()
{
    wp_register_script( 'somenano-wordpress.js', plugins_url( '/js/somenano-wordpress.js', __FILE__ ), array(), $somenano_version, true );
    wp_enqueue_script( 'somenano-wordpress.js' );

    wp_register_style( 'somenano-wordpress.css', plugins_url('/css/somenano-wordpress.css', __FILE__), array(), $somenano_version, 'all' );
    wp_enqueue_style( 'somenano-wordpress.css' );
}
add_action( 'wp_enqueue_scripts', 'somenano_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'somenano_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'somenano_enqueue_bb_scripts' );
add_action( 'admin_enqueue_scripts', 'somenano_enqueue_bb_scripts' );

function somenano_enqueue_admin_scripts()
{
    wp_register_style( 'somenano-admin.css', plugins_url('/css/somenano-admin.css', __FILE__), array(), $somenano_version, 'all' );
    wp_enqueue_style( 'somenano-admin.css' );
}
add_action( 'admin_enqueue_scripts', 'somenano_enqueue_admin_scripts' );

function somenano_ready()
{
    // Check is somenano plugin is ready for use
    $options = get_option( 'somenano_options' );
    if ( !isset($options['default_paywall_account']) ) {
        return false;
    }

    return true;
}

function somenano_action_links( $links )
{
    $alert = '';
    if ( !somenano_ready() ) {
        $alert = '<span style="color:red;">*Nano Account Required*</span>';
    }

    $links = array_merge( array(
        $alert,
        '<a href="' . esc_url( admin_url( '/options-general.php?page=somenano-settings' ) ) . '">' . __( 'Settings', 'textdomain' ) . '</a>'
    ), $links );
    return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'somenano_action_links' );

function somenano_bypass_paywall()
{
    global $wpdb;
    $table_name = somenano_default('db_payments');

    // Has registered user paid?
    if ( get_current_user_id() > 0 ) {
        $sql = 'select count(*) from ' . $table_name . ' where user_id = ' . get_current_user_id() . ' and post_id = ' . get_the_ID();
        $num_rows = $wpdb->get_var( $sql );
        if ( $num_rows > 0 ) return true;
    }

    // Is cookie set?
    $cookie_name = somenano_default('cookie_prefix').get_the_ID();
    if(!isset($_COOKIE[$cookie_name]) || !ctype_alnum($_COOKIE[$cookie_name])) {
        return false;
    }

    $sql = 'select count(*) from ' . $table_name . ' where token = "' . $_COOKIE[$cookie_name] . '" and post_id = ' . get_the_ID();
    $num_rows = $wpdb->get_var( $sql );
    if ( $num_rows > 0 ) return true;

    return false;
}

function somenano_post_handler()
{
    // Check for handling of payment
    if ( isset( $_POST['token'] ) && isset( $_POST['post_id'] ) ) {

        // Sanitize/reject invalid inputs
        if ( !ctype_alnum($_POST['token']) ) {
            error_log('somenano_post_handler: token has invalid characters');
            return;
        }

        if ( !is_numeric($_POST['post_id']) ) {
            error_log('somenano_post_handler: post_id is invalid');
            return;
        }

        // Handle inputs
        global $payment_posted;
        $payment_posted = true;
        global $payment_success;
        $payment_success = somenano_log( $_POST['token'], $_POST['post_id'] );
    }
}

function somenano_content_handler( $content )
{
    // Check for bypass of paywall
    if ( $payment_success || somenano_bypass_paywall() ) {
        return $content;
    }

    // Paywall in place, truncate content
    $shortcode_regex = "/\[somenano_paywall.*]/";
    $parts = preg_split($shortcode_regex, $content);
    preg_match($shortcode_regex, $content, $m);
    if ( is_front_page() ) {
        return $parts[0];
    }
    return $parts[0] . $m[0];
}

function somenano_paywall_shortcode( $atts )
{

    $options = get_option( 'somenano_options' );

    if ( !isset($options['default_paywall_paid_note']) ) {
        $options['default_paywall_paid_note'] = somenano_default('paywall_paid_note');
    }

    if ( !isset($options['default_paywall_account']) && !isset($atts['account']) ) {
        return '<div id="somenano-paywall">** SomeNano Error: Nano account required for paywall **</div>';
    }
    if ( !isset($options['default_paywall_currency']) ) {
        $options['default_paywall_currency'] = somenano_default('paywall_currency');
    }
    if ( !isset($options['default_paywall_amount']) ) {
        $options['default_paywall_amount'] = somenano_default('paywall_amount');
    }
    if ( !isset($options['default_paywall_preface']) ) {
        $options['default_paywall_preface'] = somenano_default('paywall_preface');
    }
    if ( !isset($options['default_paywall_paid_note']) ) {
        $options['default_paywall_paid_note'] = somenano_default('paywall_paid_note');
    }

    $a = shortcode_atts( array(
        'amount' => $options['default_paywall_amount'],
        'account' => $options['default_paywall_account'],
        'currency' => $options['default_paywall_currency'],
        'preface' => $options['default_paywall_preface'],
        'paid_note' => $options['default_paywall_paid_note']
    ), $atts );

    if ( somenano_bypass_paywall() ) {
        if ( strlen($a['paid_note']) == 0 ) return '';
        $ret = '<div id="somenano-paywall">';
        $ret .= $a['paid_note'];
        $ret .= '</div>';
        return $ret;
    }

    if ( is_front_page() ) {
        return '';
    }

    global $wp;

    // Log paywall in the db for later verification
    somenano_log_paywall($a['currency'], $a['amount'], get_the_ID(), $a['account']);

    $on_payment = 'document.getElementById("somenano-paywall-note").innerHTML = "Processing, please wait...";
log_payment("'. home_url( $wp->request ) .'", data.token, '. get_the_ID() .');';

    somenano_bb_render(
        $a['currency'],
        $a['amount'],
        $a['account'],
        $on_payment
    );

    $error = '';
    if ( $payment_posted && !$payment_success ) {
        $error = 'Error validating payment. Please contact site admin for refund and try again.<b>';
    }

    $ret = '<div id="somenano-paywall">';
    $ret .= '<div id="somenano-paywall-note">' . $error . $a['preface'] . '</div>';
    $ret .= '<div>' . somenano_bb_button() . '</div>';
    $ret .= '</div>';

    return $ret;
}
add_shortcode( 'somenano_paywall', 'somenano_paywall_shortcode' );

?>
