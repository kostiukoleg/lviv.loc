<?php
add_action('admin_post_save_popup', 'sgPopupSave');

function sgSanitize($optionsKey, $isTextField = false)
{
	if (isset($_POST[$optionsKey])) {
		if ($optionsKey == "sg_popup_html"||
			$optionsKey == "sg_ageRestriction"||
			$optionsKey == "sg_countdown"||
			$optionsKey == "sg_social" ||
			$optionsKey == "sg-exit-intent" ||
			$optionsKey == "sg_popup_fblike" ||
			$optionsKey == "sg_subscription" ||
			$optionsKey == "sg_contactForm" ||
			$optionsKey == "all-selected-page" ||
			$optionsKey == "all-selected-posts" ||
			$optionsKey == "sg_popup_mailchimp" ||
			$optionsKey == "sg_popup_aweber" ||
			$optionsKey == "sg-mailchimp-form" ||
			$isTextField == true
			) {
			if(POPUP_BUILDER_PKG > POPUP_BUILDER_PKG_FREE) {
				$sgPopupData = $_POST[$optionsKey];
				return $sgPopupData;
				/*require_once(SG_APP_POPUP_FILES ."/sg_popup_pro.php");
				return SgPopupPro::sgPopupDataSanitize($sgPopupData);*/
			}
			return SGFunctions::sgPopupDataSanitize($_POST[$optionsKey]);
		}
		return sanitize_text_field($_POST[$optionsKey]);
	}
	else {
		return "";
	}
}

