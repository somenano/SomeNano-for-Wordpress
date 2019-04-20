<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function somenano_default( $key )
{
    // Plugin
    if ( $key == 'cookie_expiration' ) return time()+86400*365;
    if ( $key == 'cookie_prefix' ) return 'wp-somenano-';
    if ( $key == 'db_payments' ) return $GLOBALS['wpdb']->prefix . 'somenano_payments';
    if ( $key == 'db_paywalls' ) return $GLOBALS['wpdb']->prefix . 'somenano_paywalls';

    // Paywall
    if ( $key == 'paywall_currency' ) return 'rai';
    if ( $key == 'paywall_amount' ) return '100000';
    if ( $key == 'paywall_preface' ) return 'Want to keep reading? It is a lot of work to produce great content. Please click the below button to make a small payment in Nano to continue reading. Thanks for your support!';
    if ( $key == 'paywall_paid_note' ) return 'Thanks for your support!  Keep reading below.';
}

?>