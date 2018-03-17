<?php

/**
 * Base class to add row and bulk actions to posts, media and terms list
 * Bulk actions management is inspired by http://www.foxrunsoftware.net/articles/wordpress/add-custom-bulk-action/
 *
 * @since 0.2
 */
abstract class Lingotek_Actions {
	/**
	 * Polylang model.
	 *
	 * @var obj
	 */
	public $pllm;
	/**
	 * Lingotek model.
	 *
	 * @var object
	 */
	public $lgtm;
	/**
	 * Must be defined in child class: 'post' or 'term'.
	 *
	 * @var string
	 */
	public $type;
	/**
	 * Actions
	 *
	 * @var array
	 */
	public static $actions;
	/**
	 * Icons
	 *
	 * @var list
	 */
	public static $icons;
	/**
	 * Confirm_message
	 *
	 * @var string
	 */
	public static $confirm_message;

	/**
	 * Constructor
	 *
	 * @since 0.2
	 */
	public function __construct( $type ) {
		// confirm message.
		self::$confirm_message = sprintf( ' onclick = "return confirm(\'%s\');"', __( 'You are about to overwrite existing translations. Are you sure?', 'lingotek-translation' ) );

		// row actions.
		self::$actions = array(
			'upload'   => array(
				'action'      => __( 'Upload to Lingotek', 'lingotek-translation' ),
				'progress'    => __( 'Uploading...', 'lingotek-translation' ),
				'description' => __( 'Upload this item to Lingotek TMS', 'lingotek-translation' ),
			),

			'request'  => array(
				'action'      => __( 'Request translations', 'lingotek-translation' ),
				'progress'    => __( 'Requesting translations...', 'lingotek-translation' ),
				'description' => __( 'Request translations of this item to Lingotek TMS', 'lingotek-translation' ),
			),

			'status'   => array(
				'action'      => __( 'Update translations status', 'lingotek-translation' ),
				'progress'    => __( 'Updating translations status...', 'lingotek-translation' ),
				'description' => __( 'Update translations status of this item in Lingotek TMS', 'lingotek-translation' ),
			),

			'download' => array(
				'action'      => __( 'Download translations', 'lingotek-translation' ),
				'progress'    => __( 'Downloading translations...', 'lingotek-translation' ),
				'description' => __( 'Download translations of this item from Lingotek TMS', 'lingotek-translation' ),
			),

			'delete' => array(
				'action'      => __( 'Disassociate translations', 'lingotek-translation' ),
				'progress'    => __( 'Disassociating translations...', 'lingotek-translation' ),
				'description' => __( 'Disassociate the translations of this item from Lingotek TMS', 'lingotek-translation' ),
			),
		);

		// action icons
		self::$icons = array(
			'upload' => array(
				'title' => __( 'Upload Now', 'lingotek-translation' ),
				'icon'  => 'upload',
			),

			'importing' => array(
				'title' => __( 'Importing source', 'lingotek-translation' ),
				'icon'  => 'clock',
			),

			'uploaded' => array(
				'title' => __( 'Source uploaded', 'lingotek-translation' ),
				'icon'  => 'yes',
			),

			'request' => array(
				'title' => __( 'Request a translation', 'lingotek-translation' ),
				'icon'  => 'plus',
			),

			'pending' => array(
				'title' => __( 'In Progress', 'lingotek-translation' ),
				'icon'  => 'clock',
			),

			'ready' => array(
				'title' => __( 'Ready to download', 'lingotek-translation' ),
				'icon'  => 'download',
			),

			'interim' => array(
				'title' => __('Interim Translation Downloaded', 'lingotek-translation'),
				'icon' => 'edit'
			),

			'current' => array(
				'title' => __( 'Current', 'lingotek-translation' ),
				'icon'  => 'edit',
			),

			'not-current' => array(
				'title' => __( 'The target translation is no longer current as the source content has been updated', 'lingotek-translation' ),
				'icon'  => 'edit',
			),

			'error' => array(
				'title' => __( 'There was an error contacting Lingotek', 'lingotek-translation' ),
				'icon'  => 'warning',
			),

			'copy' => array(
				'title' => __( 'Copy source language', 'lingotek-translation' ),
				'icon'  => 'welcome-add-page',
			),
		);

		$this->type = $type;
		$this->pllm = $GLOBALS['polylang']->model;
		$this->lgtm = $GLOBALS['wp_lingotek']->model;

		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );

