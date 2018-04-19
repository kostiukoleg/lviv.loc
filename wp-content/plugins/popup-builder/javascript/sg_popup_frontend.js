function SGPopup() {

	this.positionLeft = '';
	this.positionTop = '';
	this.positionBottom = '';
	this.positionRight = '';
	this.initialPositionTop = '';
	this.initialPositionLeft = '';
	this.isOnLoad = '';
	this.openOnce = '';
	this.numberLimit = '';
	this.popupData = new Array();
	this.popupEscKey = true;
	this.popupOverlayClose = true;
	this.popupContentClick = false;
	this.popupCloseButton = true;
	this.sgTrapFocus = true;
	this.popupType = '';
	this.popupClassEvents = ['hover'];
	this.eventExecuteCountByClass = 0;
	this.sgEventExecuteCount = 0;
	this.resizeTimer = null;
	this.sgColorboxContentTypeReset();
}

SGPopup.prototype.sgColorboxContentTypeReset = function () {

	/*colorbox settings mode*/
	this.sgColorboxHtml = false;
	this.sgColorboxPhoto = false;
	this.sgColorboxIframe = false;
	this.sgColorboxHref = false;
	this.sgColorboxInline = false;
};

/*Popup thems default paddings where key is theme number value padding*/
SGPopup.sgColorBoxDeafults = {1 : 70, 2: 34, 3: 30, 4 : 70, 5 : 62, 6: 70};

SGPopup.prototype.popupOpenById = function (popupId) {

	var sgOnScrolling = (SG_POPUP_DATA [popupId]['onScrolling']) ? SG_POPUP_DATA [popupId]['onScrolling'] : '';
	var sgInActivity = (SG_POPUP_DATA [popupId]['inActivityStatus']) ? SG_POPUP_DATA [popupId]['inActivityStatus'] : '';
	var autoClosePopup = (SG_POPUP_DATA [popupId]['autoClosePopup']) ? SG_POPUP_DATA [popupId]['autoClosePopup'] : '';

	if (sgOnScrolling) {
		this.onScrolling(popupId);
	}
	else if (sgInActivity) {
		this.showPopupAfterInactivity(popupId);
	}
	else {
		this.showPopup(popupId, true);
	}
};

SGPopup.getCookie = function (cName) {

	var name = cName + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
};

SGPopup.deleteCookie = function (name) {

	document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
};

SGPopup.setCookie = function (cName, cValue, exDays, cPageLevel) {

	var expirationDate = new Date();
	var cookiePageLevel = '';
	var cookieExpirationData = 1;
	if (!exDays || isNaN(exDays)) {
		exDays = 365 * 50;
	}
	if (typeof cPageLevel == 'undefined') {
		cPageLevel = false;
	}
	expirationDate.setDate(expirationDate.getDate() + exDays);
	cookieExpirationData = expirationDate.toString();
	var expires = 'expires='+cookieExpirationData;

	if (exDays == -1) {
		expires = '';
	}

	if (cPageLevel) {
		cookiePageLevel = 'path=/;';
	}

	var value = cValue + ((exDays == null) ? ";" : "; " + expires + ";" + cookiePageLevel);
	document.cookie = cName + "=" + value;
};

SGPopup.prototype.init = function () {

	var that = this;

	this.onCompleate();
	this.popupOpenByCookie();
	this.attacheShortCodeEvent();
	this.attacheClickEvent();
	this.popupOpenByClickUrl();
	this.attacheIframeEvent();
	this.attacheConfirmEvent();
	this.popupClassEventsTrigger();
};

SGPopup.prototype.popupOpenByClickUrl = function () {

	var that = this;
	if(jQuery("a[href*=sg-popup-id-]").length) {
		jQuery("a[href*=sg-popup-id-]").each(function () {
			jQuery(this).bind('click', function (e) {
				e.preventDefault();
				var href = jQuery(this).attr('href');
				var splitData = href.split("sg-popup-id-");

				if(typeof splitData[1] != 'undefined') {
					var popupId = parseInt(splitData[1]);
					that.showPopup(popupId, false);
				}
			})
		});
	}
};

SGPopup.prototype.attacheShortCodeEvent = function () {

	var that = this;

	jQuery(".sg-show-popup").each(function () {
		var popupEvent = jQuery(this).attr("data-popup-event");
		if (typeof popupEvent == 'undefined') {
			popupEvent = 'click';
		}
		/* For counting execute and did it one time for popup open */
		that.sgEventExecuteCount = 0;
		jQuery(this).bind(popupEvent, function () {
			that.sgEventExecuteCount += 1;
			if (that.sgEventExecuteCount > 1) {
				return;
			}
			var sgPopupID = jQuery(this).attr("data-sgpopupid");
			that.showPopup(sgPopupID, false);
		});
	});
};

SGPopup.prototype.attacheConfirmEvent = function () {

	var that = this;

	jQuery("[class*='sg-confirm-popup-']").each(function () {
		jQuery(this).bind("click", function (e) {
			e.preventDefault();
			var currentLink = jQuery(this);
			var className = jQuery(this).attr("class");

			var sgPopupId = that.findPopupIdFromClassNames(className, "sg-confirm-popup-");

			jQuery('#sgcolorbox').bind("sgPopupClose", function () {
				var target = currentLink.attr("target");

				if (typeof target == 'undefined') {
					target = "self";
				}
				var href = currentLink.attr("href");

				if (target == "_blank") {
					window.open(href);
				}
				else {
					window.location.href = href;
				}
			});
			that.showPopup(sgPopupId, false);
		})
	});
};

