<?php
$extensionManagerObj = new SGPBExtensionManager();

$popupType = @sanitize_text_field($_GET['type']);
if (!$popupType) {
	$popupType = 'html';
}
$popupCount = get_option('SGPBMaxOpenCount');

//Get current paths for popup, for addons it different
$paths = IntegrateExternalSettings::getCurrentPopupAppPaths($popupType);
//Get current form action, for addons it different
$currentActionName = IntegrateExternalSettings::getCurrentPopupAdminPostActionName($popupType);

$popupAppPath = $paths['app-path'];
$popupFilesPath = $paths['files-path'];

$popupName = "SG".ucfirst(strtolower($popupType));
$popupClassName = $popupName."Popup";
require_once($popupAppPath ."/classes/".$popupClassName.".php");
$obj = new $popupClassName();

global $removeOptions;
$removeOptions = $obj->getRemoveOptions();

if (isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	$result = call_user_func(array($popupClassName, 'findById'), $id);
	if (!$result) {
		$redirectUrl = add_query_arg( array(
			'type'  => $popupType,
		), SG_APP_POPUP_ADMIN_URL."admin.php?page=edit-popup");

		wp_safe_redirect($redirectUrl);
	}

	switch ($popupType) {
		case 'iframe':
			$sgPopupDataIframe = $result->getUrl();
			break;
		case 'video':
			$sgPopupDataVideo = $result->getRealUrl();
			$sgVideoOptions = $result->getVideoOptions();
			break;
		case 'image':
			$sgPopupDataImage = $result->getUrl();
			break;
		case 'html':
			//We cannot escape this input because the data is raw HTML
			$sgPopupDataHtml = $result->getContent();
			break;
		case 'fblike':
			//We cannot escape this input because the data is raw HTML
			$sgPopupDataFblike = $result->getContent();
			$sgFlikeOptions = $result->getFblikeOptions();
			break;
		case 'shortcode':
			$sgPopupDataShortcode = $result->getShortcode();
			break;
		case 'ageRestriction':
			//We cannot escape this input because the data is raw HTML
			$sgPopupAgeRestriction = ($result->getContent());
			$sgYesButton = sgSafeStr($result->getYesButton());
			$sgNoButton = sgSafeStr($result->getNoButton());
			$sgRestrictionUrl = sgSafeStr($result->getRestrictionUrl());
			break;
		case 'countdown':
			$sgCoundownContent = $result->getCountdownContent();
			$countdownOptions = json_decode(sgSafeStr($result->getCountdownOptions()),true);
			$sgCountdownNumbersBgColor = $countdownOptions['countdownNumbersBgColor'];
			$sgCountdownNumbersTextColor = $countdownOptions['countdownNumbersTextColor'];
			$sgDueDate = $countdownOptions['sg-due-date'];
			@$sgGetCountdownType = $countdownOptions['sg-countdown-type'];
			$sgCountdownLang = $countdownOptions['counts-language'];
			@$sgCountdownPosition = $countdownOptions['coundown-position'];
			@$sgSelectedTimeZone = $countdownOptions['sg-time-zone'];
			@$sgCountdownAutoclose = $countdownOptions['countdown-autoclose'];
			break;
		case 'social':
			$sgSocialContent = ($result->getSocialContent());
			$sgSocialButtons = sgSafeStr($result->getButtons());
			$sgSocialOptions = sgSafeStr($result->getSocialOptions());
			break;
		case 'exitIntent':
			$sgExitIntentContent = $result->getContent();
			$exitIntentOptions = $result->getExitIntentOptions();
			break;
		case 'subscription':
			$sgSunbscriptionContent = $result->getContent();
			$subscriptionOptions = $result->getSubscriptionOptions();
			break;
		case 'contactForm':
			$params = $result->getParams();
			$sgContactFormContent = $result->getContent();
			break;
	}

	$title = $result->getTitle();
	$jsonData = json_decode($result->getOptions(), true);
	$sgEscKey = @$jsonData['escKey'];
	$sgScrolling = @$jsonData['scrolling'];
	$sgDisablePageScrolling = @$jsonData['disable-page-scrolling'];
	$sgScaling = @$jsonData['scaling'];
	$sgCloseButton = @$jsonData['closeButton'];
	$sgReposition = @$jsonData['reposition'];
	$sgOverlayClose = @$jsonData['overlayClose'];
	$sgReopenAfterSubmission = @$jsonData['reopenAfterSubmission'];
	$sgOverlayColor = @$jsonData['sgOverlayColor'];
	$sgContentBackgroundColor = @$jsonData['sg-content-background-color'];
	$sgContentClick = @$jsonData['contentClick'];
	$sgContentClickBehavior = @$jsonData['content-click-behavior'];
	$sgClickRedirectToUrl = @$jsonData['click-redirect-to-url'];
	$sgRedirectToNewTab = @$jsonData['redirect-to-new-tab'];
	$sgOpacity = @$jsonData['opacity'];
	$sgPopupBackgroundOpacity = @$jsonData['popup-background-opacity'];
	$sgPopupFixed = @$jsonData['popupFixed'];
	$sgFixedPostion = @$jsonData['fixedPostion'];
	$sgOnScrolling = @$jsonData['onScrolling'];
	$sgInActivityStatus = @$jsonData['inActivityStatus'];
	$sgInactivityTimout = @$jsonData['inactivity-timout'];
	$beforeScrolingPrsent = @$jsonData['beforeScrolingPrsent'];
	$duration = @$jsonData['duration'];
	$delay = @$jsonData['delay'];

	$sgCloseButtonDelay = @$jsonData['buttonDelayValue'];
	$sgTheme3BorderColor = @$jsonData['sgTheme3BorderColor'];
	$sgTheme3BorderRadius = @$jsonData['sgTheme3BorderRadius'];
	$sgThemeCloseText = @$jsonData['theme-close-text'];
	$effect =@$jsonData['effect'];
	$sgInitialWidth = @$jsonData['initialWidth'];
	$sgInitialHeight = @$jsonData['initialHeight'];
	$sgWidth = @$jsonData['width'];
	$sgHeight = @$jsonData['height'];
	$sgPopupDimensionMode = @$jsonData['popup-dimension-mode'];
	$sgPopupResponsiveDimensionMeasure = @$jsonData['popup-responsive-dimension-measure'];
	$sgMaxWidth = @$jsonData['maxWidth'];
	$sgMaxHeight = @$jsonData['maxHeight'];
	$sgForMobile = @$jsonData['forMobile'];
	$sgOpenOnMobile = @$jsonData['openMobile'];
	$sgAllPagesStatus = @$jsonData['allPagesStatus'];
	$sgAllPostsStatus = @$jsonData['allPostsStatus'];
	$sgAllCustomPostsStatus = @$jsonData['allCustomPostsStatus'];
	$sgPostsAllCategories = @$jsonData['posts-all-categories'];
	$sgRepeatPopup = @$jsonData['repeatPopup'];
	$sgRepetitivePopup = @$jsonData['repetitivePopup'];
	$sgPopupAppearNumberLimit = @$jsonData['popup-appear-number-limit'];
	$sgRepetitivePopupPeriod = @$jsonData['repetitivePopupPeriod'];
	$sgPopupCookiePageLevel = @$jsonData['save-cookie-page-level'];
	$sgDisablePopup = @$jsonData['disablePopup'];
	$sgDisablePopupOverlay = @$jsonData['disablePopupOverlay'];
	$sgPopupClosingTimer = @$jsonData['popupClosingTimer'];
	$sgAutoClosePopup = @$jsonData['autoClosePopup'];
	$sgRandomPopup = @$jsonData['randomPopup'];
	$sgPopupOpenSound = @$jsonData['popupOpenSound'];
	$sgPopupOpenSoundFile = @$jsonData['popupOpenSoundFile'];
	$sgPopupContentBgImage = @$jsonData['popupContentBgImage'];
	$sgPopupContentBgImageUrl = @$jsonData['popupContentBgImageUrl'];
	$sgPopupContentBackgroundSize = @$jsonData['popupContentBackgroundSize'];
	$sgPopupContentBackgroundRepeat = @$jsonData['popupContentBackgroundRepeat'];
	$sgCountryStatus = @$jsonData['countryStatus'];
	$sgAllSelectedPages = @$jsonData['allSelectedPages'];
	$sgAllSelectedCustomPosts = @$jsonData['allSelectedCustomPosts'];
	$sgAllPostStatus = @$jsonData['showAllPosts'];
	$sgAllSelectedPosts = @$jsonData['allSelectedPosts'];
	$sgAllowCountries = @$jsonData['allowCountries'];
	$sgAllPages = @$jsonData['showAllPages'];
	$sgAllPosts = @$jsonData['showAllPosts'];
	$sgAllCustomPosts = @$jsonData['showAllCustomPosts'];
	$sgAllCustomPostsType = @$jsonData['all-custom-posts'];
	$sgLogedUser = @$jsonData['loggedin-user'];
	$sgUserSeperate = @$jsonData['sg-user-status'];
	$sgCountryName = @$jsonData['countryName'];
	$sgCountryIso = @$jsonData['countryIso'];
	$sgPopupTimerStatus = @$jsonData['popup-timer-status'];
	$sgPopupScheduleStatus = @$jsonData['popup-schedule-status'];
	$sgPopupScheduleStartWeeks = @$jsonData['schedule-start-weeks'];
	$sgPopupScheduleStartTime = @$jsonData['schedule-start-time'];
	$sgPopupScheduleEndTime = @$jsonData['schedule-end-time'];
	$sgPopupFinishTimer = @$jsonData['popup-finish-timer'];
	$sgPopupStartTimer = @$jsonData['popup-start-timer'];
	$sgColorboxTheme = @$jsonData['theme'];
	$sgOverlayCustomClasss = @$jsonData['sgOverlayCustomClasss'];
	$sgContentCustomClasss = @$jsonData['sgContentCustomClasss'];
	$sgPopupZIndex = @$jsonData['popup-z-index'];
	$sgPopupContentPadding = @$jsonData['popup-content-padding'];
	$sgOnceExpiresTime = @$jsonData['onceExpiresTime'];
	$sgRestrictionAction = @$jsonData['restrictionAction'];
	$yesButtonBackgroundColor = @sgSafeStr($jsonData['yesButtonBackgroundColor']);
	$noButtonBackgroundColor = @sgSafeStr($jsonData['noButtonBackgroundColor']);
	$yesButtonTextColor = @sgSafeStr($jsonData['yesButtonTextColor']);
	$noButtonTextColor = @sgSafeStr($jsonData['noButtonTextColor']);
	$yesButtonRadius = @sgSafeStr($jsonData['yesButtonRadius']);
	$noButtonRadius = @sgSafeStr($jsonData['noButtonRadius']);
	$sgRestrictionExpirationTime = @sgSafeStr($jsonData['sgRestrictionExpirationTime']);
	$sgRestrictionCookeSavingLevel = @sgSafeStr($jsonData['restrictionCookeSavingLevel']);
	$sgSocialOptions = json_decode(@$sgSocialOptions,true);
	$sgShareUrl = $sgSocialOptions['sgShareUrl'];
	$shareUrlType = @sgSafeStr($sgSocialOptions['shareUrlType']);
	$fbShareLabel = @sgSafeStr($sgSocialOptions['fbShareLabel']);
	$lindkinLabel = @sgSafeStr($sgSocialOptions['lindkinLabel']);
	$googLelabel = @sgSafeStr($sgSocialOptions['googLelabel']);
	$twitterLabel = @sgSafeStr($sgSocialOptions['twitterLabel']);
	$pinterestLabel = @sgSafeStr($sgSocialOptions['pinterestLabel']);
	$sgMailSubject = @sgSafeStr($sgSocialOptions['sgMailSubject']);
	$sgMailLable = @sgSafeStr($sgSocialOptions['sgMailLable']);
	$sgSocialButtons = json_decode(@$sgSocialButtons,true);
	$sgTwitterStatus = @sgSafeStr($sgSocialButtons['sgTwitterStatus']);
	$sgFbStatus = @sgSafeStr($sgSocialButtons['sgFbStatus']);
	$sgEmailStatus = @sgSafeStr($sgSocialButtons['sgEmailStatus']);
	$sgLinkedinStatus = @sgSafeStr($sgSocialButtons['sgLinkedinStatus']);
	$sgGoogleStatus = @sgSafeStr($sgSocialButtons['sgGoogleStatus']);
	$sgPinterestStatus = @sgSafeStr($sgSocialButtons['sgPinterestStatus']);
	$sgSocialTheme = @sgSafeStr($sgSocialOptions['sgSocialTheme']);
	$sgSocialButtonsSize = @sgSafeStr($sgSocialOptions['sgSocialButtonsSize']);
	$sgSocialLabel = @sgSafeStr($sgSocialOptions['sgSocialLabel']);
	$sgSocialShareCount = @sgSafeStr($sgSocialOptions['sgSocialShareCount']);
	$sgRoundButton = @sgSafeStr($sgSocialOptions['sgRoundButton']);
	$sgPushToBottom = @sgSafeStr($jsonData['pushToBottom']);
	$exitIntentOptions = json_decode(@$exitIntentOptions, true);
	$sgExitIntentTpype = @$exitIntentOptions['exit-intent-type'];
	$sgExitIntntExpire = @$exitIntentOptions['exit-intent-expire-time'];
	$sgExitIntentAlert = @$exitIntentOptions['exit-intent-alert'];
	$sgVideoOptions = json_decode(@$sgVideoOptions, true);
	$sgVideoAutoplay = $sgVideoOptions['video-autoplay'];
	$sgFlikeOptions = json_decode(@$sgFlikeOptions, true);
	$sgFblikeurl = @$sgFlikeOptions['fblike-like-url'];
	$sgFbLikeLayout = @$sgFlikeOptions['fblike-layout'];
	$sgFblikeDontShowShareButton = @$sgFlikeOptions['fblike-dont-show-share-button'];
	$sgFblikeClosePopupAfterLike = @$sgFlikeOptions['fblike-close-popup-after-like'];
	$subscriptionOptions = json_decode(@$subscriptionOptions, true);
	$sgSubsFirstNameStatus = $subscriptionOptions['subs-first-name-status'];
	$sgSubsLastNameStatus = $subscriptionOptions['subs-last-name-status'];
	$sgSubscriptionEmail = @$subscriptionOptions['subscription-email'];
	$sgSubsFirstName = @$subscriptionOptions['subs-first-name'];
	$sgSubsLastName = @$subscriptionOptions['subs-last-name'];
	$sgSubsButtonBgColor = @$subscriptionOptions['subs-button-bgColor'];
	$sgSubsBtnWidth = @$subscriptionOptions['subs-btn-width'];
	$sgSubsBtnHeight = @$subscriptionOptions['subs-btn-height'];
	$sgSubsTextHeight = @$subscriptionOptions['subs-text-height'];
	$sgSubsBtnTitle = @$subscriptionOptions['subs-btn-title'];
	$sgSubsTextInputBgColor = @$subscriptionOptions['subs-text-input-bgColor'];
	$sgSubsTextBorderColor = @$subscriptionOptions['subs-text-borderColor'];
	$sgSubsTextWidth = @$subscriptionOptions['subs-text-width'];
	$sgSubsButtonColor = @$subscriptionOptions['subs-button-color'];
	$sgSubsInputsColor = @$subscriptionOptions['subs-inputs-color'];
	$sgSubsPlaceholderColor = @$subscriptionOptions['subs-placeholder-color'];
	$sgSubsValidateMessage = @$subscriptionOptions['subs-validation-message'];
	$sgSuccessMessage = @$subscriptionOptions['subs-success-message'];
	$sgSubsBtnProgressTitle = @$subscriptionOptions['subs-btn-progress-title'];
	$sgSubsTextBorderWidth = @$subscriptionOptions['subs-text-border-width'];
	$sgSubsSuccessBehavior = @$subscriptionOptions['subs-success-behavior'];
	$sgSubsSuccessRedirectUrl = @$subscriptionOptions['subs-success-redirect-url'];
	$sgSubsSuccessPopupsList = @$subscriptionOptions['subs-success-popups-list'];
	$sgSubsFirstNameRequired = @$subscriptionOptions['subs-first-name-required'];
	$sgSubsLastNameRequired = @$subscriptionOptions['subs-last-name-required'];
	$sgSubsSuccessRedirectNewTab = @$subscriptionOptions['subs-success-redirect-new-tab'];
	$contactFormOptions = json_decode(@$params, true);
	$sgContactNameLabel = @$contactFormOptions['contact-name'];
	$sgContactNameStatus = @$contactFormOptions['contact-name-status'];
	$sgShowFormToTop = @$contactFormOptions['show-form-to-top'];
	$sgContactNameRequired = @$contactFormOptions['contact-name-required'];
	$sgContactSubjectLabel = @$contactFormOptions['contact-subject'];
	$sgContactSubjectStatus = @$contactFormOptions['contact-subject-status'];
	$sgContactSubjectRequired = @$contactFormOptions['contact-subject-required'];
	$sgContactEmailLabel = @$contactFormOptions['contact-email'];
	$sgContactMessageLabel = @$contactFormOptions['contact-message'];
	$sgContactValidationMessage = @$contactFormOptions['contact-validation-message'];
	$sgContactSuccessMessage = @$contactFormOptions['contact-success-message'];
	$sgContactInputsWidth = @$contactFormOptions['contact-inputs-width'];
	$sgContactInputsHeight = @$contactFormOptions['contact-inputs-height'];
	$sgContactInputsBorderWidth = @$contactFormOptions['contact-inputs-border-width'];
	$sgContactTextInputBgcolor = @$contactFormOptions['contact-text-input-bgcolor'];
	$sgContactTextBordercolor = @$contactFormOptions['contact-text-bordercolor'];
	$sgContactInputsColor = @$contactFormOptions['contact-inputs-color'];
	$sgContactPlaceholderColor = @$contactFormOptions['contact-placeholder-color'];
	$sgContactBtnWidth = @$contactFormOptions['contact-btn-width'];
	$sgContactBtnHeight = @$contactFormOptions['contact-btn-height'];
	$sgContactBtnTitle = @$contactFormOptions['contact-btn-title'];
	$sgContactBtnProgressTitle = @$contactFormOptions['contact-btn-progress-title'];
	$sgContactButtonBgcolor = @$contactFormOptions['contact-button-bgcolor'];
	$sgContactButtonColor = @$contactFormOptions['contact-button-color'];
	$sgContactAreaWidth = @$contactFormOptions['contact-area-width'];
	$sgContactAreaHeight = @$contactFormOptions['contact-area-height'];
	$sgContactResize = @$contactFormOptions['sg-contact-resize'];
	$sgContactValidateEmail = @$contactFormOptions['contact-validate-email'];
	$sgContactResiveEmail = @$contactFormOptions['contact-receive-email'];
	$sgContactFailMessage = @$contactFormOptions['contact-fail-message'];
	$sgContactSuccessBehavior = @$contactFormOptions['contact-success-behavior'];
	$sgContactSuccessRedirectUrl = @$contactFormOptions['contact-success-redirect-url'];
	$sgContactSuccessPopupsList = @$contactFormOptions['contact-success-popups-list'];
	$sgDontShowContentToContactedUser = @$contactFormOptions['dont-show-content-to-contacted-user'];
	$sgContactSuccessFrequencyDays = @$contactFormOptions['contact-success-frequency-days'];
	$sgContactSuccessRedirectNewTab = @$contactFormOptions['contact-success-redirect-new-tab'];
}

