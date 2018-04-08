<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WooCommerce_Additional_Uploads' ) ) {

	/**
	 *
	 * @class   YITH_WooCommerce_Additional_Uploads
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_WooCommerce_Additional_Uploads {

		/**
		 * Max file size allowed in mega bytes
		 *
		 * @var int
		 */
		public $max_size = 0;

		/**
		 * Allowed extension
		 *
		 * @var string comma separated list of extension allowed
		 */
		public $allowed_extension = '';

		/**
		 * Allow customers to upload files on cart page.
		 *
		 * @var bool
		 */
		public $allow_on_cart_page = false;

		/**
		 * Order status on which allow file uploads
		 *
		 * @var array
		 */
		public $allowed_status = array();

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

			$this->init_plugin_settings();

			/**
			 * Do some stuff on plugin init
			 */
			add_action( 'init', array( $this, 'on_plugin_init' ) );

			/** Add styles and scripts */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles_scripts' ) );

			/**
			 * Add metabox to question and answer post type
			 */
			add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );

			add_action( 'woocommerce_thankyou_order_received_text', array(
				$this,
				'show_upload_section_on_thankyoupage',
			), 10, 2 );

			add_action( 'woocommerce_view_order', array( $this, 'show_upload_section_on_view_page' ), 1 );

			add_action( 'yith_woocommerce_additional_uploads_order_file_uploaded', array(
				$this,
				'order_file_uploaded',
			) );

			/**
			 * Add  button on frontend my-orders page
			 */
			add_action( 'woocommerce_my_account_my_orders_actions', array(
				$this,
				'print_upload_file_button',
			), 10, 2 );

			/**
			 * Download uploaded file from order page metabox
			 */
			add_action( "admin_action_" . YWAU_ACTION_DOWNLOAD_FILE, array( $this, 'download_order_uploaded_file' ) );


			add_action( 'woocommerce_before_checkout_form', array( $this, 'show_upload_section_on_checkout' ) );

			if ( $this->allow_on_cart_page ) {
				add_action( "woocommerce_before_cart", array( $this, 'show_upload_section_on_cart_page' ) );
				add_action( 'woocommerce_checkout_order_processed', array(
					$this,
					'attach_file_from_cart_to_order',
				), 10, 2 );
			}
		}

		/**
		 * Check if there is a file attached to cart and attach it to relative order
		 *
		 * @param $order_id
		 * @param $posted
		 */
		public function attach_file_from_cart_to_order( $order_id, $posted ) {
			if ( $this->allow_on_cart_page ) {

				$uploaded_file = WC()->session->get( "ywau_order_file_uploaded" );
				if ( $uploaded_file != null ) {
					//  change file folder, using the real order id
					$relative_path = sprintf( "%s/%s", $order_id, basename( $uploaded_file ) );

					$starting_path = sprintf( "%s/%s",
						YITH_YWAU_SAVE_DIR,
						untrailingslashit( $uploaded_file ) );

					$destination_path = sprintf( "%s/%s",
						YITH_YWAU_SAVE_DIR,
						untrailingslashit( $relative_path ) );

					$new_dir = sprintf( "%s/%s",
						YITH_YWAU_SAVE_DIR,
						$order_id );

					wp_mkdir_p( $new_dir );

					//  move file to new folder
					rename( $starting_path, $destination_path );

					update_post_meta( $order_id, YWAU_METAKEY_ORDER_FILE_UPLOADED, $relative_path );

					WC()->session->__unset( "ywau_order_file_uploaded" );
				}
			}
		}

		/**
		 * Show the upload section on cart page
		 */
		public function show_upload_section_on_cart_page() {
			if ( $this->allow_on_cart_page ) {
				$this->show_upload_section( 0 );
			}
		}

		public function show_upload_section_on_checkout() {

			$this->show_upload_section( 0 );
		}

		/**
		 * Download uploaded file from order page metabox
		 */
		public function download_order_uploaded_file() {
			if ( ! isset( $_GET["order_id"] ) ) {
				return;
			}

			$order_id  = $_GET["order_id"];
			$file_path = $this->order_has_file_uploaded( $order_id );
			$full_path = YITH_YWAU_SAVE_DIR . $file_path;

			if ( ! empty( $full_path ) ) {
				header( 'Content-type: application/octet-stream' );
				header( 'Content-Disposition: attachment; filename = "' . basename( $full_path ) . '"' );
				@readfile( $full_path );
				exit();
			}
		}

		/**
		 * show upload file button on my-orders page
		 *
		 * @param array    $actions
		 * @param WC_Order $order
		 */
		public function print_upload_file_button( $actions, $order ) {

			if ( $this->can_upload_file_on_order( $order ) && ( ! $this->order_has_file_uploaded( yit_get_prop( $order, 'id' ) ) ) ) {

				$actions['upload-file'] = array(
					'url'  => $order->get_view_order_url(),
					'name' => __( 'Upload file', 'yith-woocommerce-additional-uploads' ),
				);
			}

			return $actions;
		}

		public function on_plugin_init() {

			if ( isset( $_POST["upload-file"] ) && isset( $_POST["submit-files"] ) ) {
				do_action( "yith_woocommerce_additional_uploads_order_file_uploaded" );
			}
		}

		/**
		 * Push data to $_POST vars
		 *
		 * @param $key
		 * @param $message
		 */
		private function set_upload_status( $status, $message ) {
			$_POST["upload-status"]  = $status;
			$_POST["upload-message"] = $message;
		}

		/**
		 * Upload the customer file and link it to the current order
		 */
		public function order_file_uploaded() {
			$order_id = intval( $_POST["order-id"] );

			if ( $this->order_has_file_uploaded( $order_id ) ) {
				$this->set_upload_status( "rejected", sprintf( __( "You have already sent a file for the current order, it is not possible to add the new file %s.", 'yith-woocommerce-additional-uploads' ), $_FILES['uploadFile']['name'][0] ) );

				//  order has current file uploaded, you can't add a new file.

				return;
			}

			if ( ! $_FILES['uploadFile']['name'][0] ) {
				$this->set_upload_status( "failed", sprintf( __( "The name of the file %s has not been accepted.", 'yith-woocommerce-additional-uploads' ), $_FILES['uploadFile']['name'][0] ) );

				//  No file name provided, file rejected
				return;
			}

			if ( $_FILES['uploadFile']['error'][0] ) {
				$this->set_upload_status( "failed", sprintf( __( "The following error happened during the upload of %s: %s", 'yith-woocommerce-additional-uploads' ), $_FILES['uploadFile']['name'][0], $_FILES['uploadFile']['error'][0] ) );

				//  there was an error
				return;
			}


			//now is the time to modify the future file name and validate the file
			$file_name = sanitize_file_name( strtolower( $_FILES['uploadFile']['name'][0] ) ); //rename file

			//  Check if file extension is allowed
			$allowed_ext_array = explode( ',', $this->allowed_extension );

			$check    = wp_check_filetype( $file_name );
			$file_ext = '';
			if ( ! empty( $check['ext'] ) ) {
				$file_ext = $check['ext'];
			}


			if ( ( ! empty( $file_ext ) ) && ( ! empty( $this->allowed_extension ) ) && ( count( $allowed_ext_array ) > 0 ) && ( ! in_array( $file_ext, $allowed_ext_array ) ) ) {

				//  File extension not allowed
				$this->set_upload_status( "failed", sprintf( __( "The format of the file %s is not valid. The allowed extensions are: %s.", 'yith-woocommerce-additional-uploads' ),
					$_FILES['uploadFile']['name'][0],
					$this->allowed_extension ) );

				return;
			}

			$max_size_byte = 1048576 * $this->max_size; //  max size in bytes

			if ( $this->max_size && ( $_FILES['uploadFile']['size'][0] > $max_size_byte ) ) {
				$this->set_upload_status( "failed", sprintf( __( "The file %s has not been accepted, the maximum dimension is %s MB.", 'yith-woocommerce-additional-uploads' ), $_FILES['uploadFile']['name'][0], $this->max_size ) );

				//  File size not allowed
				return;
			}


			//  Put the files on a folder reserved to current order
			$order_dir = sprintf( "%s/%s", YITH_YWAU_SAVE_DIR, $order_id );
			if ( ! file_exists( $order_dir ) ) {
				wp_mkdir_p( $order_dir );
			}

			$upload_file_path = sprintf( "%s/%s", $order_dir, $file_name );

			if ( move_uploaded_file( $_FILES['uploadFile']['tmp_name'][0], $upload_file_path ) ) {
				$relative_path = sprintf( "%s/%s", $order_id, $file_name );

				if ( $order_id ) {
					update_post_meta( $order_id, YWAU_METAKEY_ORDER_FILE_UPLOADED, $relative_path );
				} else {
					//  store reference to uploaded item on cart
					WC()->session->set( "ywau_order_file_uploaded", $relative_path );
				}
				$this->set_upload_status( "success", sprintf( __( "The file %s has been included in the current order. Your order is now being processed.", 'yith-woocommerce-additional-uploads' ), $_FILES['uploadFile']['name'][0] ) );
			}
		}

		/**
		 * Init plugin settings
		 */
		public function init_plugin_settings() {
			$this->allowed_extension  = get_option( 'ywau_allowed_extension', '' );
			$this->allow_on_cart_page = ( "yes" === get_option( 'ywau_allow_upload_on_cart', 'no' ) ) ? true : false;

			$this->max_size = get_option( "ywau_max_file_size", 0 );

			if ( "yes" == get_option( "ywau_allow_wc-completed", "no" ) ) {
				$this->allowed_status[] = 'wc-completed';
			}

			if ( "yes" == get_option( "ywau_allow_wc-on-hold", "no" ) ) {
				$this->allowed_status[] = 'wc-on-hold';
			}

			if ( "yes" == get_option( "ywau_allow_wc-pending", "no" ) ) {
				$this->allowed_status[] = 'wc-pending';
			}

			if ( "yes" == get_option( "ywau_allow_wc-processing", "no" ) ) {
				$this->allowed_status[] = 'wc-processing';
			}
		}

		/**
		 * Add metaboxes for plugin features
		 */
		function add_metaboxes() {

			//  Add metabox on order page
			add_meta_box( 'ywau_order_metabox', 'YITH Additional Uploads', array(
				$this,
				'display_order_metabox',
			), 'shop_order', 'side', 'high' );
		}

		public function display_order_metabox() {

			if ( ! isset( $_GET["post"] ) ) {
				return;
			}

			$order_id = intval( $_GET["post"] );

			echo '<div id="ywau_uploaded_file">';

			if ( $this->order_has_file_uploaded( $order_id ) ) {
				echo '<span class="file-uploaded">' . __( "The customer has sent a file.", 'yith-woocommerce-additional-uploads' ) . '</span>';
				echo '<a class="download-uploaded-file" href="' . admin_url( "admin.php?action=" . YWAU_ACTION_DOWNLOAD_FILE . "&order_id=$order_id" ) . '">' . __( "Download", 'yith-woocommerce-additional-uploads' ) . '</a>';

			} else {
				echo '<span class="file-not-uploaded">' . __( "There are no files attached to the order.", 'yith-woocommerce-additional-uploads' ) . '</span>';
			}
			echo "</div>";

		}

		/**
		 * Add frontend style
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_styles_scripts() {

			//  register and enqueue ajax calls related script file
			wp_register_script( "ywau-frontend", YITH_YWAU_ASSETS_URL . '/js/ywau-frontend.js', array( 'jquery' ) );
			wp_enqueue_script( "ywau-frontend" );

			wp_enqueue_style( 'ywau-frontend', YITH_YWAU_ASSETS_URL . '/css/ywau-frontend.css' );
		}

		/**
		 * Enqueue scripts on administration comment page
		 *
		 * @param $hook
		 */
		function admin_enqueue_styles_scripts( $hook ) {

			/**
			 * Add styles
			 */
			wp_enqueue_style( 'ywau-backend', YITH_YWAU_ASSETS_URL . '/css/ywau-backend.css' );

			/**
			 * Add scripts
			 */
			wp_register_script( "ywau-backend", YITH_YWAU_URL . 'assets/js/ywau-backend.js', array(
				'jquery',
				'jquery-blockui',
			) );

			wp_localize_script( 'ywau-backend', 'ywau', array(
				'loader'   => apply_filters( 'yith_question_answer_loader_gif', YITH_YWAU_ASSETS_URL . '/images/loading.gif' ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			) );

			wp_enqueue_script( "ywau-backend" );
		}

		/**
		 * check if current order is eligible for file uploading
		 *
		 * @param WC_Order $order current order
		 *
		 * @return bool
		 */
		public function can_upload_file_on_order( $order ) {
			return ( ! is_null( $order ) && in_array( 'wc-' . $order->get_status(), $this->allowed_status ) );
		}

		/**
		 * show upload section on thankyou page  if the plugin allow it based on current order status
		 *
		 * @param WC_Order $order current order
		 */
		public function show_upload_section_on_thankyoupage( $text, $order ) {

			if ( ! $this->can_upload_file_on_order( $order ) ) {
				return $text;
			}


			$this->show_upload_section( yit_get_prop( $order, 'id' ) );
		}

		/**
		 *
		 * @param $order_id
		 *
		 * @return mixed
		 */
		public function order_has_file_uploaded( $order_id ) {
			if ( $order_id ) {
				return get_post_meta( $order_id, YWAU_METAKEY_ORDER_FILE_UPLOADED, true );
			} else {
				return WC()->session->get( "ywau_order_file_uploaded" );
			}
		}

		/**
		 * Build HTML block for uploads feature
		 *
		 * @param int $order_id order id to whose files are attached. If $order_id is 0, files are attached to cart session and attached to the order  after
		 */
		public function show_upload_section( $order_id ) {

			if ( ! get_current_user_id() ) {
				return;
			}

			echo '<div class="upload-file-section">';

			$allow_upload = true;

			//  Check if there was a previous POST and a file was uploaded
			if ( isset( $_POST["upload-status"] ) ) {
				echo '<div class="additional-uploads-message">';

				switch ( $_POST["upload-status"] ) {

					case "rejected" :
						$allow_upload = false;
						echo '<span class="error-message">' . esc_html( $_POST["upload-message"] ) . '</span>';

						break;

					case "failed" :
						if ( $_POST["upload-message"] ) {

							echo '<span class="error-message">' . esc_html( $_POST["upload-message"] ) . '</span>';
						} else {
							echo '<span class="error-message">' . __( "An error occurred, the file has not been accepted.", 'yith-woocommerce-additional-uploads' ) . '</span>';
						}

						break;

					case "success" :
						$file_path = $this->order_has_file_uploaded( $order_id );
						if ( ! empty( $file_path ) ) {
							$allow_upload = false;
							echo '<span class="success-message">' . esc_html( $_POST["upload-message"] ) . '</span>';
							break;
						}
						echo '</div>';
				}
			} else {
				//  Check if current order has a file attached
				$uploaded_file = $this->order_has_file_uploaded( $order_id );
				if ( ! empty( $uploaded_file ) ) {
					$allow_upload = false;
					echo '<span class="success-message">' . sprintf( __( "The file %s has been included in the current order. Your order is now being processed.", 'yith-woocommerce-additional-uploads' ), basename( $uploaded_file ) ) . '</span>';
				}
			}

			if ( ! $allow_upload ) {
				return;
			}

            do_action('ywau_show_section');
			?>
            <span class="upload-file-title"><?php _e( "You can customize your order sending a file.", 'yith-woocommerce-additional-uploads' ); ?>
				<?php if ( $this->allowed_extension ):
					printf( __( "Choose one of the following formats: %s.", 'yith-woocommerce-additional-uploads' ), $this->allowed_extension );
				endif; ?>
				</span>

            <form enctype="multipart/form-data" action="" method="POST">
                <input type="hidden" name="upload-file" value="file-to-upload">
                <input type="hidden" name="order-id" value="<?php echo $order_id; ?>">

                <div class="upload-items">
                    <input type="button" value="<?php _e( "Select file", 'yith-woocommerce-additional-uploads' ); ?>"
                           id="do_uploadFile" />
                    <input type="file" name="uploadFile[]" id="uploadFile" accept="image/*" />
                </div>

                <div id="uploadFileList"></div>

            </form>

			<?php
			echo '</div>';
		}

		public function show_upload_section_on_view_page( $order_id ) {

			$this->show_upload_section( $order_id );
		}
	}
}