SGPopup.prototype.attacheIframeEvent = function () {

	var that = this;
	/* When user set popup by class name */
	jQuery("[class*='sg-iframe-popup-']").each(function () {
		var currentLink = jQuery(this);
		jQuery(this).bind("click", function (e) {
			e.preventDefault();
			var className = jQuery(this).attr("class");

			var sgPopupId = that.findPopupIdFromClassNames(className, "sg-iframe-popup-");

			/*This update for dynamic open iframe url for same popup*/
			var linkUrl = currentLink.attr("href");

			if (typeof linkUrl == 'undefined') {
				var childLinkTag = currentLink.find('a');
				linkUrl = childLinkTag.attr("href");
			}

			SG_POPUP_DATA[sgPopupId]['iframe'] = linkUrl;

			that.showPopup(sgPopupId, false);
		});
	});
};

SGPopup.prototype.attacheClickEvent = function () {

	var that = this;
	/* When user set popup by class name */
	jQuery("[class*='sg-popup-id-']").each(function () {
		jQuery(this).bind("click", function (e) {
			e.preventDefault();
			var className = jQuery(this).attr("class");
			var sgPopupId = that.findPopupIdFromClassNames(className, "sg-popup-id-");

			that.showPopup(sgPopupId, false);
		})
	});
};

SGPopup.prototype.popupClassEventsTrigger = function () {

	var popupEvents = this.popupClassEvents;
	var that = this;

	if(popupEvents.length > 0) {

		for (var i in popupEvents) {
			var eventName = popupEvents[i];

			that.attacheCustomEvent(eventName);
		}
	}
};

SGPopup.prototype.attacheCustomEvent = function (eventName) {

	if(typeof eventName == 'undefined' ||  typeof eventName == 'function' || eventName == '') {
		return;
	}
	var that = this;

	jQuery("[class*='sg-popup-"+eventName+"-']").each(function () {
		var eventCount = that.eventExecuteCountByClass;
		jQuery(this).bind(eventName, function () {
			eventCount = ++that.eventExecuteCountByClass;
			if (eventCount > 1) {
				return;
			}
			var className = jQuery(this).attr("class");
			var sgPopupId = that.findPopupIdFromClassNames(className, 'sg-popup-'+eventName+'-');

			that.showPopup(sgPopupId, false);
		})
	});
};

SGPopup.prototype.popupOpenByCookie = function () {

	var popupId = SGPopup.getCookie("sgSubmitReloadingForm");
	popupId = parseInt(popupId);

	if (typeof popupId == 'number') {
		this.showPopup(popupId, false);
	}
};

SGPopup.prototype.findPopupIdFromClassNames = function (className, classKey) {

	var classSplitArray = className.split(classKey);
	var classIdString = classSplitArray['1'];
	/*Get first all number from string*/
	var popupId = classIdString.match(/^\d+/);

	return popupId;
};

SGPopup.prototype.hexToRgba = function (hex, opacity){

	var c;
	if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
		c = hex.substring(1).split('');

		if(c.length == 3){
			c= [c[0], c[0], c[1], c[1], c[2], c[2]];
		}
		c = '0x'+c.join('');
		return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+opacity+')';
	}
	throw new Error('Bad Hex');
};


SGPopup.prototype.sgCustomizeThemes = function (popupId) {

	var popupData = SG_POPUP_DATA[popupId];
	var borderRadiues = popupData['sg3ThemeBorderRadiues'];
	var popupContentOpacity = popupData['popup-background-opacity'];
	var popupContentColor = jQuery('#sgcboxContent').css('background-color');
	var contentBackgroundColor = popupData['sg-content-background-color'];
	var changedColor = popupContentColor.replace(')', ', '+popupContentOpacity+')').replace('rgb', 'rgba');

	if(typeof contentBackgroundColor != 'undefined' && contentBackgroundColor != '') {
		changedColor = this.hexToRgba(contentBackgroundColor, popupContentOpacity);
	}


	if (popupData['theme'] == "colorbox3.css") {
		var borderColor = popupData['sgTheme3BorderColor'];
		var borderRadiues = popupData['sgTheme3BorderRadius'];
		jQuery("#sgcboxLoadedContent").css({'border-color': borderColor});
		jQuery("#sgcboxLoadedContent").css({'border-radius': borderRadiues + "%"});
		jQuery("#sgcboxContent").css({'border-radius': borderRadiues + "%"})
	}

	jQuery('#sgcboxContent').css({'background-color': changedColor});
	jQuery('#sgcboxLoadedContent').css({'background-color': changedColor})

};

SGPopup.prototype.onCompleate = function () {

	jQuery("#sgcolorbox").bind("sgColorboxOnCompleate", function () {

		/* Scroll only inside popup */
		jQuery('#sgcboxLoadedContent').isolatedScroll();
	});
	this.isolatedScroll();
};

SGPopup.prototype.isolatedScroll = function () {

	jQuery.fn.isolatedScroll = function () {
		this.bind('mousewheel DOMMouseScroll', function (e) {
			var delta = e.wheelDelta || (e.originalEvent && e.originalEvent.wheelDelta) || -e.detail,
				bottomOverflow = this.scrollTop + jQuery(this).outerHeight() - this.scrollHeight >= 0,
				topOverflow = this.scrollTop <= 0;

			if ((delta < 0 && bottomOverflow) || (delta > 0 && topOverflow)) {
				e.preventDefault();
			}
		});
		return this;
	};
};

SGPopup.prototype.sgPopupScalingDimensions = function () {

	var popupWrapper = jQuery("#sgcboxWrapper").outerWidth();
	var screenWidth = jQuery(window).width();
	/*popupWrapper != 9999  for resizing case when colorbox is calculated popup dimensions*/
	if (popupWrapper > screenWidth && popupWrapper != 9999) {
		var scaleDegree = screenWidth / popupWrapper;
		jQuery("#sgcboxWrapper").css({
			"transform-origin": "0 0",
			'transform': "scale(" + scaleDegree + ", 1)"
		});
		popupWrapper = 0;
	}
	else {
		jQuery("#sgcboxWrapper").css({
			"transform-origin": "0 0",
			'transform': "scale(1, 1)"
		})
	}
};

