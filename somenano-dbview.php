<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

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
            <th>Total rai</th>
            <th># Unique Users</th>
            <th># Anon payments</th>
        </tr>
        <tr>
            <td>'. $total_rai .'</td>
            <td>'. count( $users ) .'</td>
            <td>'. $num_anon_users .'</td>
        </tr>
    </table>';

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
            <th>Received rai</th>
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
        $content .= '<td>'. $row->received_rai .'</td>';
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