$dataPopupId = @$id;
/* For layze loading get selected data */
if(!isset($id)) {
	$dataPopupId = "-1";
}

/* FREE options default values */
$sgPopup = array(
	'escKey'=> true,
	'closeButton' => true,
	'scrolling'=> true,
	'disable-page-scrolling'=> true,
	'scaling'=> false,
	'opacity'=> 0.8,
	'popup-background-opacity'=> 1,
	'reposition' => true,
	'width' => '640px',
	'height' => '480px',
	'popup-dimension-mode' => 'customMode',
	'popup-responsive-dimension-measure' => 'auto',
	'initialWidth' => '300',
	'initialHeight' => '100',
	'maxWidth' => false,
	'maxHeight' => false,
	'overlayClose' => true,
	'reopenAfterSubmission' => false,
	'contentClick'=>false,
	'repetitivePopup' => false,
	'fixed' => false,
	'top' => false,
	'right' => false,
	'bottom' => false,
	'left' => false,
	'duration' => 1,
	'delay' => 0,
	'buttonDelayValue' => 0,
	'theme-close-text' => 'Close',
	'content-click-behavior' => 'close',
	'sgTheme3BorderRadius' => 0,
	'popup-z-index' => 9999,
	'popup-content-padding' => 0,
	'fblike-dont-show-share-button' => false,
	'fblike-close-popup-after-like' => false
);

