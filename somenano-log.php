<?php

require_once( 'somenano-brainblocks.php' );
require_once( 'somenano-defaults.php' );

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

    $token_data = bb_token( $token );
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