<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WAPO' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WAPO_Admin' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WAPO_Admin {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version;

        /* @var YIT_Plugin_Panel_WooCommerce */
        protected $_panel;

        /**
         * @var string Main Panel Option
         */
        protected $_main_panel_option;

        /**
         * @var $_premium string Premium tab template file name
         */
        protected $_premium = 'premium.php';

        /**
         * @var string The panel page
         */
        protected $_panel_page = 'yith_wapo_panel';

        /**
         * @var string Official plugin documentation
         */
        protected $_official_documentation = 'http://yithemes.com/docs-plugins/yith-woocommerce-product-add-ons';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-product-add-ons';

        /**
         * @var string Official live demo
         */
        protected $_premium_live = 'http://plugins.yithemes.com/yith-woocommerce-product-add-ons';


        /**
         * Constructor
         *
         * @access public
         * @since 1.0.0
         */
        public function __construct( $version ) {

            $this->version = $version;

            //Actions
            add_action( 'init', array( $this, 'init' ) );

            // Admin Menu
            add_filter( 'ywapo_edit_advanced_product_options_capability' , array( $this, 'ywapo_get_capability' ) );
            add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5) ;

            // Admin Init
            add_action( 'admin_init', array( $this, 'items_update' ), 9 );

            // WooCommerce Product Data Tab
            add_action( 'admin_init', array( $this, 'add_wc_product_data_tab' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'woo_add_custom_general_fields_save' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

            /* Plugin Informations */
           // add_filter( 'plugin_action_links_' . plugin_basename( YITH_WAPO_DIR . '/' . basename( YITH_WAPO_FILE ) ), array( $this, 'action_links' ) );
            //add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

            // YITH WAPO Loaded
            do_action( 'yith_wapo_loaded' );
        }


        /**
         * Init method:
         *  - default options
         *
         * @access public
         * @since 1.0.0
         */
        public function init() { }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = array(
                'general'       => __( 'General', 'yith-woocommerce-product-add-ons' ),
            );

            $admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-product-add-ons' );

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => __( 'Product Add-Ons', 'yith-woocommerce-product-add-ons' ),
                'menu_title'       => __( 'Product Add-Ons', 'yith-woocommerce-product-add-ons' ),
                'capability'       => 'manage_options',
                'parent'           => '',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'links'            => $this->get_panel_sidebar_links(),
                'admin-tabs'       => apply_filters( 'yith-wapo-admin-tabs', $admin_tabs ),
                'options-path'     => YITH_WAPO_DIR . '/plugin-options'
            );

            /* === Fixed: not updated theme  === */
            if( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( YITH_WAPO_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

            add_action( 'woocommerce_admin_field_yith_wapo_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );

            add_action( 'ywapo_premium_tab', array( $this, 'premium_tab' ) );

        }

        public function premium_tab() {
            $premium_tab_template = YITH_WAPO_TEMPLATE_ADMIN_PATH  . $this->_premium;
            if ( file_exists( $premium_tab_template ) ) {
                include_once( $premium_tab_template );
            }
        }
        

        /**
            * @return array
         */
        public function get_panel_sidebar_links() {
            return array(
                array(
                    'url' => $this->_official_documentation,
                    'title' => __( 'Plugin documentation' , 'yith-woocommerce-product-add-ons' ),
                ),
                array(
                    'url' => 'https://yithemes.com/my-account/support/dashboard',
                    'title' => __( 'Support platform' , 'yith-woocommerce-product-add-ons' ),
                ),
                array(
                    'url' => $this->_official_documentation.'/changelog',
                    'title' => 'Changelog ( '.YITH_WAPO_VERSION.' )',
                )
            );
        }

        /**
         * @author Andre Frascaspata
         * @param $capability
         * @return string
         */
        public function ywapo_get_capability( $capability ) {

            if( YITH_WAPO::$is_vendor_installed ) {

                $vendor = yith_get_vendor('current', 'user');

                if( $vendor->is_valid() && $vendor->has_limited_access() && YITH_WAPO::is_plugin_enabled_for_vendors() ) {
                    $capability = YITH_Vendors()->admin->get_special_cap();
                }

            }

            return $capability;

        }

        /**
         * Admin menu
         *
         * @access public
         * @since 1.0.0
         */
        public function admin_menu() {

            $capability = apply_filters( 'ywapo_edit_advanced_product_options_capability' , 'manage_woocommerce' );

            $page = add_submenu_page(
                'edit.php?post_type=product',
                __( 'Product Add-Ons', 'yith-woocommerce-product-add-ons' ),
                __( 'Product Add-Ons', 'yith-woocommerce-product-add-ons' ),
                $capability,
                'yith_wapo_groups',
                array( $this, 'yith_wapo_groups' )
            );
            $page = add_submenu_page(
                null,
                __( 'Product options group', 'yith-woocommerce-product-add-ons' ),
                __( 'Product options group', 'yith-woocommerce-product-add-ons' ),
                $capability,
                'yith_wapo_group',
                array( $this, 'yith_wapo_group' )
            );
        }

        /**
         * WAPO Admin
         *
         * @access public
         * @since 1.0.0
         */
        function yith_wapo_groups() { require plugin_dir_path( __FILE__ ) . '../templates/yith_wapo/admin/yith-wapo-groups.php'; }
        function yith_wapo_group() { require plugin_dir_path( __FILE__ ) . '../templates/yith_wapo/admin/yith-wapo-group.php'; }

        /**
         * Items update
         *
         * @access public
         * @since 1.0.0
         */
        public function items_update() {

            global $wpdb;

            $id = isset( $_POST['id'] ) ? $_POST['id'] : '';
            $group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : '';
            $act = isset( $_POST['act'] ) ? $_POST['act'] : '';
            $class = isset( $_POST['class'] ) ? $_POST['class'] : '';

            if ( class_exists( $class ) ) {

                $object = new $class( $id );

                if ( $act == 'new' ) {
                    $object->insert();
                    $id = $class == 'YITH_WAPO_Group' ? $wpdb->insert_id : $group_id;
                } else if ( $act == 'update' ) {
                    $object->update( $id );
                    if ( isset($_POST['types-order']) && $_POST['types-order'] != '' ){ YITH_WAPO_Type::update_priorities( $_POST['types-order'] ); }
                    $id = $class == 'YITH_WAPO_Group' ? $id : $object->group_id;
                }
                
                if ( $class == 'YITH_WAPO_Group' ) { $object = new YITH_WAPO_Group( $id ); }
                $redirect_url = $id > 0 && $object->del != 1 ? 'edit.php?post_type=product&page=yith_wapo_group&id=' . $id : 'edit.php?post_type=product&page=yith_wapo_groups';

                wp_redirect( $redirect_url );
                exit;

            }

        }

        /**
         * Enqueue admin styles and scripts
         *
         * @access public
         * @return void
         * @since 1.0.0
         */
        public function enqueue_styles_scripts() {
            global $pagenow, $woocommerce;

            /*
             *  Js
             */

            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui', YITH_WAPO_URL . 'assets/js/jquery-ui/jquery-ui.min.js' );
            // wp_enqueue_script( 'jquery-ui-core' );
            // wp_enqueue_script( 'jquery-ui-datepicker' );
            // wp_enqueue_script( 'jquery-ui-sortable' );

            if ( version_compare( WC()->version, '3.0', '<' ) ) {
            
                // select2
                wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.min.js', array( 'jquery' ) );
                wp_enqueue_script( 'select2' );

                wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select.min.js', array( 'jquery', 'select2' ) );
                wp_enqueue_script( 'wc-enhanced-select' );

                wp_register_script( 'wc-tooltip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery', 'select2' ) );
                wp_enqueue_script( 'wc-tooltip' );

            } else {

                // selectWoo
                wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select.min.js', array( 'jquery', 'select2', 'selectWoo' ) );
                wp_enqueue_script( 'wc-enhanced-select');

                wp_register_script( 'wc-tooltip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery', 'select2', 'selectWoo' ) );
                wp_enqueue_script( 'wc-tooltip' );

            }

            /*
             *  Css
             */

            wp_enqueue_style( 'jquery-ui' );
            wp_enqueue_style( 'bootstrap-css' );
            wp_enqueue_style( 'font-awesome' );
            wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css' );
            wp_enqueue_style( 'wapo-admin', YITH_WAPO_URL . 'assets/css/yith-wapo-admin.css' );

        }

        function add_wc_product_data_tab() {

            $current_vendor = YITH_WAPO::get_current_multivendor();
            if( isset( $current_vendor ) && is_object( $current_vendor ) && $current_vendor->has_limited_access() && ! YITH_WAPO::is_plugin_enabled_for_vendors() ) {
                return;
            }

            add_filter( 'woocommerce_product_data_tabs', 'wapo_product_data_tab' );
            function wapo_product_data_tab( $product_data_tabs ) {
                $product_data_tabs['wapo-product-options'] = array(
                    'label' => __( 'Product Add-Ons', 'yith-woocommerce-product-add-ons' ),
                    'target' => 'my_custom_product_data',
                );
                return $product_data_tabs;
            }

            add_action( 'woocommerce_product_data_panels', 'wapo_product_data_fields' );
            function wapo_product_data_fields() {
                global $woocommerce, $post, $wpdb; ?>

                <div id="my_custom_product_data" class="panel woocommerce_options_panel">

                    <div class="options_group hide_if_grouped wapo-plugin" style="padding: 10px;">

                        <div style="margin-bottom: 10px;">
                            <label>Name</label>
                            <input type="text" name="wapo-group-name" id="wapo-group-name" placeholder="Group name" style="width: 200px;">
                            <!--<span class="button button-primary wapo-add-group">Add Group</span>-->
                            <input type="button" class="button button-primary wapo-add-group" value="Add Group">
                        </div>
                        
                        <ul id="sortable-list" class="sortable">
                        
                            <?php

                            $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}yith_wapo_groups WHERE FIND_IN_SET( {$post->ID} , products_id ) AND del='0' ORDER BY visibility DESC, priority ASC" );

                            foreach ( $rows as $key => $value ) :

                                $visibility = '';
                                switch (  $value->visibility ) {
                                    case 0: $visibility = 'hidden group.'; break;
                                    case 1: $visibility = 'private, visible to administrators only.'; break;
                                    case 9: $visibility = 'public, visible to everyone.'; break;
                                    default: $visibility = 'hidden group.'; break;
                                }

                            ?>

                                <li class="group-row">
                                    <span class="dashicons dashicons-exerpt-view" style="margin: 5px 5px 0px 0px;"></span>
                                    <strong class="wapo-group-edit">Group "<?php echo $value->name; ?>"</strong> - <i><?php echo $visibility; ?></i>
                                    <a href="edit.php?post_type=product&page=yith_wapo_group&id=<?php echo $value->id; ?>&KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox button manage" target="_blank"><?php echo __( 'Manage', 'yith-woocommerce-product-add-ons' ); ?> &raquo;</a>
                                </li>

                            <?php endforeach; ?>

                        </ul>

                    </div>

                    <div class="options_group hide_if_grouped">

                        <?php
                        woocommerce_wp_checkbox(
                            array( 
                                'id'            => '_wapo_disable_global', 
                                'wrapper_class' => 'wapo-disable-global', 
                                'label'         => __( 'Disable globals', 'yith-woocommerce-product-add-ons' ),
                                'description'   => __( 'Check this box if you want to disable global groups and use the above ones only!',
                                    'yith-woocommerce-product-add-ons' ),
                                'default'       => '0',
                                'desc_tip'      => false,
                            )
                        );
                        ?>
                    </div>

                    <div class="options_group hide_if_grouped">
                        
                        <p><a href="edit.php?post_type=product&page=yith_wapo_groups&KeepThis=true&TB_iframe=true&modal=false" onclick="return false;" class="thickbox button button-primary"><?php echo __( 'Manage all groups', 'yith-woocommerce-product-add-ons' ); ?> &raquo;</a></p>

                    </div>

                    <!--<a href="edit.php?post_type=product&page=yith_wapo_groups&KeepThis=true&TB_iframe=true&modal=false" class="thickbox button">POPUP</a>-->

                </div>

                <?php
            }

            add_action( 'admin_footer', 'yith_wapo_my_action_javascript' );
            function yith_wapo_my_action_javascript() {
                global $post; ?>
                <script type="text/javascript" >
                    jQuery(document).ready(function($) {
                        jQuery('.wapo-add-group').click( function(){
                            var data = {
                                'action': 'wapo_save_group',
                                'group_name': jQuery('#wapo-group-name').val(),
                                'post_id': <?php echo isset( $post->ID ) ? $post->ID : 0; ?>
                            };
                            jQuery.post(ajaxurl, data, function(response) {
                                if ( response == '::no_name' ) { alert( 'NO NAME' ); }
                                else if ( response == '::db_error' ) { alert( 'DB ERROR' ); }
                                else {

                                    response = response.split(',');
                                    var group_name = response[0];
                                    var post_id = response[1];

                                    var new_row = '<li class="group-row"><span class="dashicons dashicons-exerpt-view" style="margin: 5px 5px 0px 0px;"></span><strong class="wapo-group-edit">Group "' + group_name + '</strong>" - <i>hidden group.</i>';
                                    new_row += '<a href="edit.php?post_type=product&page=yith_wapo_group&id=' + post_id + '&KeepThis=true&TB_iframe=true&modal=false" class="thickbox button manage" target="_blank"> <?php echo __( 'Manage', 'yith-woocommerce-product-add-ons' ); ?> &raquo;</a></li>';

                                    jQuery('.wapo-plugin #sortable-list').prepend( new_row );
                                    jQuery('#wapo-group-name').val('');

                                }
                            });
                        });
                    });
                </script><?php
            }
            add_action( 'wp_ajax_wapo_save_group', 'wapo_save_group_callback' );
            function wapo_save_group_callback() {
                global $wpdb;
                if ( isset( $_POST['group_name'] ) && $_POST['group_name'] != '' ) {
                    $group_name = $_POST['group_name'];
                    $post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
                    $result = $wpdb->query("INSERT INTO {$wpdb->prefix}yith_wapo_groups (id,name,products_id,priority,visibility,reg_date,del) VALUES ('','$group_name','$post_id',99,0,CURRENT_TIMESTAMP,'0')");
                    echo $result ? $group_name . ',' . $wpdb->insert_id : '::db_error';
                } else { echo '::no_name'; }
                wp_die();
            }

        }

        function woo_add_custom_general_fields_save( $post_id ){
            
            // Checkbox
            $woocommerce_checkbox = isset( $_POST['_wapo_disable_global'] ) ? 'yes' : 'no';
            update_post_meta( $post_id, '_wapo_disable_global', $woocommerce_checkbox );
                        
        }

        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri(){
            return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing .'?refer_id=1030585';
        }
    }

}
