<?php
/**
 * Type class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;

/*
 *  
 */

if ( ! class_exists( 'YITH_WAPO_Type' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WAPO_Type {

        public $id              = 0;
        public $group_id        = 0;
        public $type            = '';
        public $label           = '';
        public $image           = '';
        public $description     = '';
        public $depend          = '';
        public $options         = '';
        public $required        = 0;
        public $step            = 0;
        public $priority        = 0;
        public $reg_date        = '0000-00-00 00:00:00';
        public $del             = 0;

        /**
         * Constructor
         *
         * @access public
         * @since 1.0.0
         */
        public function __construct( $id = 0 ) {

            global $wpdb;

            if ( $id > 0 ) {

                $row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}yith_wapo_types WHERE id='$id'" );

                if ( $row->id == $id ) {

                    $this->id               = $row->id;
                    $this->group_id         = $row->group_id;
                    $this->type             = $row->type;
                    $this->label            = $row->label;
                    $this->image            = $row->image;
                    $this->description      = $row->description;
                    $this->depend           = $row->depend;
                    $this->options          = $row->options;
                    $this->required         = $row->required;
                    $this->step             = $row->step;
                    $this->priority         = $row->priority;
                    $this->reg_date         = $row->reg_date;
                    $this->del              = $row->del;

                }

            }
            
        }

        /**
         * @author Corrado Porzio
         */
        function insert() {

            global $wpdb;
            $wpdb->hide_errors();

            $new_group_id                = isset( $_POST['group_id'] )       ? $_POST['group_id']    : '';
            $new_type                    = isset( $_POST['type'] )           ? $_POST['type']        : '';
            $new_label                   = isset( $_POST['label'] )          ? $_POST['label']       : '';
            $new_image                   = isset( $_POST['image'] )          ? $_POST['image']       : '';
            $new_description             = isset( $_POST['description'] )    ? $_POST['description'] : '';
            $new_depend                  = isset( $_POST['depend'] )         ? $_POST['depend']      : '';
            $new_options                 = isset( $_POST['options'] )        ? $_POST['options']     : '';
            $new_required                = isset( $_POST['required'] )       ? $_POST['required']    : 0;
            $new_step                    = isset( $_POST['step'] )           ? $_POST['step']        : 0;
            $new_priority                = isset( $_POST['priority'] )       ? $_POST['priority']    : 0;

            $new_depend = is_array( $new_depend ) ? implode( ',', $new_depend ) : $new_depend;

            if ( is_array( $new_options ) ) {
                array_pop( $new_options['label'] );
                array_pop( $new_options['price'] );
                if ( isset( $new_options['min'] ) ) { array_pop( $new_options['min'] ); }
                if ( isset( $new_options['max'] ) ) { array_pop( $new_options['max'] ); }
                $new_options = serialize( $new_options );
            }

            $sql = "INSERT INTO {$wpdb->prefix}yith_wapo_types (
                    id,
                    group_id,
                    type,
                    label,
                    image,
                    description,
                    depend,
                    options,
                    required,
                    step,
                    priority,
                    reg_date,
                    del
                ) VALUES (
                    '',
                    '$new_group_id',
                    '$new_type',
                    '$new_label',
                    '$new_image',
                    '$new_description',
                    '$new_depend',
                    '$new_options',
                    '$new_required',
                    '$new_step',
                    '$new_priority',
                    CURRENT_TIMESTAMP,
                    '0'
                )";

            $wpdb->query( $sql );

            if( YITH_WAPO::$is_wpml_installed ) {

                YITH_WAPO_WPML::register_option_type( $new_label , $new_description , $new_options );

            }

        }

        /**
         * @author Corrado Porzio
         * @param $ids
         */
        public static function update_priorities( $ids ) {
            global $wpdb;
            $ids = explode( ',', $ids);
            $priority = 1;
            foreach ( $ids as $key => $value ) {
                if ( $value > 0 ) {
                    $wpdb->query( "UPDATE {$wpdb->prefix}yith_wapo_types SET  priority='$priority' WHERE id='$value'" );
                    $priority++;
                }
            }
        }

        /**
         * @author Corrado Porzio
         * @param $id
         */
        function update( $id ) {

            global $wpdb;
            $wpdb->hide_errors();

            $new_group_id                = isset( $_POST['group_id'] )       ? $_POST['group_id']    : '';
            $new_type                    = isset( $_POST['type'] )           ? $_POST['type']        : '';
            $new_label                   = isset( $_POST['label'] )          ? $_POST['label']       : '';
            $new_image                   = isset( $_POST['image'] )          ? $_POST['image']       : '';
            $new_description             = isset( $_POST['description'] )    ? $_POST['description'] : '';
            $new_depend                  = isset( $_POST['depend'] )         ? $_POST['depend']      : '';
            $new_options                 = isset( $_POST['options'] )        ? $_POST['options']     : '';
            $new_required                = isset( $_POST['required'] )       ? $_POST['required']    : 0;
            $new_step                    = isset( $_POST['step'] )           ? $_POST['step']        : 0;
            $new_priority                = isset( $_POST['priority'] )       ? $_POST['priority']    : 0;
            $new_del                     = isset( $_POST['del'] )            ? $_POST['del']         : 0;

            $new_depend = is_array( $new_depend ) ? implode( ',', $new_depend ) : $new_depend;

            if ( is_array( $new_options ) ) {
                array_pop( $new_options['label'] );
                array_pop( $new_options['price'] );
                if ( isset( $new_options['min'] ) ) { array_pop( $new_options['min'] ); }
                if ( isset( $new_options['max'] ) ) { array_pop( $new_options['max'] ); }
                $new_options = serialize( $new_options );
            }
            
            $sql = "UPDATE {$wpdb->prefix}yith_wapo_types SET
                group_id             = '$new_group_id',
                type                 = '$new_type',
                label                = '$new_label',
                image                = '$new_image',
                description          = '$new_description',
                depend               = '$new_depend',
                options              = '" . addslashes( $new_options ) . "',
                required             = '$new_required',
                step                 = '$new_step',
                priority             = '$new_priority',
                del                  = '$new_del'
                WHERE id='$id'";

            $wpdb->query( $sql );

            if( YITH_WAPO::$is_wpml_installed ) {

                YITH_WAPO_WPML::register_option_type( $new_label , $new_description , $new_options );

            }

        }

        /**
         * @author Corrado Porzio
         */
        public static function create_tables() {

            /**
             * Check if dbDelta() exists
             */
            if ( ! function_exists( 'dbDelta' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            }

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $create = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}yith_wapo_types (
                        id              BIGINT(20) NOT NULL AUTO_INCREMENT,
                        group_id        BIGINT(20),
                        type            VARCHAR(250),
                        label           VARCHAR(250),
                        image           VARCHAR(250),
                        description     VARCHAR(250),
                        depend          VARCHAR(250),
                        options         TEXT,
                        required        TINYINT(1) NOT NULL DEFAULT '0',
                        sold_individually        TINYINT(1) NOT NULL DEFAULT '0',
                        step            int(2),
                        priority        INT(2),
                        reg_date        TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                        del             TINYINT(1) NOT NULL DEFAULT '0',
                        PRIMARY KEY     (id)
                    ) $charset_collate;";
            dbDelta( $create );

        }

        /**
         * @param int  $product_id
         * @param null $wpdb
         *
         * @author Andrea Frascaspata
         * @return mixed
         */
        public static function getAllowedGroupTypes(  $product_id = 0 , $wpdb = null ) {

            if( ! ( $product_id > 0 ) ) return array();

            if( ! isset( $wpdb ) ) {
                global $wpdb;
            }

            //exclude global
            $exclude_global =  get_post_meta( $product_id , '_wapo_disable_global' , true ) === 'yes' ? 1 : 0;

            //visibility
            $is_administrator = current_user_can('administrator') ? 1 : 0;

            //category filter
            $category_query = '';
            $product_categories_ids = wc_get_product_cat_ids( $product_id );

            for( $i=0 ; $i < count( $product_categories_ids ) ; $i++ ){
                $category_query .= "FIND_IN_SET( {$product_categories_ids[$i]} , ywg.categories_id )";
                if( $i< ( count( $product_categories_ids ) -1 ) )  {
                    $category_query.=' or ' ;
                }
            }

            if( !empty( $category_query ) ) {
                $category_query = "OR ( {$category_query} )";
            }


            //vendor

            $vendor_filter = 'AND ( ywg.user_id=0 OR ywg.user_id IS NULL )';
            $vendor = YITH_WAPO::get_multivendor_by_id( $product_id , 'product' );
            if( isset( $vendor ) && is_object( $vendor ) && YITH_WAPO::is_plugin_enabled_for_vendors() ) {
                $vendor_filter = " AND ( (ywg.user_id=0 OR ywg.user_id IS NULL ) OR ywg.user_id={$vendor->id} ) ";

                // visibility

                if( $is_administrator == 0 ) {
                    $current_logged_vendor = YITH_WAPO::get_current_multivendor();
                    $is_administrator = isset( $current_logged_vendor ) && is_object( $current_logged_vendor ) && $current_logged_vendor->id == $vendor->id ? 1 : 0;
                }

            }


            $query = "SELECT distinct ywt.* FROM {$wpdb->prefix}yith_wapo_groups ywg join {$wpdb->prefix}yith_wapo_types ywt on ywg.id=ywt.group_id
            WHERE ywg.del='0' and ywt.del='0' and ( ( ( {$exclude_global}=0 and ( ywg.products_id='' and ywg.categories_id='' ) ) || ( FIND_IN_SET( {$product_id} , ywg.products_id ) ) {$category_query} ) and (ywg.visibility=9 or ( ywg.visibility=1 and {$is_administrator}=1 ) )) $vendor_filter
            ORDER BY ywg.priority ASC, ywt.priority ASC";

            //var_dump($query);

            $rows = $wpdb->get_results( $query );

            return $rows;

        }

        public static function getSingleGroupType(  $group_id = 0 , $wpdb = null ) {

            if( ! ( $group_id > 0 ) ) return array();

            if( ! isset( $wpdb ) ) {
                global $wpdb;
            }

            $query = "SELECT ywt.* FROM {$wpdb->prefix}yith_wapo_types ywt WHERE ywt.del='0' and ywt.id={$group_id}";

            $rows = $wpdb->get_results( $query );

            return $rows;

        }

        /**
         * @param $yith_wapo_frontend
         * @param $product
         * @param $single_type
         * @param $value
         * @param $upload_value
         * @return array
         */
        public static function getCartDataByPostValue( $yith_wapo_frontend, $product , $variation , $single_type , $value , $upload_value ) {

            $cart_item_data = array();

            switch ( $single_type->type ) {

                case 'select' :

                    $cart_item_data[] = YITH_WAPO_Type::getCartDataByPostValueSelect( $yith_wapo_frontend, $product , $variation, $single_type , $value );

                    break;

                case 'checkbox' :

                    YITH_WAPO_Type::getCartDataByPostValueCheckbox(  $yith_wapo_frontend, $product , $variation , $single_type , $value , $cart_item_data );

                    break;

                case 'radio' :

                    YITH_WAPO_Type::getCartDataByPostValueRadio(  $yith_wapo_frontend, $product , $variation, $single_type , $value , $cart_item_data );

                    break;

                case 'file' :

                    YITH_WAPO_Type::getCartDataByPostValueFile(  $yith_wapo_frontend, $product, $variation , $single_type , $upload_value , $cart_item_data );

                    break;

                case 'labels' :

                    $cart_item_data[] = YITH_WAPO_Type::getCartDataByPostValueLabels( $yith_wapo_frontend, $product , $variation , $single_type , $value );

                    break;

                default :

                    YITH_WAPO_Type::getCartDataByPostValueDefault(  $yith_wapo_frontend, $product , $variation , $single_type , $value , $cart_item_data );

                    break;

            }

            return $cart_item_data;

        }

        /**
         * @param $yith_wapo_frontend
         * @param $product
         * @param $variation
         * @param $single_type
         * @param $value
         * @return array
         */
        private static function getCartDataByPostValueSelect( $yith_wapo_frontend, $product , $variation , $single_type , $value  ) {

            $price = YITH_WAPO_Option::getOptionDataByValueSelect( $single_type , $value , 'price' );
            $price_type = YITH_WAPO_Option::getOptionDataByValueSelect( $single_type , $value , 'type' );

            return  array(
                'name'  => $single_type->label,
                'value' => YITH_WAPO_Option::getOptionDataByValueSelect( $single_type , $value , 'label' ),
                'price' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type , true, $variation ),
                'price_original' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type, false, $variation ),
                'price_type' => $price_type,
                'type_id' => $single_type->id,
                'original_value' => $value,
            );

        }

        /**
         * @param $yith_wapo_frontend
         * @param $product
         * @param $variation
         * @param $single_type
         * @param $value
         * @return array
         */
        private static function getCartDataByPostValueLabels( $yith_wapo_frontend, $product , $variation , $single_type , $value  ) {

            $price = YITH_WAPO_Option::getOptionDataByValueLabels( $single_type , $value , 'price' );
            $price_type = YITH_WAPO_Option::getOptionDataByValueLabels( $single_type , $value , 'type' );

            return  array(
                'name'  => $single_type->label,
                'value' => YITH_WAPO_Option::getOptionDataByValueLabels( $single_type , $value , 'label' ),
                'price' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type , true , $variation ),
                'price_original' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type, false , $variation ),
                'price_type' => $price_type,
                'type_id' => $single_type->id,
                'original_value' => $value,
            );

        }

        /**
         * @param $yith_wapo_frontend
         * @param $product
         * @param $variation
         * @param $single_type
         * @param $value
         * @param $cart_item_data
         */
        private static function getCartDataByPostValueCheckbox( $yith_wapo_frontend, $product , $variation , $single_type , $value , &$cart_item_data ) {

            if( is_array( $value ) ) {

                $i=0;

                foreach( $value as $key => $single_value ) {

                    $price = YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $key , 'price' );
                    $price_type = YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $key , 'type' );
                    $cart_item_data[] = array(
                        'name'  => $single_type->label,
                        'value' => YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $key , 'label' ),
                        'price' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type , true , $variation ),
                        'price_original' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type, false, $variation ),
                        'price_type' => $price_type,
                        'type_id' => $single_type->id,
                        'original_value' => $value,
                        'original_index' => $i,
                    );

                    $i++;

                }

            }

        }

        /**
         * @param $yith_wapo_frontend
         * @param $product
         * @param $variation
         * @param $single_type
         * @param $value
         * @param $cart_item_data
         */
        private static function getCartDataByPostValueRadio( $yith_wapo_frontend, $product, $variation , $single_type , $value , &$cart_item_data ) {

            if( is_array( $value ) ) {

                $i=0;

                foreach( $value as $key => $single_value ) {

                    if( $single_value!='' ) {

                        $price = YITH_WAPO_Option::getOptionDataByValueRadio( $single_type , $single_value , 'price' );
                        $price_type = YITH_WAPO_Option::getOptionDataByValueRadio( $single_type , $single_value , 'type' );
                        $cart_item_data[] = array(
                            'name'  => $single_type->label,
                            'value' => YITH_WAPO_Option::getOptionDataByValueRadio( $single_type , $single_value , 'label' ),
                            'price' => $yith_wapo_frontend->get_display_price( $product , $price  , $price_type , true, $variation ),
                            'price_original' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type, false, $variation ),
                            'price_type' => $price_type,
                            'type_id' => $single_type->id,
                            'original_value' => $value,
                            'original_index' => $i,
                        );

                    }

                    $i++;

                }

            }

        }

        /**
         * @param $yith_wapo_frontend
         * @param $product
         * @param $variation
         * @param $single_type
         * @param $upload_value
         * @param $cart_item_data
         */
        private static function getCartDataByPostValueFile( $yith_wapo_frontend, $product , $variation , $single_type , $upload_value , &$cart_item_data ) {

            if( is_array( $upload_value ) ) {

                for( $i=0; $i<count( $upload_value['name'] ); $i++ ){

                    if( isset( $upload_value['name'][$i] ) && !empty( $upload_value['name'][$i] ) ) {
                        // allowed upload types
                        $extension = '';
                        $pathinfo = pathinfo( $upload_value['name'][$i] );
                        if( is_array( $pathinfo ) ) {
                            $extension = '.'.$pathinfo['extension'];
                        }

                        if( ! is_array( $yith_wapo_frontend->_option_upload_allowed_type ) || ! in_array( $extension , $yith_wapo_frontend->_option_upload_allowed_type )  ) {
                            wc_add_notice( sprintf( __( 'Uploading error: %s extension is not allowed' , 'yith-woocommerce-product-add-ons' ) , $extension )  );
                            continue;
                        }

                        $file_data['name'] = $upload_value['name'][$i];
                        $file_data['type'] = $upload_value['type'][$i];
                        $file_data['tmp_name'] = $upload_value['tmp_name'][$i];
                        $file_data['error'] = $upload_value['error'][$i];
                        $file_data['size'] = $upload_value['size'][$i];

                        $uploaded_file =  YITH_WAPO_Type::getUploadedFile( $yith_wapo_frontend , $file_data);

                        $value = '';
                        if ( empty( $uploaded_file['error'] ) && ! empty( $uploaded_file['file'] ) ) {
                            $value = '<a href="'.esc_url( $uploaded_file['url']  ).'" target="_blank">'.$uploaded_file['type'].'</a>' ;
                        } else {
                            wc_add_notice( $uploaded_file['error'] );
                            continue;
                        }

                        $price = YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $i , 'price' );
                        $price_type = YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $i , 'type' );

                        $cart_item_data[] = array(
                            'name'  => YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $i , 'label' ),
                            'value' => $value,
                            'price' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type , true , $variation ),
                            'price_original' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type, false , $variation ),
                            'price_type' => $price_type,
                            'type_id' => $single_type->id,
                            'original_value' => $uploaded_file,
                            'original_index' => $i,
                            'uploaded_file' => true
                        );

                    }

                }

            }

        }


        public static function checkUploadedFilesError( $yith_wapo_frontend , $upload_value ) {

            if( is_array( $upload_value ) ) {

                for( $i=0; $i<count( $upload_value['name'] ); $i++ ){

                    if( isset( $upload_value['name'][$i] ) && !empty( $upload_value['name'][$i] ) ) {
                        // allowed upload types
                        $extension = '';
                        $pathinfo = pathinfo( $upload_value['name'][$i] );
                        if( is_array( $pathinfo ) ) {
                            $extension = '.'.$pathinfo['extension'];
                        }

                        if( ! is_array( $yith_wapo_frontend->_option_upload_allowed_type ) || ! in_array( $extension , $yith_wapo_frontend->_option_upload_allowed_type )  ) {
                            wc_add_notice( sprintf( __( 'Uploading error: %s extension is not allowed' , 'yith-woocommerce-product-add-ons' ) , $extension )  );
                            return false;
                        }

                    }

                }

            }

            return true;

        }

        /**
         * @param $yith_wapo_frontend
         * @param $file
         * @return array
         */
        private static function getUploadedFile( $yith_wapo_frontend , $file ){

            include_once( ABSPATH . 'wp-admin/includes/file.php' );
            include_once( ABSPATH . 'wp-admin/includes/media.php' );

            add_filter( 'upload_dir',  array( $yith_wapo_frontend, 'upload_dir' ) );

            $upload = wp_handle_upload( $file, array( 'test_form' => false ) );

           remove_filter( 'upload_dir',  array( $yith_wapo_frontend, 'upload_dir' ) );

            return $upload;

        }

        /**
         * @param $yith_wapo_frontend
         * @param $product
         * @param $variation
         * @param $single_type
         * @param $value
         * @param $cart_item_data
         */
        private static function getCartDataByPostValueDefault( $yith_wapo_frontend, $product , $variation , $single_type , $value , &$cart_item_data ) {

            if( is_array( $value ) ) {

                $i=0;

                foreach( $value as $key => $single_value ) {

                    $single_value = trim( $single_value , ' ' );

                    if( ! empty( $single_value ) ) {

                        $price = YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $key , 'price' );
                        $price_type = YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $key , 'type' );
                        $cart_item_data[] = array(
                            'name'  => YITH_WAPO_Option::getOptionDataByValueKey( $single_type , $key , 'label' ),
                            'value' => $single_value,
                            'price' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type , true , $variation ),
                            'price_original' => $yith_wapo_frontend->get_display_price( $product , $price , $price_type, false , $variation ),
                            'price_type' => $price_type,
                            'type_id' => $single_type->id,
                            'original_value' => $value,
                            'original_index' => $i,
                        );

                    }

                    $i++;

                }

            }

        }

        public static function printOptionTypeForm( $wpdb , $group , $type = null ) {
           
            include( YITH_WAPO_TEMPLATE_ADMIN_PATH.'yith-wapo-form-option-type.php' );
            
        }
        


    }

}