<?php

/*
 * Abstract class for Translations groups objects
 *
 * @since 0.2
 */
abstract class Lingotek_Group {
	static public $creating_translation; // used to avoid uploading a translation when using automatinc upload

	/*
	 * constructor
	 *
	 * @since 0.2
	 */
	public function __construct($term, &$pllm) {
		$this->pllm = &$pllm;
		$this->load($term);
	}

	/*
	 * assigns this object properties from the underlying term
	 *
	 * @since 0.2
	 *
	 * @param object $term term translation object
	 */
	protected function load($term) {
		$this->term_id = (int) $term->term_id;
		$this->tt_id = (int) $term->term_taxonomy_id;
		$this->document_id = $term->slug;
		$this->taxonomy = $term->taxonomy;
		$this->desc_array = unserialize($term->description);

		foreach (array('type', 'source', 'status', 'translations') as $prop)
			$this->$prop = &$this->desc_array['lingotek'][$prop];
	}

	/*
	 * updates the translation term in DB
	 *
	 * @since 0.2
	 */
	public function save() {
		wp_update_term((int) $this->term_id, $this->taxonomy, array('slug' => $this->document_id, 'name' => $this->document_id, 'description' => serialize($this->desc_array)));
	}

	/*
	 * provides a safe way to update the translations statuses when receiving "simultaneous" TMS callbacks
	 *
	 * @since 0.2
	 *
	 * @param string $locale
	 * @param string $status
	 * @param array $arr translations to add
	 */
	protected function safe_translation_status_update($locale, $status, $arr = array()) {
		global $wpdb;
		$wpdb->query("LOCK TABLES $wpdb->term_taxonomy WRITE");
		$d = $wpdb->get_var($wpdb->prepare("SELECT description FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = %d", $this->tt_id));
		$d = unserialize($d);
		$this->translations[$locale] = $d['lingotek']['translations'][$locale] = $status;
		$d = array_merge($d, $arr); // optionally add a new translation
		$d = serialize($d);
		$wpdb->query($wpdb->prepare("UPDATE $wpdb->term_taxonomy SET description = %s WHERE term_taxonomy_id = %d", $d, $this->tt_id));
		$wpdb->query("UNLOCK TABLES");
	}

	/*
	 * creates a new term translation object in DB
	 *
	 * @since 0.2
	 *
	 * @param int $object_id the id of the object to translate
	 * @param string $document_id Lingotek document id
	 * @param array $desc data to store in the Lingotek array
	 * @param string $taxonomy either 'post_translations' or 'term_translations'
	 */
	protected static function _create($object_id, $document_id, $desc, $taxonomy) {
		$terms = wp_get_object_terms($object_id, $taxonomy);
		$term = array_pop($terms);

		if (empty($term)) {
			wp_insert_term($document_id, $taxonomy, array('description' => serialize($desc)));
		}

		// the translation already exists but was not managed by Lingotek
		else {
			if (is_array($old_desc = maybe_unserialize($term->description)))
				$desc = array_merge($old_desc, $desc);
			wp_update_term((int) $term->term_id, $taxonomy, array('slug' => $document_id, 'name' => $document_id, 'description' => serialize($desc)));
		}

		wp_set_object_terms($object_id, $document_id, $taxonomy);
	}

	/*
	 * disassociates translations from the Lingotek TMS
	 *
	 * @since 0.2
	 *
	 * @param bool $delete whether to delete the Lingotek document or not
	 */
	public function disassociate() {
		$client = new Lingotek_API();
		$prefs = Lingotek_Model::get_prefs();

		if ($prefs['delete_document_from_tms']) {
			$client->delete_document($this->document_id, $this->source);
			unset($this->desc_array['lingotek']);
			$this->save();
		}
		else {
			unset($this->desc_array['lingotek']);
			$this->save();
		}

	}

	/*
	 * uploads a modified source
	 *
	 * @since 0.2
	 *
	 * @param string $title
	 * @param object $content can be a post object, a term object
	 */
	public function patch($title, $content, $external_url = '', $filters = array()) {
		$client = new Lingotek_API();

		$params = array(
			'title' => $title,
			'content' => $this->get_content($content),
			'external_url' => $external_url,
		);
		$params = array_merge($params, $filters);

		$res = $client->patch_document($this->document_id, $params, $this->source);

		if ($res) {
			$this->status = 'importing';
			$this->translations = array_fill_keys(array_keys($this->translations), 'pending');
			$this->save();
		}
	}

