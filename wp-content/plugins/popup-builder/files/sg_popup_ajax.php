<?php
//sanitizing and validating input before any action
function sgSanitizeAjaxField($optionValue,  $isTextField = false) {
	/*TODO: Extend function for other sanitization and validation actions*/
	if(!$isTextField) {
		return sanitize_text_field($optionValue);
	}
}

function sgPopupDelete()
{
	check_ajax_referer('sgPopupBuilderDeleteNonce', 'ajaxNonce');
	$id = (int)@$_POST['popup_id'];

	if($id == 0 || !$id) {
		return;
	}

	require_once(SG_APP_POPUP_CLASSES.'/SGPopup.php');
	SGPopup::delete($id);
	SGPopup::removePopupFromPages($id);

	$args = array('popupId'=> $id);
	do_action('sgPopupDelete', $args);
}

add_action('wp_ajax_delete_popup', 'sgPopupDelete');

function sgFrontend()
{
	global $wpdb;
	check_ajax_referer('sgPopupBuilderSubsNonce', 'subsSecurity');
	parse_str($_POST['subsribers'], $subsribers);
	if(!empty($subsribers['sg-subs-hidden-checker'])) {
		return 'Bot';
	}
	$email = sanitize_email($subsribers['subs-email-name']);
	$firstName = sgSanitizeAjaxField($subsribers['subs-first-name']);
	$lastName = sgSanitizeAjaxField($subsribers['subs-last-name']);
	$title = sanitize_title($subsribers['subs-popup-title']);

	$query = $wpdb->prepare("SELECT id FROM ". $wpdb->prefix ."sg_subscribers WHERE email = %s AND subscriptionType = %s", $email, $title);
	$list = $wpdb->get_row($query, ARRAY_A);
	if(!isset($list['id'])) {
		$sql = $wpdb->prepare("INSERT INTO ".$wpdb->prefix."sg_subscribers (firstName, lastName, email, subscriptionType, status) VALUES (%s, %s, %s, %s,%d)", $firstName, $lastName, $email, $title, 0);
		$res = $wpdb->query($sql);
	}
	die();
}

add_action('wp_ajax_nopriv_subs_send_mail', 'sgFrontend');
add_action('wp_ajax_subs_send_mail', 'sgFrontend');

function sgpbAddToCounter()
{
	check_ajax_referer('sgPbNonce', 'ajaxNonce');
	$popupParams = $_POST['params'];
	$popupId = (int)$popupParams['popupId'];
	$popupsCounterData = get_option('SgpbCounter');

	if($popupsCounterData === false) {
		$popupsCounterData = array();
	}

	if(empty($popupsCounterData[$popupId])) {
		$popupsCounterData[$popupId] = 0;
	}
	$popupsCounterData[$popupId] += 1;

	update_option('SgpbCounter', $popupsCounterData);
	die();
}

add_action('wp_ajax_nopriv_send_to_open_counter', 'sgpbAddToCounter');
add_action('wp_ajax_send_to_open_counter', 'sgpbAddToCounter');

function sgContactForm()
{
	global $wpdb;
	parse_str($_POST['contactParams'], $params);
	//CSRF CHECK
	check_ajax_referer('sgPopupBuilderContactNonce', 'contactSecurity');
	if(!empty($params['sg-hidden-checker'])) {
		return 'Bot';
	}
	$adminMail = sanitize_email($_POST['receiveMail']);
	$popupTitle = sanitize_title($_POST['popupTitle']);
	$name = sgSanitizeAjaxField($params['contact-name']);
	$subject = sgSanitizeAjaxField($params['contact-subject']);
	$userMessage = sgSanitizeAjaxField($params['content-message']);
	$mail = sanitize_email($params['contact-email']);


	$message = '';
	if(isset($name)) {
		if($name == '') {
			$name = 'Not provided';
		}
		$message .= '<b>Name</b>: '.$name."<br>";
	}

	$message .= '<b>E-mail</b>: '.$mail."<br>";
	if(isset($subject)) {
		if($subject == '') {
			$subject = 'Not provided';
		}
		$message .= '<b>Subject</b>: '.$subject."<br>";
	}

	$message .= '<b>Message</b>: '.$userMessage."<br>";
	$headers  = 'MIME-Version: 1.0'."\r\n";
	$headers  = 'From: '.$adminMail.''."\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8'."\r\n"; //set UTF-8

	$sendStatus = wp_mail($adminMail, $popupTitle.'- Popup contact form', $message, $headers); //return true or false
	echo $sendStatus;
	die();
}

