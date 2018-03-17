<?php

/**
 * Adds row and bulk actions to posts, pages and media list
 * Manages actions which trigger communication with Lingotek TMS
 *
 * @since 0.2
 */
class Lingotek_Post_actions extends Lingotek_Actions {

	/**
	 * Constructor
	 *
	 * @since 0.2
	 */
	public function __construct() {
		parent::__construct( 'post' );

		// row actions.
		add_filter( 'post_row_actions', array( &$this, 'post_row_actions' ), 10, 2 );
		add_filter( 'page_row_actions', array( &$this, 'post_row_actions' ), 10, 2 ); // hierarchical post types.
		add_filter( 'media_row_actions', array( &$this, 'post_row_actions' ), 10, 2 );

		// add bulk actions.
		add_filter( 'bulk_actions-edit-post', array( &$this, 'add_bulk_actions' ) );
		add_filter( 'bulk_actions-edit-page', array( &$this, 'add_bulk_actions' ) );
		
		foreach(PLL()->model->get_translated_taxonomies() as $taxonomy) {
			add_filter( "bulk_actions-edit-$taxonomy", array( &$this, 'add_bulk_actions' ) );
		}
		// add_filter( 'bulk_actions-edit-category', array( &$this, 'add_bulk_actions' ) );

		$polylang_enabled = PLL()->model->get_translated_post_types();
		$custom_post_types = get_post_types( array( '_builtin' => false ) );
		unset( $custom_post_types[ 'polylang_mo' ] );

		foreach ( $custom_post_types as $type )
		{
			if (isset($polylang_enabled[$type]))
			{
				add_filter( "bulk_actions-edit-$type", array( &$this, 'add_bulk_actions' ) );
			}
		}

		// manage bulk actions, row actions and icon actions.
		add_action( 'load-edit.php', array( &$this, 'manage_actions' ) );
		add_action( 'load-upload.php', array( &$this, 'manage_actions' ) );

		// handle promotional banner
		add_action( 'load-edit.php', array( &$this, 'manage_promotions' ) );

		// add meta box on post and page edit pages, and hook on save.
		add_action( 'add_meta_boxes', array( &$this, 'lingotek_add_meta_boxes' ) );
		add_action( 'save_post', array( &$this, 'lingotek_save_meta_boxes' ) );
	}

	/**
	 * Get the language of a post
	 *
	 * @since 0.2
	 *
	 * @param int $post_id
	 * @return object
	 */
	protected function get_language( $post_id ) {
		return PLL()->model->post->get_language( $post_id );
	}

	/**
	 * Displays the icon of an uploaded post with the relevant link
	 *
	 * @since 0.2
	 *
	 * @param int $id
	 */
	public static function uploaded_icon( $id ) {
		return self::display_icon( 'uploaded', get_edit_post_link( $id ) );
	}

	/**
	 * Adds a row action link
	 *
	 * @since 0.1
	 *
	 * @param array  $actions list of action links.
	 * @param object $post post object.
	 * @return array
	 */
	public function post_row_actions( $actions, $post ) {
		if ( $this->pllm->is_translated_post_type( $post->post_type ) ) {
			$actions = $this->_row_actions( $actions, $post->ID );

			$language = PLL()->model->post->get_language( $post->ID );
			if ( ! empty( $language ) ) {
				$profile = Lingotek_Model::get_profile( $post->post_type, $language, $post->ID );
				if ( 'disabled' === $profile['profile'] ) {
					unset( $actions['lingotek-upload'] );
				}
			}
		}
		return $actions;
	}

	/**
	 * Adds actions to bulk dropdown in posts list table
	 *
	 * @since 0.1
	 */
	public function add_bulk_actions( $bulk_actions ) {
		if ( isset( $GLOBALS['post_type'] ) && $this->pllm->is_translated_post_type( $GLOBALS['post_type'] ) ) {
			return $this->_add_bulk_actions( $bulk_actions );
		}
	}