$popupProDefaultValues = array(
	'closeType' => false,
	'onScrolling' => false,
	'inactivity-timout' => '0',
	'inActivityStatus' => false,
	'video-autoplay' => false,
	'forMobile' => false,
	'openMobile' => false,
	'repetPopup' => false,
	'disablePopup' => false,
	'disablePopupOverlay' => false,
	'redirect-to-new-tab' => true,
	'autoClosePopup' => false,
	'randomPopup' => false,
	'popupOpenSound' => false,
	'popupContentBgImage' => false,
	'popupOpenSoundFile' => SG_APP_POPUP_URL.'/files/lib/popupOpenSound.wav',
	'popupContentBgImageUrl' => '',
	'popupContentBackgroundSize' => 'cover',
	'popupContentBackgroundRepeat' => 'no-repeat',
	'fbStatus' => true,
	'twitterStatus' => true,
	'emailStatus' => true,
	'linkedinStatus' => true,
	'googleStatus' => true,
	'pinterestStatus' => true,
	'sgSocialLabel'=>true,
	'roundButtons'=>false,
	'sgShareUrl' => '',
	'pushToBottom' => true,
	'allPages' => "all",
	'allPosts' => "all",
	'allCustomPosts' => "all",
	'allPagesStatus' => false,
	'allPostsStatus' => false,
	'allCustomPostsStatus' => false,
	'onceExpiresTime' => 7,
	'popup-appear-number-limit' => 1,
	'repetitivePopupPeriod' => 60,
	'save-cookie-page-level' => false,
	'overlay-custom-classs' => 'sg-popup-overlay',
	'content-custom-classs' => 'sg-popup-content',
	'countryStatus' => false,
	'sg-user-status' => false,
	'allowCountries' => 'allow',
	'loggedin-user' => 'true',
	'sgRestrictionExpirationTime' => 365,
	'restrictionCookeSavingLevel' => '',
	'countdownNumbersTextColor' => '',
	'countdownNumbersBgColor' => '',
	'countDownLang' => 'English',
	'popup-timer-status' => false,
	'popup-schedule-status' => false,
	'countdown-position' => true,
	'countdown-autoclose' => true,
	'time-zone' => 'Etc/GMT',
	'due-date' => date('Y-m-d H:i', strtotime(' +1 day')),
	'popup-start-timer' => date('M d y H:i'),
	'schedule-start-time' => date("H:i"),
	'exit-intent-type' => "soft",
	'exit-intent-expire-time' => '1',
	'subs-first-name-status' => true,
	'subs-last-name-status' => true,
	'subscription-email' => 'Email *',
	'subs-first-name' => 'First name',
	'subs-last-name' => 'Last name',
	'subs-button-bgColor' => '#239744',
	'subs-button-color' => '#FFFFFF',
	'subs-text-input-bgColor' => '#FFFFFF',
	'subs-inputs-color' => '#000000',
	'subs-placeholder-color' => '#CCCCCC',
	'subs-text-borderColor' => '#CCCCCC',
	'subs-btn-title' => 'Subscribe',
	'subs-text-height' => '30px',
	'subs-btn-height' => '30px',
	'subs-text-width' => '200px',
	'subs-btn-width' => '200px',
	'subs-text-border-width' => '2px',
	'subs-success-message' =>'You have successfully subscribed to the newsletter',
	'subs-validation-message' => 'This field is required.',
	'subs-btn-progress-title' => 'Please wait...',
	'subs-success-behavior' => 'showMessage',
	'subs-success-redirect-url' => '',
	'subs-success-popups-list' => '',
	'subs-first-name-required' => '',
	'subs-last-name-required' => '',
	'subs-success-redirect-new-tab' => false,
	'contact-name' => 'Name *',
	'contact-name-required' => true,
	'contact-name-status' => true,
	'show-form-to-top' => false,
	'contact-subject-status' => true,
	'contact-subject-required' => true,
	'contact-email' => 'E-mail *',
	'contact-message' => 'Message *',
	'contact-subject' => 'Subject *',
	'contact-success-message' => 'Your message has been successfully sent.',
	'contact-btn-title' => 'Contact',
	'contact-validate-email' => 'Please enter a valid email.',
	'contact-receive-email' => get_option('admin_email'),
	'contact-fail-message' => 'Unable to send.',
	'contact-success-behavior' => 'showMessage',
	'contact-success-redirect-url' => '',
	'contact-success-popups-list' => 0,
	'dont-show-content-to-contacted-user' => '',
	'contact-success-frequency-days' => 365,
	'contact-success-redirect-new-tab' => false
);

$escKey = sgBoolToChecked($sgPopup['escKey']);
$closeButton = sgBoolToChecked($sgPopup['closeButton']);
$scrolling = sgBoolToChecked($sgPopup['scrolling']);
$disablePageScrolling = sgBoolToChecked($sgPopup['disable-page-scrolling']);
$scaling = sgBoolToChecked($sgPopup['scaling']);
$reposition	= sgBoolToChecked($sgPopup['reposition']);
$overlayClose = sgBoolToChecked($sgPopup['overlayClose']);
$reopenAfterSubmission = sgBoolToChecked($sgPopup['reopenAfterSubmission']);
$contentClick = sgBoolToChecked($sgPopup['contentClick']);
$repetitivePopup = sgBoolToChecked($sgPopup['repetitivePopup']);
$fblikeDontShowShareButton = sgBoolToChecked($sgPopup['fblike-dont-show-share-button']);
$fblikeClosePopupAfterLike = sgBoolToChecked($sgPopup['fblike-close-popup-after-like']);

$buttonDelayValue = $sgPopup['buttonDelayValue'];
$contentClickBehavior = $sgPopup['content-click-behavior'];
$theme3BorderRadius = $sgPopup['sgTheme3BorderRadius'];
$popupZIndex = $sgPopup['popup-z-index'];
$popupContentPadding = $sgPopup['popup-content-padding'];

$closeType = sgBoolToChecked($popupProDefaultValues['closeType']);
$onScrolling = sgBoolToChecked($popupProDefaultValues['onScrolling']);
$inActivityStatus = sgBoolToChecked($popupProDefaultValues['inActivityStatus']);
$userSeperate = sgBoolToChecked($popupProDefaultValues['sg-user-status']);
$forMobile = sgBoolToChecked($popupProDefaultValues['forMobile']);
$openMobile = sgBoolToChecked($popupProDefaultValues['openMobile']);
$popupTimerStatus = sgBoolToChecked($popupProDefaultValues['popup-timer-status']);
$popupScheduleStatus = sgBoolToChecked($popupProDefaultValues['popup-schedule-status']);
$repetPopup = sgBoolToChecked($popupProDefaultValues['repetPopup']);
$disablePopup = sgBoolToChecked($popupProDefaultValues['disablePopup']);
$disablePopupOverlay = sgBoolToChecked($popupProDefaultValues['disablePopupOverlay']);
$autoClosePopup = sgBoolToChecked($popupProDefaultValues['autoClosePopup']);
$randomPopup = sgBoolToChecked($popupProDefaultValues['randomPopup']);
$popupOpenSound = sgBoolToChecked($popupProDefaultValues['popupOpenSound']);
$popupContentBgImage = sgBoolToChecked($popupProDefaultValues['popupContentBgImage']);
$fbStatus = sgBoolToChecked($popupProDefaultValues['fbStatus']);
$twitterStatus = sgBoolToChecked($popupProDefaultValues['twitterStatus']);
$emailStatus = sgBoolToChecked($popupProDefaultValues['emailStatus']);
$linkedinStatus = sgBoolToChecked($popupProDefaultValues['linkedinStatus']);
$googleStatus = sgBoolToChecked($popupProDefaultValues['googleStatus']);
$pinterestStatus = sgBoolToChecked($popupProDefaultValues['pinterestStatus']);
$socialLabel = sgBoolToChecked($popupProDefaultValues['sgSocialLabel']);
$roundButtons = sgBoolToChecked($popupProDefaultValues['roundButtons']);
$countdownAutoclose = sgBoolToChecked($popupProDefaultValues['countdown-autoclose']);
$shareUrl = $popupProDefaultValues['sgShareUrl'];
$pushToBottom = sgBoolToChecked($popupProDefaultValues['pushToBottom']);
$allPages = $popupProDefaultValues['allPages'];
$allPosts = $popupProDefaultValues['allPosts'];
$allCustomPosts = $popupProDefaultValues['allCustomPosts'];
$allPagesStatus = sgBoolToChecked($popupProDefaultValues['allPagesStatus']);
$allPostsStatus = sgBoolToChecked($popupProDefaultValues['allPostsStatus']);
$allCustomPostsStatus = sgBoolToChecked($popupProDefaultValues['allCustomPostsStatus']);
$contactNameStatus = sgBoolToChecked($popupProDefaultValues['contact-name-status']);
$showFormToTop = sgBoolToChecked($popupProDefaultValues['show-form-to-top']);
$subsSuccessRedirectNewTab = sgBoolToChecked($popupProDefaultValues['subs-success-redirect-new-tab']);
$contactNameRequired = sgBoolToChecked($popupProDefaultValues['contact-name-required']);
$contactSubjectStatus = sgBoolToChecked($popupProDefaultValues['contact-subject-status']);
$contactSubjectRequired = sgBoolToChecked($popupProDefaultValues['contact-subject-required']);
$saveCookiePageLevel = sgBoolToChecked($popupProDefaultValues['save-cookie-page-level']);
$onceExpiresTime = $popupProDefaultValues['onceExpiresTime'];
$popupAppearNumberLimit = $popupProDefaultValues['popup-appear-number-limit'];
$repetitivePopupPeriod = $popupProDefaultValues['repetitivePopupPeriod'];
$countryStatus = sgBoolToChecked($popupProDefaultValues['countryStatus']);
$allowCountries = $popupProDefaultValues['allowCountries'];
$logedUser = $popupProDefaultValues['loggedin-user'];
$restrictionExpirationTime = $popupProDefaultValues['sgRestrictionExpirationTime'];
$restrictionCookeSavingLevel = sgBoolToChecked($popupProDefaultValues['restrictionCookeSavingLevel']);
$countdownNumbersTextColor = $popupProDefaultValues['countdownNumbersTextColor'];
$countdownNumbersBgColor = $popupProDefaultValues['countdownNumbersBgColor'];
$countdownLang = $popupProDefaultValues['countDownLang'];
$countdownPosition = $popupProDefaultValues['countdown-position'];
$timeZone = $popupProDefaultValues['time-zone'];
$dueDate = $popupProDefaultValues['due-date'];
$popupStartTimer = $popupProDefaultValues['popup-start-timer'];
$scheduleStartTime = $popupProDefaultValues['schedule-start-time'];
$inactivityTimout = $popupProDefaultValues['inactivity-timout'];
$exitIntentType = $popupProDefaultValues['exit-intent-type'];
$exitIntentExpireTime = $popupProDefaultValues['exit-intent-expire-time'];
$subsFirstNameStatus = sgBoolToChecked($popupProDefaultValues['subs-first-name-status']);
$subsLastNameStatus = sgBoolToChecked($popupProDefaultValues['subs-last-name-status']);
$subscriptionEmail = $popupProDefaultValues['subscription-email'];
$subsFirstName = $popupProDefaultValues['subs-first-name'];
$subsLastName = $popupProDefaultValues['subs-last-name'];
$subsButtonBgColor = $popupProDefaultValues['subs-button-bgColor'];
$subsButtonColor = $popupProDefaultValues['subs-button-color'];
$subsInputsColor = $popupProDefaultValues['subs-inputs-color'];
$subsBtnTitle = $popupProDefaultValues['subs-btn-title'];
$subsPlaceholderColor = $popupProDefaultValues['subs-placeholder-color'];
$subsTextHeight = $popupProDefaultValues['subs-text-height'];
$subsBtnHeight = $popupProDefaultValues['subs-btn-height'];
$subsSuccessMessage = $popupProDefaultValues['subs-success-message'];
$subsValidationMessage = $popupProDefaultValues['subs-validation-message'];
$subsTextWidth = $popupProDefaultValues['subs-text-width'];
$subsBtnWidth = $popupProDefaultValues['subs-btn-width'];
$subsBtnProgressTitle = $popupProDefaultValues['subs-btn-progress-title'];
$subsTextBorderWidth = $popupProDefaultValues['subs-text-border-width'];
$subsTextBorderColor = $popupProDefaultValues['subs-text-borderColor'];
$subsTextInputBgColor = $popupProDefaultValues['subs-text-input-bgColor'];
$subsSuccessBehavior = $popupProDefaultValues['subs-success-behavior'];
$subsSuccessPopupsList = $popupProDefaultValues['subs-success-popups-list'];
$subsSuccessRedirectUrl = $popupProDefaultValues['subs-success-redirect-url'];
$subsFirstNameRequired = $popupProDefaultValues['subs-first-name-required'];
$subsLastNameRequired = $popupProDefaultValues['subs-last-name-required'];
$contactName = $popupProDefaultValues['contact-name'];
$contactEmail = $popupProDefaultValues['contact-email'];
$contactMessage = $popupProDefaultValues['contact-message'];
$contactSubject = $popupProDefaultValues['contact-subject'];
$contactSuccessMessage = $popupProDefaultValues['contact-success-message'];
$contactBtnTitle = $popupProDefaultValues['contact-btn-title'];
$contactValidateEmail = $popupProDefaultValues['contact-validate-email'];
$contactResiveEmail = $popupProDefaultValues['contact-receive-email'];
$contactFailMessage = $popupProDefaultValues['contact-fail-message'];
$overlayCustomClasss = $popupProDefaultValues['overlay-custom-classs'];
$contentCustomClasss = $popupProDefaultValues['content-custom-classs'];
$contactSuccessBehavior = $popupProDefaultValues['contact-success-behavior'];
$contactSuccessRedirectUrl = $popupProDefaultValues['contact-success-redirect-url'];
$contactSuccessPopupsList = $popupProDefaultValues['contact-success-popups-list'];
$redirectToNewTab = $popupProDefaultValues['redirect-to-new-tab'];
$dontShowContentToContactedUser = sgBoolToChecked($popupProDefaultValues['dont-show-content-to-contacted-user']);
$contactSuccessFrequencyDays = $popupProDefaultValues['contact-success-frequency-days'];
$contactSuccessRedirectNewTab = $popupProDefaultValues['contact-success-redirect-new-tab'];
$popupOpenSoundFile = $popupProDefaultValues['popupOpenSoundFile'];
$popupContentBgImageUrl = $popupProDefaultValues['popupContentBgImageUrl'];
$popupContentBackgroundSize = $popupProDefaultValues['popupContentBackgroundSize'];
$popupContentBackgroundRepeat = $popupProDefaultValues['popupContentBackgroundRepeat'];