add_action('wp_ajax_nopriv_contact_send_mail', 'sgContactForm');
add_action('wp_ajax_contact_send_mail', 'sgContactForm');

function sgImportPopups()
{
	global $wpdb;
	check_ajax_referer('sgPopupBuilderImportNonce', 'ajaxNonce');
	$url = sgSanitizeAjaxField($_POST['attachmentUrl']);

	$contents = unserialize(base64_decode(file_get_contents($url)));

	/* For tables wich they are not popup tables child ex. subscribers */
	foreach ($contents['customData'] as $tableName => $datas) {
		$columns = '';

		$columsArray = array();
		foreach ($contents['customTablesColumsName'][$tableName] as $key => $value) {
			$columsArray[$key] = $value['Field'];
		}
		$columns .= implode(array_values($columsArray), ', ');
		foreach ($datas as $key => $data) {
			$values = "'".implode(array_values($data), "','")."'";
			$customInsertSql = $wpdb->prepare("INSERT INTO ".$wpdb->prefix.$tableName."($columns) VALUES ($values)");
			$wpdb->query($customInsertSql);
		}
	}

	foreach ($contents['wpOptions'] as $key => $option) {
		update_option($key,$option);
	}

	foreach ($contents['exportArray'] as $content) {
		//Main popup table data
		$popupData = $content['mainPopupData'];
		$popupId = $popupData['id'];
		$popupType = $popupData['type'];
		$popupTitle = $popupData['title'];
		$popupOptions = $popupData['options'];

		//Insert popup
		$sql = $wpdb->prepare("INSERT INTO ".$wpdb->prefix.PopupInstaller::$mainTableName."(id, type, title, options) VALUES (%d, %s, %s, %s)", $popupId, $popupType, $popupTitle, $popupOptions);
		$res = $wpdb->query($sql);
		//Get last insert popup id
		$lastInsertId = $wpdb->insert_id;

		//Child popup data
		$childPopupTableName = $content['childTableName']; // change it Tbale to Table
		$childPopupData = $content['childData']; //change it child

		//Foreach throw child popups
		foreach ($childPopupData as $childPopup) {
			//Child popup table columns
			$values = '';
			$columns = implode(array_keys($childPopup), ', ');
			foreach (array_values($childPopup) as $value) {
				$values .= "'".addslashes($value)."', ";
			}
			$values = rtrim($values, ', ');

			$queryValues = str_repeat("%s, ", count(array_keys($childPopup)));
			$queryValues = "%d, ".rtrim($queryValues, ', ');

			$queryStr = 'INSERT INTO '.$wpdb->prefix.$childPopupTableName.'(id, '.$columns.') VALUES ('.$lastInsertId.','. $values.')';

			$resa = (int)$wpdb->query($queryStr);

			echo 'ChildRes: '.$resa;
		}
		echo 'MainRes: '.$res;
	}
}

add_action('wp_ajax_import_popups', 'sgImportPopups');

function sgCloseReviewPanel()
{
	check_ajax_referer('sgPopupBuilderReview', 'ajaxNonce');
	update_option('SG_COLOSE_REVIEW_BLOCK', true);
	die();
}
add_action('wp_ajax_close_review_panel', 'sgCloseReviewPanel');

function sgDontShowReviewPopup()
{
	check_ajax_referer('sgPopupBuilderReview', 'ajaxNonce');
	update_option('SGPBCloseReviewPopup', true);
	die();
}
add_action('wp_ajax_dont_show_review_popup', 'sgDontShowReviewPopup');