	/*
	 * checks the status of source document
	 *
	 * @since 0.2
	 */
 	public function source_status() {
		$client = new Lingotek_API();

		if ('importing' == $this->status && $client->get_document_status($this->document_id)){
			$this->status = 'current';
			$this->save();
		}
	}

	/*
	 * sets source status to ready
	 *
	 * @since 0.2
	 */
	public function source_ready() {
		$this->status = 'current';
		$this->save();
	}

	/*
	 * requests a translation to Lingotek TMS
	 *
	 * @since 0.2
	 *
	 * @param string $locale
	 */
	public function request_translation($locale) {
		$workflow = $this->get_workflow_object($this->get_source_language(), $locale, $this->type, $this->source);
		if ($workflow->has_custom_request_procedure()) {
			$workflow->do_custom_request();
		} else {
			$client = new Lingotek_API();
			$language = $this->pllm->get_language($locale);
			$workflow = Lingotek_Model::get_profile_option('workflow_id', $this->type, $this->get_source_language(), $language, $this->source);
			if ('project-default' === $workflow) {
				$workflow = null;
			}
			$args = $workflow ? array('workflow_id' => $workflow) : array();

			if (!$this->is_disabled_target($language) && empty($this->translations[$language->locale])) {
				// don't change translations to pending if the api call failed
				if ($client->request_translation($this->document_id, $language->locale, $args, $this->source)) {
					$this->status = 'current';
					$this->translations[$language->locale] = 'pending';
				}

				$this->save();
			}
		}
	}

	/*
	 * requests translations to Lingotek TMS
	 *
	 * @since 0.2
	 *
	 * @param object $source_language language of the source
	 */
	protected function _request_translations($source_language) {
		
		$type_id;
		$client = new Lingotek_API();

		foreach ($this->pllm->get_languages_list() as $lang) {
			$workflow = $this->get_workflow_object($source_language, $lang->locale, $this->type, $this->source);
			if ($workflow->has_custom_request_procedure()) {
				$workflow->do_custom_request();
			} else {
				if ($source_language->slug != $lang->slug && !$this->is_disabled_target($source_language, $lang) && empty($this->translations[$lang->locale])) {
					$workflow = Lingotek_Model::get_profile_option('workflow_id', $this->type, $source_language, $lang, $this->source);
					if ('project-default' === $workflow) {
						$workflow = null;
					}
					$args = $workflow ? array('workflow_id' => $workflow) : array();

					if ($this->type == 'string') {
						$type_id = $this->name;
					}
					else {
						$type_id = $this->source;
					}
					// don't change translations to pending if the api call failed
					if ($client->request_translation($this->document_id, $lang->locale, $args, $type_id)) {

						/**
						 * This is a fix that reloads the object before editing & saving it. The problem 
						 * was that the callbacks were coming back before this method finished so the 
						 * $this->translations array was out of sync with what was in the database. We fix this
						 * by reading the DB only when we need to -> make our edit -> save the edit. This keeps us from holding on to
						 * old data and overwritting the new data.
						 */
						if ('post_translations' === $this->taxonomy) {
							$this->load( PLL()->model->post->get_object_term((int) $this->source, 'post_translations') );
						} else if ('term_translations' === $this->taxonomy) {
							$this->load( PLL()->model->term->get_object_term((int) $this->source, 'term_translations') );
						}
						$this->status = 'current';
						if (!isset($this->translations[$lang->locale]) || isset($this->translations[$lang->locale]) && $this->translations[$lang->locale] != 'current') {
							$this->translations[$lang->locale] = 'pending';
						}
						$this->save();
					}
				}
			}
		}
	}

	/**
	* Publicly exposes the safe_translation_status_update method that allows us to safely update
	* translation statuses. This method is used when a request translation call is made to bridge and that 
	* translation was requested successfully. 
	*/
	public function update_translation_status($locale, $status)
	{
		$this->safe_translation_status_update($locale, $status);
	}

	/*
	 * checks the translations status of a document
	 *
	 * @since 0.1
	 */
	public function translations_status() {
		// $client = new Lingotek_API();
		// $translations = $client->get_translations_status($this->document_id, $this->source); // key are Lingotek locales
		// foreach($this->translations as $locale => $status) {
		// 	$lingotek_locale = $this->pllm->get_language($locale)->lingotek_locale;
		// 	if ('current' != $status && isset($translations[$lingotek_locale]) && 100 == $translations[$lingotek_locale])
		// 		$this->translations[$locale] = 'ready';
		// }
		// $this->save();

		$this->translation_status_hard_refresh();
	}