function sgBoolToChecked($var)
{
	return ($var?'checked':'');
}

function sgRemoveOption($option)
{
	global $removeOptions;
	return isset($removeOptions[$option]);
}

$width = $sgPopup['width'];
$height = $sgPopup['height'];
$popupDimensionMode = $sgPopup['popup-dimension-mode'];
$popupResponsiveDimensionMeasure = $sgPopup['popup-responsive-dimension-measure'];
$opacityValue = $sgPopup['opacity'];
$popupBackgroundOpacity = $sgPopup['popup-background-opacity'];
$top = $sgPopup['top'];
$right = $sgPopup['right'];
$bottom = $sgPopup['bottom'];
$left = $sgPopup['left'];
$initialWidth = $sgPopup['initialWidth'];
$initialHeight = $sgPopup['initialHeight'];
$maxWidth = $sgPopup['maxWidth'];
$maxHeight = $sgPopup['maxHeight'];
$deafultFixed = $sgPopup['fixed'];
$defaultDuration = $sgPopup['duration'];
$defaultDelay = $sgPopup['delay'];
$defaultButtonDelayValue = $sgPopup['buttonDelayValue'];
$themeCloseText = $sgPopup['theme-close-text'];

$sgCloseButton = @sgSetChecked($sgCloseButton, $closeButton);
$sgEscKey = @sgSetChecked($sgEscKey, $escKey);
$sgContentClick = @sgSetChecked($sgContentClick, $contentClick);
$sgOverlayClose = @sgSetChecked($sgOverlayClose, $overlayClose);
$sgReopenAfterSubmission = @sgSetChecked($sgReopenAfterSubmission, $reopenAfterSubmission);
$sgReposition = @sgSetChecked($sgReposition, $reposition);
$sgScrolling = @sgSetChecked($sgScrolling, $scrolling);
$sgDisablePageScrolling = @sgSetChecked($sgDisablePageScrolling, $disablePageScrolling);
$sgScaling = @sgSetChecked($sgScaling, $scaling);
$sgCountdownAutoclose = @sgSetChecked($sgCountdownAutoclose, $countdownAutoclose);
$sgFblikeDontShowShareButton = @sgSetChecked($sgFblikeDontShowShareButton, $fblikeDontShowShareButton);
$sgFblikeClosePopupAfterLike = @sgSetChecked($sgFblikeClosePopupAfterLike, $fblikeClosePopupAfterLike);

$sgCloseType = @sgSetChecked($sgCloseType, $closeType);
$sgOnScrolling = @sgSetChecked($sgOnScrolling, $onScrolling);
$sgInActivityStatus = @sgSetChecked($sgInActivityStatus, $inActivityStatus);
$sgForMobile = @sgSetChecked($sgForMobile, $forMobile);
$sgOpenOnMobile = @sgSetChecked($sgOpenOnMobile, $openMobile);
$sgPopupCookiePageLevel = @sgSetChecked($sgPopupCookiePageLevel, $saveCookiePageLevel);
$sgUserSeperate = @sgSetChecked($sgUserSeperate, $userSeperate);
$sgPopupTimerStatus = @sgSetChecked($sgPopupTimerStatus, $popupTimerStatus);
$sgPopupScheduleStatus = @sgSetChecked($sgPopupScheduleStatus, $popupScheduleStatus);
$sgRepeatPopup = @sgSetChecked($sgRepeatPopup, $repetPopup);
$sgRepetitivePopup = @sgSetChecked($sgRepetitivePopup, $repetitivePopup);
$sgDisablePopup = @sgSetChecked($sgDisablePopup, $disablePopup);
$sgDisablePopupOverlay = @sgSetChecked($sgDisablePopupOverlay, $disablePopupOverlay);
$sgAutoClosePopup = @sgSetChecked($sgAutoClosePopup, $autoClosePopup);
$sgRandomPopup = @sgSetChecked($sgRandomPopup, $randomPopup);
$sgPopupOpenSound = @sgSetChecked($sgPopupOpenSound, $popupOpenSound);
$sgPopupContentBgImage = @sgSetChecked($sgPopupContentBgImage, $popupContentBgImage);
$sgFbStatus = @sgSetChecked($sgFbStatus, $fbStatus);
$sgTwitterStatus = @sgSetChecked($sgTwitterStatus, $twitterStatus);
$sgEmailStatus = @sgSetChecked($sgEmailStatus, $emailStatus);
$sgLinkedinStatus = @sgSetChecked($sgLinkedinStatus, $linkedinStatus);
$sgGoogleStatus = @sgSetChecked($sgGoogleStatus, $googleStatus);
$sgPinterestStatus = @sgSetChecked($sgPinterestStatus, $pinterestStatus);
$sgRoundButtons = @sgSetChecked($sgRoundButton, $roundButtons);
$sgSocialLabel = @sgSetChecked($sgSocialLabel, $socialLabel);
$sgPopupFixed = @sgSetChecked($sgPopupFixed, $deafultFixed);
$sgPushToBottom = @sgSetChecked($sgPushToBottom, $pushToBottom);
$sgRestrictionCookeSavingLevel = @sgSetChecked($sgRestrictionCookeSavingLevel, $restrictionCookeSavingLevel);
$sgSubsFirstNameRequired = @sgSetChecked($sgSubsFirstNameRequired, $subsFirstNameRequired);
$sgSubsLastNameRequired = @sgSetChecked($sgSubsLastNameRequired, $subsLastNameRequired);
$sgSubsSuccessRedirectNewTab = @sgSetChecked($sgSubsSuccessRedirectNewTab, $subsSuccessRedirectNewTab);
$sgContactSuccessRedirectNewTab = @sgSetChecked($sgContactSuccessRedirectNewTab, $contactSuccessRedirectNewTab);

$sgAllPagesStatus = @sgSetChecked($sgAllPagesStatus, $allPagesStatus);
$sgAllPostsStatus = @sgSetChecked($sgAllPostsStatus, $allPostsStatus);
$sgAllCustomPostsStatus = @sgSetChecked($sgAllCustomPostsStatus, $allCustomPostsStatus);
$sgCountdownPosition = @sgSetChecked($sgCountdownPosition, $countdownPosition);
$sgVideoAutoplay = @sgSetChecked($sgVideoAutoplay, $videoAutoplay);
$sgSubsLastNameStatus = @sgSetChecked($sgSubsLastNameStatus, $subsLastNameStatus);
$sgSubsFirstNameStatus = @sgSetChecked($sgSubsFirstNameStatus, $subsFirstNameStatus);
$sgCountryStatus = @sgSetChecked($sgCountryStatus, $countryStatus);
/* Contact popup otions */
$sgContactNameStatus = @sgSetChecked($sgContactNameStatus, $contactNameStatus);
$sgContactNameRequired = @sgSetChecked($sgContactNameRequired, $contactNameRequired);
$sgContactSubjectStatus = @sgSetChecked($sgContactSubjectStatus, $contactSubjectStatus);
$sgContactSubjectRequired = @sgSetChecked($sgContactSubjectRequired, $contactSubjectRequired);
$sgShowFormToTop = @sgSetChecked($sgShowFormToTop, $showFormToTop);
$sgRedirectToNewTab = @sgSetChecked($sgRedirectToNewTab, $redirectToNewTab);
$sgDontShowContentToContactedUser = @sgSetChecked($sgDontShowContentToContactedUser, $dontShowContentToContactedUser);

function sgSetChecked($optionsParam,$defaultOption)
{
	if (isset($optionsParam)) {
		if ($optionsParam == '') {
			return '';
		}
		else {
			return 'checked';
		}
	}
	else {
		return $defaultOption;
	}
}