function sgChangeReviewPopupPeriod()
{
	check_ajax_referer('sgPopupBuilderReview', 'ajaxNonce');
	$messageType = sanitize_text_field($_POST['messageType']);

	if($messageType == 'count') {
		$maxPopupCount = get_option('SGPBMaxOpenCount');
		if(!$maxPopupCount) {
			$maxPopupCount = SG_POPUP_SHOW_COUNT;
		}
		$maxPopupData = SGFunctions::getMaxOpenPopupId();
		if(!empty($maxPopupData['maxCount'])) {
			$maxPopupCount = $maxPopupData['maxCount'];
		}

		$maxPopupCount += SG_POPUP_SHOW_COUNT;
		update_option('SGPBMaxOpenCount', $maxPopupCount);
		die();
	}

	$popupTimeZone = @SgPopupGetData::getPopupTimeZone();
	$timeDate = new DateTime('now', new DateTimeZone($popupTimeZone));
	$timeDate->modify('+'.SG_REVIEW_POPUP_PERIOD.' day');

	$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
	update_option('SGPBOpenNextTime', $timeNow);
	$usageDays = get_option('SGPBUsageDays');
	$usageDays += SG_REVIEW_POPUP_PERIOD;
	update_option('SGPBUsageDays', $usageDays);
	die();
}

add_action('wp_ajax_change_review_popup_show_period', 'sgChangeReviewPopupPeriod');

function addToSubscribers() {

	global $wpdb;
	check_ajax_referer('sgPopupBuilderAddSubsToListNonce', 'ajaxNonce');
	$firstName = sgSanitizeAjaxField($_POST['firstName']);
	$lastName = sgSanitizeAjaxField($_POST['lastName']);
	$email = sanitize_email($_POST['email']);
	$subsType = array_map( 'sanitize_text_field', $_POST['subsType']);

	foreach ($subsType as $subType) {
		$selectSql = $wpdb->prepare('SELECT id FROM '.$wpdb->prefix.'sg_subscribers WHERE email = %s AND subscriptionType = %s', $email, $subType);
		$res = $wpdb->get_row($selectSql, ARRAY_A);
		if(empty($res)) {
			$sql = $wpdb->prepare('INSERT INTO '.$wpdb->prefix.'sg_subscribers (firstName, lastName, email, subscriptionType) VALUES (%s, %s, %s, %s) ', $firstName, $lastName, $email, $subType);
			$wpdb->query($sql);
		}
		else {
			$sql = $wpdb->prepare('UPDATE '.$wpdb->prefix.'sg_subscribers SET firstName = %s, lastName = %s, email = %s, subscriptionType = %s WHERE id = %s', $firstName, $lastName, $email, $subType, $res['id']);
			$wpdb->query($sql);
		}
	}

	die();
}
add_action('wp_ajax_add_to_subsribers', 'addToSubscribers');

function sgDeleteSubscribers() {

	global $wpdb;
	check_ajax_referer('sgPopupBuilderAddSubsNonce', 'ajaxNonce');
	$subsribersId = array_map( 'sanitize_text_field', $_POST['subsribersId']);
	foreach ($subsribersId as $subsriberId) {
		$prepareSql = $wpdb->prepare("DELETE FROM ". $wpdb->prefix ."sg_subscribers WHERE id = %d",$subsriberId);
		$wpdb->query($prepareSql);
	}
	die();
}

add_action('wp_ajax_subsribers_delete', 'sgDeleteSubscribers');

function sgIsHaveErrorLog() {

	global $wpdb;
	check_ajax_referer('sgPopupBuilderSubsLogNonce', 'ajaxNonce');
	$countRows = '';
	$popupType = sgSanitizeAjaxField($_POST['subsType']);

	$getErrorCounteSql = $wpdb->prepare("SELECT count(*) FROM ". $wpdb->prefix ."sg_subscription_error_log WHERE popupType=%s",$popupType);
	$countRows = $wpdb->get_var($getErrorCounteSql);
	echo $countRows;
	die();
}

add_action('wp_ajax_subs_error_log_count', 'sgIsHaveErrorLog');

function sgChangePopupStatus() {
	check_ajax_referer('sgPopupBuilderDeactivateNonce', 'ajaxNonce');
	$popupId = (int)$_POST['popupId'];
	$obj = SGPopup::findById($popupId);
	$options = json_decode($obj->getOptions(), true);
	$options['isActiveStatus'] = sgSanitizeAjaxField($_POST['popupStatus']);
	$obj->setOptions(json_encode($options));
	$obj->save();
}
add_action('wp_ajax_change_popup_status', 'sgChangePopupStatus');

