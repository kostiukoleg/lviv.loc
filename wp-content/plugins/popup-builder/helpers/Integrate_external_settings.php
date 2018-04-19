<?php

Class IntegrateExternalSettings {

	public static function getAllExternalPlugins() {

		global $wpdb;

		$query = "SELECT name FROM ". $wpdb->prefix ."sg_popup_addons WHERE type='plugin'";
		$addons = $wpdb->get_results($query, ARRAY_A);

		if(empty($addons)) {
			return false;
		}
		return $addons;
	}

	public static function getAllAddons() {

		global $wpdb;

		$query = "SELECT name FROM ". $wpdb->prefix ."sg_popup_addons";
		$addons = $wpdb->get_results($query, ARRAY_A);

		if(empty($addons)) {
			return false;
		}
		return $addons;
	}

	public static function doesntHaveAnyActiveExtensions() {

		global  $POPUP_ADDONS;

		$addons = self::getAllAddons();

		if(empty($addons)) {
			return true;
		}

		$activeExtensionsCount = count($addons);
		$allSizeOf = count($POPUP_ADDONS);

		return $allSizeOf > $activeExtensionsCount;
	}

	public static function isExtensionExists($extensionName) {

		global $wpdb;
		$sql = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."sg_popup_addons WHERE name=%s", $extensionName);
		$ressults = $wpdb->get_results($sql, ARRAY_A);

		if(empty($ressults)) {
			return false;
		}
		return true;
	}

	/* retrun All paths */
	public static function getCurrentPopupAppPaths($popupType) {

		$pathsArray = array();

		global $wpdb;
		$sql = $wpdb->prepare("SELECT paths FROM ". $wpdb->prefix ."sg_popup_addons WHERE name=%s", $popupType);
		$ressults = $wpdb->get_results($sql, ARRAY_A);

		if(empty($ressults)) {
			$pathsArray['app-path'] = SG_APP_POPUP_PATH;
			$pathsArray['files-path'] = SG_APP_POPUP_FILES;
		}
		else {
			$addonPaths = json_decode($ressults['0']['paths'], true);
			$appPath = $addonPaths['app-path'];
			$filesPath = $addonPaths['files-path'];
			$hasPluginDirPathApp = strrpos($appPath, WP_PLUGIN_DIR);
			$hasPluginDirPath = strrpos($filesPath, WP_PLUGIN_DIR);

			if ($hasPluginDirPathApp === false) {
				$appPath = WP_PLUGIN_DIR.'/'.$appPath;
			}
			if ($hasPluginDirPath === false) {
				$filesPath = WP_PLUGIN_DIR.'/'.$filesPath;
			}
			$addonPaths['app-path'] = $appPath;
			$addonPaths['files-path'] = $filesPath;
			$pathsArray = $addonPaths;
		}
		return $pathsArray;
	}

	public static function getCurrentPopupAdminPostActionName($popupType) {

		global $wpdb;
		$getcurrentAddonSql = $wpdb->prepare("SELECT id FROM ". $wpdb->prefix ."sg_popup_addons WHERE name=%s and type='plugin'", $popupType);
		$addonId = $wpdb->get_results($getcurrentAddonSql, ARRAY_A);

		if(!empty($addonId)) {
			return $popupType;
		}
		return "save_popup";
	}

	public static function getPopupGeneralOptions($params) {

		$options = array(
			'width' => sgSanitize('width'),
			'height' => sgSanitize('height'),
			'delay' => (int)sgSanitize('delay'),
			'buttonDelayValue' => (int)sgSanitize('buttonDelayValue'),
			'duration' => (int)sgSanitize('duration'),
			'effect' => sgSanitize('effect'),
			'escKey' => sgSanitize('escKey'),
			'isActiveStatus' => sgSanitize('isActiveStatus'),
			'scrolling' => sgSanitize('scrolling'),
			'disable-page-scrolling' => sgSanitize('disable-page-scrolling'),
			'scaling' => sgSanitize('scaling'),
			'reposition' => sgSanitize('reposition'),
			'overlayClose' => sgSanitize('overlayClose'),
			'reopenAfterSubmission' => sgSanitize('reopenAfterSubmission'),
			'contentClick' => sgSanitize('contentClick'),
			'content-click-behavior' => sgSanitize('content-click-behavior'),
			'click-redirect-to-url' => sgSanitize('click-redirect-to-url'),
			'redirect-to-new-tab' => sgSanitize('redirect-to-new-tab'),
			'opacity' => sgSanitize('opacity'),
			'popup-background-opacity' => sgSanitize('popup-background-opacity'),
			'sgOverlayColor' => sgSanitize('sgOverlayColor'),
			'sg-content-background-color' => sgSanitize('sg-content-background-color'),
			'popupFixed' => sgSanitize('popupFixed'),
			'fixedPostion' => sgSanitize('fixedPostion'),
			'popup-dimension-mode' => sgSanitize('popup-dimension-mode'),
			'popup-responsive-dimension-measure' => sgSanitize('popup-responsive-dimension-measure'),
			'maxWidth' => sgSanitize('maxWidth'),
			'maxHeight' => sgSanitize('maxHeight'),
			'initialWidth' => sgSanitize('initialWidth'),
			'initialHeight' => sgSanitize('initialHeight'),
			'closeButton' => sgSanitize('closeButton'),
			'theme' => sgSanitize('theme'),
			'sgTheme3BorderColor'=> sgSanitize("sgTheme3BorderColor"),
			'sgTheme3BorderRadius'=> sgSanitize("sgTheme3BorderRadius"),
			'onScrolling' => sgSanitize('onScrolling'),
			'inActivityStatus' => sgSanitize('inActivityStatus'),
			'inactivity-timout' => sgSanitize('inactivity-timout'),
			'beforeScrolingPrsent' => (int)sgSanitize('beforeScrolingPrsent'),
			'forMobile' => sgSanitize('forMobile'),
			'openMobile' => sgSanitize('openMobile'), // open only for mobile
			'repeatPopup' => sgSanitize('repeatPopup'),
			'popup-appear-number-limit' => sgSanitize('popup-appear-number-limit'),
			'save-cookie-page-level' => sgSanitize('save-cookie-page-level'),
			'autoClosePopup' => sgSanitize('autoClosePopup'),
			'countryStatus' => sgSanitize('countryStatus'),
			'showAllPages' => $params['showAllPages'],
			'allPagesStatus' => sgSanitize('allPagesStatus'),
			'allPostsStatus' => sgSanitize('allPostsStatus'),
			'allCustomPostsStatus' => sgSanitize('allCustomPostsStatus'),
			'allSelectedPages' => $params['allSelectedPages'],
			'showAllPosts' => $params['showAllPosts'],
			'showAllCustomPosts' => $params['showAllCustomPosts'],
			'allSelectedPosts' => $params['allSelectedPosts'],
			'allSelectedCustomPosts' => $params['allSelectedCustomPosts'],
			'posts-all-categories'=> $params['allSelectedCategories'],
			'all-custom-posts' => sgSanitize('all-custom-posts', true),
			'sg-user-status' => sgSanitize('sg-user-status'),
			'loggedin-user' => sgSanitize('loggedin-user'),
			'popup-timer-status' => sgSanitize('popup-timer-status'),
			'popup-schedule-status' => sgSanitize('popup-schedule-status'),
			'popup-start-timer' => sgSanitize('popup-start-timer'),
			'popup-finish-timer' => sgSanitize('popup-finish-timer'),
			'schedule-start-weeks' => sgSanitize('schedule-start-weeks', true),
			'schedule-start-time' => sgSanitize('schedule-start-time'),
			'schedule-end-time' => sgSanitize('schedule-end-time'),
			'allowCountries' => sgSanitize('allowCountries'),
			'countryName' => sgSanitize('countryName'),
			'countryIso' => sgSanitize('countryIso'),
			'disablePopup' => sgSanitize('disablePopup'),
			'disablePopupOverlay' => sgSanitize('disablePopupOverlay'),
			'popupClosingTimer' => sgSanitize('popupClosingTimer'),
			'yesButtonLabel' => sgSanitize('yesButtonLabel', true),
			'noButtonLabel' => sgSanitize('noButtonLabel', true),
			'restrictionUrl' => sgSanitize('restrictionUrl'),
			'yesButtonBackgroundColor' => sgSanitize('yesButtonBackgroundColor'),
			'noButtonBackgroundColor' => sgSanitize('noButtonBackgroundColor'),
			'yesButtonTextColor' => sgSanitize('yesButtonTextColor'),
			'noButtonTextColor' => sgSanitize('noButtonTextColor'),
			'yesButtonRadius' => (int)sgSanitize('yesButtonRadius'),
			'noButtonRadius' => (int)sgSanitize('noButtonRadius'),
			'sgRestrictionExpirationTime' => (int)sgSanitize('sgRestrictionExpirationTime'),
			'restrictionCookeSavingLevel' => sgSanitize('restrictionCookeSavingLevel'),
			'pushToBottom' => sgSanitize('pushToBottom'),
			'onceExpiresTime' => sgSanitize('onceExpiresTime'),
			'sgOverlayCustomClasss' => sgSanitize('sgOverlayCustomClasss'),
			'sgContentCustomClasss' => sgSanitize('sgContentCustomClasss'),
			'popup-z-index' => sgSanitize('popup-z-index'),
			'popup-content-padding' => sgSanitize('popup-content-padding'),
			'theme-close-text' => sgSanitize('theme-close-text'),
			'socialButtons' => json_encode($params['socialButtons']),
			'socialOptions' => json_encode($params['socialOptions']),
			'countdownOptions' => json_encode($params['countdownOptions']),
			'exitIntentOptions' => json_encode($params['exitIntentOptions']),
			'videoOptions' => json_encode($params['videoOptions']),
			'fblikeOptions' => json_encode($params['fblikeOptions']),
			'repetitivePopup' => sgSanitize('repetitivePopup'),
			'repetitivePopupPeriod' => sgSanitize('repetitivePopupPeriod'),
			'randomPopup' => sgSanitize('randomPopup'),
			'popupOpenSound' => sgSanitize('popupOpenSound'),
			'popupOpenSoundFile' => sgSanitize('popupOpenSoundFile'),
			'popupContentBgImage' => sgSanitize('popupContentBgImage'),
			'popupContentBgImageUrl' => sgSanitize('popupContentBgImageUrl'),
			'popupContentBackgroundSize' => sgSanitize('popupContentBackgroundSize'),
			'popupContentBackgroundRepeat' => sgSanitize('popupContentBackgroundRepeat')
		);

		return $options;
	}
}
