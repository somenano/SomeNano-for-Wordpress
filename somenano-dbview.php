<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function somenano_rai_to_nano( $rai )
{
    // 1,000,000 rai == 1 Nano
    return $rai / 1000000.0;
}

function somenano_truncate_string( $string, $start_length, $end_length )
{
    if ( strlen($string) <= $start_length+$end_length ) return $string;
    return substr( $string, 0, $start_length ) . '...' . substr( $string, strlen($string)-$end_length, $end_length);
}

function somenano_show_payments_data()
{
    global $wpdb;
    $table_name = somenano_default('db_payments');
    $results = $wpdb->get_results( ' SELECT * FROM '. $table_name );

    $total_rai = 0;
    $users = array();
    $num_anon_users = 0;
    foreach ( $results as $row ) {
        $total_rai += $row->received_rai;
        if ( $row->user_id == 0 ) {
            $num_anon_users += 1;
        } else {
            array_push( $users, $row->user_id );
        }
    }
    $users = array_unique( $users );

    $content = '
    <table class="somenano-stats-table">
        <tr>
            <th>Total Nano</th>
            <th># Unique Users</th>
            <th># Anon payments</th>
        </tr>
        <tr>
            <td>'. somenano_rai_to_nano( $total_rai ) .' Nano</td>
            <td>'. count( $users ) .'</td>
            <td>'. $num_anon_users .'</td>
        </tr>
    </table>';

    print $content;
}

function somenano_show_payments_top()
{
    global $wpdb;
    $table_name = somenano_default('db_payments');
    $results = $wpdb->get_results( ' SELECT post_id, received_rai FROM '. $table_name );
    $num_per_post = array();
    $amount_per_post = array();

    foreach ( $results as $row ) {
        if ( array_key_exists( $row->post_id, $num_per_post ) ) {
            $num_per_post[ $row->post_id ] += 1;
            $amount_per_post[ $row->post_id ] += $row->received_rai;
        } else {
            $num_per_post[ $row->post_id ] = 1;
            $amount_per_post[ $row->post_id ] = $row->received_rai;
        }
    }
    arsort( $num_per_post );
    $num_per_post = array_slice( $num_per_post, 0, 10, true );

    $content = '
    <table class="somenano-top">
        <tr>
            <th>Post</th>
            <th># Payments</th>
            <th>Total Received Nano</th>
        </tr>';

    foreach ( $num_per_post as $post_id => $pay_count ) {
        $content .= '<tr>';
        $content .= '<td><a href="'. get_permalink( $post_id ) .'" target="_new">'. get_the_title( $post_id ) .'</a></td>';
        $content .= '<td>'. $pay_count .'</td>';
        $content .= '<td>'. somenano_rai_to_nano( $amount_per_post[ $post_id ] ) .' Nano</td>';
        $content .= '</tr>';
    }

    $content .= '</table>';
    print $content;
}

function somenano_show_payments_table()
{
    global $wpdb;
    $table_name = somenano_default('db_payments');
    $results = $wpdb->get_results( ' SELECT * FROM '. $table_name .' ORDER BY dtg DESC' );
    $content = '
    <table class="somenano-dbtable">
        <tr>
            <th>Timestamp</th>
            <th>Received Nano</th>
            <th>Currency</th>
            <th>Currency Amount</th>
            <th>Page/Post</th>
            <th>User</th>
            <th>Nano Block</th>
            <th>BrainBlocks Token</th>
        </tr>';
    foreach ( $results as $row ) {
        $content .= '<tr>';
        $content .= '<td>'. $row->dtg .'</td>';
        $content .= '<td>'. somenano_rai_to_nano( $row->received_rai ) .' Nano</td>';
        $content .= '<td>'. $row->currency .'</td>';
        $content .= '<td>'. $row->currency_amount .'</td>';
        $content .= '<td><a href="'. get_permalink( $row->post_id ) .'" target="_new">'. get_the_title( $row->post_id ) .'</a></td>';
        $content .= '<td>'. ($row->user_id != 0 ? get_userdata( $row->user_id )->user_login : 'n/a') .'</td>';
        $content .= '<td><a href="https://nanocrawler.cc/explorer/block/'. $row->block .'" target="_new">'. somenano_truncate_string( $row->block, 3, 3 ) .'</a></td>';
        $content .= '<td><a href="https://api.brainblocks.io/api/session/'. $row->token .'/verify" target="_new">'. somenano_truncate_string( $row->token, 5, 5 ) .'</td>';
        $content .= '</tr>';
    }

    $content .= '</table>';
    print $content;
}