function savePopupPreviewData() {
	check_ajax_referer('popup-builder-ajax', 'ajaxNonce');

	$formSerializedData = $_POST['popupDta'];
	if(get_option('popupPreviewId')) {
		$id = (int)get_option('popupPreviewId');

		if($id == 0 || !$id) {
			return;
		}

		require_once(SG_APP_POPUP_CLASSES.'/SGPopup.php');
		$delete = SGPopup::delete($id);
		if(!$delete) {
			delete_option('popupPreviewId');
		}

		$args = array('popupId'=> $id);
		do_action('sgPopupDelete', $args);
	}

	parse_str($formSerializedData, $popupPreviewPostData);
	$popupPreviewPostData['allPagesStatus'] = '';
	$popupPreviewPostData['allPostsStatus'] = '';
	$popupPreviewPostData['allCustomPostsStatus'] = '';
	$popupPreviewPostData['onScrolling'] = '';
	$popupPreviewPostData['inActivityStatus'] = '';
	$popupPreviewPostData['popup-timer-status'] = '';
	$popupPreviewPostData['popup-schedule-status'] = '';
	$popupPreviewPostData['sg-user-status'] = '';
	$popupPreviewPostData['countryStatus'] = '';
	$popupPreviewPostData['forMobile'] = '';
	$popupPreviewPostData['openMobile'] = '';
	$popupPreviewPostData['hidden_popup_number'] = '';
	$popupPreviewPostData['repeatPopup'] = '';
	$_POST += $popupPreviewPostData;

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
		'showAllPages' => array(),
		'showAllPosts' => array(),
		'showAllCustomPosts' => array(),
		'allSelectedPages' => array(),
		'allSelectedPosts' => array(),
		'allSelectedCustomPosts' => array(),
		'allSelectedCategories'=> array(),
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
	$mailchimp = stripslashes(sgSanitize('sg_popup_mailchimp'));
	$aweber = stripslashes(sgSanitize('sg_popup_aweber'));
	$exitIntent = stripslashes(sgSanitize('sg-exit-intent'));
	$type = sgSanitize('type');

	if($type == 'mailchimp') {

		$mailchimpOptions = array(
			'mailchimp-disable-double-optin' => sgSanitize('mailchimp-disable-double-optin'),
			'mailchimp-list-id' => sgSanitize('mailchimp-list-id'),
			'sg-mailchimp-form' => stripslashes(sgSanitize('sg-mailchimp-form')),
			'mailchimp-required-error-message' => sgSanitize('mailchimp-required-error-message'),
			'mailchimp-email-validate-message' => sgSanitize('mailchimp-email-validate-message'),
			'mailchimp-error-message' => sgSanitize('mailchimp-error-message'),
			'mailchimp-submit-button-bgcolor' => sgSanitize('mailchimp-submit-button-bgcolor'),
			'mailchimp-form-aligment' => sgSanitize('mailchimp-form-aligment'),
			'mailchimp-label-aligment' => sgSanitize('mailchimp-label-aligment'),
			'mailchimp-success-message' => sgSanitize('mailchimp-success-message'),
			'mailchimp-only-required' => sgSanitize('mailchimp-only-required'),
			'mailchimp-show-form-to-top' => sgSanitize('mailchimp-show-form-to-top'),
			'mailchimp-label-color' => sgSanitize('mailchimp-label-color'),
			'mailchimp-input-width' => sgSanitize('mailchimp-input-width'),
			'mailchimp-input-height' => sgSanitize('mailchimp-input-height'),
			'mailchimp-input-border-radius' => sgSanitize('mailchimp-input-border-radius'),
			'mailchimp-input-border-width' => sgSanitize('mailchimp-input-border-width'),
			'mailchimp-input-border-color' => sgSanitize('mailchimp-input-border-color'),
			'mailchimp-input-bg-color' => sgSanitize('mailchimp-input-bg-color'),
			'mailchimp-input-text-color' => sgSanitize('mailchimp-input-text-color'),
			'mailchimp-submit-width' => sgSanitize('mailchimp-submit-width'),
			'mailchimp-submit-height' => sgSanitize('mailchimp-submit-height'),
			'mailchimp-submit-border-width' => sgSanitize('mailchimp-submit-border-width'),
			'mailchimp-submit-border-radius' => sgSanitize('mailchimp-submit-border-radius'),
			'mailchimp-submit-border-color' => sgSanitize('mailchimp-submit-border-color'),
			'mailchimp-submit-color' => sgSanitize('mailchimp-submit-color'),
			'mailchimp-submit-title' => sgSanitize('mailchimp-submit-title'),
			'mailchimp-email-label' => sgSanitize('mailchimp-email-label'),
			'mailchimp-indicates-required-fields' => sgSanitize('mailchimp-indicates-required-fields'),
			'mailchimp-asterisk-label' => sgSanitize('mailchimp-asterisk-label'),
			'mailchimp-success-behavior' => sgSanitize('mailchimp-success-behavior'),
			'mailchimp-success-redirect-url' => sgSanitize('mailchimp-success-redirect-url'),
			'mailchimp-success-popups-list' => sgSanitize('mailchimp-success-popups-list'),
			'mailchimp-success-redirect-new-tab' => sgSanitize('mailchimp-success-redirect-new-tab'),
			'mailchimp-close-popup-already-subscribed' => sgSanitize('mailchimp-close-popup-already-subscribed')
		);

		$options['mailchimpOptions'] = json_encode($mailchimpOptions);
	}

	if($type == 'aweber') {
		$aweberOptions = array(
			'sg-aweber-webform' => sgSanitize('sg-aweber-webform'),
			'sg-aweber-list' => sgSanitize('sg-aweber-list'),
			'aweber-custom-success-message' => sgSanitize('aweber-custom-success-message'),
			'aweber-success-message' => sgSanitize('aweber-success-message'),
			'aweber-custom-invalid-email-message' => sgSanitize('aweber-custom-invalid-email-message'),
			'aweber-invalid-email' => sgSanitize('aweber-invalid-email'),
			'aweber-custom-error-message' => sgSanitize('aweber-custom-error-message'),
			'aweber-error-message' => sgSanitize('aweber-error-message'),
			'aweber-custom-subscribed-message' => sgSanitize('aweber-custom-subscribed-message'),
			'aweber-already-subscribed-message' => sgSanitize('aweber-already-subscribed-message'),
			'aweber-validate-email-message' => sgSanitize('aweber-validate-email-message'),
			'aweber-required-message' => sgSanitize('aweber-required-message'),
			'aweber-success-behavior' => sgSanitize('aweber-success-behavior'),
			'aweber-success-redirect-url' => sgSanitize('aweber-success-redirect-url'),
			'aweber-success-popups-list' => sgSanitize('aweber-success-popups-list'),
			'aweber-success-redirect-new-tab' => sgSanitize('aweber-success-redirect-new-tab')
		);
		$options['aweberOptions'] = json_encode($aweberOptions);
	}


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
		'mailchimp' => $mailchimp,
		'aweber' => $aweber,
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

	$popupName = "SG".sanitize_text_field(ucfirst(strtolower($popupPreviewPostData['type'])));
	$popupClassName = $popupName."Popup";
	$classPath = SG_APP_POPUP_PATH;

	if($type == 'mailchimp' || $type == 'aweber') {

		$currentActionName1 = IntegrateExternalSettings::getCurrentPopupAppPaths($type);
		$classPath = $currentActionName1['app-path'];
	}

	require_once($classPath ."/classes/".$popupClassName.".php");

	if ($id == "") {
		global $wpdb;

		call_user_func(array($popupClassName, 'create'), $data);

		$lastId = $wpdb->get_var("SELECT LAST_INSERT_ID() FROM ".  $wpdb->prefix."sg_popup");
		$postData['saveMod'] = '';
		$postData['popupId'] = $lastId;
		$extensionManagerObj = new SGPBExtensionManager();
		$extensionManagerObj->setPostData($postData);
		$extensionManagerObj->save();
		update_option('popupPreviewId', $lastId);
		setOptionPopupType($lastId, $type);
		echo $lastId;
		die();
	}

	die();
}

add_action('wp_ajax_save_popup_preview_data', 'savePopupPreviewData');