		add_action( 'wp_ajax_estimate_cost', array( &$this, 'ajax_estimate_cost' ) );
		add_action( 'wp_ajax_request_professional_translation', array( &$this, 'ajax_request_professional_translation' ) );
		add_action( 'wp_ajax_get_user_payment_information', array( &$this, 'ajax_get_user_payment_information' ) );
		add_action( 'wp_ajax_get_ltk_terms_and_conditions', array( &$this, 'ajax_get_ltk_terms_and_conditions' ) ); 

		foreach ( array_keys( self::$actions ) as $action ) {
			add_action( 'wp_ajax_lingotek_progress_' . $this->type . '_' . $action , array( &$this, 'ajax_' . $action ) );
		}
	}

	/**
	 * Generates a workbench link
	 *
	 * @since 0.1
	 *
	 * @param string $document_id
	 * @param string $locale Lingotek locale
	 * @return string workbench link
	 */
	public static function workbench_link( $document_id, $locale ) {
		$client_id = Lingotek_API::CLIENT_ID;
		$token_details = get_option( 'lingotek_token' );
		$user = wp_get_current_user();
		$base_url = get_option( 'lingotek_base_url' );

		$acting_login_id = $user->user_email; // user_nicename;

		return self::generate_workbench_link(
			$document_id,
			$locale,
			$client_id,
			$token_details['access_token'],
			$token_details['login_id'],
			$acting_login_id,
			$base_url
		);
	}

	/**
	 * Generates a workbench link
	 * function provided by Matt Smith from Lingotek
	 *
	 * @since 0.1
	 *
	 * @param string   $document_id
	 * @param string   $locale_code
	 * @param string   $client_id
	 * @param string   $access_token
	 * @param string   $login_id
	 * @param string   $acting_login_id
	 * @param string   $base_url
	 * @param int|null $expiration
	 * @return string workbench link
	 */
	public static function generate_workbench_link( $document_id, $locale_code, $client_id, $access_token, $login_id, $acting_login_id = 'anonymous', $base_url = 'https://myaccount.lingotek.com', $expiration = null ) {
		$expiration_default = time() + (60 * 30); // 30-minute default, otherwise use $expiration as passed in
		$expiration = is_null( $expiration ) ? $expiration_default : $expiration;
		$data = array(
			'document_id'     => $document_id,
			'locale_code'     => $locale_code,
			'client_id'       => $client_id,
			'login_id'        => $login_id,
			'acting_login_id' => $acting_login_id,
			'expiration'      => $expiration,
		);
		$query_data = utf8_encode( http_build_query( $data ) );
		$hmac = rawurlencode( base64_encode( hash_hmac( 'sha1', $query_data, $access_token, true ) ) );
		$workbench_url = $base_url . '/lingopoint/portal/wb.action?' . $query_data . '&hmac=' . $hmac;
		return $workbench_url;
	}

	/**
	 * Outputs an action icon
	 *
	 * @since 0.2
	 *
	 * @param string $name
	 * @param string $link
	 * @param string $additional parameters to add (js, target)
	 */
	public static function display_icon( $name, $link, $additional = '' ) {
		self::link_to_settings_if_not_connected($link);
		if ($name == 'interim') {
			return sprintf('<a class="lingotek-interim-color dashicons dashicons-%s dashicons-%s-lingotek" title="%s" href="%s"%s></a>',
			self::$icons[ $name ]['icon'], self::$icons[ $name ]['icon'], self::$icons[ $name ]['title'], esc_url( $link ), $additional);
		}
		return sprintf('<a class="lingotek-color dashicons dashicons-%s dashicons-%s-lingotek" title="%s" href="%s"%s></a>',
		self::$icons[ $name ]['icon'], self::$icons[ $name ]['icon'], self::$icons[ $name ]['title'], esc_url( $link ), $additional);
	}

	/**
	 * Outputs an API error icon
	 *
	 * @since 1.2
	 *
	 * @param string $name
	 * @param string $additional parameters to add (js, target)
	 */
	public static function display_error_icon( $name, $api_error, $additional = '' ) {
		return sprintf('<span class="lingotek-error dashicons dashicons-%s" title="%s"></span>',
		self::$icons[ $name ]['icon'], self::$icons[ $name ]['title'] . "\n" . $api_error, $additional);
	}

	/**
	 * Outputs an upload icon
	 *
	 * @since 0.2
	 *
	 * @param int|string $object_id
	 * @param bool       $warning
	 */
	public function upload_icon( $object_id, $confirm = false ) {
		$args = array( $this->type => $object_id, 'action' => 'lingotek-upload', 'noheader' => true );
		if (isset($args['string'])) {
			$args['string'] = urlencode($args['string']);
		}
		$link = wp_nonce_url( defined( 'DOING_AJAX' ) && DOING_AJAX ? add_query_arg( $args, wp_get_referer() ) : add_query_arg( $args ), 'lingotek-upload' );
		self::link_to_settings_if_not_connected($link);
		return self::display_icon( 'upload', $link, $confirm ? self::$confirm_message : '' );
	}

	/**
	 * Outputs a copy icon
	 *
	 * @param int|string $object_id
	 * @param string     $target
	 * @param bool       $warning
	 */
	public function copy_icon( $object_id, $target, $confirm = false ) {
		$args = array( $this->type => $object_id, 'target' => $target, 'action' => 'lingotek-copy', 'noheader' => true );
		$link = wp_nonce_url( defined( 'DOING_AJAX' ) && DOING_AJAX ? add_query_arg( $args, wp_get_referer() ) : add_query_arg( $args ), 'lingotek-copy' );
		self::link_to_settings_if_not_connected($link);
		return self::display_icon( 'copy', $link, $confirm ? self::$confirm_message : '' );
	}

	/**
	 * Outputs an importing icon
	 *
	 * @since 0.2
	 *
	 * @param object $document
	 */
	public static function importing_icon( $document ) {
		$args = array( 'document_id' => $document->document_id, 'action' => 'lingotek-status', 'noheader' => true );
		$link = wp_nonce_url( defined( 'DOING_AJAX' ) && DOING_AJAX ? add_query_arg( $args, wp_get_referer() ) : add_query_arg( $args ), 'lingotek-status' );
		self::link_to_settings_if_not_connected($link);
		return self::display_icon( 'importing', $link );
	}

	/**
	 * Outputs icons for translations
	 *
	 * @since 0.2
	 *
	 * @param object $document
	 * @param object $language
	 */
	public static function translation_icon( $document, $language ) {
		if ( isset( $document->translations[ $language->locale ] ) ) {
			if ( 'ready' === $document->translations[ $language->locale ] ) {
				$link = wp_nonce_url( add_query_arg( array( 'document_id' => $document->document_id, 'locale' => $language->locale, 'action' => 'lingotek-download', 'noheader' => true ) ), 'lingotek-download' );
				self::link_to_settings_if_not_connected($link);
				return self::display_icon( $document->translations[ $language->locale ], $link );
			} elseif ( 'not-current' === $document->translations[ $language->locale ] ) {
				return  '<div class="lingotek-color dashicons dashicons-no"></div>';
			} elseif ('current' !== $document->translations[ $language->locale ] && $custom_icon = $document->get_custom_in_progress_icon($language)) {
				return $custom_icon;
			} else {
				$link = self::workbench_link( $document->document_id, $language->lingotek_locale );
				self::link_to_settings_if_not_connected($link);
				return self::display_icon( $document->translations[ $language->locale ], $link, ' target="_blank"' );
			}
		} else {
			$link = wp_nonce_url( add_query_arg( array( 'document_id' => $document->document_id, 'locale' => $language->locale, 'action' => 'lingotek-request', 'noheader' => true ), defined( 'DOING_AJAX' ) && DOING_AJAX ? wp_get_referer() : wp_get_referer() ), 'lingotek-request' );
			self::link_to_settings_if_not_connected($link);
			return self::display_icon( 'request', $link );
		}
	}

	/**
	 * Creates an html action link
	 *
	 * @since 0.2
	 *
	 * @param array $args parameters to add to the link
	 * @param bool  $warning whether to display an alert or not, optional, defaults to false
	 * @return string
	 */
	protected function get_action_link( $args, $warning = false ) {
		$action = $args['action'];
		$args['action'] = 'lingotek-' . $action;
		$args['noheader'] = true;
		if (isset($args['string'])) {
			$args['string'] = urlencode($args['string']);
		}
		$link = wp_nonce_url( defined( 'DOING_AJAX' ) && DOING_AJAX ? add_query_arg( $args, wp_get_referer() ) : add_query_arg( $args ), 'lingotek-' . $action );
		self::link_to_settings_if_not_connected($link);

		return sprintf(
			'<a class="lingotek-color" title="%s" href="%s"%s>%s</a>',
			self::$actions[ $action ]['description'],
			$link,
			empty( $warning ) ? '' : self::$confirm_message,
			self::$actions[ $action ]['action']
		);
	}

	private static function link_to_settings_if_not_connected(&$link)
	{
		if (! get_option('lingotek_token') || ! get_option('lingotek_community')) {
			$link = get_site_url(null, '/wp-admin/admin.php?page=lingotek-translation_settings');
		}
	}

	/**
	 * Adds a row action link
	 *
	 * @since 0.2
	 *
	 * @param array        $actions list of action links
	 * @param $id object id
	 * @return array
	 */
	protected function _row_actions( $actions, $id ) {
		// first check that a language is associated to this object
		if ( ! $this->get_language( $id ) ) {
			return $actions;
		}

		$document = $this->lgtm->get_group( $this->type, $id );
		if ( $this->type !== 'string' && isset( $document->desc_array['lingotek']['source'] ) ) {
			$id = $document->desc_array['lingotek']['source'];
		}

		if ( $this->lgtm->can_upload( $this->type, $id ) || (isset( $document->source ) && 'string' !== $this->type && $this->lgtm->can_upload( $this->type, $document->source )) ) {
			if ( $document ) {
				$desc_array = $document->desc_array;
				unset( $desc_array['lingotek'] );
				if ( count( $desc_array ) >= 2 ) {
					$actions['lingotek-upload'] = $this->get_action_link( array( $this->type => $id, 'action' => 'upload' ), true );
				} else {
					$actions['lingotek-upload'] = $this->get_action_link( array( $this->type => $id, 'action' => 'upload' ) );
				}

				/**
				* If a document has been changed but still has translations or is importing we still want to have the
				* update translation status option.
				*/
				if ( 'importing' === $document->status || $document->has_translation_status( 'pending' ) ) {
					$actions['lingotek-status'] = $this->get_action_link( array( 'document_id' => $document->document_id, 'action' => 'status' ) );
				}

				if ( $document->has_translation_status( 'ready' ) ) {
					$actions['lingotek-download'] = $this->get_action_link( array( 'document_id' => $document->document_id, 'action' => 'download' ) );
				}
			} else {
				$actions['lingotek-upload'] = $this->get_action_link( array( $this->type => $id, 'action' => 'upload' ) );
			}
		} elseif ( isset( $document->translations ) ) {
			// translations to download ?
			if ( $document->has_translation_status( 'ready' ) ) {
				$actions['lingotek-download'] = $this->get_action_link( array( 'document_id' => $document->document_id, 'action' => 'download' ) );
			}

			if ($document->has_translation_status('interim')) {
				$actions['lingotek-status'] = $this->get_action_link( array( 'document_id' => $document->document_id, 'action' => 'status' ) );
			}

			// need to request translations ?
			$language = $this->get_language( $document->source );
			$all_locales = array_flip( $this->pllm->get_languages_list( array( 'fields' => 'locale' ) ) );
			if ( ! empty( $language ) ) { // in case a language has been deleted
				unset( $all_locales[ $language->locale ] );
			}
			$untranslated = array_diff_key( $all_locales, $document->translations );

			// remove disabled target language from untranslated languages list
			foreach ( $untranslated as $k => $v ) {
				if ( $this->type === 'term' ) {
					if ( $document->is_disabled_target( $language, $this->pllm->get_language( $k ) ) ) {
						unset( $untranslated[ $k ] );
					}
				} else {
					if ( $document->is_disabled_target( $language, $this->pllm->get_language( $k ) ) ) {
						unset( $untranslated[ $k ] );
					}
				}
			}

			if ( 'current' === $document->status && ! empty( $untranslated ) ) {
				$actions['lingotek-request'] = $this->get_action_link( array( 'document_id' => $document->document_id, 'action' => 'request' ) );
			}

			// offers to update translations status
			if ( 'importing' === $document->status || $document->has_translation_status( 'pending' ) ) {
				$actions['lingotek-status'] = $this->get_action_link( array( 'document_id' => $document->document_id, 'action' => 'status' ) );
			}
		} elseif ( empty( $document->source ) ) {
			$actions['lingotek-upload'] = $this->get_action_link( array( $this->type => $id, 'action' => 'upload' ), true );
		}

		// offers to disassociate translations
		if ( isset( $document->source ) ) {
			$actions['lingotek-delete'] = $this->get_action_link( array( 'document_id' => $document->document_id, 'action' => 'delete' ) );
		}

		return $actions;
	}

	/**
	 * Adds actions to bulk dropdown list table using a javascript hack
	 * as the existing filter does not allow to *add* actions
	 * also displays the progress dialog placeholder
	 *
	 * @since 0.2
	 */
	protected function _add_bulk_actions( $bulk_actions ) {

		foreach ( self::$actions as $action => $strings ) {
			$bulk_actions[ 'bulk-lingotek-' . $action ] = __( $strings['action'], $action );
			if ( null !== filter_input( INPUT_GET, 'bulk-lingotek-' . $action ) ) {
				$text = $strings['progress'];
			}
		}

		if ( ! empty( $text ) ) {
			printf( '<div id="lingotek-progressdialog" style="display:none" title="%s"><div id="lingotek-progressbar"></div></div>', esc_html( $text ) );
		}

		return $bulk_actions;
	}

	/**
	 * Outputs javascript data for progress.js
	 *
	 * @since 0.1
	 */
	public function admin_enqueue_scripts() {
		foreach ( array_keys( self::$actions ) as $action ) {
			if ( null !== filter_input( INPUT_GET, 'bulk-lingotek-' . $action ) ) {
				wp_localize_script('lingotek_progress', 'lingotek_data', array(
					'action'   => null === filter_input( INPUT_GET, 'page' ) ? (null === filter_input( INPUT_GET, 'taxonomy' ) ? 'post_' . $action : 'term_' . $action) : 'string_' . $action,
					'taxonomy' => null === filter_input( INPUT_GET, 'taxonomy' ) || ! taxonomy_exists( wp_unslash( filter_input( INPUT_GET, 'taxonomy' ) ) ) ? '' : filter_input( INPUT_GET, 'taxonomy' ),
					'sendback' => remove_query_arg( array( 'bulk-lingotek-' . $action, 'ids', 'lingotek_warning' ), wp_get_referer() ),
					'ids'      => array_map( 'intval', explode( ',', filter_input( INPUT_GET, 'ids' ) ) ),
					'warning'  => null === filter_input( INPUT_GET, 'lingotek_warning' ) ? '' : __( 'You are about to overwrite existing translations. Are you sure?', 'lingotek-translation' ),
					'nonce'    => wp_create_nonce( 'lingotek_progress' ),
				));
				return;
			}
		}
	}

	/**
	 * Manages actions driven by dcoument_id
	 *
	 * @since 0.2
	 *
	 * @param string $action action name
	 * @return bool true if the action was managed, false otherwise
	 */
	protected function _manage_actions( $action ) {
		if ( null !== filter_input( INPUT_GET, 'document_id' ) ) {
			$document_id = filter_input( INPUT_GET, 'document_id' );
			$document = $this->lgtm->get_group_by_id( $document_id );
		}

		switch ( $action ) {
			case 'lingotek-status':
				check_admin_referer( 'lingotek-status' );
				$document->source_status();
				$document->translations_status();
				break;

			case 'lingotek-request':
				check_admin_referer( 'lingotek-request' );
				Lingotek_Logger::info("User requested to translate an item", array("document_id" => isset($document_id) ? $document_id : "", "locale" => filter_input( INPUT_GET, 'locale' )));
				null !== filter_input( INPUT_GET, 'locale' ) ? $document->request_translation( filter_input( INPUT_GET, 'locale' ) ) : $document->request_translations();
				break;

			case 'lingotek-download':
				check_admin_referer( 'lingotek-download' );
				Lingotek_Logger::info("User requested to download translation", array("document_id"=> isset($document_id) ? $document_id : "", "locale" => filter_input( INPUT_GET, 'locale' )));
				null !== filter_input( INPUT_GET, 'locale' ) ? $document->create_translation( filter_input( INPUT_GET, 'locale' ) ) : $document->create_translations();
				break;

			case 'lingotek-delete':
				check_admin_referer( 'lingotek-delete' );
				$document->disassociate();
				if ( null !== filter_input( INPUT_GET, 'lingotek_redirect' ) && filter_input( INPUT_GET, 'lingotek_redirect' ) === true ) {
					$site_id = get_current_blog_id();
					wp_safe_redirect( get_site_url( $site_id, '/wp-admin/edit.php?post_type=page' ) );
					exit();
				}
				break;

			default:
				return false;
		}

		return true;
	}

	/**
	 * Ajax response to download translations and showing progress
	 *
	 * @since 0.1
	 */
	public function ajax_download() {
		check_ajax_referer( 'lingotek_progress', '_lingotek_nonce' );

		if ( $document = $this->lgtm->get_group( $this->type, filter_input( INPUT_POST, 'id' ) ) ) {
			foreach ( $document->translations as $locale => $status ) {
				if ( 'pending' === $status || 'ready' === $status || 'interim' === $status || 'current' === $status) {
					$document->create_translation( $locale );
				}
			}
		}
		die();
	}

	/**
	 * Ajax response to request translations and showing progress
	 *
	 * @since 0.2
	 */
	public function ajax_request() {
		check_ajax_referer( 'lingotek_progress', '_lingotek_nonce' );
		if ( $document = $this->lgtm->get_group( $this->type, filter_input( INPUT_POST, 'id' ) ) ) {
			$document->request_translations();
		}
		die();
	}

	/**
	 * Ajax response to check translation status and showing progress
	 *
	 * @since 0.1
	 */
	public function ajax_status() {
		check_ajax_referer( 'lingotek_progress', '_lingotek_nonce' );
		if ( $document = $this->lgtm->get_group( $this->type, filter_input( INPUT_POST, 'id' ) ) ) {
			$document->source_status();
			$document->translations_status();
		}
		die();
	}

	/**
	 * Ajax response disassociate translations and showing progress
	 *
	 * @since 0.2
	 */
	public function ajax_delete() {
		check_ajax_referer( 'lingotek_progress', '_lingotek_nonce' );
		if ( $document = $this->lgtm->get_group( $this->type, filter_input( INPUT_POST, 'id' ) ) ) {
			$document->disassociate();
		}
		die();
	}

	/**
	 * Ajax call to get the price estimation of a given document.
	 */
	public function ajax_estimate_cost() {
		check_ajax_referer( 'lingotek_professional', '_lingotek_nonce' );
		$document_id = filter_input( INPUT_GET, 'document_id' );
		$locale = filter_input( INPUT_GET, 'locale' );
		$lingotek_auth = filter_input( INPUT_GET, 'Authorization-Lingotek' );
		$client = new Lingotek_API();
		$response = $client->get_cost_estimate($lingotek_auth, $document_id, $locale);
		echo json_encode($response);
		die();
	}

	/**
	* Ajax call to request professional translation of a document through bridge.
	*/
	public function ajax_request_professional_translation() {
		check_ajax_referer( 'lingotek_professional', '_lingotek_nonce' );
		$post_vars = filter_input_array(INPUT_POST);
		$client = new Lingotek_API();
		$response = $client->request_professional_translation_bulk($post_vars['workflow_id'], $post_vars['translations'], $post_vars['total_estimate'], $post_vars['summary']);
		if (true === $response['data']['transaction_approved']) {
			foreach ($post_vars['translations'] as $document_id => $locales) {
				if ( $document = $this->lgtm->get_group( $post_vars['type'], $post_vars['ids'][$document_id] ) ) {
					foreach ($locales as $locale) {
						$locale = $post_vars['lingotek_locale_to_wp_locale'][$locale];
						$document->update_translation_status($locale, 'pending');
					}
				} else {
					// TODO: what if a document doesn't exists? T_T
				}
			}
		}

		echo json_encode($response);
		die();
	}

	public function ajax_get_ltk_terms_and_conditions() {
		check_ajax_referer( 'lingotek_professional', '_lingotek_nonce' );
		$client = new Lingotek_API();
		echo json_encode($client->get_lingotek_terms_and_conditions());
		die();
	}

	public function ajax_get_user_payment_information() {
		check_ajax_referer( 'lingotek_professional', '_lingotek_nonce' );
		$client = new Lingotek_API();
		$response = $client->get_user_payment_information();
		echo json_encode($response);
		die();
	}

	/**
	 * Collects and returns all API errors
	 *
	 * @since 1.1
	 *
	 * @param string errors
	 */
	public static function retrieve_api_error( $errors ) {
		$api_error = "\n";

		foreach ( $errors as $error => $error_message ) {
			if ( is_array( $error_message ) ) {
				if ( ! empty( $error_message ) ) {
					foreach ( $error_message as $locale => $message ) {
						$api_error = $api_error . $message . "\n";
					}
				}
			} else {
				$api_error = $api_error . $error_message . "\n";
			}
		}

		return $api_error;
	}
}