$sgTheme3BorderRadius = @sgGetValue($sgTheme3BorderRadius, $theme3BorderRadius);
$sgPopupOpenSoundFile = @sgGetValue($sgPopupOpenSoundFile, $popupOpenSoundFile);
$sgPopupContentBackgroundSize = @sgGetValue($sgPopupContentBackgroundSize, $popupContentBackgroundSize);
$sgPopupContentBackgroundRepeat = @sgGetValue($sgPopupContentBackgroundRepeat, $popupContentBackgroundRepeat);
$sgPopupContentBgImageUrl = @sgGetValue($sgPopupContentBgImageUrl, $popupContentBgImageUrl);
$sgOpacity = @sgGetValue($sgOpacity, $opacityValue);
$sgPopupBackgroundOpacity = @sgGetValue($sgPopupBackgroundOpacity, $popupBackgroundOpacity);
$sgWidth = @sgGetValue($sgWidth, $width);
$sgHeight = @sgGetValue($sgHeight, $height);
$sgPopupZIndex = @sgGetValue($sgPopupZIndex, $popupZIndex);
$sgPopupContentPadding = @sgGetValue($sgPopupContentPadding, $popupContentPadding);
$sgPopupDimensionMode = @sgGetValue($sgPopupDimensionMode, $popupDimensionMode);
$sgPopupResponsiveDimensionMeasure = @sgGetValue($sgPopupResponsiveDimensionMeasure, $popupResponsiveDimensionMeasure);
$sgInitialWidth = @sgGetValue($sgInitialWidth, $initialWidth);
$sgInitialHeight = @sgGetValue($sgInitialHeight, $initialHeight);
$sgMaxWidth = @sgGetValue($sgMaxWidth, $maxWidth);
$sgMaxHeight = @sgGetValue($sgMaxHeight, $maxHeight);
$sgThemeCloseText = @sgGetValue($sgThemeCloseText, $themeCloseText);
$duration = @sgGetValue($duration, $defaultDuration);
$sgOnceExpiresTime = @sgGetValue($sgOnceExpiresTime, $onceExpiresTime);
$sgPopupAppearNumberLimit = @sgGetValue($sgPopupAppearNumberLimit, $popupAppearNumberLimit);
$sgRepetitivePopupPeriod = @sgGetValue($sgRepetitivePopupPeriod, $repetitivePopupPeriod);
$delay = @sgGetValue($delay, $defaultDelay);
$sgCloseButtonDelay = @sgGetValue($sgCloseButtonDelay, $buttonDelayValue);

$sgInactivityTimout = @sgGetValue($sgInactivityTimout, $inactivityTimout);
$sgContentClickBehavior = @sgGetValue($sgContentClickBehavior, $contentClickBehavior);
$sgPopupStartTimer = @sgGetValue($sgPopupStartTimer, $popupStartTimer);
$sgPopupFinishTimer = @sgGetValue($sgPopupFinishTimer, '');
$sgPopupScheduleStartTime = @sgGetValue($sgPopupScheduleStartTime, $scheduleStartTime);
$sgPopupDataIframe = @sgGetValue($sgPopupDataIframe, '');
$sgShareUrl = @sgGetValue($sgShareUrl, $shareUrl);
$sgPopupDataHtml = @sgGetValue($sgPopupDataHtml, '');
$sgPopupDataImage = @sgGetValue($sgPopupDataImage, '');
$sgAllowCountries = @sgGetValue($sgAllowCountries, $allowCountries);
$sgAllPages = @sgGetValue($sgAllPages, $allPages);
$sgAllPosts = @sgGetValue($sgAllPosts, $allPosts);
$sgAllCustomPosts = @sgGetValue($sgAllCustomPosts, $allCustomPosts);
$sgLogedUser = @sgGetValue($sgLogedUser, $logedUser);
$sgRestrictionExpirationTime = @sgGetValue($sgRestrictionExpirationTime, $restrictionExpirationTime);
$sgCountdownNumbersTextColor = @sgGetValue($sgCountdownNumbersTextColor, $countdownNumbersTextColor);
$sgCountdownNumbersBgColor = @sgGetValue($sgCountdownNumbersBgColor, $countdownNumbersBgColor);
$sgCountdownLang = @sgGetValue($sgCountdownLang, $countdownLang);
$sgSelectedTimeZone  = @sgGetValue($sgSelectedTimeZone, $timeZone);
$sgDueDate = @sgGetValue($sgDueDate, $dueDate);
$sgExitIntentTpype = @sgGetValue($sgExitIntentTpype, $exitIntentType);
$sgExitIntntExpire = @sgGetValue($sgExitIntntExpire, $exitIntentExpireTime);
$sgSubsTextWidth = @sgGetValue($sgSubsTextWidth, $subsTextWidth);
$sgSubsBtnWidth = @sgGetValue($sgSubsBtnWidth, $subsBtnWidth);
$sgSubsTextInputBgColor = @sgGetValue($sgSubsTextInputBgColor, $subsTextInputBgColor);
$sgSubsButtonBgColor  = @sgGetValue($sgSubsButtonBgColor, $subsButtonBgColor);
$sgSubsTextBorderColor = @sgGetValue($sgSubsTextBorderColor, $subsTextBorderColor);
$sgSubscriptionEmail = @sgGetValue($sgSubscriptionEmail, $subscriptionEmail);
$sgSubsFirstName = @sgGetValue($sgSubsFirstName, $subsFirstName);
$sgSubsLastName = @sgGetValue($sgSubsLastName, $subsLastName);
$sgSubsButtonColor = @sgGetValue($sgSubsButtonColor, $subsButtonColor);
$sgSubsInputsColor = @sgGetValue($sgSubsInputsColor, $subsInputsColor);
$sgSubsBtnTitle = @sgGetValue($sgSubsBtnTitle, $subsBtnTitle);
$sgSubsPlaceholderColor = @sgGetValue($sgSubsPlaceholderColor, $subsPlaceholderColor);
$sgSubsTextHeight = @sgGetValue($sgSubsTextHeight, $subsTextHeight);
$sgSubsBtnHeight = @sgGetValue($sgSubsBtnHeight, $subsBtnHeight);
$sgSuccessMessage = @sgGetValue($sgSuccessMessage, $subsSuccessMessage);
$sgSubsValidateMessage = @sgGetValue($sgSubsValidateMessage, $subsValidationMessage);
$sgSubsBtnProgressTitle = @sgGetValue($sgSubsBtnProgressTitle, $subsBtnProgressTitle);
$sgSubsTextBorderWidth = @sgGetValue($sgSubsTextBorderWidth, $subsTextBorderWidth);
$sgSubsSuccessBehavior = @sgGetValue($sgSubsSuccessBehavior, $subsSuccessBehavior);
$sgSubsSuccessRedirectUrl = @sgGetValue($sgSubsSuccessRedirectUrl, $subsSuccessRedirectUrl);
$sgSubsSuccessPopupsList = @sgGetValue($sgSubsSuccessPopupsList, $subsSuccessPopupsList);
$sgContactNameLabel = @sgGetValue($sgContactNameLabel, $contactName);
$sgContactSubjectLabel = @sgGetValue($sgContactSubjectLabel, $contactSubject);
$sgContactEmailLabel = @sgGetValue($sgContactEmailLabel, $contactEmail);
$sgContactMessageLabel = @sgGetValue($sgContactMessageLabel, $contactMessage);
$sgContactValidationMessage = @sgGetValue($sgContactValidationMessage, $subsValidationMessage);
$sgContactSuccessMessage = @sgGetValue($sgContactSuccessMessage, $contactSuccessMessage);
$sgContactInputsWidth = @sgGetValue($sgContactInputsWidth, $subsTextWidth);
$sgContactInputsHeight = @sgGetValue($sgContactInputsHeight, $subsTextHeight);
$sgContactInputsBorderWidth = @sgGetValue($sgContactInputsBorderWidth, $subsTextBorderWidth);
$sgContactTextInputBgcolor = @sgGetValue($sgContactTextInputBgcolor, $subsTextInputBgColor);
$sgContactTextBordercolor = @sgGetValue($sgContactTextBordercolor, $subsTextBorderColor);
$sgContactInputsColor = @sgGetValue($sgContactInputsColor, $subsInputsColor);
$sgContactPlaceholderColor = @sgGetValue($sgContactPlaceholderColor, $subsPlaceholderColor);
$sgContactBtnWidth = @sgGetValue($sgContactBtnWidth, $subsBtnWidth);
$sgContactBtnHeight = @sgGetValue($sgContactBtnHeight, $subsBtnHeight);
$sgContactBtnTitle = @sgGetValue($sgContactBtnTitle, $contactBtnTitle);
$sgContactBtnProgressTitle = @sgGetValue($sgContactBtnProgressTitle, $subsBtnProgressTitle);
$sgContactButtonBgcolor = @sgGetValue($sgContactButtonBgcolor, $subsButtonBgColor);
$sgContactButtonColor = @sgGetValue($sgContactButtonColor, $subsButtonColor);
$sgContactAreaWidth = @sgGetValue($sgContactAreaWidth, $subsTextWidth);
$sgContactAreaHeight = @sgGetValue($sgContactAreaHeight, '');
$sgContactValidateEmail = @sgGetValue($sgContactValidateEmail, $contactValidateEmail);
$sgContactResiveEmail = @sgGetValue($sgContactResiveEmail, $contactResiveEmail);
$sgContactFailMessage = @sgGetValue($sgContactFailMessage, $contactFailMessage);
$sgOverlayCustomClasss = @sgGetValue($sgOverlayCustomClasss, $overlayCustomClasss);
$sgContentCustomClasss = @sgGetValue($sgContentCustomClasss, $contentCustomClasss);
$sgContactSuccessBehavior = @sgGetValue($sgContactSuccessBehavior, $contactSuccessBehavior);
$sgContactSuccessRedirectUrl = @sgGetValue($sgContactSuccessRedirectUrl, $contactSuccessRedirectUrl);
$sgContactSuccessPopupsList = @sgGetValue($sgContactSuccessPopupsList, $contactSuccessPopupsList);
$sgContactSuccessFrequencyDays = @sgGetValue($sgContactSuccessFrequencyDays, $contactSuccessFrequencyDays);
$sgAllSelectedPages = @sgGetValue($sgAllSelectedPages, array());
$sgAllSelectedPosts = @sgGetValue($sgAllSelectedPosts, array());
$sgAllSelectedCustomPosts = @sgGetValue($sgAllSelectedCustomPosts, array());

function sgGetValue($getedVal,$defValue)
{
	if (!isset($getedVal)) {
		return $defValue;
	}
	else {
		return $getedVal;
	}
}

$radioElements = array(
	array(
		'name'=>'shareUrlType',
		'value'=>'activeUrl',
		'additionalHtml'=>''.'<span>'.'Use active URL'.'</span></span>
							<span class="span-width-static"></span><span class="dashicons dashicons-info scrollingImg sameImageStyle sg-active-url"></span><span class="info-active-url samefontStyle">If this option is active Share URL will be current page URL.</span>'
	),
	array(
		'name'=>'shareUrlType',
		'value'=>'shareUrl',
		'additionalHtml'=>''.'<span>'.'Share url'.'</span></span>'.' <input class="input-width-static sg-active-url" type="text" name="sgShareUrl" value="'.@$sgShareUrl.'">'
	)
);

$countriesRadio = array(
	array(
		'name'=>'allowCountries',
		'value'=>'allow',
		'additionalHtml'=>'<span class="countries-radio-text allow-countries">allow</span>',
		'newline' => false
	),
	array(
		'name'=>'allowCountries',
		'value'=>'disallow',
		'additionalHtml'=>'<span class="countries-radio-text">disallow</span>',
		'newline' => true
	)
);

$usersGroup = array(
	array(
		'name'=>'loggedin-user',
		'value'=>'true',
		'additionalHtml'=>'<span id="sg-radio-logged-in" class="countries-radio-text allow-countries">logged in</span></label>',
		'newline' => false
	),
	array(
		'name'=>'loggedin-user',
		'value'=>'false',
		'additionalHtml'=>'<span id="sg-radio-not-logged-in" class="countries-radio-text">not logged in</span></label>',
		'newline' => true
	)
);