SGPopup.prototype.sgPopupScaling = function () {

	var that = this;
	jQuery("#sgcolorbox").bind("sgColorboxOnCompleate", function () {
		that.sgPopupScalingDimensions();
	});
	jQuery(window).resize(function () {
		setTimeout(function () {
			that.sgPopupScalingDimensions();
		}, 1000);
	});
};

SGPopup.prototype.varToBool = function (optionName) {

	var returnValue = (optionName) ? true : false;
	return returnValue;
};

SGPopup.prototype.canOpenPopup = function (id, openOnce, isOnLoad) {

	if (!isOnLoad) {
		return true;
	}

	var currentCookies = SGPopup.getCookie('sgPopupCookieList');
	if (currentCookies) {
		currentCookies = JSON.parse(currentCookies);

		for (var cookieIndex in currentCookies) {
			var cookieName = currentCookies[cookieIndex];
			var cookieData = SGPopup.getCookie(cookieName + id);

			if (cookieData) {
				return false;
			}
		}
	}

	var popupCookie = SGPopup.getCookie('sgPopupDetails' + id);
	var popupType = this.popupType;

	/*for popup this often case */
	if (openOnce && popupCookie != '') {
		return this.canOpenOnce(id);
	}

	return true;
};

SGPopup.prototype.canOpenOnce = function(id) {

	var cookieData = SGPopup.getCookie('sgPopupDetails'+id);
	if(!cookieData) {
		return true;
	}
	var cookieData = JSON.parse(cookieData);

	if(cookieData.popupId == id && cookieData.openCounter >= this.numberLimit) {
		return false;
	}
	else {
		return true
	}

};


SGPopup.prototype.setFixedPosition = function (sgPositionLeft, sgPositionTop, sgPositionBottom, sgPositionRight, sgFixedPositionTop, sgFixedPositionLeft) {

	this.positionLeft = sgPositionLeft;
	this.positionTop = sgPositionTop;
	this.positionBottom = sgPositionBottom;
	this.positionRight = sgPositionRight;
	this.initialPositionTop = sgFixedPositionTop;
	this.initialPositionLeft = sgFixedPositionLeft;
};

SGPopup.prototype.percentToPx = function (percentDimention, screenDimension) {

	var dimension = parseInt(percentDimention) * screenDimension / 100;
	return dimension;
};

SGPopup.prototype.getPositionPercent = function (needPercent, screenDimension, dimension) {

	var sgPosition = (((this.percentToPx(needPercent, screenDimension) - dimension / 2) / screenDimension) * 100) + "%";
	return sgPosition;
};

SGPopup.prototype.getPreviewPopupId = function(popupId)
{
	var currentUrl = window.location.href;

	var classSplitArray = currentUrl.split('sg_popup_preview_id=');
	var classIdString = classSplitArray['1'];

	if (typeof classIdString == 'undefined') {
		return false;
	}
	/*Get first all number from string*/
	var urlId = classIdString.match(/^\d+/);

	return urlId;
};

SGPopup.prototype.currentPopupId = false;

SGPopup.prototype.restrictIfThereIsPreviewPopup = function(popupId)
{
	var previewPopupId = this.getPreviewPopupId();
	if (previewPopupId) {
		if (popupId != previewPopupId) {
			return false;
		}

		if (this.currentPopupId) {
			return false;
		}
	}

	return true;
};

SGPopup.prototype.showPopup = function (id, isOnLoad) {

	var that = this;

	/*When id does not exist*/
	if (!id) {
		return;
	}

	if (typeof SG_POPUP_DATA[id] == "undefined") {
		return;
	}

	if (!this.restrictIfThereIsPreviewPopup(id)) {
		return false;
	}

	this.popupData = SG_POPUP_DATA[id];
	this.popupType = this.popupData['type'];
	this.isOnLoad = isOnLoad;
	this.openOnce = this.varToBool(this.popupData['repeatPopup']);
	this.numberLimit = this.popupData['popup-appear-number-limit'];

	if (typeof that.removeCookie !== 'undefined') {
		that.removeCookie(this.openOnce);
	}

	if (!this.canOpenPopup(this.popupData['id'], this.openOnce, isOnLoad)) {
		return;
	}

	popupColorboxUrl = SG_APP_POPUP_URL + '/style/sgcolorbox/sgthemes.css';
	head = document.getElementsByTagName('head')[0];
	link = document.createElement('link');
	link.type = "text/css";
	link.id = "sg_colorbox_theme-css";
	link.rel = "stylesheet";
	link.href = popupColorboxUrl;
	document.getElementsByTagName('head')[0].appendChild(link);
	var img = document.createElement('img');
	sgAddEvent(img, "error", function () {
		that.sgShowColorboxWithOptions();
	});
	setTimeout(function () {
		img.src = popupColorboxUrl;
	}, 0);
};

SGPopup.setToPopupsCookiesList = function (cookieName) {

	var currentCookies = SGPopup.getCookie('sgPopupCookieList');

	if (!currentCookies) {
		currentCookies = [];
	}
	else {
		currentCookies = JSON.parse(currentCookies);
	}

	if (jQuery.inArray(cookieName, currentCookies) == -1) {
		cookieName = currentCookies.push(cookieName);
	}

	SGPopup.deleteCookie('sgPopupCookieList');
	var currentCookies = JSON.stringify(currentCookies);
	SGPopup.setCookie('sgPopupCookieList', currentCookies, 365, true);
};