	public function manage_promotions() {
		if (null !== filter_input(INPUT_GET, 'ltk-promotion') && 'dismiss' === filter_input(INPUT_GET, 'ltk-promotion')) {
			update_option('lingotek_professional_promotion_shown', true);
		} else if (null !== filter_input(INPUT_GET, 'ltk-promotion') && 'view' === filter_input(INPUT_GET, 'ltk-promotion')) {
			update_option('lingotek_professional_promotion_shown', true);
			wp_safe_redirect( 'admin.php?page=lingotek-translation_tutorial&sm=content&tutorial=ltk-prof#ltk-prof-trans-header' );
			die();
		}
		function lingotek_professional_translation_notice() {
			echo sprintf( '<div style="height: 45px;" class="notice notice-success"><p>%s <a style="float:right;" class="button" href="%s" >Dismiss</a><a style="float:right; margin-right: 6px;" class="button button-primary" href="%s" >Learn More</a></p></div>', 
				__( '<b>NEW FEATURE!</b> Lingotek Professional Translation is now available.', 'lingotek-translation' ),
				admin_url( 'edit.php?ltk-promotion=dismiss' ),
				admin_url( 'edit.php?ltk-promotion=view' )
			);
		}
		// update_option('lingotek_professional_promotion_shown', false);
		if (!get_option('lingotek_professional_promotion_shown')) {
			add_action( 'admin_notices', 'lingotek_professional_translation_notice' );
		}
	}

