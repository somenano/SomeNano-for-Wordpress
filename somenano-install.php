<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// REF: https://codex.wordpress.org/Creating_Tables_with_Plugins

global $somenano_db_version;
$somenano_db_version = '1.0.0';

global $somenano_version;
$somenano_version = '0.1.0';

function somenano_install()
{
    global $wpdb;
    global $somenano_db_version;

    $table_name = somenano_default('db_payments');

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        token varchar(300) NOT NULL,
        dtg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        received_rai int UNSIGNED NOT NULL,
        currency varchar(4) NOT NULL,
        currency_amount varchar(20) NOT NULL,
        post_id mediumint(9) UNSIGNED NOT NULL,
        user_id smallint UNSIGNED NOT NULL,
        block varchar(65) NOT NULL
        PRIMARY KEY (token)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( '$somenano_db_version', $somenano_db_version );
    add_option( '$somenano_version', $somenano_version );
}

function somenano_install_data()
{

}

?>