SGPopup.prototype.popupThemeDefaultMeasure = function () {

	var themeName = this.popupData['theme'];
	var defaults = SGPopup.sgColorBoxDeafults;
	/*return theme id*/
	var themeId = themeName.replace( /(^.+\D)(\d+)(\D.+$)/i,'$2');

	return defaults[themeId];
};

SGPopup.prototype.changePopupSettings = function () {

	var popupData = this.popupData;
	var popupDimensionMode = popupData['popup-dimension-mode'];
	var maxWidth = popupData['maxWidth'];
	var screenWidth = jQuery(window).width();
	var popupResponsiveDimensionMeasure = popupData['popup-responsive-dimension-measure'];
	var isMaxWidthInPercent = maxWidth.indexOf("%") != -1 ? true: false;

	if(popupDimensionMode == 'responsiveMode') {

		if(popupResponsiveDimensionMeasure == 'auto') {
			this.popupMaxWidth = '100%';

			/*When max with in px*/
			if(maxWidth && !isMaxWidthInPercent && parseInt(maxWidth) < screenWidth) {
				this.popupMaxWidth = parseInt(maxWidth);
			}
			else if(isMaxWidthInPercent && parseInt(maxWidth) < 100) { /*For example when max width is 800% */
				this.popupMaxWidth = maxWidth;
			}

		}
	}
};

SGPopup.prototype.resizePopup = function (settings) {

	var that = this;
	/*Initial window dimensions*/
	window.sgpbInitialWindowWith = window.innerWidth;
	window.sgpbInitialWindowHeight = window.innerHeight;

	function resizeColorBox () {
		var currentWindow = jQuery(this);
		var windowWidth = currentWindow.width();
		var windowHeight = currentWindow.height();

		var maxWidth = that.popupData['maxWidth'];
		var maxHeight = that.popupData['maxHeight'];

		if(!maxWidth) {
			maxWidth = '100%';
		}

		if(!maxHeight) {
			maxHeight = '100%';
		}

		if (that.resizeTimer) clearTimeout(that.resizeTimer);
		that.resizeTimer = setTimeout(function() {
			if (jQuery('#sgcboxOverlay').is(':visible')) {
				if (window.sgpbFullScreen === true) {
					window.sgpbShouldResize = false;
					window.sgpbFullScreen = false;
				}
				else {
					window.sgpbShouldResize = true;
				}

				if ((window.innerHeight == screen.height) || (!window.screenTop && !window.screenY)) {
					window.sgpbShouldResize = false;
				}

				if (maxWidth.indexOf("%") != -1) {
					maxWidth = that.percentToPx(maxWidth, windowWidth);
				}
				else {
					maxWidth = parseInt(maxWidth);
				}

				if (maxHeight.indexOf("%") != -1) {
					maxHeight = that.percentToPx(maxHeight, windowHeight);
				}
				else {
					maxHeight = parseInt(maxHeight);
				}

				if(maxWidth > windowWidth) {
					maxWidth = windowWidth;
				}

				if(maxHeight > windowHeight) {
					maxHeight = windowHeight;
				}

				settings.maxWidth = maxWidth;
				settings.maxHeight = maxHeight;
				var hasFocusedInput = false;
				jQuery('#sgcboxLoadedContent input,#sgcboxLoadedContent textarea').each(function() {

					if(jQuery(this).is(':focus')) {
						hasFocusedInput = true;
					}
				});
				/*For mobile when has some focused input popup does not resize*/
				if( /Android|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && hasFocusedInput ) {
					window.sgpbShouldResize = false;
				}
				/*For just call resize*/
				if (currentWindow.width() == window.sgpbInitialWindowWith && currentWindow.height() == window.sgpbInitialWindowHeight) {
					window.sgpbShouldResize = false;
				}

				if(window.sgpbShouldResize) {
					jQuery.sgcolorbox(settings);
					jQuery('#sgcboxLoadingGraphic').css({'display': 'none'});
				}
			}
		}, 500);
	}

	jQuery(window).resize(resizeColorBox);
	window.addEventListener("orientationchange", resizeColorBox, false);

	document.addEventListener("fullscreenchange", function() {
		window.sgpbFullScreen = true;
	}, false);

	document.addEventListener("msfullscreenchange", function() {
		window.sgpbFullScreen = true;
	}, false);

	document.addEventListener("mozfullscreenchange", function() {
		window.sgpbFullScreen = true;
	}, false);

	document.addEventListener("webkitfullscreenchange", function() {
		window.sgpbFullScreen = true;
	}, false);
};

SGPopup.prototype.resizeAfterContentResizing = function () {

	var visibilityClasses = [".js-validate-required", ".js-sgpb-visibility"];
	var maxHeight = this.popupData['maxHeight'];
	var diffContentHight = jQuery("#sgcboxWrapper").height() - jQuery("#sgcboxLoadedContent").height();
	for(var index in visibilityClasses) {
		jQuery(visibilityClasses[index]).visibilityChanged({
			callback: function(element, visible) {
				if(maxHeight !== '' && parseInt(maxHeight) < (jQuery("#sgcboxLoadedContent").prop('scrollHeight') + diffContentHight)) {
					return false;
				}
				jQuery.sgcolorbox.resize();
			},
			runOnLoad: false,
			frequency: 2000
		});
	}

	new ResizeSensor(jQuery('#sgcboxLoadedContent'), function(){
		if(maxHeight !== '' && parseInt(maxHeight) < (jQuery("#sgcboxLoadedContent").prop('scrollHeight') + diffContentHight)) {
			return false;
		}
		jQuery.sgcolorbox.resize();
	});

};

