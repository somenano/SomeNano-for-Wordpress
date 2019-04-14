<?php

if ( !defined('ABSPATH') ) {
    //If wordpress isn't loaded load it up.
    $path = $_SERVER['DOCUMENT_ROOT'];
    include_once $path . '/wp-load.php';
}

require_once( plugin_dir_path( __FILE__ ) . 'somenano-brainblocks.php' );
require_once( plugin_dir_path( __FILE__ ) . 'somenano-defaults.php' );

global $wpdb;

// Validate Inputs

if ( !isset($_POST['token']) ) {
    error_log('somenano-log: token not given as post variable');
    die();
}

if ( !isset($_POST['post_id']) ) {
    error_log('somenano-log: post_id not given as post variable');
    die();
}

$token_data = bb_token( $_POST['token'] );
if ( array_key_exists('status', $token_data) && $token_data['status'] == 'error' ) {
    error_log('somenano-log: BrainBlocks returned error status');
    die();
}

if ( !isset($token_data['received_rai']) ) {
    error_log('somenano-log: BrainBlocks token did not return received_rai');
    die();
}

if ( !isset($token_data['send_block']) ) {
    error_log('somenano-log: BrainBlocks token did not return send_block');
    die();
}

if ( !isset($token_data['currency']) ) {
    error_log('somenano-log: BrainBlocks token did not return currency');
    die();
}

if ( !isset($token_data['amount']) ) {
    error_log('somenano-log: BrainBlocks token did not return amount');
    die();
}

// Update database

$wpdb->insert(
    somenano_default('db_payments'),
    array(
        'token' => $_POST['token'],
        'received_rai' => $token_data['received_rai'],
        'post_id' => $_POST['post_id'],
        'user_id' => get_current_user_id(),
        'block' => $token_data['send_block'],
        'currency' => $token_data['currency'],
        'currency_amount' => $token_data['amount']
    )
);

// Save cookie

setcookie( somenano_default('cookie_prefix').$_POST['post_id'], $_POST['token'], somenano_default('cookie_expiration'), '/' );

?>