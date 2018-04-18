<?php
/**
 * Frontend class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WAPO' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WAPO_Frontend' ) ) {
    /**
     * Frontend class.
     * The class manage all the frontend behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WAPO_Frontend {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version;

        /**
     * @var string
     * @since 1.0.0
     */
        public $_option_show_label_type;

        /**
         * @var string
         * @since 1.0.0
         */
        public $_option_show_description_type;

        /**
         * @var string
         * @since 1.0.0
         */
        public $_option_show_image_type;

        /**
     * @var string
     * @since 1.0.0
     */
        public $_option_show_description_option;

        /**
         * @var string
         * @since 1.0.0
         */
        public $_option_show_image_option;

        /**
         * @var string
         * @since 1.0.0
         */
        public $_option_icon_description_option_url;

        /**
         * @var string
         * @since 1.0.0
         */
        public $_option_upload_folder_name;

        /**
         * @var string
         * @since 1.0.0
         */
        public $_option_upload_allowed_type;

        /**
         * @var string
         * @since 1.0.0
         */
        public $_option_loop_add_to_cart_text;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct( $version ) {

            $this->version = $version;

            //Settings

            $this->_option_show_label_type = get_option( 'yith_wapo_settings_showlabeltype' , 'yes' );
            $this->_option_show_description_type = get_option( 'yith_wapo_settings_showdescrtype' , 'yes' );
            $this->_option_show_image_type = get_option( 'yith_wapo_settings_showimagetype' , 'yes' );
            $this->_option_show_description_option = get_option( 'yith_wapo_settings_showdescropt' , 'yes' );
            $this->_option_show_image_option = get_option( 'yith_wapo_settings_showimageopt' , 'yes' );
            $this->_option_icon_description_option_url = get_option( 'yith_wapo_settings_tooltip_icon' , YITH_WAPO_ASSETS_URL . '/img/description-icon.png' );
            $this->_option_upload_folder_name = get_option( 'yith_wapo_settings_uploadfolder' , 'yith_advanced_product_options' );

            $this->_option_upload_allowed_type = get_option( 'yith_wapo_settings_filetypes' , '' );
            if( !empty( $this->_option_upload_allowed_type ) ) {
                $this->_option_upload_allowed_type = explode( ',' , $this->_option_upload_allowed_type );

                if( is_array( $this->_option_upload_allowed_type ) ) {

                    foreach( $this->_option_upload_allowed_type as &$extension ) {
                        $extension = trim( $extension , ' ' );
                    }

                }

            }

            $this->_option_loop_add_to_cart_text = get_option( 'yith_wapo_settings_addtocartlabel' ,__( 'Select options' , 'yith-woocommerce-product-add-ons' ) );

            //Actions

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) , 999);

            // Show product addons

            $form_position = get_option( 'yith_wapo_settings_formposition' , 'before' );

            add_action( 'woocommerce_'.$form_position.'_add_to_cart_button', array( $this, 'show_product_options' ) );

            add_action( 'wc_ajax_yith_wapo_update_variation_price', array( $this, 'yith_wapo_update_variation_price' ) );

            // Products Loop

            add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );

            add_action( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 1 );

            /* yith theme */
            add_action( 'add_to_cart_text', array( $this, 'add_to_cart_text' ), 99, 1 );

            // cart actions

            // Add item data to the cart

            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 50, 6 );

            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

            // Add to cart
            add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 20, 1 );

            // Get item data to display
            add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

            // Load cart data per page load
            add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 20, 2 );

            // Add meta to order
            add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 2 );

            // order again functionality
            add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 're_add_cart_item_data' ), 10, 3 );

            // remove undo link
            add_action( 'woocommerce_cart_item_restored' , array( $this, 'cart_item_restored' ), 10, 2 );

            // end cart actions

            // YITH WAPO Loaded
            do_action( 'yith_wapo_loaded' );

        }

        /**
         * Enqueue frontend styles and scripts
         *
         * @access public
         * @return void
         * @since  1.0.0
         */
        public  function enqueue_styles_scripts() {


            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            // css

            wp_register_style( 'jquery-ui', YITH_WAPO_ASSETS_URL. '/css/jquery-ui.min.css', false, '1.11.4' );
            wp_enqueue_style( 'jquery-ui' );

            wp_register_style( 'yith_wapo_frontend-colorpicker', YITH_WAPO_ASSETS_URL . '/css/color-picker'.$suffix.'.css' , array( 'yith_wapo_frontend' ), $this->version );
            wp_register_style( 'yith_wapo_frontend', YITH_WAPO_ASSETS_URL . '/css/yith-wapo.css' , false, $this->version );

            wp_enqueue_style( 'yith_wapo_frontend-colorpicker' );
            wp_enqueue_style( 'yith_wapo_frontend' );

            // js

            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-datepicker' );

            wp_register_script( 'yith_wapo_frontend-jquery-ui', YITH_WAPO_ASSETS_URL . '/js/jquery-ui/jquery-ui'. $suffix .'.js', '', '1.11.4', true );
            wp_register_script( 'yith_wapo_frontend-accounting', YITH_WAPO_ASSETS_URL . '/js/accounting'. $suffix .'.js', '', '0.4.2', true );
            wp_register_script( 'yith_wapo_frontend-iris', YITH_WAPO_ASSETS_URL . '/js/iris.min.js', array( 'jquery'), '1.0.0', true );
            wp_register_script( 'yith_wapo_frontend-colorpicker', YITH_WAPO_ASSETS_URL . '/js/color-picker'. $suffix .'.js', array( 'jquery'), '1.0.0', true );
            wp_register_script( 'yith_wapo_frontend', YITH_WAPO_ASSETS_URL . '/js/yith-wapo-frontend'. $suffix .'.js', array( 'jquery', 'wc-add-to-cart-variation' ), $this->version, true );

            wp_enqueue_script( 'yith_wapo_frontend-jquery-ui' );
            wp_enqueue_script( 'yith_wapo_frontend-accounting' );
            wp_enqueue_script( 'yith_wapo_frontend-iris' );
            wp_enqueue_script( 'yith_wapo_frontend-colorpicker' );
            wp_enqueue_script( 'wc-add-to-cart-variation' );
            wp_enqueue_script( 'yith_wapo_frontend' );

            $script_params = array(
                'ajax_url'                                 => admin_url( 'admin-ajax' ).'.php',
                'wc_ajax_url'                              => WC_AJAX::get_endpoint( "%%endpoint%%" ),
                'tooltip'                                  => get_option( 'yith-wapo-enable-tooltip' ) == 'yes',
                'tooltip_pos'                              => get_option( 'yith-wapo-tooltip-position' ),
                'tooltip_ani'                              => get_option( 'yith-wapo-tooltip-animation' ),
                'currency_format_num_decimals' 			=> absint( get_option( 'woocommerce_price_num_decimals' ) ),
                'currency_format_symbol'       			=> get_woocommerce_currency_symbol(),
                'currency_format_decimal_sep'  			=> esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
                'currency_format_thousand_sep' 			=> esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
                'currency_format'              			=> esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
                'do_submit'                                 => true,
            ) ;

            wp_localize_script( 'yith_wapo_frontend', 'yith_wapo_general', $script_params );


            $color_picker_param = array(
                'clear'                                 => __( 'Clear' , 'yith-woocommerce-product-add-ons' ),
                'defaultString'                         => __( 'Default' , 'yith-woocommerce-product-add-ons' ),
                'pick'                                  => __( 'Select color' , 'yith-woocommerce-product-add-ons' ),
                'current'                               => __( 'Current color' , 'yith-woocommerce-product-add-ons' ),
            ) ;

            wp_localize_script( 'yith_wapo_frontend', 'wpColorPickerL10n', $color_picker_param );


            $color      = get_option( 'yith-wapo-tooltip-text-color' );
            $background = get_option( 'yith-wapo-tooltip-background' );

            $inline_css = "
            .ywapo_option_description .yith_wccl_tooltip.bottom span:after {
                border-bottom-color: {$background};
            }
            .ywapo_option_description .yith_wccl_tooltip.top span:after {
                border-top-color: {$background};
            }";

            wp_add_inline_style( 'yith_wapo_frontend', $inline_css );

        }

        /**
         * Show the product advanced options
         *
         * @access public
         * @author Andrea Frascaspata
         * @since  1.0.0
         */
        public function show_product_options() {

            global $product;

              if( is_object($product) && $product->get_id() > 0 ) {

                  $product_type_list = YITH_WAPO::getAllowedProductTypes();

                  $product_type = property_exists( 'WC_Product', 'product_type' ) ? $product->product_type : $product->get_type();
                  if( in_array( $product_type, $product_type_list ) ) {

                      $types_list = YITH_WAPO_Type::getAllowedGroupTypes( $product->get_id() );

                      echo '<div id="yith_wapo_groups_container" class="yith_wapo_groups_container">';

                      wc_get_template( 'yith-wapo-group-container.php', array(
                          'yith_wapo_frontend' => $this,
                          'product'            => $product,
                          'types_list'         => $types_list
                      ), '', YITH_WAPO_TEMPLATE_FRONTEND_PATH );

                      echo '</div>';

                  }
              }

        }

        /**
         * Print the single product options group
         *
         * @access private
         * @author Andrea Frascaspata
         * @since  1.0.0
         */
        public function printSingleGroupType( $product, $single_type ) {

               $single_type = ( array ) $single_type;

                //--- WPML ---
                if( YITH_WAPO::$is_wpml_installed ) {

                    $single_type['label'] = YITH_WAPO_WPML::string_translate( $single_type['label'] );
                    $single_type['description'] = YITH_WAPO_WPML::string_translate( $single_type['description'] );

                }
                //---END WPML---------

                wc_get_template( 'yith-wapo-group-type.php', array(
                    'yith_wapo_frontend' => $this,
                    'product'            => $product,
                    'single_type'          => $single_type,
                ), '', YITH_WAPO_TEMPLATE_FRONTEND_PATH );

        }

        /**
         * Print a single input tag
         *
         * @access public
         * @author Andrea Frascaspata
         * @since  1.0.0
         */
        public function printOptions( $key , $product, $type_id ,$type , $name, $value, $price ,$label='',$image='', $price_type='fixed', $description='', $required=false , $checked=false, $disabled='' , $label_position ='before' , $min = false , $max = false  ){

            // arg type exception

            if ( $type=='text' || $type=='number' || $type=='range' ||  $type=='textarea' ||  $type=='color' ||  $type=='date' ) {
                $value='';
            }


            if ( $type=='radio' || $type=='checkbox' ) {
                $label_position='after';
            }

            //---------------------------------------------


            $price_calculated = $this->get_display_price( $product , $price , $price_type );
            $price_hmtl   = ! empty( $price ) ? sprintf( '<span class="ywapo_label_price"> + %s</span>', wc_price( $price_calculated ) ) : '';

            $image_html = '';
            if( $image ) {
                $image_html = '<img src="'.esc_attr( $image ).'" alt="">';
            }

            $control_id = 'ywapo_ctrl_id_'.$type_id.'_'.$key;
            $required_simbol =  $required ? '<abbr class="required" title="required">*</abbr>' : '' ;
            $span_label   = sprintf( '<label for="%s" class="ywapo_label_tag_position_%s">%s<span class="ywapo_option_label ywapo_label_position_%s">%s</span>%s</label>', $control_id, $label_position , $image_html, $label_position, esc_html( $label ) , $required_simbol );
            $before_label = $label_position == 'before' ? $span_label : '';
            $after_label  = $label_position == 'after' ? $span_label : '';
            $min_html = $min !== false ? 'min="' . esc_attr( $min ) . '"' : '';
            $max_html = $max !== false ? 'max="' . esc_attr( $max ) . '"' : '';

            $max_length = $max !== false ? 'maxlength="' . esc_attr( $max ) . '"' : '';

            $default_args = array(
                'yith_wapo_frontend' => $this,
                'control_id'         => $control_id,
                'product'            => $product,
                'key'                => $key,
                'type_id'            => $type_id,
                'type'               => $type,
                'name'               => $name,
                'value'              => $value,
                'price'              => $price,
                'price_hmtl'         => $price_hmtl,
                'price_type'         => $price_type,
                'price_calculated'   => $price_calculated,
                'label'              => $label ,
                'span_label'         => $span_label,
                'before_label'       => $before_label,
                'after_label'        => $after_label,
                'description'        => $description,
                'required'           => $required,
                'checked'            => $checked,
                'disabled'           => $disabled,
                'label_position'     => $label_position,
                'min_html'           => $min_html,
                'max_html'           => $max_html,
                'max_length'         => $max_length,
            );

            switch ( $type ) {

                default :

                    wc_get_template( 'yith-wapo-input-base.php', $default_args , '', YITH_WAPO_TEMPLATE_FRONTEND_PATH );

            }

        }


        /**
         *@author Andrea Frascaspata
         */
        public function yith_wapo_update_variation_price(){

            if( ! isset( $_REQUEST['variation_id'] ) || ! isset( $_REQUEST['variation_price'] ) || ! isset( $_REQUEST['type_id'] ) || ! isset( $_REQUEST['option_index'] )  ) {
                die();
            }

            $variation_id = intval( $_REQUEST['variation_id'] );

            if( $variation_id > 0 ) {

                $variation_price = floatval( $_REQUEST['variation_price'] );

                $variation = new WC_Product_Variation( $variation_id );

                if( is_object( $variation ) ) {

                    $product = wc_get_product( $variation->post->ID );

                    if( is_object( $product ) ) {

                        $type_id = intval( $_REQUEST['type_id'] );

                        if( $type_id > 0 ) {

                            $single_group_type = YITH_WAPO_Type::getSingleGroupType( $type_id );

                            if( is_array( $single_group_type ) ) $single_group_type = $single_group_type[0];

                            if( is_object( $single_group_type ) ) {

                                $option_index = $_REQUEST['option_index'];

                                if( $option_index >= 0 ) {

                                    $options = $single_group_type->options;
                                    $options = maybe_unserialize( $options );

                                    if( is_array( $options ) ) {

                                        $price = $options['price'][$option_index];
                                        $price_type = $options['type'][$option_index];

                                        $price_calculated = $this->get_display_price( $product, $price , $price_type, true, $variation);

                                        echo $price_calculated;

                                    }
                                }
                            }
                        }
                    }
                }
                die;
            }



            // get product status


            die();

        }


        /**
         * @param $description
         * @return string
         */
        public function getTooltip( $description ){

            if( $description ) {

                $icon_url = !empty( $this->_option_icon_description_option_url ) ? $this->_option_icon_description_option_url : YITH_WAPO_ASSETS_URL.'/img/description-icon.png' ;

                $tooltip = '<div class="ywapo_option_description" data-tooltip="'.esc_attr( $description ).'">';

                $tooltip .= '<span><img src="'.esc_url( $icon_url ).'" alt=""></span>';

                $tooltip .= '</div>';

                return $tooltip;
            }

            return '';
        }

        /**
         * @param string $text
         * @return string|void
         */
        public function add_to_cart_text( $text="" ) {

            global $product, $post;

            if ( is_object( $product )  && ! is_single( $post ) ) {

                $product_type_list = YITH_WAPO::getAllowedProductTypes();

                $product_type = property_exists( 'WC_Product', 'product_type' ) ? $product->product_type : $product->get_type();
                if( in_array( $product_type, $product_type_list ) ) {

                    $types_list = YITH_WAPO_Type::getAllowedGroupTypes( $product->get_id() );

                    if ( !empty( $types_list ) ) {
                        $text = ! empty( $this->_option_loop_add_to_cart_text ) ? $this->_option_loop_add_to_cart_text : __( 'Select options', 'yith-woocommerce-product-add-ons' );
                    }

                }

            }

            return $text;
        }

        /**
         * @param string $url
         * @return false|string
         */
        public function add_to_cart_url( $url="" ) {

            global $product;

            if ( is_object( $product ) && ( ( is_shop() || is_product_category() || is_product_tag() ) ) ) {

                $product_type_list = YITH_WAPO::getAllowedProductTypes();

                $product_type = property_exists( 'WC_Product', 'product_type' ) ? $product->product_type : $product->get_type();
                if( in_array( $product_type, $product_type_list ) ) {

                    $types_list = YITH_WAPO_Type::getAllowedGroupTypes( $product->get_id() );

                    if ( !empty( $types_list ) ) {
                        $url = get_permalink( $product->get_id() );
                    }

                }

            }

            return $url;
        }

        /**
         * @param $passed
         * @param $product_id
         * @param $qty
         * @param string $variation_id
         * @param array $variations
         * @param array $cart_item_data
         * @return bool
         */
        public function add_to_cart_validation( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

            /* disables add_to_cart_button class on shop page */
            if ( is_ajax() ) {

                $product = wc_get_product( $product_id );

                $product_type_list = YITH_WAPO::getAllowedProductTypes();

                $product_type = property_exists( 'WC_Product', 'product_type' ) ? $product->product_type : $product->get_type();
                if( in_array( $product_type, $product_type_list ) ) {

                    $types_list = YITH_WAPO_Type::getAllowedGroupTypes( $product->get_id() );

                    if ( !empty( $types_list ) ) {
                        return false;
                    }

                }

            }

            if ( ! empty( $_FILES ) ) {
                $upload_data = $_FILES;

                foreach( $upload_data as $single_data ) {
                    $passed = YITH_WAPO_Type::checkUploadedFilesError( $this , $single_data );
                    if( !$passed ) {
                        break;
                    }
                }

            }

            return $passed;
        }


        /**
         * @param $cart_item_meta
         * @param $product_id
         * @param null $post_data
         *
         * @author Andrea Frascaspata
         * @return mixed
         * @throws Exception
         */
        public function add_cart_item_data( $cart_item_meta, $product_id, $post_data = null ) {

            if ( is_null( $post_data ) ) {
                $post_data = $_POST;
            }

            $upload_data = array();
            if ( ! empty( $_FILES ) ) {
                $upload_data = $_FILES;
            }

            /* yith bundle fix */

            if( isset( $cart_item_meta['bundled_by'] ) ) {
                return $cart_item_meta;
            }

            $type_list = YITH_WAPO_Type::getAllowedGroupTypes( $product_id );

            if ( empty( $cart_item_meta['yith_wapo_options'] ) ) {
                $cart_item_meta['yith_wapo_options'] = array();
            }

            if ( is_array( $type_list ) && ! empty( $type_list ) ) {

                $product = wc_get_product( $product_id );

                $variation = isset( $post_data['variation_id'] ) ? new WC_Product_Variation( $post_data['variation_id'] ) : null;

                foreach ( $type_list as $single_type ) {

                    $post_name = 'ywapo_'.$single_type->type.'_'.$single_type->id;

                    $value = isset( $post_data[ $post_name ] ) ? $post_data[ $post_name ] : '';

                    $upload_value = isset( $upload_data[ $post_name ] ) ? $upload_data[ $post_name ] : '';

                    if( empty( $value ) && empty( $upload_value ) ) {
                        continue;
                    }
                    else if ( is_array( $value ) ) {
                        $value = array_map( 'stripslashes', $value );
                    } else {
                        $value = stripslashes( $value );
                    }

                    $data = YITH_WAPO_Type::getCartDataByPostValue( $this , $product , $variation, $single_type , $value , $upload_value );

                    if ( is_wp_error( $data ) ) {

                        // Throw exception for add_to_cart to pickup
                        throw new Exception( $data->get_error_message() );

                    } elseif ( $data ) {
                        $cart_item_meta['yith_wapo_options'] = array_merge( $cart_item_meta['yith_wapo_options'], apply_filters( 'yith_wapo_cart_item_data', $data, $single_type, $product_id, $post_data  ) );
                    }
                }
            }

            return $cart_item_meta;
        }


        /**
         * @param $cart_item_data
         * @param $item
         * @param $order
         *
         * @author Andrea Frascaspata
         * @return mixed
         */
        public function re_add_cart_item_data( $cart_item_data, $item, $order ) {

            // Disable validation
            remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 50, 6 );

            $stored_meta_data = null;

            if ( isset( $item['item_meta']['_ywapo_meta_data'][0] ) ) {

                $stored_meta_data = maybe_unserialize( $item['item_meta']['_ywapo_meta_data'][0] );
            }
            else if ( isset( $item['item_meta']['_ywraq_wc_ywapo'][0] ) ) { // order by request a quote

                 $stored_meta_data = maybe_unserialize( $item['item_meta']['_ywraq_wc_ywapo'][0] );

            }

            if( isset( $stored_meta_data ) ) {
                foreach ( $stored_meta_data as $key => $single_data ) {

                    $type_object = new YITH_WAPO_Type( $single_data['type_id'] );

                    if( is_object( $type_object ) ) {

                        $product = wc_get_product( $item['item_meta']['_product_id'][0] );

                        $variation = isset( $item['item_meta']['_variation_id'][0] ) ? new WC_Product_Variation( $item['item_meta']['_variation_id'][0] ) : null;

                        $new_single_data = YITH_WAPO_Type::getCartDataByPostValue( $this, $product , $variation , $type_object, $single_data['original_value'] , array() );

                        $index = isset( $single_data['original_index'] ) ? $single_data['original_index'] : 0;
                        if( isset( $new_single_data[$index] ) ) {
                            $new_single_data = $new_single_data[$index];
                        }

                        if ( empty( $single_data ) || ( $new_single_data['name'] != $single_data['name'] ) ) {
                            unset( $single_data[$key] );
                        }
                        else {
                            $stored_meta_data[$key] = $new_single_data;
                        }

                    }

                }


                $cart_item_meta['yith_wapo_options'] =  apply_filters( 'yith_wapo_re_add_cart_item_data', $stored_meta_data, $item );
            }

            return $cart_item_meta;
        }


        /**
         * @param $cart_item
         *
         * @author Andrea Frascaspata
         * @return mixed
         */
        public function add_cart_item( $cart_item ) {

            // Adjust price if addons are set
            $this->cart_adjust_price( $cart_item );

            return $cart_item;
        }

        /**
         * @param $other_data
         * @param $cart_item
         *
         * @author Andrea Frascaspata
         * @return array
         */
        public function get_item_data( $other_data, $cart_item ) {

            if ( ! empty( $cart_item['yith_wapo_options'] ) ) {

                $base_product = wc_get_product( $cart_item['data']->get_id() );
                $display_price = function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $base_product ) : $base_product->get_display_price();

                $other_data[] = array(
                    'name'    => __( 'Base price' , 'yith-woocommerce-product-add-ons' ) ,
                    'value'   => wc_price( $display_price ),
                );

                foreach ( $cart_item['yith_wapo_options'] as $single_type_options ) {
                    $name = $single_type_options['name'];

                    //aggiungere opzione per mostrare prezzo
                    if ( $single_type_options['price'] >= 0 ) {

                        if( $single_type_options['price'] > 0 ) {
                            $name .= ' ( ' . wc_price( $single_type_options['price'] ) . ' )';
                        }

                        $value = '';
                        if( isset( $single_type_options['uploaded_file'] ) ) {
                            $value = $single_type_options['value'];
                        } else {
                            $value = esc_html( strip_tags( $single_type_options['value'] ) );
                        }

                        $other_data[] = array(
                            'name'    => $name,
                            'value'   => $value,
                        );

                    }
                }
            }

            return $other_data;
        }

        /**
         * @param $cart_item
         * @param $values
         *
         * @author Andrea Frascaspata
         * @return mixed
         */
        public function get_cart_item_from_session( $cart_item, $values ) {

            if ( ! empty( $values['yith_wapo_options'] ) ) {
                $cart_item['yith_wapo_options'] = $values['yith_wapo_options'];
                $cart_item = $this->add_cart_item( $cart_item );
            }
            return $cart_item;
        }

        /**
         * @param $item_id
         *
         * @author Andrea Frascaspata
         * @param $values
         */
        public function order_item_meta( $item_id, $values ) {

            if ( ! empty( $values['yith_wapo_options'] ) ) {

                foreach ( $values['yith_wapo_options'] as $single_type_options ) {

                        if( $single_type_options['price'] >= 0 ) {

                            $name = '<span id="'.$single_type_options['type_id'].'">'.$single_type_options['name'].'</span>';

                            if( $single_type_options['price'] > 0 ) {
                                $name .= ' (' . wc_price( $single_type_options['price'] ) . ')';
                            }

                            wc_add_order_item_meta( $item_id, $name, $single_type_options['value'] );

                        }

                        wc_add_order_item_meta( $item_id, '_ywapo_meta_data',  $values['yith_wapo_options'] );

                 }

            }
        }

        /**
         * @param $cart_item_key
         * @param $cart
         * @author Andrea Frascaspata
         */
        public function cart_item_restored( $cart_item_key, $cart ) {

            if( isset( $cart->cart_contents[$cart_item_key] ) ) {

                $cart_item = $cart->cart_contents[$cart_item_key];

                $this->cart_adjust_price( $cart_item ) ;

            }

        }

        /**
         * @param $cart_item
         * @author Andrea Frascaspata
         */
        public function cart_adjust_price( $cart_item ) {

            // Adjust price if addons are set
            if ( ! empty( $cart_item['yith_wapo_options'] ) && apply_filters( 'yith_wapo_adjust_price', true, $cart_item ) ) {

                if ( method_exists( 'WC_Product', 'set_price' ) ) {

                    $base_product = wc_get_product( $cart_item['product_id'] );
                    $types_total_price = $base_product->get_price();

                    foreach ( $cart_item['yith_wapo_options'] as $single_type_option ) {
                        if ( isset( $single_type_option['price_original'] ) && $single_type_option['price_original'] >= 0) {
                            $types_total_price += $single_type_option['price_original'];
                        }
                    }

                    $cart_item['data']->set_price( $types_total_price );

                } else {

                    $types_total_price = 0;

                    foreach ( $cart_item['yith_wapo_options'] as $single_type_option ) {
                        if ( isset( $single_type_option['price_original'] ) && $single_type_option['price_original'] >= 0) {
                            $types_total_price += $single_type_option['price_original'];
                        }
                    }

                    $cart_item['data']->adjust_price( $types_total_price );

                }
            }

        }

        /**
         * @param $product
         * @param $price
         * @param $price_type
         * @param bool $use_display
         * @return float|int
         */
        public function get_display_price( $product , $price , $price_type , $use_display = true , $variation = null ) {

            // price calculation
            $price_calculated = 0;

            if( $price > 0 ) {

                switch( $price_type ) {
                    case 'fixed':
                        $display_price = function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $product, array( 'price' => $price ) ) : $product->get_display_price( $price );
                        $price_calculated = $use_display ? $display_price : $price;
                        break;
                    case 'percentage':

                        $product_object = isset( $variation ) ? $variation : $product ;

                        $display_price = function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $product_object, array( 'price' => $price ) ) : $product_object->get_display_price();
                        $price_calculated = ( ( $use_display ? $display_price : $product_object->price ) / 100 ) * $price;

                        break;
                }

            }

            return $price_calculated;

        }

        /**
         * @param $param
         * @return mixed
         */
        public function upload_dir( $param ) {

            $path_dir = '/'.$this->_option_upload_folder_name.'/';

            $unique_dir = md5( WC()->session->get_customer_id() );
            $subdir = $path_dir . $unique_dir;

            if ( empty( $param['subdir'] ) ) {
                $param['path']   = $param['path'] . $subdir;
                $param['url']    = $param['url']. $subdir;
                $param['subdir'] = $subdir;
            } else {
                $param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
                $param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
                $param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
            }
            return $param;

            return ;

        }

        public function checkConditionalOptions( $depend ) {

            global $wpdb;

            $options_list = explode( ',' , $depend );

            foreach ( $options_list as $key => $option_id ) {

                $option_data = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}yith_wapo_types WHERE id='$option_id' and del=0" );

                if( ! isset( $option_data ) ) {
                    unset( $options_list[ $key ] );
                }

            }
            
            return implode( ',' , $options_list );

        }



    }

}
