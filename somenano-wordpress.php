<?php
/**
 * Plugin Name: SomeNano for Wordpress
 * Plugin URI: https://somenano.com
 * Description: Accept Nano cryptocurrency as payment for users to view content on your Wordpress site
 * Version: 0.1.0
 * Author: Jason Pawlak
 * Author URI: https://github.com/pawapps
 */

/*
 Ideas/Todo:
 - Author Nano Account
 - Donation widget
 - Post donation
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( plugin_dir_path( __FILE__ ) . 'somenano-options.php' );
require_once( plugin_dir_path( __FILE__ ) . 'somenano-install.php' );
require_once( plugin_dir_path( __FILE__ ) . 'somenano-defaults.php' );

register_activation_hook( __FILE__, 'somenano_install' );
register_activation_hook( __FILE__, 'somenano_install_data' );
add_action( 'the_content', 'paywall_truncate' );

function enqueue_scripts()
{
    wp_register_script( 'somenano-wordpress.js', plugins_url( '/js/somenano-wordpress.js', __FILE__ ), array(), $somenano_version, true );
    wp_enqueue_script( 'somenano-wordpress.js' );

    wp_register_style( 'somenano-wordpress.css', plugins_url('/css/somenano-wordpress.css', __FILE__), array(), $somenano_version, 'all' );
    wp_enqueue_style( 'somenano-wordpress.css' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

function bypass_paywall()
{
    global $wpdb;
    $table_name = somenano_default('db_payments');

    // Has registered user paid?
    $sql = 'select count(*) from ' . $table_name . ' where user_id = ' . get_current_user_id() . ' and post_id = ' . get_the_ID();
    $num_rows = $wpdb->get_var( $sql );
    if ( $num_rows > 0 ) return true;

    // Is cookie set?
    $cookie_name = somenano_default('cookie_prefix').get_the_ID();

    if(!isset($_COOKIE[$cookie_name])) {
        return false;
    }
    $sql = 'select count(*) from ' . $table_name . ' where token = "' . $_COOKIE[$cookie_name] . '" and post_id = ' . get_the_ID();
    $num_rows = $wpdb->get_var( $sql );
    if ( $num_rows > 0 ) return true;

    return false;
}

function paywall_truncate( $content )
{
    if ( bypass_paywall() ) {
        return $content;
    }

    $shortcode_regex = "/\[somenano_paywall.*]/";
    $parts = preg_split($shortcode_regex, $content);
    preg_match($shortcode_regex, $content, $m);
    return $parts[0] . $m[0];
}

function paywall_shortcode( $atts )
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

    if ( bypass_paywall() ) {
        if ( strlen($a['paid_note']) == 0 ) return '';
        $ret = '<div id="somenano-paywall">';
        $ret .= $a['paid_note'];
        $ret .= '</div>';
        return $ret;
    }

    $on_payment = 'document.getElementById("somenano-paywall-note").innerHTML = "Processing, please wait...";
log_payment("'. plugins_url( 'somenano-log.php', __FILE__ ) .'", data.token, '. get_the_ID() .');';

    $ret = '<div id="somenano-paywall">';
    $ret .= '<div id="somenano-paywall-note">' . $a['preface'] . '</div>';
    $ret .= '<div>' . bb_button() . '</div>';
    $ret .= bb_script();
    $ret .= bb_render(
        $a['currency'],
        $a['amount'],
        $a['account'],
        $on_payment
    );
    $ret .= '</div>';

    return $ret;
}
add_shortcode( 'somenano_paywall', 'paywall_shortcode' );

?>
