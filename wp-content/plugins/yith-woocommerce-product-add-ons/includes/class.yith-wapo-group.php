<?php
/**
 * Group class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;

/*
 *  
 */

if ( ! class_exists( 'YITH_WAPO_Group' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WAPO_Group {

        public $id              = 0;
        public $name            = '';
        public $user_id         = '';
        public $products_id     = '';
        public $categories_id   = '';
        public $attributes_id   = '';
        public $priority        = 0;
        public $visibility      = 0;
        public $reg_date        = '0000-00-00 00:00:00';
        public $del             = 0;

        const VISIBILITY_HIDDEN = 0;
        const VISIBILITY_ADMIN = 1;
        const VISIBILITY_PUBLIC = 9;

        /**
         * Constructor
         *
         * @access public
         * @since 1.0.0
         */
        public function __construct( $id = 0 ) {

            global $wpdb;

            if ( $id > 0 ) {

                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}yith_wapo_groups WHERE id='$id'" );

                if ( isset( $row ) && $row->id == $id ) {

                    $this->id               = $row->id;
                    $this->name             = $row->name;
                    $this->user_id          = $row->user_id;
                    $this->products_id      = $row->products_id;
                    $this->categories_id    = $row->categories_id;
                    $this->attributes_id    = $row->attributes_id;
                    $this->priority         = $row->priority;
                    $this->visibility       = $row->visibility;
                    $this->reg_date         = $row->reg_date;
                    $this->del              = $row->del;

                }

            }
            
        }

        function insert() {

            global $wpdb;
            $wpdb->hide_errors();

            $new_name            = isset( $_POST['name'] )          ? $_POST['name']            : '';
            $new_user_id = 0;
            $new_products_id     = isset( $_POST['products_id'] )   ? $_POST['products_id']     : '';
            $new_categories_id   = isset( $_POST['categories_id'] ) ? $_POST['categories_id']   : '';
            $new_attributes_id   = isset( $_POST['attributes_id'] ) ? $_POST['attributes_id']   : '';
            $new_priority        = isset( $_POST['priority'] )      ? $_POST['priority']        : '';
            $new_visibility      = isset( $_POST['visibility'] )    ? $_POST['visibility']      : '';

            /* multi vendor */
            $vendor_user = YITH_WAPO::get_current_multivendor();

            if( isset( $vendor_user ) && is_object( $vendor_user ) ) {
                $new_user_id = $vendor_user->id;
            }
            /* end multi vendor */


            $new_categories_id = is_array( $new_categories_id ) ? implode( ',', $new_categories_id ) : $new_categories_id;

            $sql = "INSERT INTO {$wpdb->prefix}yith_wapo_groups (
                    id,
                    name,
                    user_id,
                    products_id,
                    categories_id,
                    attributes_id,
                    priority,
                    visibility,
                    reg_date,
                    del
                ) VALUES (
                    '',
                    '$new_name',
                     $new_user_id,
                    '$new_products_id',
                    '$new_categories_id',
                    '$new_attributes_id',
                    '$new_priority',
                    '$new_visibility',
                    CURRENT_TIMESTAMP,
                    '0'
                )";

            $wpdb->query( $sql );

        }

        function update( $id ) {

            global $wpdb;
            $wpdb->hide_errors();

            $new_name           = isset( $_POST['name'] )           ? $_POST['name']            : 0;
            $new_products_id    = isset( $_POST['products_id'] )    ? $_POST['products_id']     : '';
            $new_categories_id  = isset( $_POST['categories_id'] )  ? $_POST['categories_id']   : '';
            $new_attributes_id  = isset( $_POST['attributes_id'] )  ? $_POST['attributes_id']   : '';
            $new_priority       = isset( $_POST['priority'] )       ? $_POST['priority']        : 0;
            $new_visibility     = isset( $_POST['visibility'] )     ? $_POST['visibility']      : 0;
            $new_del            = isset( $_POST['del'] )            ? $_POST['del']             : 0;

            $new_products_id = is_array( $new_products_id ) ? implode( ',', $new_products_id ) : $new_products_id;
            $new_categories_id = is_array( $new_categories_id ) ? implode( ',', $new_categories_id ) : $new_categories_id;

            $sql = "UPDATE {$wpdb->prefix}yith_wapo_groups SET
                name            = '$new_name',
                products_id     = '$new_products_id',
                categories_id   = '$new_categories_id',
                attributes_id   = '$new_attributes_id',
                priority        = '$new_priority',
                visibility      = '$new_visibility',
                del             = '$new_del'
                WHERE id='$id'";

            $wpdb->query( $sql );

        }

        public static function create_tables() {

            /**
             * Check if dbDelta() exists
             */
            if ( ! function_exists( 'dbDelta' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            }

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $create = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}yith_wapo_groups (
                        id              BIGINT(20) NOT NULL AUTO_INCREMENT,
                        name            VARCHAR(250),
                        user_id         BIGINT(20),
                        products_id     VARCHAR(250),
                        categories_id   VARCHAR(250),
                        attributes_id   VARCHAR(250),
                        priority        INT(2),
                        visibility      INT(1),
                        reg_date        TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                        del             TINYINT(1) NOT NULL DEFAULT '0',
                        PRIMARY KEY     (id)
                    ) $charset_collate;";
            $result = dbDelta( $create );

        }

    }

}