	/**
	 * Manages Lingotek specific actions before WordPress acts
	 *
	 * @since 0.1
	 */
	public function manage_actions() {
		global $typenow;
		$post_type = 'load-upload.php' === current_filter() ? 'attachment' : $typenow;

		if ( ! $this->pllm->is_translated_post_type( $post_type ) ) {
			return;
		}

		// get the action.
		// $typenow is empty for media.
		$wp_list_table = _get_list_table( empty( $typenow ) ? 'WP_Media_List_Table' : 'WP_Posts_List_Table' );
		$action = $wp_list_table->current_action();

		if ( empty( $action ) ) {
			return;
		}

		$redirect = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), wp_get_referer() );
		if ( ! $redirect ) {
			$redirect = admin_url( "edit.php?post_type=$typenow" );
		}

		switch ( $action ) {
			case 'bulk-lingotek-upload':
				$type = empty( $typenow ) ? 'media' : 'post';
				$filtered_get = filter_input_array( INPUT_GET );
				if ( ! isset( $filtered_get[ $type ] ) ) {
					return;
				}

				$post_ids = array();

				foreach ( array_map( 'intval', $filtered_get[ $type ] ) as $post_id ) {
					// safe upload.
					if ( $this->lgtm->can_upload( 'post', $post_id ) ) {
						$post_ids[] = $post_id;
					} // the document is already translated so will be overwritten.
					elseif ( ($document = $this->lgtm->get_group( 'post', $post_id )) && empty( $document->source ) ) {
						// take care to upload only one post in a translation group.
						$intersect = array_intersect( $post_ids, PLL()->model->post->get_translations( $post_id ) );
						if ( empty( $intersect ) ) {
							$post_ids[] = $post_id;
							$redirect = add_query_arg( 'lingotek_warning', 1, $redirect );
						}
					}
				}

				// check if translation is disabled.
				if ( ! empty( $post_ids ) ) {
					foreach ( $post_ids as $key => $post_id ) {
						$language = PLL()->model->post->get_language( $post_id );
						$profile = Lingotek_Model::get_profile( $post_type, $language );
						if ( 'disabled' === $profile['profile'] ) {
							unset( $post_ids[ $key ] );
						}
					}
				}

			case 'bulk-lingotek-request':
			case 'bulk-lingotek-download':
			case 'bulk-lingotek-status':
			case 'bulk-lingotek-delete':
				if ( empty( $post_ids ) ) {
					$type = empty( $typenow ) ? 'media' : 'post';
					$filtered_get = filter_input_array( INPUT_GET );
					if ( ! isset( $filtered_get[ $type ] ) ) {
						return;
					}

					$post_ids = array_map( 'intval', $filtered_get[ $type ] );
				}

				empty( $typenow ) ? check_admin_referer( 'bulk-media' ) : check_admin_referer( 'bulk-posts' );

				$redirect = add_query_arg( $action, 1, $redirect );
				$redirect = add_query_arg( 'ids', implode( ',', $post_ids ), $redirect );

				break;

			case 'lingotek-upload':
				check_admin_referer( 'lingotek-upload' );
				$this->lgtm->upload_post( (int) filter_input( INPUT_GET, 'post' ) );
				break;

			case 'lingotek-copy':
				check_admin_referer( 'lingotek-copy' );
				$post_to_copy = get_post( (int) filter_input( INPUT_GET, 'post' ) );
				$target = filter_input( INPUT_GET, 'target' );
				$this->lgtm->copy_post( $post_to_copy, $target );
				break;

			default:
				if ( ! $this->_manage_actions( $action ) ) {
					return; // do not redirect if this is not one of our actions.
				}
		}

		wp_safe_redirect( $redirect );
		exit();

	}

	/**
	 * Ajax response to upload documents and showing progress
	 *
	 * @since 0.1
	 */
	public function ajax_upload() {
		check_ajax_referer( 'lingotek_progress', '_lingotek_nonce' );
		$this->lgtm->upload_post( (int) filter_input( INPUT_POST, 'id' ) );
		die();
	}

	/**
	 * Adds the lingotek profile meta box on edit pages
	 *
	 * @since 0.1
	 */
	public function lingotek_add_meta_boxes() {
		global $post;
		$lgtm = new Lingotek_Model();
		$group = $lgtm->get_group( $post->post_type, $post->ID );

		// if it's a new page, $group will be null, so don't check if it's a source page.
		if ( $group ) {
			$desc_array = $group->desc_array;
			$source_id = $desc_array['lingotek']['source'];
			// only display the meta box if it's a source.
			if ( isset( $desc_array['lingotek']['source'] ) && $post->ID !== $source_id ) {
				return;
			}
		}

		$polylang_enabled = PLL()->model->get_translated_post_types();
		$custom_post_types = get_post_types( array( '_builtin' => false ) );
		unset( $custom_post_types[ 'polylang_mo' ] );

		foreach ( $custom_post_types as $type )
		{
			if (isset($polylang_enabled[$type]))
			{
				add_meta_box('lingotek_post_meta_box', __('Lingotek Translation', 'lingotek-translation'), array( __CLASS__, 'lingotek_edit_meta_box_html'), $type, 'side', 'default');
			}
		}

		add_meta_box('lingotek_post_meta_box', __('Lingotek Translation', 'lingotek-translation'), array( __CLASS__, 'lingotek_edit_meta_box_html'), 'post', 'side', 'default');
		add_meta_box('lingotek_page_meta_box', __('Lingotek Translation', 'lingotek-translation'), array( __CLASS__, 'lingotek_edit_meta_box_html'), 'page', 'side', 'default');
	}

	/**
	 * Builds the html for the edit post and edit page meta boxes.
	 *
	 * @since 0.1
	 */
	public static function lingotek_edit_meta_box_html() {
		wp_enqueue_script( 'lingotek_defaults', LINGOTEK_URL . '/js/defaults.js' );

		global $post;
		$post_type = get_post_type( $post->ID );
		$lgtm = new Lingotek_Model();
		$group = $lgtm->get_group( 'post', $post->ID );
		$profiles = Lingotek::get_profiles();
		$content_profiles = get_option('lingotek_content_type');
		$language_profiles = self::retrieve_lang_Profiles($post_type, $profiles, $content_profiles);
		$default_name = empty($content_profiles) == false ? $profiles[$content_profiles[$post->post_type]['profile']]['name'] :  __('Manual', 'lingotek-translation');
		if ( ! isset( $default_name ) || 'disabled' === $default_name )
		{
			echo esc_html( __('You must enable translation for this content type in Lingotek\'s Content Type Configuration to enable Translation Profiles.', 'lingotek-translation') );
			return;
		}
		$content_default_profile = array('default' => array(
			'name' => __('Content Default', 'lingotek-translation') . ' (' . $default_name . ')', // Adds in the name of the content type default profile
		));

		$language_profiles['defaults'] = array(
			'content_default' => $default_name,
			'title' => __( 'Content Default', 'lingotek-translation' ),
		);
		$profiles = array_merge( $content_default_profile, $profiles );
		$post_profile = self::get_post_profile( $post->ID );
		if ( isset( $post_profile ) ) {
			$selected[ $post_profile->description ] = $profiles[ $post_profile->description ];
			unset( $profiles[ $post_profile->description ] );
			$profiles = array_merge( $selected, $profiles );
		}

		if ( isset( $group->source ) ) { // Disables selection of a different profile if content has been uploaded to Lingotek.
			$args = array(
				'document_id' => $group->document_id,
				'action' => 'lingotek-delete',
				'noheader' => true,
			);
			if ( $post_type === 'page' ) {
				$args['lingotek_redirect'] = true;
			}
			$site_id = get_current_blog_id();
			$url = $post_type === 'page' ? get_site_url( $site_id, '/wp-admin/edit.php?post_type=page' ) : get_site_url( $site_id, '/wp-admin/edit.php' );
			$disassociate_url = wp_nonce_url( add_query_arg( $args, $url ), 'lingotek-delete' );
			$remove_post = 'post=' . $post->ID;
			$disassociate_url = str_replace( $remove_post, '', $disassociate_url );
			$prefs = Lingotek_Model::get_prefs();
			$confirm_message = isset( $prefs['delete_document_from_tms'] ) === false ?  __( 'Are you sure you want to do this?', 'lingotek-translation' ) : __( 'Are you sure you want to do this? The document will be deleted from Lingotek TMS.', 'lingotek-translation' );
			$confirm_message = sprintf( ' onclick = "return confirm(\'%s\');"', esc_html( $confirm_message ) );
			printf( '<strong>%s</strong><br><br>', esc_html( __( 'Translation Profile', 'lingotek-translation' ) ) );
			printf( '<em>%s</em><br>', esc_html( __( 'Disassociate this content to change the Translation Profile', 'lingotek-translation' ) ) );
			printf( ('<a class="button button-small" href="%s" %s>%s</a><br><br>'), esc_url( $disassociate_url ), esc_html( $confirm_message ), esc_html( __( 'Disassociate', 'lingotek-translation' ) ) );
			printf( '<select disabled class="lingotek-profile-setting" name="%1$s" id="%1$s">', esc_html( 'lingotek_profile_meta' ) );
		} else {
			printf( '<strong>%s</strong><br><br>', esc_html( __( 'Translation Profile', 'lingotek-translation' ) ) );
			printf( '<select class="lingotek-profile-setting" name="%1$s" id="%1$s">', 'lingotek_profile_meta' );
		}

		foreach ( $profiles as $key => $profile ) {
			echo "\n\t<option value=" . esc_attr( $key ) . '>' . esc_attr( $profile['name'] ) . '</option>';
		}
		echo '</select>';
		echo '<div id="lingotek-language-profiles" style="display: none;">' . wp_json_encode( $language_profiles ) . '</div>';
	}

	public function lingotek_save_meta_boxes() {
		if ( null === filter_input( INPUT_POST, 'lingotek_profile_meta' ) ) {
			return;
		}

		global $post;
		$profile_choice = filter_input( INPUT_POST, 'lingotek_profile_meta' );
		$document_id = 'lingotek_profile_' . $post->ID;
		$term = self::get_post_profile( $post->ID );
		$post_language = $this->get_language( $post->ID );
		$content_profiles = get_option( 'lingotek_content_type' );

		if ( $profile_choice === 'default' && ! empty( $term ) ) {
			wp_delete_term( (int) $term->term_id, 'lingotek_profile' );
		} elseif ( $profile_choice !== 'default' ) {
			if ( empty( $term ) ) {
				wp_insert_term( $document_id, 'lingotek_profile', array( 'description' => $profile_choice ) );
			} else {
				wp_update_term( (int) $term->term_id, 'lingotek_profile', array( 'description' => $profile_choice ) );
			}

			wp_set_object_terms( $post->ID, $document_id, 'lingotek_profile' );
		}
	}

	public static function get_post_profile( $post_id ) {
		if ( taxonomy_exists( 'lingotek_profile' ) ) {
			$terms = get_the_terms( $post_id, 'lingotek_profile' );
			if ( is_array( $terms ) ) {
				return array_pop( $terms );
			} else {
				return null;
			}
		} else {
			return 'false';
		}
	}

	public static function retrieve_lang_Profiles( $post_type, $profiles, $content_profiles ) {
		$language_profiles = array();

		if ( isset( $content_profiles[ $post_type ]['sources'] ) ) {
			$sources = $content_profiles[ $post_type ]['sources'];
			foreach ( $sources as $lang_code => $profile ) {
				$language_profiles[ $lang_code ] = $profiles[ $profile ]['name'];
			}
		}

		return $language_profiles;
	}

}
