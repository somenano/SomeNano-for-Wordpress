<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

// REF: https://codex.wordpress.org/Creating_Tables_with_Plugins

global $somenano_db_version;
$somenano_db_version = '1.0.0';

global $somenano_version;
$somenano_version = '0.1.3';

function somenano_install()
{
    global $wpdb;
    global $somenano_db_version;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = somenano_default('db_payments');
    $sql = "CREATE TABLE $table_name (
        token varchar(300) NOT NULL,
        dtg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        received_rai int UNSIGNED NOT NULL,
        currency varchar(4) NOT NULL,
        currency_amount varchar(20) NOT NULL,
        post_id mediumint(9) UNSIGNED NOT NULL,
        user_id smallint UNSIGNED NOT NULL,
        block varchar(65) NOT NULL,
        PRIMARY KEY (token)
    ) $charset_collate;";
    dbDelta( $sql );

    $table_name = somenano_default('db_paywalls');
    $sql = "CREATE TABLE $table_name (
        dtg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        currency varchar(4) NOT NULL,
        currency_amount varchar(20) NOT NULL,
        post_id mediumint(9) UNSIGNED NOT NULL,
        destination varchar(70) NOT NULL,
        PRIMARY KEY (post_id)
    ) $charset_collate;";
    dbDelta( $sql );

    add_option( '$somenano_db_version', $somenano_db_version );
    add_option( '$somenano_version', $somenano_version );
}

function somenano_install_data()
{

}

?>