function sgPopupSave()
{
	global $wpdb;

	if(isset($_POST)) {
		check_admin_referer('sgPopupBuilderSave');
	}
	/*Removing all added slashes*/
	$_POST = stripslashes_deep($_POST);
	$postData = $_POST;
	$socialButtons = array();
	$socialOptions = array();
	$countdownOptions = array();
	$fblikeOptions = array();
	$subscriptionOptions = array();
	$options = array();
	$contactFormOptions = array();
	$showAllPages = sgSanitize('allPages');
	$showAllPosts = sgSanitize('allPosts');
	$showAllCustomPosts = sgSanitize('allCustomPosts');
	$allSelectedPages = "";
	$allSelectedPosts = "";
	$allSelectedCustomPosts = "";
	$allSelectedCategories = sgSanitize("posts-all-categories", true);

	$selectedPages = sgSanitize('all-selected-page');
	$selectedPosts = sgSanitize('all-selected-posts');
	$selectedCustomPosts = sgSanitize('all-selected-custom-posts');

	/* if popup check for all pages it is not needed for save all pages all posts */
	if($showAllPages !== "all" && !empty($selectedPages)) {
		$allSelectedPages = explode(",", $selectedPages);
	}
	
	if($showAllPosts !== "all" && !empty($selectedPosts)) {
		$allSelectedPosts = explode(",", $selectedPosts);
	}
	if($showAllCustomPosts !== "all" && !empty($selectedCustomPosts)) {
		$allSelectedCustomPosts = explode(",", $selectedCustomPosts);
	}

	$socialOptions = array(
		'sgSocialTheme' => sgSanitize('sgSocialTheme'),
		'sgSocialButtonsSize' => sgSanitize('sgSocialButtonsSize'),
		'sgSocialLabel' => sgSanitize('sgSocialLabel'),
		'sgSocialShareCount' => sgSanitize('sgSocialShareCount'),
		'sgRoundButton' => sgSanitize('sgRoundButton'),
		'fbShareLabel' => sgSanitize('fbShareLabel'),
		'lindkinLabel' => sgSanitize('lindkinLabel'),
		'sgShareUrl' => esc_url_raw(@$_POST['sgShareUrl']),
		'shareUrlType' => sgSanitize('shareUrlType'),
		'googLelabel' => sgSanitize('googLelabel'),
		'twitterLabel' => sgSanitize('twitterLabel'),
		'pinterestLabel' => sgSanitize('pinterestLabel'),
		'sgMailSubject' => sgSanitize('sgMailSubject'),
		'sgMailLable' => sgSanitize('sgMailLable')
	);

	$socialButtons = array(
		'sgTwitterStatus' => sgSanitize('sgTwitterStatus'),
		'sgFbStatus' => sgSanitize('sgFbStatus'),
		'sgEmailStatus' => sgSanitize('sgEmailStatus'),
		'sgLinkedinStatus' => sgSanitize('sgLinkedinStatus'),
		'sgGoogleStatus' => sgSanitize('sgGoogleStatus'),
		'sgPinterestStatus' => sgSanitize('sgPinterestStatus'),
		'pushToBottom' => sgSanitize('pushToBottom')
	);

	$countdownOptions = array(
		'pushToBottom' => sgSanitize('pushToBottom'),
		'countdownNumbersBgColor' => sgSanitize('countdownNumbersBgColor'),
		'countdownNumbersTextColor' => sgSanitize('countdownNumbersTextColor'),
		'sg-due-date' => sgSanitize('sg-due-date'),
		'countdown-position' => sgSanitize('countdown-position'),
		'counts-language'=> sgSanitize('counts-language'),
		'sg-time-zone' => sgSanitize('sg-time-zone'),
		'sg-countdown-type' => sgSanitize('sg-countdown-type'),
		'countdown-autoclose' => sgSanitize('countdown-autoclose')
	);

	$videoOptions = array(
		'video-autoplay' => sgSanitize('video-autoplay')
	);

	$exitIntentOptions = array(
		'exit-intent-type' => sgSanitize('exit-intent-type'),
		'exit-intent-expire-time' => sgSanitize('exit-intent-expire-time'),
		'exit-intent-alert' => sgSanitize('exit-intent-alert')
	);

	$subscriptionOptions = array(
		'subs-first-name-status' => sgSanitize('subs-first-name-status'),
		'subs-last-name-status' => sgSanitize('subs-last-name-status'),
		// email input placeholder text
		'subscription-email' => sgSanitize('subscription-email'),
		'subs-first-name' => sgSanitize('subs-first-name'),
		'subs-last-name' => sgSanitize('subs-last-name'),
		'subs-text-width' => sgSanitize('subs-text-width'),
		'subs-button-bgColor' => sgSanitize('subs-button-bgColor'),
		'subs-btn-width' => sgSanitize('subs-btn-width'),
		'subs-btn-title' => sgSanitize('subs-btn-title'),
		'subs-text-input-bgColor' => sgSanitize('subs-text-input-bgColor'),
		'subs-text-borderColor' => sgSanitize('subs-text-borderColor'),
		'subs-button-color' => sgSanitize('subs-button-color'),
		'subs-inputs-color' => sgSanitize('subs-inputs-color'),
		'subs-btn-height' => sgSanitize('subs-btn-height'),
		'subs-text-height' => sgSanitize('subs-text-height'),
		'subs-placeholder-color' => sgSanitize('subs-placeholder-color'),
		'subs-validation-message' => sgSanitize('subs-validation-message'),
		'subs-success-message' => sgSanitize('subs-success-message'),
		'subs-btn-progress-title' => sgSanitize('subs-btn-progress-title'),
		'subs-text-border-width' => sgSanitize('subs-text-border-width'),
		'subs-success-behavior' => sgSanitize('subs-success-behavior'),
		'subs-success-redirect-url' => esc_url_raw(@$_POST['subs-success-redirect-url']),
		'subs-success-popups-list' => sgSanitize('subs-success-popups-list'),
		'subs-first-name-required' => sgSanitize('subs-first-name-required'),
		'subs-last-name-required' => sgSanitize('subs-last-name-required'),
		'subs-success-redirect-new-tab' => sgSanitize('subs-success-redirect-new-tab')
	);

	$contactFormOptions = array(
		'contact-name' => sgSanitize('contact-name'),
		'contact-name-status' => sgSanitize('contact-name-status'),
		'contact-name-required' => sgSanitize('contact-name-required'),
		'contact-subject' => sgSanitize('contact-subject'),
		'contact-subject-status' => sgSanitize('contact-subject-status'),
		'contact-subject-required' => sgSanitize('contact-subject-required'),
		// email input placeholder text(string)
		'contact-email' => sgSanitize('contact-email'),
		'contact-message' => sgSanitize('contact-message'),
		'contact-validation-message' => sgSanitize('contact-validation-message'),
		'contact-success-message' => sgSanitize('contact-success-message'),
		'contact-inputs-width' => sgSanitize('contact-inputs-width'),
		'contact-inputs-height' => sgSanitize('contact-inputs-height'),
		'contact-inputs-border-width' => sgSanitize('contact-inputs-border-width'),
		'contact-text-input-bgcolor' => sgSanitize('contact-text-input-bgcolor'),
		'contact-text-bordercolor' => sgSanitize('contact-text-bordercolor'),
		'contact-inputs-color' => sgSanitize('contact-inputs-color'),
		'contact-placeholder-color' => sgSanitize('contact-placeholder-color'),
		'contact-btn-width' => sgSanitize('contact-btn-width'),
		'contact-btn-height' => sgSanitize('contact-btn-height'),
		'contact-btn-title' => sgSanitize('contact-btn-title'),
		'contact-btn-progress-title' => sgSanitize('contact-btn-progress-title'),
		'contact-button-bgcolor' => sgSanitize('contact-button-bgcolor'),
		'contact-button-color' => sgSanitize('contact-button-color'),
		'contact-area-width' => sgSanitize('contact-area-width'),
		'contact-area-height' => sgSanitize('contact-area-height'),
		'sg-contact-resize' => sgSanitize('sg-contact-resize'),
		'contact-validate-email' => sgSanitize('contact-validate-email'),
		'contact-receive-email' => sanitize_email(@$_POST['contact-receive-email']),
		'contact-fail-message' => sgSanitize('contact-fail-message'),
		'show-form-to-top' => sgSanitize('show-form-to-top'),
		'contact-success-behavior' => sgSanitize('contact-success-behavior'),
		'contact-success-redirect-url' => sgSanitize('contact-success-redirect-url'),
		'contact-success-popups-list' => sgSanitize('contact-success-popups-list'),
		'dont-show-content-to-contacted-user' => sgSanitize('dont-show-content-to-contacted-user'),
		'contact-success-frequency-days' => sgSanitize('contact-success-frequency-days'),
		'contact-success-redirect-new-tab' => sgSanitize('contact-success-redirect-new-tab')
	);

	$fblikeOptions = array(
		'fblike-like-url' => esc_url_raw(@$_POST['fblike-like-url']),
		'fblike-layout' => sgSanitize('fblike-layout'),
		'fblike-dont-show-share-button' => sgSanitize('fblike-dont-show-share-button'),
		'fblike-close-popup-after-like' => sgSanitize('fblike-close-popup-after-like')
	);

	$addToGeneralOptions = array(
		'showAllPages' => $showAllPages,
		'showAllPosts' => $showAllPosts,
		'showAllCustomPosts' => $showAllCustomPosts,
		'allSelectedPages' => $allSelectedPages,
		'allSelectedPosts' => $allSelectedPosts,
		'allSelectedCustomPosts' => $allSelectedCustomPosts,
		'allSelectedCategories'=> $allSelectedCategories,
		'fblikeOptions'=> $fblikeOptions,
		'videoOptions'=>$videoOptions,
		'exitIntentOptions'=> $exitIntentOptions,
		'countdownOptions'=> $countdownOptions,
		'socialOptions'=> $socialOptions,
		'socialButtons'=> $socialButtons
	);

	$options = IntegrateExternalSettings::getPopupGeneralOptions($addToGeneralOptions);

	$html = stripslashes(sgSanitize("sg_popup_html"));
	$fblike = stripslashes(sgSanitize("sg_popup_fblike"));
	$ageRestriction = stripslashes(sgSanitize('sg_ageRestriction'));
	$social = stripslashes(sgSanitize('sg_social'));
	$image = sgSanitize('ad_image');
	$countdown = stripslashes(sgSanitize('sg_countdown'));
	$subscription = stripslashes(sgSanitize('sg_subscription'));
	$sgContactForm = stripslashes(sgSanitize('sg_contactForm'));
	$iframe = sgSanitize('iframe');
	$video = sgSanitize('video');
	$shortCode = stripslashes(sgSanitize('shortcode'));
	$exitIntent = stripslashes(sgSanitize('sg-exit-intent'));
	$type = sgSanitize('type');
	$title = stripslashes(sgSanitize('title'));
	$id = sgSanitize('hidden_popup_number');
	$jsonDataArray = json_encode($options);

	$data = array(
		'id' => $id,
		'title' => $title,
		'type' => $type,
		'image' => $image,
		'html' => $html,
		'fblike' => $fblike,
		'iframe' => $iframe,
		'video' => $video,
		'shortcode' => $shortCode,
		'ageRestriction' => $ageRestriction,
		'countdown' => $countdown,
		'exitIntent' => $exitIntent,
		'sg_subscription' => $subscription,
		'sg_contactForm' => $sgContactForm,
		'social' => $social,
		'options' => $jsonDataArray,
		'subscriptionOptions' => json_encode($subscriptionOptions),
		'contactFormOptions' => json_encode($contactFormOptions)
	);

	function setPopupForAllPages($id, $data, $type) {
		//-1 is the home page key
		if(is_array($data) && $data[0] == -1 && defined('ICL_LANGUAGE_CODE')) {
			$data[0] .='_'.ICL_LANGUAGE_CODE;
		}
		SGPopup::addPopupForAllPages($id, $data, $type);
	}

	function setOptionPopupType($id, $type) {
		update_option("SG_POPUP_".strtoupper($type)."_".$id,$id);
	}

	if (empty($title)) {
		$redirectUrl = add_query_arg( array(
			'titleError' => 1,
			'type'  => $type,
		), SG_APP_POPUP_ADMIN_URL."admin.php?page=edit-popup");

		wp_safe_redirect($redirectUrl);
		exit();
	}
	$popupName = "SG".sanitize_text_field(ucfirst(strtolower($_POST['type'])));
	$popupClassName = $popupName."Popup";
	
	require_once(SG_APP_POPUP_PATH ."/classes/".$popupClassName.".php");

	if ($id == "") {
		global $wpdb;

		call_user_func(array($popupClassName, 'create'), $data);
		$lastId = $wpdb->get_var("SELECT LAST_INSERT_ID() FROM ".  $wpdb->prefix."sg_popup");
		$postData['saveMod'] = '';
		$postData['popupId'] = $lastId;
		$extensionManagerObj = new SGPBExtensionManager();
		$extensionManagerObj->setPostData($postData);
		$extensionManagerObj->save();

		if(POPUP_BUILDER_PKG != POPUP_BUILDER_PKG_FREE) {
			SGPopup::removePopupFromPages($lastId,'page');
			SGPopup::removePopupFromPages($lastId,'categories');
			if($options['allPagesStatus']) {
				if(!empty($showAllPages) && $showAllPages != 'all') {
					setPopupForAllPages($lastId, $allSelectedPages, 'page');
				}
				else {

					updatePopupOptions($lastId, array('page'), true);
				}
			}
			
			if($options['allPostsStatus']) {
				if(!empty($showAllPosts) && $showAllPosts == "selected") {

					setPopupForAllPages($lastId, $allSelectedPosts, 'page');
				}
				else if($showAllPosts == "all") {
					updatePopupOptions($lastId, array('post'), true);
				}
				if($showAllPosts == "allCategories") {
					setPopupForAllPages($lastId, $allSelectedCategories, 'categories');
				}
			}

			if($options['allCustomPostsStatus']) {
				if(!empty($showAllCustomPosts) && $showAllCustomPosts == "selected") {
					setPopupForAllPages($lastId, $allSelectedCustomPosts, 'page');
				}
				else if($showAllCustomPosts == "all") {
					updatePopupOptions($lastId, $options['all-custom-posts'], true);
				}
			}
			
		}
		
		setOptionPopupType($lastId, $type);

		$redirectUrl = add_query_arg( array(
			'id'    => $lastId,
			'saved' => 1,
			'type'  => $type,
		), SG_APP_POPUP_ADMIN_URL."admin.php?page=edit-popup");

		wp_safe_redirect($redirectUrl);
		exit();
	}
	else {
		$popup = SGPopup::findById($id);
		$popup->setTitle($title);
		$popup->setId($id);
		$popup->setType($type);
		$popup->setOptions($jsonDataArray);

		switch ($popupName) {
			case 'SGImage':
				$popup->setUrl($image);
				break;
			case 'SGIframe':
				$popup->setUrl($iframe);
				break;
			case 'SGVideo':
				$popup->setUrl($video);
				$popup->setRealUrl($video);
				$popup->setVideoOptions(json_encode($videoOptions));
				break;
			case 'SGHtml':
				$popup->setContent($html);
				break;
			case 'SGFblike':
				$popup->setContent($fblike);
				$popup->setFblikeOptions(json_encode($fblikeOptions));
				break;
			case 'SGShortcode':
				$popup->setShortcode($shortCode);
				break;
			case 'SGAgerestriction':
				$popup->setContent($ageRestriction);
				$popup->setYesButton($options['yesButtonLabel']);
				$popup->setNoButton($options['noButtonLabel']);
				$popup->setRestrictionUrl($options['restrictionUrl']);
				break;
			case 'SGCountdown':
				$popup->setCountdownContent($countdown);
				$popup->setCountdownOptions(json_encode($countdownOptions));
				break;
			case 'SGSocial':
				$popup->setSocialContent($social);
				$popup->setButtons(json_encode($socialButtons));
				$popup->setSocialOptions(json_encode($socialOptions));
				break;
			case 'SGExitintent':
				$popup->setContent($exitIntent);
				$popup->setExitIntentOptions(json_encode($exitIntentOptions));
				break;
			case 'SGSubscription':
				$popup->setContent($subscription);
				$popup->setSubscriptionOptions(json_encode($subscriptionOptions));
				break;
			case 'SGContactform':
				$popup->setContent($sgContactForm);
				$popup->steParams(json_encode($contactFormOptions));
			break;
		}
		if(POPUP_BUILDER_PKG != POPUP_BUILDER_PKG_FREE) {
			SGPopup::removePopupFromPages($id, 'page');
			SGPopup::removePopupFromPages($id, 'categories');
			if(!empty($options['allPagesStatus'])) {
				if($showAllPages && $showAllPages != 'all') {
					updatePopupOptions($id, array('page'), false);
					setPopupForAllPages($id, $allSelectedPages, 'page');
				}
				else {
					updatePopupOptions($id, array('page'), true);
				}
			}
			else  {
				updatePopupOptions($id, array('page'), false);
			}

			if(!empty($options['allPostsStatus'])) {
				if(!empty($showAllPosts) && $showAllPosts == "selected") {
					updatePopupOptions($id, array('post'), false);
					setPopupForAllPages($id, $allSelectedPosts, 'page');
				}
				else if($showAllPosts == "all"){
					updatePopupOptions($id, array('post'), true);
				}
				if($showAllPosts == "allCategories") {
					setPopupForAllPages($id, $allSelectedCategories, 'categories');
				}
			}
			else {
				updatePopupOptions($id, array('post'), false);
			}

			if(!empty($options['allCustomPostsStatus'])) {
				if(!empty($showAllCustomPosts) && $showAllCustomPosts == "selected") {
					updatePopupOptions($id, $options['all-custom-posts'], false);
					setPopupForAllPages($id, $allSelectedCustomPosts, 'page');
				}
				else if($showAllCustomPosts == "all") {
					updatePopupOptions($id, $options['all-custom-posts'], true);
				}
			}
			else {
				updatePopupOptions($id, $options['all-custom-posts'], false);
			}
		}
	
		setOptionPopupType($id, $type);
		$postData['saveMod'] = '1';
		$postData['popupId'] = $id;
		$extensionManagerObj = new SGPBExtensionManager();
		$extensionManagerObj->setPostData($postData);
		$extensionManagerObj->save();
		$popup->save();

		$redirectUrl = add_query_arg( array(
			'id'    => $id,
			'saved' => 1,
			'type'  => $type,
		), SG_APP_POPUP_ADMIN_URL."admin.php?page=edit-popup");

		wp_safe_redirect($redirectUrl);
		exit();
	}

}