	public function translation_status_hard_refresh() {
		$client = new Lingotek_API();
		$translations = $client->get_translations_status($this->document_id, $this->source); // key are Lingotek locales
		$lingotek_locale_to_pll_locale = array();
		foreach (PLL()->model->get_languages_list() as $pll_language) {
			$lingotek_locale_to_pll_locale[$pll_language->lingotek_locale] = $pll_language->locale;
		}
		foreach ($translations as $lingotek_locale => $percent)
		{
			if (!isset($lingotek_locale_to_pll_locale[$lingotek_locale])) { continue; }

			$wp_locale = $lingotek_locale_to_pll_locale[$lingotek_locale];
			if ($translations[$lingotek_locale] < 100 && $this->translations[$wp_locale] !== 'interim') {
				$this->translations[$wp_locale] = 'pending';
			}
			else if ($this->translations[$wp_locale] === 'interim' && $translations[$lingotek_locale] === 100) {
				$this->translations[$wp_locale] = 'ready';
			}
			else if ((!isset($this->translations[$wp_locale])) || ($this->translations[$wp_locale] !== 'current') && $this->translations[$wp_locale] !== 'interim') {
				$this->translations[$wp_locale] = 'ready';
			}
		}

		$this->save();
	}

	/*
	 * sets translation status to ready
	 *
	 * @since 0.1
	 * @uses Lingotek_Group::safe_translation_status_update() as the status can be automatically set by the TMS callback
	 */
	public function translation_ready($locale) {
		$this->safe_translation_status_update($locale, 'ready');
	}

	/*
	 * attempts to create all translations from an object
	 *
	 * @since 0.2
	 */
	public function create_translations() {
		if (isset($this->translations)) {
			foreach ($this->translations as $locale => $status)
				if ('pending' == $status || 'ready' == $status)
					$this->create_translation($locale);
		}
	}

	/*
	 * sets document status to edited
	 *
	 * @since 0.1
	 */
	public function source_edited() {
		$this->status = 'edited';
		// $this->translations = array_fill_keys(array_keys($this->translations), 'not-current');
		$this->save();
	}

	/*
	 * returns true if at least one of the translations has the requested status
	 *
	 * @since 0.2
	 *
	 * @param string $status
	 * @return bool
	 */
	public function has_translation_status($status) {
		return isset($this->translations) && array_intersect(array_keys($this->translations, $status), $this->pllm->get_languages_list(array('fields' => 'locale')));
	}

	/*
	 * checks if target should be automatically downloaded
	 *
	 * @since 0.2
	 *
	 * @param string $locale
	 * @return bool
	 */
	public function is_automatic_download($locale) {
		return 'automatic' == Lingotek_Model::get_profile_option('download', $this->type, $this->get_source_language(), $this->pllm->get_language($locale), $this->source);
	}

	public function is_automatic_upload() {
		$workflow = $this->get_workflow_object($this->get_source_language(), false, $this->type, $this->source);
		$can_auto_upload = $workflow->auto_upload_allowed();
		if ($can_auto_upload) {
			/**
			 * Check each of the translations and if one of them doesn't allow automatic upload then we don't auto upload the doc.
			 */
			foreach ($this->translations as $locale => $progress) {
				$workflow = $this->get_workflow_object($this->get_source_language(), $locale, $this->type, $this->source);
				$can_auto_upload = $can_auto_upload && $workflow->auto_upload_allowed();
			}
		}
		
		return $can_auto_upload;
	}

	/*
	 * checks if translation is disabled for a target language
	 *
	 * @since 0.2
	 *
	 * @param string $type post type or taxonomy
	 * @param object $language
	 */
	public function is_disabled_target($language, $target = null) {
		$profile = Lingotek_Model::get_profile($this->type, $language, $this->source);
		if ($target) {
			return isset($profile['targets'][$target->slug]) && ('disabled' == $profile['targets'][$target->slug] || 'copy' == $profile['targets'][$target->slug]);
		}
		else {
			return isset($profile['targets'][$language->slug]) && ('disabled' == $profile['targets'][$language->slug] || 'copy' == $profile['targets'][$language->slug]);
		}
	}

	/**
	 * Goes through the source document and all locales and calls the pre_upload_to_lingotek() on the Workflow object unless
	 * a locale has been disabled.
	 *
	 * @param string $item_id
	 * @param string $type
	 * @param object $source_language
	 * @return void
	 */
	public function pre_upload_to_lingotek($item_id, $type, $source_language, $item_type) {
		$workflow = $this->get_workflow_object($source_language, false, $type, $item_id);
		$workflow->pre_upload_to_lingotek($item_id, $item_type);
		foreach ($this->pllm->get_languages_list() as $lang) {
			if ($this->_is_disabled_target($lang, $type, $item_id)) {
				continue;
			}
			$workflow = $this->get_workflow_object($source_language, $lang->locale, $type, $item_id);
			$workflow->pre_upload_to_lingotek($item_id, $item_type);
		}
	}