SGPopup.prototype.sgColorboxContentMode = function() {

	var that = this;

	this.sgColorboxContentTypeReset();
	var popupType = this.popupData['type'];
	var popupHtml = (this.popupData['html'] == '') ? '&nbsp;' : this.popupData['html'];
	var popupImage = this.popupData['image'];
	var popupIframeUrl = this.popupData['iframe'];
	var popupVideo = this.popupData['video'];
	var popupId = this.popupData['id'];

	popupImage = (popupImage) ? popupImage : false;
	popupVideo = (popupVideo) ? popupVideo : false;
	popupIframeUrl = (popupIframeUrl) ? popupIframeUrl : false;

	if(popupType == 'image') {
		this.sgColorboxPhoto = true;
		this.sgColorboxHref = popupImage;
	}

	if(popupIframeUrl) {
		this.sgColorboxIframe = true;
		this.sgColorboxHref = popupIframeUrl;
	}

	if(popupVideo) {
		this.sgColorboxIframe = true;
		this.sgColorboxHref = popupVideo;
	}

	/*this condition jQuery('#sg-popup-content-wrapper-'+popupId).length != 0 for backward compatibility*/
	if(popupHtml && jQuery('#sg-popup-content-wrapper-'+popupId).length != 0) {
		this.sgColorboxInline = true;
		this.sgColorboxHref = '#sg-popup-content-wrapper-'+popupId;
	}
	else {
		this.sgColorboxHtml = popupHtml;
	}
};

SGPopup.prototype.addToCounter = function (popupId) {

	var params = {};
	params.popupId = popupId;

	var data = {
		action: 'send_to_open_counter',
		ajaxNonce: SGPBParams.ajaxNonce,
		params: params
	};

	jQuery.post(SGPBParams.ajaxUrl, data, function(response,d) {

	});
};
SGPopup.soundValue = 1;

SGPopup.prototype.contentClickRedirect = function () {

	var popupData = this.popupData;
	var contentClickBehavior = popupData['content-click-behavior'];
	var clickRedirectToUrl = popupData['click-redirect-to-url'];
	var redirectToNewTab = popupData['redirect-to-new-tab'];

	/* If has url for redirect */
	if ((contentClickBehavior !== 'close' || clickRedirectToUrl !== '') && typeof contentClickBehavior !== 'undefined') {
		jQuery('#sgcolorbox').css({
			"cursor": 'pointer'
		});
	}

	jQuery(".sg-current-popup-" + popupData['id']).bind('click', function () {
		if (contentClickBehavior == 'close' || clickRedirectToUrl == '' || typeof contentClickBehavior == 'undefined') {
			jQuery.sgcolorbox.close();
		}
		else {
			if (!redirectToNewTab) {
				window.location = clickRedirectToUrl;
			}
			else {
				window.open(clickRedirectToUrl);
			}
		}

	});
};

SGPopup.prototype.closeButtonDelay = function (buttonDelayValue) {
	setTimeout(function(){
			jQuery('#sgcboxClose').attr('style', 'display: block !important;');
		},
		buttonDelayValue * 1000 /* received values covert to seconds */
	);
}

SGPopup.prototype.htmlIframeFilterForOpen = function (popupEventName) {

	var popupId = this.popupData['id'];
	var popupContentWrapper = jQuery('#sg-popup-content-wrapper-'+popupId);

	if(!popupContentWrapper.length || typeof this.popupData['htmlIframeUrl'] == 'undefined') {
		return;
	}

	if(!popupContentWrapper.find('iframe').length) {
		return;
	}

	if(popupEventName == 'open') {
		var iframeUrl = this.popupData['htmlIframeUrl'];
		popupContentWrapper.find('iframe').attr('src', iframeUrl);
		return;
	}

	popupContentWrapper.find('iframe').attr('src', ' ');
	return;
};

SGPopup.prototype.getSearchDataFromContent = function(content)
{
	var pattern = /\[(\[?)(pbvariable)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]\*+(?:\[(?!\/\2\])[^\[]\*+)\*+)\[\/\2\])?)(\]?)/gi;
	var match;
	var collectedData = [];

	while (match = pattern.exec(content)) {
		var currentSearchData = [];
		currentSearchData['replaceString'] = match[0];
		var parseAttributes = /\s(\w+?)="(.+?)"/g;
		var attributes;
		var attributesKeyValue = [];
		while (attributes = parseAttributes.exec(match[3])) {
			attributesKeyValue[attributes[1]] = attributes[2];
		}
		currentSearchData['searchData'] = attributesKeyValue;
		collectedData.push(currentSearchData);
	}

	return collectedData;
};

SGPopup.prototype.replaceWithCustomShortcode = function(popupId)
{
	var currentHtmlContent = jQuery('#sg-popup-content-wrapper-'+popupId).html();
	var searchData = this.getSearchDataFromContent(currentHtmlContent);
	var that = this;

	if (!searchData.length) {
		return false;
	}

	for (var index in searchData) {
		var currentSearchData = searchData[index];
		var searchAttributes = currentSearchData['searchData'];

		if (typeof searchAttributes['selector'] == 'undefined' || typeof searchAttributes['attribute'] == 'undefined') {
			that.replaceShortCode(currentSearchData['replaceString'], '');
			continue;
		}

		try {
			if (!jQuery(searchAttributes['selector']).length) {
				that.replaceShortCode(currentSearchData['replaceString'], '');
				continue;
			}
		}
		catch (e) {
			that.replaceShortCode(currentSearchData['replaceString'], '');
			continue;
		}

		var replaceName = jQuery(searchAttributes['selector']).attr(searchAttributes['attribute']);

		if (typeof replaceName == 'undefined') {
			that.replaceShortCode(currentSearchData['replaceString'], '');
			continue;
		}

		that.replaceShortCode(currentSearchData['replaceString'], replaceName);
	}
};

