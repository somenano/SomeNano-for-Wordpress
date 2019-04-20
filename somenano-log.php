<?php

require_once( 'somenano-brainblocks.php' );
require_once( 'somenano-defaults.php' );

function somenano_has_log_paywall($currency, $currency_amount, $post_id, $destination)
{
    global $wpdb;

    $sql = 'select count(*) from ' . somenano_default('db_paywalls') . ' where currency = "' . $currency . '" and currency_amount = ' . $currency_amount . ' and post_id = ' . $post_id . ' and destination = "' . $destination . '"';
    $num_rows = $wpdb->get_var( $sql );
    if ( $num_rows > 0 ) return true;
    return false;
}

function somenano_log_paywall($currency, $currency_amount, $post_id, $destination)
{
    global $wpdb;

    // Validate inputs
    if ( !in_array( $currency, somenano_bb_currencies() ) ) {
        error_log('somenano-log: invalid currency: '. $currency);
        return false;
    }

    if ( !is_numeric( $currency_amount ) ) {
        error_log('somenano-log: invalid currency_amount: '. $currency_amount);
        return false;
    }

    if ( !is_numeric( $post_id ) ) {
        error_log('somenano-log: invalid post_id: '. $post_id);
        return false;
    }

    if ( !preg_match( '/^(xrb|nano)_[a-z0-9]{60}$/i', $destination ) ) {
        error_log('somenano-log: invalid destination: '. $destination);
        return false;
    }

    $wpdb->replace(
        somenano_default('db_paywalls'),
        array(
            'currency' => $currency,
            'currency_amount' => $currency_amount,
            'post_id' => $post_id,
            'destination' => $destination
        )
    );
    return true;
}

function somenano_log($token, $post_id)
{

    global $wpdb;

    // Validate Inputs
    if ( !ctype_alnum($token) ) {
        error_log('somenano-log: token has invalid characters');
        return false;
    }

    if ( !is_numeric($post_id) ) {
        error_log('somenano-log: post_id is invalid value');
        return false;
    }

    $token_data = somenano_bb_token( $token );
    if ( array_key_exists('status', $token_data) && $token_data['status'] == 'error' ) {
        error_log('somenano-log: BrainBlocks returned error status');
        return false;
    }

    if ( !isset($token_data['received_rai']) ) {
        error_log('somenano-log: BrainBlocks token did not return received_rai');
        return false;
    }

    if ( !isset($token_data['send_block']) ) {
        error_log('somenano-log: BrainBlocks token did not return send_block');
        return false;
    }

    if ( !isset($token_data['currency']) ) {
        error_log('somenano-log: BrainBlocks token did not return currency');
        return false;
    }

    if ( !isset($token_data['amount']) ) {
        error_log('somenano-log: BrainBlocks token did not return amount');
        return false;
    }

    // Check if paywall is in the database
    if ( !somenano_has_log_paywall( $token_data['currency'], $token_data['amount'], $post_id, $token_data['destination'] ) ) {
        error_log('somenano-log: No paywall found in database');
        return false;
    }

    // Update database
    $wpdb->insert(
        somenano_default('db_payments'),
        array(
            'token' => $token,
            'received_rai' => $token_data['received_rai'],
            'post_id' => $post_id,
            'user_id' => get_current_user_id(),
            'block' => $token_data['send_block'],
            'currency' => $token_data['currency'],
            'currency_amount' => $token_data['amount']
        )
    );

    // Save cookie

    setcookie( somenano_default('cookie_prefix').$post_id, $token, somenano_default('cookie_expiration'), '/' );

    return true;

}

?>