<?php

/**
 * Database Version Update
 */

//Add support to YITH Product Add-Ons db version 1.0.1
function yith_wapo_update_db_1_0_1() {
    $wapo_db_option = get_option( 'yith_wapo_db_version', '1.0.0' );
    if ( $wapo_db_option && version_compare( $wapo_db_option, '1.0.1', '<' ) ) {
        global $wpdb;

        $sql = "ALTER TABLE `{$wpdb->prefix}yith_wapo_types` ADD `sold_individually` BOOLEAN DEFAULT 0";
        $wpdb->query( $sql );

        update_option( 'yith_wapo_db_version', '1.0.1' );
    }
}



add_action( 'admin_init', 'yith_wapo_update_db_1_0_1' );