function sgCreateRadioElements($radioElements,$checkedValue)
{
	$content = '';
	for ($i = 0; $i < count($radioElements); $i++) {
		$checked = '';
		$radioElement = @$radioElements[$i];
		$name = @$radioElement['name'];
		$label = @$radioElement['label'];
		$value = @$radioElement['value'];
		$additionalHtml = @$radioElement['additionalHtml'];
		if ($checkedValue == $value) {
			$checked = 'checked';
		}
		$content .= '<span  class="liquid-width"><input class="radio-btn-fix" type="radio" name="'.esc_attr($name).'" value="'.esc_attr($value).'" '.esc_attr($checked).'>';
		$content .= $additionalHtml."<br>";
	}
	return $content;
}

$contentClickOptions = array(
	array(
		"title" => "close Popup:",
		"value" => "close",
		"info" => ""
	),
	array(
		"title" => "redirect:",
		"value" => "redirect",
		"info" => ""
	)
);

$ajaxNonce = wp_create_nonce("sgPopupBuilderPageNonce");
$ajaxNoncePages = wp_create_nonce("sgPopupBuilderPagesNonce");
$pagesRadio = array(
	array(
		"title" => "show on all pages:",
		"value" => "all",
		"info" => ""
	),
	array(
		"title" => "show on selected pages:",
		"value" => "selected",
		"info" => "",
		"data-attributes" => array(
			"data-name" => SG_POST_TYPE_PAGE,
			"data-popupid" => $dataPopupId,
			"data-loading-number" => 0,
			"data-selectbox-role" => "js-all-pages",
			"data-ajaxNoncePages" => $ajaxNoncePages
		)
	)
);

$postsRadio = array(
	array(
		"title" => "show on all posts:",
		"value" => "all",
		"info" => ""
	),
	array(
		"title" => "show on selected post:",
		"value" => "selected",
		"info" => "",
		"data-attributes" => array(
			"data-name" => SG_POST_TYPE_POST,
			"data-popupid" => $dataPopupId,
			"data-loading-number" => 0,
			"data-selectbox-role" => "js-all-posts",
			"data-ajaxNonce" => $ajaxNonce
		)

	),
	array(
		"title" => "show on selected categories",
		"value" => "allCategories",
		"info" => "",
		"data-attributes" => array(
			"class" => 'js-all-categories',
			"data-ajaxNonce" => $ajaxNonce
		)
	)
);

function getResponsiveData($popupType = '') {
	$responsiveDataAttrs = array(
		"class" => "js-responsive-mode"
	);

	if($popupType == 'iframe' || $popupType == 'video') {
		$responsiveDataAttrs['disabled'] = true;
	}

	$responsiveMode = array(
		array(
			"title" => "Responsive mode:",
			"value" => "responsiveMode",
			"info" => "",
			"data-attributes" => $responsiveDataAttrs
		),
		array(
			"title" => "Custom mode:",
			"value" => "customMode",
			"info" => "",
			"data-attributes" => array(
				"class" => "js-custom-mode"
			)

		)
	);

	return $responsiveMode;
}



$subsSuccessBehavior = array(
	array(
		"title" => "Show success message:",
		"value" => "showMessage",
		"info" => "",
		"data-attributes" => array(
			"class" => "js-subs-success-message"
		)

	),
	array(
		"title" => "Redirect to url:",
		"value" => "redirectToUrl",
		"info" => "",
		"data-attributes" => array(
			"class" => "js-subs-success-redirect"
		)

	),
	array(
		"title" => "Open popup:",
		"value" => "openPopup",
		"info" => "",
		"data-attributes" => array(
			"class" => "js-subs-success-redirect"
		)
	),
	array(
		"title" => "Hide popup:",
		"value" => "hidePopup",
		"info" => "",
		"data-attributes" => array(
			"class" => ""
		)
	)
);

$customPostsRadio = array(
	array(
		"title" => "show on all custom posts:",
		"value" => "all",
		"info" => ""
	),
	array(
		"title" => "show on selected custom post:",
		"value" => "selected",
		"info" => "",
		"data-attributes" => array(
			"data-name" => 'allCustomPosts',
			"data-popupid" => $dataPopupId,
			"data-loading-number" => 0,
			"data-selectbox-role" => "js-all-custom-posts"
		)

	)
);

function createRadiobuttons($elements, $name, $newLine, $selectedInput, $class)
{
	$str = "";

	foreach ($elements as $key => $element) {
		$breakLine = "";
		$infoIcon = "";
		$title = "";
		$value = "";
		$infoIcon = "";
		$checked = "";

		if(isset($element["title"])) {
			$title = $element["title"];
		}
		if(isset($element["value"])) {
			$value = $element["value"];
		}
		if($newLine) {
			$breakLine = "<br>";
		}
		if(isset($element["info"])) {
			$infoIcon = $element['info'];
		}
		if($element["value"] == $selectedInput) {
			$checked = "checked";
		}
		$attrStr = '';
		if(isset($element['data-attributes'])) {
			foreach ($element['data-attributes'] as $key => $dataValue) {
				$attrStr .= $key.'="'.esc_attr($dataValue).'" ';
			}
		}

		$str .= "<span class=".$class.">".$element['title']."</span>
				<input type=\"radio\" name=".esc_attr($name)." ".$attrStr." value=".esc_attr($value)." $checked>".$infoIcon.$breakLine;
	}

	echo $str;
}

$sgPopupEffects = array(
	'No effect' => 'No Effect',
	'sgpb-flip' => 'flip',
	'sgpb-shake' => 'shake',
	'sgpb-wobble' => 'wobble',
	'sgpb-swing' => 'swing',
	'sgpb-flash' => 'flash',
	'sgpb-bounce' => 'bounce',
	'sgpb-bounceInRight' => 'bounceInRight',
	'sgpb-bounceIn' => 'bounceIn',
	'sgpb-pulse' => 'pulse',
	'sgpb-rubberBand' => 'rubberBand',
	'sgpb-tada' => 'tada',
	'sgpb-slideInUp' => 'slideInUp',
	'sgpb-jello' => 'jello',
	'sgpb-rotateIn' => 'rotateIn',
	'sgpb-fadeIn' => 'fadeIn'
);

$sgPopupBgSizes = array(
	'auto' => 'Auto',
	'cover' => 'Cover',
	'contain' => 'Contain'
);

$sgPopupBgRepeat = array(
	'repeat' => 'Repeat',
	'repeat-x' => 'Repeat x',
	'repeat-y' => 'Repeat y',
	'no-repeat' => 'Not repeat'
);

$sgPopupTheme = array(
	'colorbox1.css',
	'colorbox2.css',
	'colorbox3.css',
	'colorbox4.css',
	'colorbox5.css',
	'colorbox6.css'
);

$sgFbLikeButtons = array(
	'standard' => 'Standard',
	'box_count' => 'Box with count',
	'button_count' => 'Button with count',
	'button' => 'Button'
);

$sgTheme = array(
	'flat' => 'flat',
	'classic' => 'classic',
	'minima' => 'minima',
	'plain' => 'plain'
);

$sgResponsiveMeasure = array(
	'auto' => 'Auto',
	'10' => '10%',
	'20' => '20%',
	'30' => '30%',
	'40' => '40%',
	'50' => '50%',
	'60' => '60%',
	'70' => '70%',
	'80' => '80%',
	'90' => '90%',
	'100' => '100%'
);

$sgThemeSize = array(
	'8' => '8',
	'10' => '10',
	'12' => '12',
	'14' => '14',
	'16' => '16',
	'18' => '18',
	'20' => '20',
	'24' => '24'
);

$sgSocialCount = array(
	'true' => 'True',
	'false' => 'False',
	'inside' => 'Inside'
);

$sgCountdownType = array(
	1 => 'DD:HH:MM:SS',
	2 => 'DD:HH:MM'
);

$sgCountdownlang = array(
	'English' => 'English',
	'German' => 'German',
	'Spanish' => 'Spanish',
	'Arabic' => 'Arabic',
	'Italian' => 'Italian',
	'Italian' => 'Italian',
	'Dutch' => 'Dutch',
	'Norwegian' => 'Norwegian',
	'Portuguese' => 'Portuguese',
	'Russian' => 'Russian',
	'Swedish' => 'Swedish',
	'Chinese' => 'Chinese'
);

$sgTextAreaResizeOptions = array(
	'both' => 'Both',
	'horizontal' => 'Horizontal',
	'vertical' => 'Vertical',
	'none' => 'None',
	'inherit' => 'Inherit'
);

$sgWeekDaysArray = array(
	'Mon' => 'Monday',
	'Tue' => 'Tuesday',
	'Wed' => 'Wendnesday',
	'Thu' => 'Thursday',
	'Fri' => 'Friday',
	'Sat' => 'Saturday',
	'Sun' => 'Sunday'
);

if (POPUP_BUILDER_PKG != POPUP_BUILDER_PKG_FREE) {
	require_once(SG_APP_POPUP_FILES ."/sg_params_arrays.php");
	$popupDefaultData = SgParamsArray::defaultDataArray();
}

function sgCreateSelect($options,$name,$selecteOption)
{
	$selected ='';
	$str = "";
	$checked = "";
	if ($name == 'theme' || $name == 'restrictionAction') {

		$popup_style_name = 'popup_theme_name';
		$firstOption = array_shift($options);
		$i = 1;
		foreach ($options as $key) {
			$checked ='';

			if ($key == $selecteOption) {
				$checked = "checked";
			}
			$i++;
			$str .= "<input type='radio' name=\"$name\" value=\"$key\" $checked class='popup_theme_name' sgPoupNumber=".$i.">";

		}
		if ($checked == ''){
			$checked = "checked";
		}
		$str = "<input type='radio' name=\"".esc_attr($name)."\" value=\"".esc_attr($firstOption)."\" $checked class='popup_theme_name' sgPoupNumber='1'>".$str;
		return $str;
	}
	else {
		@$popup_style_name = ($popup_style_name) ? $popup_style_name : '';
		$str .= "<select name=$name class=$popup_style_name input-width-static >";
		foreach ($options as $key => $option) {

			$selected ='';

			if ($key == $selecteOption) {
				$selected = 'selected';
			}

			$str .= "<option value='".esc_attr($key)."' ".$selected." >$option</potion>";
		}

		$str .="</select>" ;
		return $str;

	}

}

if(!SG_SHOW_POPUP_REVIEW) {
	echo SGFunctions::addReview();
}

if (isset($_GET['saved']) && $_GET['saved']==1) {
	echo '<div id="default-message" class="updated notice notice-success is-dismissible" ><p>Popup updated.</p></div>';
}
if (isset($_GET["titleError"])): ?>
	<div class="error notice" id="title-error-message">
		<p>Invalid Title</p>
	</div>