	/**
	 * Goes through the source document and all locales and calls the save_post_hook() on the Workflow object unless
	 * a locale has been disabled.
	 *
	 * @param string $item_id
	 * @param string $type
	 * @param object $source_language
	 * @return void
	 */
	public function pre_save_post($item_id, $type, $source_language) {
		$workflow = $this->get_workflow_object($source_language, false, $type, $item_id);
		$workflow->save_post_hook();
		foreach ($this->pllm->get_languages_list() as $lang) {
			if ($this->_is_disabled_target($lang, $type, $item_id)) {
				continue;
			}
			$workflow = $this->get_workflow_object($source_language, $lang->locale, $type, $item_id);
			$workflow->save_post_hook();
		}
	}

	/**
	 * Goes through the source document and all locales and calls the save_term_hook() on the Workflow object unless
	 * a locale has been disabled.
	 *
	 * @param string $item_id
	 * @param string $type
	 * @param object $source_language
	 * @return void
	 */
	public function pre_save_terms($item_id, $type, $source_language) {
		$workflow = $this->get_workflow_object($source_language, false, $type, $item_id);
		$workflow->save_term_hook();
		foreach ($this->pllm->get_languages_list() as $lang) {
			if ($this->_is_disabled_target($lang, $type, $item_id)) {
				continue;
			}
			$workflow = $this->get_workflow_object($source_language, $lang->locale, $type, $item_id);
			$workflow->save_term_hook();
		}
	}

	public function get_custom_in_progress_icon($language) {
		$workflow = $this->get_workflow_object($this->get_source_language(), $language->locale, $this->type, $this->source);
		return $workflow->get_custom_in_progress_icon();
	}

	/**
	 * Checks the source language and all of its target language's workflows to determine whether a bulk translation request is allowed. 
	 * If one or more of the workflows return true on has_custom_request_procedure() then the bulk translation request will be aborted.
	 *
	 * @param object $source_language
	 * @param string $type
	 * @param string $item_id
	 * @return boolean
	 */
	private function can_bulk_request_translations($source_language, $type, $item_id) {
		$workflow = $this->get_workflow_object($source_language, false, $type, $item_id);
		if ($workflow->has_custom_request_procedure()) { return false; }

		foreach ($this->pllm->get_languages_list() as $lang) {
			if ($this->_is_disabled_target($lang, $type, $item_id)) {
				continue;
			}
			$workflow = $this->get_workflow_object($source_language, $lang->locale, $type, $item_id);
			if ($workflow->has_custom_request_procedure()) { return false; }
		}

		return true;
	}

	/**
	 * Instantiates and returns a workflow object. If only the source language is passed in then it will return the workflow object
	 * for the source locale; however, if a locale is passed in with the source language then a workflow object will be returned 
	 * for the locale.
	 *
	 * @param string $source_language
	 * @param boolean | string $locale
	 * @param string $type
	 * @param string $item_id
	 * @return void
	 */
	private function get_workflow_object( $source_language, $locale = false, $type,  $item_id ) {
		$target_language = ($locale) ? $this->pllm->get_language($locale) : false;
		$source_language = (!$source_language) ? PLL()->model->post->get_language( $this->source ) : $source_language;
		$workflow_id;
		if ($type === 'post') {
			$post = ($item_id) ? get_post($item_id) : get_post( $this->source );
			$workflow_id = Lingotek_Model::get_profile_option( 'workflow_id', $post->post_type, $source_language, $target_language , $this->source );
		} else {
			$workflow_id = Lingotek_Model::get_profile_option( 'workflow_id', $type, $source_language, $target_language );
		}
		$workflow = Lingotek_Workflow_Factory::get_workflow_instance( $workflow_id ); 
		return $workflow;
	}

	/**
	 * Checks if a target language has been disabled. Is different than the other is_disabled_target method by 
	 * allowing the caller to supply all of the arguments used.
	 *
	 * @param object $target_language
	 * @param string $type
	 * @param string $item_id
	 * @return void
	 */
	private function _is_disabled_target($target_language, $type, $item_id) {
		$profile = Lingotek_Model::get_profile($type, $target_language, $item_id);
		return isset($profile['targets'][$target_language->slug]) && ('disabled' == $profile['targets'][$target_language->slug] || 'copy' == $profile['targets'][$target_language->slug]);
	}
}