SGPopup.prototype.replaceShortCode = function(shortCode, replaceText)
{
	var popupId = parseInt(this.popupData['id']);
	var popupContentWrapper = jQuery('#sg-popup-content-wrapper-'+popupId);

	if (!popupContentWrapper.length) {
		return false;
	}

	var currentHtmlContent = popupContentWrapper.contents();

	if (!currentHtmlContent.length) {
		return false;
	}

	for (var index in currentHtmlContent) {
		var currentChild = currentHtmlContent[index];
		var currentChildNodeValue = currentChild.nodeValue;
		var currentChildNodeType = currentChild.nodeType;

		if (currentChildNodeType != Node.TEXT_NODE) {
			continue;
		}

		if (currentChildNodeValue.indexOf(shortCode) != -1) {
			currentChild.nodeValue =  currentChildNodeValue.replace(shortCode, replaceText);
		}
	}

	return true;
};

SGPopup.prototype.colorboxEventsListener = function ()
{
	var that = this;
	var disablePageScrolling = this.varToBool(this.popupData['disable-page-scrolling']);
	var popupOpenSound = this.varToBool(this.popupData['popupOpenSound']);
	var popupContentBgImage = this.varToBool(this.popupData['popupContentBgImage']);
	var popupOpenSoundFile = this.popupData['popupOpenSoundFile'];
	var popupContentBgImageUrl = this.popupData['popupContentBgImageUrl'];
	var popupContentBackgroundSize = this.popupData['popupContentBackgroundSize'];
	var popupContentBackgroundRepeat = this.popupData['popupContentBackgroundRepeat'];
	var repetitivePopup = this.popupData['repetitivePopup'];
	var repetitivePopupPeriod = this.popupData['repetitivePopupPeriod'];
	var buttonDelayValue = this.popupData['buttonDelayValue'];
	/*this.popupCloseButton*/
	repetitivePopupPeriod = parseInt(repetitivePopupPeriod)*1000;
	var repetitiveTimeout = null;

	if(popupOpenSound && popupOpenSoundFile && typeof that.audio == 'undefined') {
		that.audio = new Audio(popupOpenSoundFile);
	}
	jQuery('#sgcolorbox').one("sgColorboxOnOpen", function (e, args) {
		var popupId = args.popupId;
		that.replaceWithCustomShortcode(popupId);
		if(disablePageScrolling) {
			jQuery('html').addClass('sgpb-disable-page-scrolling');
		}
	});

	jQuery('#sgcolorbox').one("sgColorboxOnCompleate", function (e, args) {

		var popupId = args.popupId;
		that.addToCounter(popupId);

		if(that.popupData['type'] == 'html') {
			that.htmlIframeFilterForOpen('open');
		}

		/* if close button is set to be shown and has delay value */
		if (that.popupCloseButton && buttonDelayValue) {
			that.closeButtonDelay(buttonDelayValue);
		}

		if(that.popupContentClick) {
			that.contentClickRedirect();
		}

		clearInterval(repetitiveTimeout);
		if(that.varToBool(that.popupData['popupOpenSound']) && popupOpenSoundFile) {
			/*
			 * SGPopup.soundValue -> 1 sound should play
			 * SGPopup.soundValue -> 2 sound should pause
			 * */
			if (SGPopup.soundValue == 1) {

				that.audio.play();
				SGPopup.soundValue = 2;

			} else if (SGPopup.soundValue == 2) {
				that.audio.pause();
				SGPopup.soundValue = 1;
			}
		}
		if(popupContentBgImage) {
			jQuery('#sgcboxLoadedContent').css({
				'background-image': 'url('+popupContentBgImageUrl+')',
				'background-size': popupContentBackgroundSize,
				'background-repeat': popupContentBackgroundRepeat
			});
		}
	});

	jQuery('#sgcolorbox').one("sgPopupCleanup", function () {
		if(repetitivePopup) {
			repetitiveTimeout = setTimeout(function() {
				var sgPoupFrontendObj = new SGPopup();
				sgPoupFrontendObj.popupOpenById(that.popupData['id']);
			}, repetitivePopupPeriod);
		}
		if(that.varToBool(that.popupData['popupOpenSound']) && popupOpenSoundFile) {
			if(typeof that.audio != 'undefined') {
				that.audio.pause();
				delete that.audio;
			}

			SGPopup.soundValue = 1;
		}
	});

	jQuery('#sgcolorbox').one("sgPopupClose", function () {
		if(disablePageScrolling) {
			jQuery('html').removeClass('sgpb-disable-page-scrolling');
		}
		if(that.popupData['type'] == 'html') {
			that.htmlIframeFilterForOpen('close');
		}
	});
};