<?php endif; ?>
	<form method="POST" action="<?php echo SG_APP_POPUP_ADMIN_URL;?>admin-post.php" id="add-form">
		<?php
			if(function_exists('wp_nonce_field')) {
				wp_nonce_field('sgPopupBuilderSave');
			}
		?>
		<input type="hidden" name="action" value="<?php echo $currentActionName;?>">
		<div class="crud-wrapper">
			<div class="cereate-title-wrapper">
				<div class="sg-title-crud">
					<?php if (isset($id)): ?>
						<h2>Edit popup</h2>
					<?php else: ?>
						<h2>Create new popup</h2>
					<?php endif; ?>
	                <?php $pageUrl = SgPopupGetData::getPageUrl(); ?>
                </div>
                <div class="button-wrapper">
                	<div class="sg-tooltip">
                		<input type="submit" id="sg-save-button" class="button-primary" value="<?php echo 'Save Changes'; ?>">
                		<?php if( !empty($pageUrl)): ?>
							<input type="button" class="sg-popup-preview button-primary sg-popup-general-option" data-page-url="<?php echo $pageUrl; ?>" value="Preview">
						<?php endif; ?>
                		<span class="sg-tooltip-text">
                			Liked the preview of your popup?
                			<a href="https://sygnoos.ladesk.com/377214-How-to-insert-popups-on-a-pagepost">Don't forget to insert it in any post/page</a>.
                		</span>
                	</div>
	                <?php if (POPUP_BUILDER_PKG == POPUP_BUILDER_PKG_FREE): ?>
		                <input class="crud-to-pro" type="button" value="Upgrade to PRO version" onclick="window.open('<?php echo SG_POPUP_PRO_URL;?>')"><div class="clear"></div>
	                <?php endif; ?>

                </div>
            </div>
            <div class="clear"></div>
            <div class="general-wrapper">
                <div id="titlediv">
                    <div id="titlewrap">
                        <input  id="title" class="sg-js-popup-title" type="text" name="title" size="30" value="<?php echo esc_attr(@$title)?>" spellcheck="true" autocomplete="off" required = "required"  placeholder='Enter title here'>
                    </div>
                </div>
                <div id="left-main-div">
                    <div id="sg-general">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="postbox-container-2" class="postbox-container">
                                <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                    <div class="postbox popupBuilder_general_postbox sgSameWidthPostBox" style="display: block;">
                                        <div class="handlediv generalTitle" title="Click to toggle"><br></div>
                                        <h3 class="hndle ui-sortable-handle generalTitle" style="cursor: pointer"><span>General</span></h3>
                                        <div class="generalContent sgSameWidthPostBox">
											<?php require_once($popupFilesPath."/main_section/".$popupType.".php");?>
											<input type="hidden" name="type" value="<?php echo $popupType;?>">
											<span class="liquid-width" id="theme-span">Popup theme:</span>
											<?php echo  sgCreateSelect($sgPopupTheme,'theme',esc_html(@$sgColorboxTheme));?>
											<div class="theme1 sg-hide"></div>
											<div class="theme2 sg-hide"></div>
											<div class="theme3 sg-hide"></div>
											<div class="theme4 sg-hide"></div>
											<div class="theme5 sg-hide"></div>
											<div class="theme6 sg-hide"></div>
											<div class="sg-popup-theme-3 themes-suboptions sg-hide">
												<span class="liquid-width">Border color:</span>
												<div id="color-picker"><input  class="sgOverlayColor" id="sgOverlayColor" type="text" name="sgTheme3BorderColor" value="<?php echo esc_attr(@$sgTheme3BorderColor); ?>" /></div>
												<br><span class="liquid-width">Border radius:</span>
												<input class="input-width-percent" type="number" min="0" max="50" name="sgTheme3BorderRadius" value="<?php echo esc_attr(@$sgTheme3BorderRadius); ?>">
												<span class="span-percent">%</span>
											</div>
											<div class="sg-popup-theme-4 themes-suboptions sg-hide">
												<span class="liquid-width">Close button text:</span>
												<input type="text" name="theme-close-text" value="<?php echo esc_attr($sgThemeCloseText);?>">
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div id="effect">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="postbox-container-2" class="postbox-container">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<div class="postbox popupBuilder_effect_postbox sgSameWidthPostBox" style="display: block;">
										<div class="handlediv effectTitle" title="Click to toggle"><br></div>
										<h3 class="hndle ui-sortable-handle effectTitle" style="cursor: pointer"><span>Effects</span></h3>
										<div class="effectsContent">
											<span class="liquid-width">Effect type:</span>
											<?php echo  sgCreateSelect($sgPopupEffects,'effect',esc_html(@$effect));?>
											<span class="js-preview-effect"></span>
											<div class="effectWrapper"><div id="effectShow" ></div></div>

											<span  class="liquid-width">Effect duration:</span>
											<input class="input-width-static" type="text" name="duration" value="<?php echo esc_attr($duration); ?>" pattern = "\d+" title="It must be number" /><span class="dashicons dashicons-info contentClick infoImageDuration sameImageStyle"></span><span class="infoDuration samefontStyle">Specify how long the popup appearance animation should take (in sec).</span></br>

											<span class="liquid-width">Popup open sound:</span>
											<div class="input-width-static sg-display-inline">
												<input class="input-width-static js-checkbox-sound-option" type="checkbox" name="popupOpenSound" <?php echo $sgPopupOpenSound;?>></div><span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">If this option enabled a sound will play after popup opened.Sound option is not available on mobile devices, as there are restrictions on sound auto-play options for mobile devices.</span><br>
											<div class="acordion-main-div-content js-checkbox-sound-option-wrapper">
												<div class="sound-uploader-wrapper">
													<div class="liquid-width-div sg-vertical-top"><input id="js-upload-open-sound-button" class="button" type="button" value="Change the sound">
														<button data-default-song="<?php echo $popupOpenSoundFile; ?> " id="reset-to-default" class="button">Reset</button>
													</div>
													<input class="input-width-static sg-margin-top-0" id="js-upload-open-sound" type="text" size="36" name="popupOpenSoundFile" value="<?php echo esc_attr($sgPopupOpenSoundFile); ?>" required readonly>
													<span class="dashicons dashicons-controls-volumeon sg-preview-sound"></span>
												</div>
											</div>

											<span  class="liquid-width">Popup opening delay:</span>
											<input class="input-width-static" type="text" name="delay" value="<?php echo esc_attr($delay);?>"  pattern = "\d+" title="It must be number"/><span class="dashicons dashicons-info contentClick infoImageDelay sameImageStyle"></span><span class="infoDelay samefontStyle">Specify how long the popup appearance should be delayed after loading the page (in sec).</span></br>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<?php
						require_once($popupFilesPath."/options_section/".$popupType.".php");
						echo $extensionManagerObj->optionsInclude($popupType);
					?>
				</div>
				<div id="right-main-div">
					<div id="right-main">
						<div id="dimentions">
							<div id="post-body" class="metabox-holder columns-2">
								<div id="postbox-container-2" class="postbox-container">
									<div id="normal-sortables" class="meta-box-sortables ui-sortable">
										<div class="postbox popupBuilder_dimention_postbox sgSameWidthPostBox" style="display: block;">
											<div class="handlediv dimentionsTitle" title="Click to toggle"><br></div>
											<h3 class="hndle ui-sortable-handle dimentionsTitle" style="cursor: pointer"><span>Dimensions</span></h3>
											<div class="dimensionsContent">
												<div class="sg-radio-option-behavior">
													<?php $responsiveMode = getResponsiveData($popupType);?>
													<?php createRadiobuttons($responsiveMode, 'popup-dimension-mode', true, esc_html($sgPopupDimensionMode), "liquid-width");?>
												</div>
												<div class="js-accordion-responsiveMode js-radio-accordion sg-accordion-content">
													<span class="liquid-width">size</span>
													<?php echo  sgCreateSelect($sgResponsiveMeasure,'popup-responsive-dimension-measure',esc_html(@$sgPopupResponsiveDimensionMeasure));?>
												</div>
												<div class="js-accordion-customMode js-radio-accordion sg-accordion-content">
													<span class="liquid-width">Width:</span>
													<input class="input-width-static" type="text" name="width" value="<?php echo esc_attr($sgWidth); ?>"  pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
													<span class="liquid-width">Height:</span>
													<input class="input-width-static" type="text" name="height" value="<?php echo esc_attr($sgHeight);?>" pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
													<span class="liquid-width">Initial width:</span>
													<input class="input-width-static" type="text" name="initialWidth" value="<?php echo esc_attr($sgInitialWidth);?>"  pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
													<span class="liquid-width">Initial height:</span>
													<input class="input-width-static" type="text" name="initialHeight" value="<?php echo esc_attr($sgInitialHeight);?>"  pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
												</div>
												<span class="liquid-width">Max width:</span>
												<input class="input-width-static" type="text" name="maxWidth" value="<?php echo esc_attr($sgMaxWidth);?>"  pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
												<span class="liquid-width">Max height:</span>
												<input class="input-width-static" type="text" name="maxHeight" value="<?php echo esc_attr(@$sgMaxHeight);?>"   pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<div id="options">
							<div id="post-body" class="metabox-holder columns-2">
								<div id="postbox-container-2" class="postbox-container">
									<div id="normal-sortables" class="meta-box-sortables ui-sortable">
										<div class="postbox popupBuilder_options_postbox sgSameWidthPostBox" style="display: block;">
											<div class="handlediv optionsTitle" title="Click to toggle"><br></div>
											<h3 class="hndle ui-sortable-handle optionsTitle" style="cursor: pointer"><span>Options</span></h3>
											<div class="optionsContent">
												<span class="liquid-width">Dismiss on &quot;esc&quot; key:</span><input class="input-width-static" type="checkbox" name="escKey"  <?php echo $sgEscKey;?>/>
												<span class="dashicons dashicons-info escKeyImg sameImageStyle"></span><span class="infoEscKey samefontStyle">The popup will be dismissed when user presses on 'esc' key.</span></br>

												<span class="liquid-width" id="createDescribeClose">Show &quot;close&quot; button:</span><input class="input-width-static js-checkbox-acordion" type="checkbox" name="closeButton" <?php echo $sgCloseButton;?> />
												<span class="dashicons dashicons-info CloseImg sameImageStyle"></span><span class="infoCloseButton samefontStyle">The popup will contain 'close' button.</span><br>

												<div class="acordion-main-div-content">
													<span class="liquid-width" style="margin-left: 10px;">&quot;close&quot; button delay:</span>
													<input class="input-width-static sg-close-button-delay" type="number" min="0" name="buttonDelayValue" value="<?php echo esc_attr($sgCloseButtonDelay);?>" title="It must be number"/>
													<span class="dashicons dashicons-info contentClick infoImageDelay sameImageStyle"></span>
													<span class="infoDelay samefontStyle">Add seconds after which the close button will appear.If no seconds are mentioned, the close button will be shown by default.</span></br>
												</div>

												<span class="liquid-width">Enable content scrolling:</span><input class="input-width-static" type="checkbox" name="scrolling" <?php echo $sgScrolling;?> />
												<span class="dashicons dashicons-info scrollingImg sameImageStyle"></span><span class="infoScrolling samefontStyle">If the content is larger than the specified dimensions, then the content will be scrollable.</span><br>

												<span class="liquid-width">Disable page scrolling:</span><input class="input-width-static" type="checkbox" name="disable-page-scrolling" <?php echo $sgDisablePageScrolling; ?>>
												<span class="dashicons dashicons-info scrollingImg sameImageStyle"></span><span class="infoScrolling samefontStyle">If this option is enabled, the page scrolling will be disabled when the popup is open.</span><br>

												<span class="liquid-width">Enable reposition:</span><input class="input-width-static" type="checkbox" name="reposition" <?php echo $sgReposition;?> />
												<span class="dashicons dashicons-info repositionImg sameImageStyle"></span><span class="infoReposition samefontStyle">The popup will be resized/repositioned automatically when window is being resized.</span><br>

												<span class="liquid-width">Enable scaling:</span><input class="input-width-static" type="checkbox" name="scaling" <?php echo $sgScaling;?> />
												<span class="dashicons dashicons-info scrollingImg sameImageStyle"></span><span class="infoScaling samefontStyle">Resize popup according to screen size</span><br>

												<span class="liquid-width">Dismiss on overlay click:</span><input class="input-width-static" type="checkbox" name="overlayClose" <?php echo $sgOverlayClose;?> />
												<span class="dashicons dashicons-info overlayImg sameImageStyle"></span><span class="infoOverlayClose samefontStyle">The popup will be dismissed when user clicks on the popup overlay.</span><br>

												<?php if(!sgRemoveOption('contentClick')): ?>
												<span class="liquid-width">Dismiss on content click:</span><input class="input-width-static js-checkbox-contnet-click" type="checkbox" name="contentClick" <?php echo $sgContentClick;?> />
												<span class="dashicons dashicons-info contentClick sameImageStyle"></span><span class="infoContentClick samefontStyle">The popup will be dismissed when user clicks inside popup area.</span><br>

												<div class="sg-hide sg-full-width js-content-click-wrraper">
													<?php echo createRadiobuttons($contentClickOptions, "content-click-behavior", true, esc_html($sgContentClickBehavior), "liquid-width"); ?>
													<div class="sg-hide js-readio-buttons-acordion-content sg-full-width">
														<span class="liquid-width">URL:</span><input class="input-width-static" type="text" name='click-redirect-to-url' value="<?php echo esc_attr(@$sgClickRedirectToUrl); ?>">
														<span class="liquid-width">redirect to new tab:</span><input type="checkbox" name="redirect-to-new-tab" <?php echo $sgRedirectToNewTab; ?> >
													</div>
												</div>
												<?php endif;?>

												<span class="liquid-width">Reopen after form submission:</span><input class="input-width-static" type="checkbox" name="reopenAfterSubmission" <?php echo $sgReopenAfterSubmission;?> />
												<span class="dashicons dashicons-info overlayImg sameImageStyle"></span><span class="infoReopenSubmiting samefontStyle">If checked, the popup will reopen after form submission.</span><br>

	                                            <?php if(!sgRemoveOption('showOnlyOnce')): ?>
		                                            <span class="liquid-width">Show popup this often:</span><input class="input-width-static js-checkbox-acordion" id="js-popup-only-once" type="checkbox" name="repeatPopup" <?php echo $sgRepeatPopup;?>>
		                                            <span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">Specify how many times the popup should be shown to the user. The “expire time” specifies when this rule expires and you can show the popup to the same user again.</span><br>
		                                            <div class="acordion-main-div-content js-popup-only-once-content">
			                                            <span class="liquid-width">show popup</span><input class="before-scroling-percent" type="number" min="1" name="popup-appear-number-limit" value="<?php echo esc_attr(@$sgPopupAppearNumberLimit); ?>">
			                                            <span class="span-percent">time(s) for same user</span><br>
			                                            <span class="liquid-width">expire time</span><input class="before-scroling-percent improveOptionsstyle" type="number" min="1" name="onceExpiresTime" value="<?php echo esc_attr(@$sgOnceExpiresTime); ?>">
			                                            <span class="span-percent">days</span><br>
			                                            <span class="liquid-width">page level cookie saving</span>
			                                            <input type="checkbox" name="save-cookie-page-level" <?php echo $sgPopupCookiePageLevel; ?>>
			                                            <span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">If this option is checked popup's cookie will be saved for a current page.By default cookie is set for all site.</span>
		                                            </div>
	                                            <?php endif;?>

	                                            <?php if(!sgRemoveOption('repetitivePopup')): ?>
		                                            <span class="liquid-width">Repetitive popup:</span><input class="input-width-static js-checkbox-acordion" id="js-popup-only-once" type="checkbox" name="repetitivePopup" <?php echo $sgRepetitivePopup;?>>
		                                            <span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">If this option is enabled the same popup will open up after every X seconds you have defined (after closing it).</span><br>
		                                            <div class="acordion-main-div-content js-popup-only-once-content">
			                                            <span class="liquid-width">show popup</span>
			                                            <input type="number" class="before-scroling-percent" name="repetitivePopupPeriod" min="10" value="<?php echo esc_attr($sgRepetitivePopupPeriod); ?>">
			                                            <span class="span-percent">after X seconds</span>
		                                            </div>
	                                            <?php endif;?>

	                                            <?php if(!sgRemoveOption('popupContentBgImage')): ?>
		                                            <span class="liquid-width">Popup background image:</span><input class="input-width-static js-popup-content-bg-image" type="checkbox" name="popupContentBgImage" <?php echo $sgPopupContentBgImage;?>><span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">Enable this option if you need to have background image for popup.</span><br>
		                                            <div class="acordion-main-div-content js-popup-content-bg-image-wrapper">
			                                            <span  class="liquid-width">Background size:</span>
			                                            <?php echo  sgCreateSelect($sgPopupBgSizes,'popupContentBackgroundSize',esc_html(@$sgPopupContentBackgroundSize));?>
			                                            <span  class="liquid-width">Background repeat:</span>
			                                            <?php echo  sgCreateSelect($sgPopupBgRepeat,'popupContentBackgroundRepeat',esc_html(@$sgPopupContentBackgroundRepeat));?>

			                                            <div class="sg-wp-editor-container">
				                                            <div class="liquid-width-div sg-vertical-top">
					                                            <input id="js-upload-image-button" class="button popup-content-bg-image-btn" type="button" value="Select image">
				                                            </div>
				                                            <input class="input-width-static popup-content-bg-image-url" id="js-upload-image" type="text" size="36" name="popupContentBgImageUrl" value="<?php echo esc_attr($sgPopupContentBgImageUrl); ?>" >
				                                            <span class="liquid-width-div"></span>
				                                            <div class="show-image-contenier popup-content-bg-image-preview">
					                                            <span class="no-image">(No image selected)</span>
				                                            </div>
			                                            </div>

		                                            </div>
	                                            <?php endif; ?>

                                                <span class="liquid-width">Change overlay color:</span><div id="color-picker"><input  class="sgOverlayColor" id="sgOverlayColor" type="text" name="sgOverlayColor" value="<?php echo esc_attr(@$sgOverlayColor); ?>" /></div><br>

                                                <span class="liquid-width">Change background color:</span><div id="color-picker"><input  class="sgOverlayColor" id="sgOverlayColor" type="text" name="sg-content-background-color" value="<?php echo esc_attr(@$sgContentBackgroundColor); ?>" /></div><br>

	                                            <span class="liquid-width">Background opacity:</span>
	                                            <div class="slider-wrapper">
		                                            <input type="text" class="js-popup-content-opacity" value="<?php echo esc_attr($sgPopupBackgroundOpacity);?>" rel="<?php echo esc_attr($sgPopupBackgroundOpacity);?>" name="popup-background-opacity">
		                                            <div id="js-popup-content-opacity" data-init="false" class="display-box"></div>
	                                            </div><br>

                                                <span class="liquid-width" id="createDescribeOpacitcy">Background overlay opacity:</span>
	                                            <div class="slider-wrapper">
                                                    <input type="text" class="js-decimal" value="<?php echo esc_attr($sgOpacity);?>" rel="<?php echo esc_attr($sgOpacity);?>" name="opacity"/>
                                                    <div id="js-decimal" data-init="false" class="display-box"></div>
                                                </div><br>

                                                <span class="liquid-width">Overlay custom class:</span><input class="input-width-static" type="text" name="sgOverlayCustomClasss" value="<?php echo esc_attr(@$sgOverlayCustomClasss);?>">
                                                <br>

                                                <span class="liquid-width">Content custom class:</span><input class="input-width-static" type="text" name="sgContentCustomClasss" value="<?php echo esc_attr(@$sgContentCustomClasss);?>">
                                                <br>

	                                            <span class="liquid-width">Popup z-index:</span><input class="input-width-static" type="number" name="popup-z-index" value="<?php echo esc_attr($sgPopupZIndex);?>">
                                                <br>

												<?php if (!sgRemoveOption('popup-content-padding')): ?>
												<span class="liquid-width">Content padding:</span><input class="input-width-static" type="number" name="popup-content-padding" value="<?php echo esc_attr($sgPopupContentPadding);?>">
												<br>
												<?php endif; ?>

												<span  class="liquid-width" id="createDescribeFixed">Popup location:</span><input class="input-width-static js-checkbox-acordion" type="checkbox" name="popupFixed" <?php echo $sgPopupFixed;?> />
												<div class="js-popop-fixeds">
													<span class="fix-wrapper-style" >&nbsp;</span>
													<div class="fixed-wrapper">
														<div class="js-fixed-position-style" id="fixed-position1" data-sgvalue="1"></div>
														<div class="js-fixed-position-style" id="fixed-position2"data-sgvalue="2"></div>
														<div class="js-fixed-position-style" id="fixed-position3" data-sgvalue="3"></div>
														<div class="js-fixed-position-style" id="fixed-position4" data-sgvalue="4"></div>
														<div class="js-fixed-position-style" id="fixed-position5" data-sgvalue="5"></div>
														<div class="js-fixed-position-style" id="fixed-position6" data-sgvalue="6"></div>
														<div class="js-fixed-position-style" id="fixed-position7" data-sgvalue="7"></div>
														<div class="js-fixed-position-style" id="fixed-position8" data-sgvalue="8"></div>
														<div class="js-fixed-position-style" id="fixed-position9" data-sgvalue="9"></div>
													</div>
												</div>
												<input type="hidden" name="fixedPostion" class="js-fixed-postion" value="<?php echo esc_attr(@$sgFixedPostion);?>">
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<?php require_once("options_section/pro.php"); ?>
					</div>
				</div>
				<div class="clear"></div>
				<?php
				$isActivePopup = SgPopupGetData::isActivePopup(@$id);
				if(!@$id) $isActivePopup = 'checked';
				?>
				<input class="sg-hide-element" name="isActiveStatus" data-switch-id="'.$id.'" type="checkbox" <?php echo $isActivePopup; ?> >
				<input type="hidden" class="button-primary" value="<?php echo esc_attr(@$id);?>" name="hidden_popup_number" />
			</div>
		</div>
	</form>
<?php
SGFunctions::showInfo();