/**
 * Save data to wp options
 *
 * @since 3.2.2
 *
 * @param int $id popup id number
 * @param array $postTypes page post types
 * @param bool $isInsert true for insert false for remove
 *
 * @return void
 *
 */

function updatePopupOptions($id, $postTypes, $isInsert) {

	/*getting wp option data*/
	$allPosts = get_option("SG_ALL_POSTS");
	$key = false;

	if(!$allPosts) {
		$allPosts = array();
	}

	if(empty($postTypes)) {
		$postTypes = array();
	}

	if($allPosts && !empty($allPosts)) {
		/*Get current popup id key from assoc array*/
		$key = SGFunctions::getCurrentPopupIdFromOptions($id);
	}

	/*When isset like id data in wp options*/
	if($key !== false) {
		$popupPostTypes = $allPosts[$key]['popstTypes'];
		if(empty($popupPostTypes)) {
			$popupPostTypes = array();
		}

		/*Insert or remove from exist post types*/
		if($isInsert) {
			$popupPostTypes = array_merge($popupPostTypes, $postTypes);
			$popupPostTypes = array_unique($popupPostTypes);
		}
		else {
			if(!empty($postTypes)) {
				$popupPostTypes = array_diff($popupPostTypes, $postTypes);
			}

		}

		/*After modificition remove popup id from all post types or cghanged exist value*/
		if(empty($popupPostTypes)) {
			unset($allPosts[$key]);
		}
		else {
			$allPosts[$key]['popstTypes'] = $popupPostTypes;
			if(defined('ICL_LANGUAGE_CODE')){
				$allPosts[$key]['lang'] = ICL_LANGUAGE_CODE;
			}
		}

	}
	else if($isInsert && !empty($postTypes)) {
		$data = array('id'=>$id, 'popstTypes'=>$postTypes);
		if(defined('ICL_LANGUAGE_CODE')){
			$data['lang'] = ICL_LANGUAGE_CODE;
		}
		if(is_array($allPosts)) {
			array_push($allPosts, $data);
		}
	}

	update_option("SG_ALL_POSTS", $allPosts);
}