SGPopup.prototype.sgShowColorboxWithOptions = function () {

	var that = this;
	setTimeout(function () {

		that.colorboxEventsListener();
		var sgPopupFixed = that.varToBool(that.popupData['popupFixed']);
		var popupId = that.popupData['id'];
		that.popupOverlayClose = that.varToBool(that.popupData['overlayClose']);
		that.popupContentClick = that.varToBool(that.popupData['contentClick']);
		var popupReposition = that.varToBool(that.popupData['reposition']);
		var popupScrolling = that.varToBool(that.popupData['scrolling']);
		var popupScaling = that.varToBool(that.popupData['scaling']);
		that.popupEscKey = that.varToBool(that.popupData['escKey']);
		that.popupCloseButton = that.varToBool(that.popupData['closeButton']);
		var countryStatus = that.varToBool(that.popupData['countryStatus']);
		var popupForMobile = that.varToBool(that.popupData['forMobile']);
		var onlyMobile = that.varToBool(that.popupData['openMobile']);
		var popupCantClose = that.varToBool(that.popupData['disablePopup']);
		var disablePopupOverlay = that.varToBool(that.popupData['disablePopupOverlay']);
		var popupAutoClosePopup = that.varToBool(that.popupData['autoClosePopup']);
		var saveCookiePageLevel = that.varToBool(that.popupData['save-cookie-page-level']);
		var popupClosingTimer = that.popupData['popupClosingTimer'];

		if (popupScaling) {
			that.sgPopupScaling();
		}
		if (popupCantClose) {
			that.cantPopupClose();
		}
		that.popupMaxWidth = (!that.popupData['maxWidth']) ? '100%' : that.popupData['maxWidth'];
		var popupPosition = sgPopupFixed ? that.popupData['fixedPostion'] : '';
		var popupVideo = that.popupData['video'];
		var popupOverlayColor = that.popupData['sgOverlayColor'];
		var contentBackgroundColor = that.popupData['sg-content-background-color'];
		var popupDimensionMode = that.popupData['popup-dimension-mode'];
		var popupResponsiveDimensionMeasure = that.popupData['popup-responsive-dimension-measure'];
		var popupWidth = that.popupData['width'];
		var popupHeight = that.popupData['height'];
		var popupOpacity = that.popupData['opacity'];
		var popupMaxHeight = (!that.popupData['maxHeight']) ? '100%' : that.popupData['maxHeight'];
		var popupInitialWidth = that.popupData['initialWidth'];
		var popupInitialHeight = that.popupData['initialHeight'];
		var popupEffectDuration = that.popupData['duration'];
		var popupEffect = that.popupData['effect'];
		var pushToBottom = that.popupData['pushToBottom'];
		var onceExpiresTime = parseInt(that.popupData['onceExpiresTime']);
		var sgType = that.popupData['type'];
		var overlayCustomClass = that.popupData['sgOverlayCustomClasss'];
		var contentCustomClass = that.popupData['sgContentCustomClasss'];
		var popupTheme = that.popupData['theme'];
		var themeStringLength = popupTheme.length;
		var customClassName = popupTheme.substring(0, themeStringLength - 4);
		var closeButtonText = that.popupData['theme-close-text'];

		that.sgColorboxContentMode();

		if(popupDimensionMode == 'responsiveMode') {

			popupWidth = '';
			if(popupResponsiveDimensionMeasure != 'auto') {
				popupWidth = parseInt(popupResponsiveDimensionMeasure)+'%';
			}

			if(that.popupData['type'] != 'iframe' && that.popupData['type'] != 'video') {
				popupHeight = '';
			}
		}

		var sgScreenWidth = jQuery(window).width();
		var sgScreenHeight = jQuery(window).height();

		var sgIsWidthInPercent = popupWidth.indexOf("%");
		var sgIsHeightInPercent = popupHeight.indexOf("%");
		var sgPopupHeightPx = popupHeight;
		var sgPopupWidthPx = popupWidth;
		if (sgIsWidthInPercent != -1) {
			sgPopupWidthPx = that.percentToPx(popupWidth, sgScreenWidth);
		}
		if (sgIsHeightInPercent != -1) {
			sgPopupHeightPx = that.percentToPx(popupHeight, sgScreenHeight);
		}
		/*for when width or height in px*/
		sgPopupWidthPx = parseInt(sgPopupWidthPx);
		sgPopupHeightPx = parseInt(sgPopupHeightPx);

		var staticPositionWidth = sgPopupWidthPx;
		if(staticPositionWidth > sgScreenWidth) {
			staticPositionWidth = sgScreenWidth;
		}

		var popupPositionTop = that.getPositionPercent("50%", sgScreenHeight, sgPopupHeightPx);
		var popupPositionLeft = that.getPositionPercent("50%", sgScreenWidth, staticPositionWidth);
		var posTopForSettings = popupPositionTop;

		if (popupPosition == 1) {
			that.setFixedPosition('0%', '3%', false, false, 0, 0);
		} else if (popupPosition == 2) {
			that.setFixedPosition(popupPositionLeft, '3%', false, false, 0, 50);
		} else if (popupPosition == 3) {
			that.setFixedPosition(false, '3%', false, '0%', 0, 90);
		} else if (popupPosition == 4) {

			if(isNaN(popupPositionTop)) {
				posTopForSettings = false;
			}
			that.setFixedPosition('0%', posTopForSettings, false, false, posTopForSettings, 0);
		} else if (popupPosition == 5) {
			sgPopupFixed = true;
			that.setFixedPosition(false, false, false, false, 50, 50);
		} else if (popupPosition == 6) {
			if(isNaN(popupPositionTop)) {
				posTopForSettings = false;
			}
			that.setFixedPosition('0%', posTopForSettings, false, '0%', 50, 90);
		} else if (popupPosition == 7) {
			that.setFixedPosition('0%', false, '0%', false, 90, 0);
		} else if (popupPosition == 8) {
			that.setFixedPosition(popupPositionLeft, false, '0%', false, 90, 50);
		} else if (popupPosition == 9) {
			that.setFixedPosition(false, false, '0%', '0%', 90, 90);
		}
		if (!sgPopupFixed) {
			that.setFixedPosition(false, false, false, false, 50, 50);
		}

		var userDevice = false;
		if (popupForMobile) {
			userDevice = that.forMobile();
		}

		if (popupAutoClosePopup) {
			setTimeout(that.autoClosePopup, popupClosingTimer * 1000);
		}

		if (disablePopupOverlay) {
			that.sgTrapFocus = false;
			that.disablePopupOverlay();
		}

		if (onlyMobile) {
			var openOnlyMobile = false;
			openOnlyMobile = that.forMobile();
			if (openOnlyMobile == false) {
				return;
			}
		}

		if (userDevice) {
			return;
		}
		that.changePopupSettings();
		SG_POPUP_SETTINGS = {
			popupId: popupId,
			html: that.sgColorboxHtml,
			photo: that.sgColorboxPhoto,
			iframe: that.sgColorboxIframe,
			href: that.sgColorboxHref,
			inline: that.sgColorboxInline,
			width: popupWidth,
			height: popupHeight,
			className: customClassName,
			close: closeButtonText,
			overlayCutsomClassName: overlayCustomClass,
			contentCustomClassName: contentCustomClass,
			onOpen: function () {
				that.currentPopupId = popupId;
				if(that.sgColorboxInline) {
					var contentImage = jQuery(that.sgColorboxHref).find('img').first();
					if(contentImage.length) {
						var height = contentImage.attr('height');
						height = parseInt(height);
						contentImage.attr('style', 'height: '+height+' !important');
					}
				}
				jQuery('#sgcolorbox').removeAttr('style');
				jQuery('#sgcolorbox').removeAttr('left');
				jQuery('#sgcolorbox').css('top', '' + that.initialPositionTop + '%');
				jQuery('#sgcolorbox').css('left', '' + that.initialPositionLeft + '%');
				jQuery('#sgcolorbox').css('animation-duration', popupEffectDuration + "s");
				jQuery('#sgcolorbox').css('-webkit-animation-duration', popupEffectDuration + "s");
				jQuery("#sgcolorbox").addClass('sg-animated ' + popupEffect + '');
				jQuery("#sgcboxOverlay").addClass("sgcboxOverlayBg");
				jQuery("#sgcboxOverlay").removeAttr('style');

				if (popupOverlayColor) {
					jQuery("#sgcboxOverlay").css({'background': 'none', 'background-color': popupOverlayColor});
				}
				var openArgs = {
					popupId: popupId
				};

				jQuery('#sgcolorbox').trigger("sgColorboxOnOpen", openArgs);

			},
			onLoad: function () {
			},
			onComplete: function () {
				if (contentBackgroundColor) {
					jQuery("#sgcboxLoadedContent").css({'background-color': contentBackgroundColor});
				}
				jQuery("#sgcboxLoadedContent").addClass("sg-current-popup-" + that.popupData['id']);
				var completeArgs = {
					pushToBottom: pushToBottom,
					popupId: popupId
				};

				jQuery('#sgcolorbox').trigger("sgColorboxOnCompleate", completeArgs);

				var sgpopupInit = new SgPopupInit(that.popupData);
				sgpopupInit.overallInit();
				/* For specific popup Like Countdown AgeRestcion popups */
				sgpopupInit.initByPopupType();
				that.sgCustomizeThemes(that.popupData['id']);
				if(popupDimensionMode == 'responsiveMode') {
					/* it's temporary deactivated  for colorbox resize good work that.resizeAfterContentResizing(); */
					that.resizePopup(SG_POPUP_SETTINGS);
					jQuery('#sgcboxLoadingGraphic').remove()
				}
			},
			onCleanup: function () {
				jQuery('#sgcolorbox').trigger("sgPopupCleanup", []);
			},
			onClosed: function () {
				that.currentPopupId = false;
				jQuery("#sgcboxLoadedContent").removeClass("sg-current-popup-" + that.popupData['id']);
				jQuery('#sgcolorbox').trigger("sgPopupClose", []);
			},
			trapFocus: that.sgTrapFocus,
			opacity: popupOpacity,
			escKey: that.popupEscKey,
			closeButton: that.popupCloseButton,
			fixed: sgPopupFixed,
			top: that.positionTop,
			bottom: that.positionBottom,
			left: that.positionLeft,
			right: that.positionRight,
			scrolling: popupScrolling,
			reposition: popupReposition,
			overlayClose: that.popupOverlayClose,
			maxWidth: that.popupMaxWidth,
			maxHeight: popupMaxHeight,
			initialWidth: popupInitialWidth,
			initialHeight: popupInitialHeight
		};

		if(popupDimensionMode == 'responsiveMode') {
			/*colorbox open speed*/
			SG_POPUP_SETTINGS.speed = 10;
		}
		if (!that.currentPopupId) {
			jQuery.sgcolorbox(SG_POPUP_SETTINGS);
		}

		if (countryStatus == true && typeof SgUserData != "undefined") {
			jQuery.cookie("SG_POPUP_USER_COUNTRY_NAME", SgUserData.countryIsoName, {expires: 365});
		}
		/* Cookie can't be set here as it's set in Age Restriction popup when the user clicks "yes" */
		if (that.popupData['id'] && that.isOnLoad == true && that.openOnce != '' && that.popupData['type'] != "ageRestriction") {
			var sgCookieData = '';

			var currentCookie = SGPopup.getCookie('sgPopupDetails' + that.popupData['id']);

			if (!currentCookie) {
				var openCounter = 1;
			}
			else {
				var currentCookie = JSON.parse(currentCookie);
				var openCounter = currentCookie.openCounter += 1;
			}
			sgCookieData = {
				'popupId': that.popupData['id'],
				'openCounter': openCounter,
				'openLimit': that.numberLimit
			};

			/*!saveCookiePageLevel it's mean for all site level*/
			SGPopup.setCookie("sgPopupDetails"+that.popupData['id'],JSON.stringify(sgCookieData), onceExpiresTime, !saveCookiePageLevel);
		}


		jQuery('#sgcolorbox').bind('sgPopupClose', function (e) {
			/* reset event execute count for popup open */
			that.sgEventExecuteCount = 0;
			that.eventExecuteCountByClass = 0;
			jQuery('#sgcolorbox').removeClass(customClassName);
			/* Remove custom class for another popup */
			jQuery('#sgcboxOverlay').removeClass(customClassName);
			jQuery('#sgcolorbox').removeClass(popupEffect);
			/* Remove animated effect for another popup */
		});

	}, this.popupData['delay'] * 1000);
};

jQuery(document).ready(function ($) {

	var popupObj = new SGPopup();
	popupObj.init();
});
