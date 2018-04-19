function beckend() {

}

beckend.prototype.sgInit =  function() {
	this.imageUpload(); /* It's Image Upload function */
	this.soundUpload();
	this.soundPreview();
	this.deletePopup(); /* Delete popup */
	this.titleNotEmpty(); /* Check title is Empty */
	this.showThemePicture(); /* Show themes pictures */
	this.showEffects(); /* Show effect type */
	this.pageAcordion(); /* For page accordion divs */
	this.fixedPostionSelection(); /* Functionality for selected position */
	this.showInfo(); /* Show description options */
	this.opacityRange();
	this.subOptionContents();
	this.addCountries();
	this.showCloseTextFieldForTheme();
	this.popupReview();
	this.reviewPopup();
	this.colorPicekr(); /* Color picker */
	this.switchPopupActive();
	this.initAccordions();
	this.popupPreview();
};

beckend.prototype.switchPopupActive = function() {
	var that = this;

	jQuery(".sg-switch-checkbox").bind('change', function() {
		var dataOptions = {};
		var popupId = jQuery(this).attr('data-switch-id');
		var ajaxNonce = jQuery(this).attr('data-checkbox-ajaxNonce');
		dataOptions.ajaxNonce = ajaxNonce;

		if(jQuery(this).is(":checked")) {
			that.chengePopupStatus('on', popupId, dataOptions);
		}
		else {
			that.chengePopupStatus('off', popupId, dataOptions);
		}
	});
};

beckend.prototype.chengePopupStatus = function(status, popupId, dataOptions) {
	var data = {
		action: 'change_popup_status',
		ajaxNonce: dataOptions.ajaxNonce,
		popupId: popupId,
		popupStatus: status
	};

	jQuery.post(ajaxurl, data, function(response,d) {

	});
};

beckend.prototype.colorPicekr = function() {
	var that = this;
	var sgColorPicker = jQuery('.sgOverlayColor').wpColorPicker({
		change: function() {
			var sgColorpicker = jQuery(this);
			that.changeColor(sgColorpicker);
		}
	});
	jQuery(".wp-picker-holder").bind('click',function() {
		var selectedInput = jQuery(this).prev().find('.sgOverlayColor');
		that.changeColor(selectedInput);
	});
};

beckend.prototype.changeColor = function(elemet) {
	var selectedName = elemet.attr("name");
	var elementVal = elemet.val();
	if(selectedName == 'countdownNumbersTextColor') {
		jQuery("#sg-counts-text").remove();
		jQuery("body").append("<style id=\"sg-counts-text\">.flip-clock-wrapper ul li a div div.inn { color: "+elementVal+"; }</style>");
	}
	if(selectedName == 'countdownNumbersBgColor') {
		jQuery("#sg-counts-style").remove();
		jQuery("body").append("<style id=\"sg-counts-style\">.flip-clock-wrapper ul li a div div.inn { background-color: "+elementVal+"; }</style>");
	}
};

beckend.prototype.reviewPopup = function () {

	jQuery('#sgcolorbox').ready(function () {
		jQuery('#sgcolorbox').on('sgPopupCleanup', function () {
			var ajaxNonce = jQuery(this).attr('data-ajaxnonce');

			var data = {
				action: 'change_review_popup_show_period',
				ajaxNonce: SGPB_AJAX_NONCE
			};
			jQuery.post(ajaxurl, data, function(response,d) {
				jQuery.sgcolorbox.close();
			});
		});
	});

};

beckend.prototype.popupReview = function() {
	jQuery(".sg-dont-show-agin").on("click", function() {

		var ajaxNonce = jQuery(this).attr('data-ajaxnonce');

		var data = {
			action: 'close_review_panel',
			ajaxNonce: ajaxNonce
		};
		jQuery.post(ajaxurl, data, function(response,d) {

		});
		jQuery( ".sg-info-panel-wrapper" ).hide(300);
	});

	jQuery('.sg-info-close').on('click', function() {
		jQuery( ".sg-info-panel-wrapper" ).hide(300);
	});
};

beckend.prototype.checkboxAcordion = function(element) {
	if(!element.is(':checked')) {
		element.nextAll("div").first().css({'display': 'none'});
	}
	else {
		element.nextAll("div").first().css({'display':'inline-block'});
	}
};

beckend.prototype.soundUpload = function () {

	var custom_uploader;
	jQuery('#js-upload-open-sound-button').click(function(e) {
		e.preventDefault();
		/* If the uploader object has already been created, reopen the dialog */
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}
		/* Extend the wp.media object */
		custom_uploader = wp.media.frames.file_frame = wp.media({
			titleFF: 'Change the sound',
			button: {
				text: 'Change the sound'
			},
			library : { type  :  ['audio/mpeg', 'audio/wav']},
			multiple: false
		});
		/* When a file is selected, grab the URL and set it as the text field's value */
		custom_uploader.on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			jQuery('#js-upload-open-sound').val(attachment.url);
		});
		/* Open the uploader dialog */
		custom_uploader.open();
	});
};

beckend.prototype.soundPreview = function () {

	var songValue = 1;
	var lastSong = undefined;

	jQuery('.sg-preview-sound').bind('click', function () {

		var uploadFile = jQuery('#js-upload-open-sound').val();
		if(typeof lastSong == 'undefined') {
			lastSong = new Audio(uploadFile);
		}

		/*
		 * songValue == 1 should be song
		 * songValue == 2 song should be pause
		 * */
		if (songValue == 1) {

			lastSong.play();
			songValue = 2;

		} else if (songValue == 2) {
			lastSong.pause();
			songValue = 1;

		}

		lastSong.onended = function () {
			lastSong = undefined;
			songValue = 1;
		}
	});

	jQuery('#js-upload-open-sound').change(function () {
		if(typeof lastSong != 'undefined') {
			lastSong.pause();
			lastSong = undefined;
		}
		songValue = 1;
	});

	jQuery('#reset-to-default').click(function (e) {
		e.preventDefault();
		if(typeof lastSong != 'undefined') {
			lastSong.pause();
			lastSong = undefined;
		}
		songValue = 1;

		var defaultSong = jQuery(this).attr('data-default-song');
		jQuery('#js-upload-open-sound').val(defaultSong);
	});
};

beckend.prototype.imageUpload = function() {
	if(jQuery("#js-upload-image").val()) {
		jQuery(".show-image-contenier").html("");
		jQuery(".show-image-contenier").css({'background-image': 'url("' + jQuery("#js-upload-image").val() + '")'});
	}
	var custom_uploader;
	jQuery('#js-upload-image-button').click(function(e) {
		e.preventDefault();

		/* If the uploader object has already been created, reopen the dialog */
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}
		/* Extend the wp.media object */
		custom_uploader = wp.media.frames.file_frame = wp.media({
			titleFF: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});
		/* When a file is selected, grab the URL and set it as the text field's value */
		custom_uploader.on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			jQuery(".show-image-contenier").css({'background-image': 'url("' + attachment.url + '")'});
			jQuery(".show-image-contenier").html("");
			jQuery('#js-upload-image').val(attachment.url);
		});
		/* Open the uploader dialog */
		custom_uploader.open();
	});

	/* its finish image uploader */
};

beckend.prototype.deletePopup = function() {
	jQuery(".sg-js-delete-link").bind('click',function() {
		var request = confirm("Are you sure?");
		if(!request) {
			return false;
		}
		var popup_id = jQuery(this).attr("data-sg-popup-id");
		var ajaxNonce = jQuery(this).attr('data-ajaxNonce');

		var data = {
			action: 'delete_popup',
			ajaxNonce: ajaxNonce,
			popup_id: popup_id
		};

		jQuery.post(ajaxurl, data, function(response,d) {
			location.reload();
		});
	});
};

beckend.prototype.titleNotEmpty = function() {
	jQuery("#add-form").submit(function() {
		var popupTitle = jQuery(".sg-js-popup-title").val();
		if(popupTitle == '' || popupTitle == ' ') {
			alert('Please fill in title field');
			return false;
		}
	});
};

beckend.prototype.showThemePicture = function() {
	jQuery(".popup_theme_name").bind("mouseover",function(e) {
		jQuery('.theme'+jQuery(this).attr("sgpoupnumber")+'').css('display', 'block');
	});
};

beckend.prototype.showEffects = function() {
	var effectTimer = '';

	jQuery('select[name="effect"]').bind('change', function() {
		if (effectTimer!='') {
			clearTimeout(effectTimer);
		}
		effectTimer = setTimeout(function() {
			jQuery("#effectShow").hide();
			effectTimer = '';
		},1400);
		jQuery("#effectShow").removeClass();
		jQuery("#effectShow").show();
		jQuery("#effectShow").addClass('sg-animated '+jQuery(this).val()+'');
	});
	jQuery('.js-preview-effect').click(function() {
		if (effectTimer!='') {
			clearTimeout(effectTimer);
		}
		effectTimer = setTimeout(function() {
			jQuery("#effectShow").hide();
			effectTimer = '';
		},1400);
		jQuery("#effectShow").removeClass();
		jQuery("#effectShow").show();
		jQuery("#effectShow").addClass('sg-animated '+jQuery('select[name="effect"] option:selected').val()+'');
	});
};

beckend.prototype.pageAcordion = function() {
	jQuery("#specialoptionsTitle").toggle(function(){
		jQuery('.specialOptionsContent').fadeOut();
		jQuery("#specialoptionsTitle > img").css("transform", 'rotate(0deg)');
	},function(){
		jQuery('.specialOptionsContent').fadeIn();
		jQuery("#specialoptionsTitle > img").css("transform", 'rotate(180deg)');
	});

	function acardionDivs(prama1,param2,param3) {
		jQuery(prama1).toggle(function() {
			jQuery(param2).addClass('closed');
			jQuery(param3).fadeOut();

		},function() {
			jQuery(param3).fadeIn();
			jQuery(param2).removeClass('closed');
		});
	}

	acardionDivs(".generalTitle",'.popupBuilder_general_postbox','.generalContent');
	acardionDivs(".effectTitle",'.popupBuilder_effect_postbox','.effectsContent');
	acardionDivs(".optionsTitle",'.popupBuilder_options_postbox','.optionsContent');
	acardionDivs(".dimentionsTitle",'.popupBuilder_dimention_postbox','.dimensionsContent');
	acardionDivs(".js-advanced-title",'.js-advanced-postbox','.advanced-options-content');
	acardionDivs(".js-special-title",'.popup-builder-special-postbox','.special-options-content');
};

beckend.prototype.fixedPostionSelection = function() {
	jQuery(".js-fixed-position-style").bind("click",function() {
		var sgelement = jQuery(this);
		var sgpos = sgelement.attr('data-sgvalue');
		jQuery(".js-fixed-position-style").css("backgroundColor","#FFFFFF");
		jQuery(this).css("backgroundColor","rgba(70,173,208,0.5)");
		jQuery(".js-fixed-postion").val(sgpos);
	});

	jQuery(".js-fixed-position-style").bind("mouseover",function() {
		jQuery(".js-fixed-position-style").css("backgroundColor","#FFFFFF");
		jQuery(this).css("backgroundColor","rgb(70,173,208)");
		jQuery(".js-fixed-position-style").each(function() {
			if (jQuery(this).attr("data-sgvalue") == jQuery('.js-fixed-postion').val())
				jQuery(this).css("backgroundColor","rgba(70,173,208,0.5)");
		});
	});

	jQuery(".js-fixed-position-style").bind("mouseout",function() {
		if(jQuery(".js-fixed-position-style").attr("data-sgvalue") !== jQuery(".js-fixed-postion").val() || jQuery(".js-fixed-postion").val() == 1) {
			jQuery(this).css("backgroundColor","#FFFFFF");
		}
		jQuery(".js-fixed-position-style").each(function() {
			if (jQuery(this).attr("data-sgvalue") == jQuery('.js-fixed-postion').val()) {
				jQuery(this).css("backgroundColor","rgba(70,173,208,0.5)");
			}
		});
	});

	if(jQuery('.js-fixed-postion').val()!='') {
		jQuery(".js-fixed-position-style").each(function(){
			if (jQuery(this).attr("data-sgvalue") == jQuery('.js-fixed-postion').val()) {
				jQuery(this).css("backgroundColor","rgba(70,173,208,0.5)");
			}
		});
	}
};

beckend.prototype.showInfo = function() {
	jQuery(".dashicons.dashicons-info").hover(
		function() {
			jQuery(this).next('span').css({"display": 'inline-block'});
		}, function() {
			jQuery(this).next('span').css({"display": 'none'});
		}
	);
};

beckend.prototype.opacityRange = function() {
	if (typeof Powerange != 'undefined') {
		var powerRangeSelectors = ['js-decimal', 'js-popup-content-opacity'];

		for(var i in powerRangeSelectors) {

			if(jQuery('.'+powerRangeSelectors[i]).length == 0) {
				continue;
			}
			this.powerRange(powerRangeSelectors[i]);
		}
	}
};

beckend.prototype.powerRange = function (cssSelectorName) {
	var dec = document.querySelector('.'+cssSelectorName);
	function displayDecimalValue() {
		var dec = document.querySelector('.'+cssSelectorName);
		document.getElementById(cssSelectorName).innerHTML = jQuery('.'+cssSelectorName).attr("value");
	}
	if(jQuery('#'+cssSelectorName).attr('data-init') == 'false') {
		jQuery('#'+cssSelectorName).attr('data-init', true);
		var initDec = new Powerange(dec, { decimal: true, callback: displayDecimalValue, max: 1, start: jQuery('.'+cssSelectorName).attr("value") });
	}

};

beckend.prototype.showOptionsInfo = function(cehckboxSelector, param2) {
	if(jQuery(""+cehckboxSelector+":checked").length == 0) {
		jQuery("."+param2+"").css({'display': 'none'});
	}
	else
	{
		jQuery("."+param2+"").css({'display':'inline-block'});
	}

	jQuery(""+cehckboxSelector+"").bind("click",function() {
		if(jQuery(""+cehckboxSelector+":checked").length == 0) {
			jQuery("."+param2+"").css({'display':'none'});
		}
		else {
			jQuery("."+param2+"").css({'display':'inline-block'});
		}
	});
	jQuery('input.popup_theme_name').bind('mouseout',function() {
		jQuery('.theme1').css('display', 'none');
		jQuery('.theme2').css('display', 'none');
		jQuery('.theme3').css('display', 'none');
		jQuery('.theme4').css('display', 'none');
		jQuery('.theme5').css('display', 'none');
		jQuery('.theme6').css('display', 'none');
	});

};

beckend.prototype.subOptionContents = function() {
	this.showOptionsInfo("#js-auto-close", "js-auto-close-content");
	this.showOptionsInfo("#js-scrolling-event-inp", "js-scrolling-content");
	this.showOptionsInfo("#js-inactivity-event-inp", "js-inactivity-content");
	this.showOptionsInfo("#js-countris", "js-countri-content");
	this.showOptionsInfo("#js-popup-only-once", "js-popup-only-once-content");
	this.showOptionsInfo(".js-on-all-pages", "js-all-pages-content");
	this.showOptionsInfo(".js-on-all-posts", "js-all-posts-content");
	this.showOptionsInfo(".js-on-all-custom-posts", "js-all-custom-posts-content");
	this.showOptionsInfo(".js-user-seperator", "js-user-seperator-content");
	this.showOptionsInfo(".js-checkbox-contnet-click", "js-content-click-wrraper");
	this.showOptionsInfo(".js-checkbox-contact-success-frequency-click", "js-checkbox-contact-success-frequency-wrraper");
	this.showOptionsInfo(".js-checkbox-sound-option", "js-checkbox-sound-option-wrapper");
	this.showOptionsInfo(".js-popup-content-bg-image", "js-popup-content-bg-image-wrapper");

	var that = this;
	var element = jQuery(".js-checkbox-acordion");
	element.each(function() {
		that.checkboxAcordion(jQuery(this));
	});

	element.click(function() {
		var elements = jQuery(this);
		that.checkboxAcordion(jQuery(this));
	});

	this.radioButtonAcordion(jQuery("[name='allPages']"),jQuery("[name='allPages']:checked"),"selected", jQuery('.js-pages-selectbox-content'));
	this.radioButtonAcordion(jQuery("[name='allPosts']"),jQuery("[name='allPosts']:checked"),"selected",jQuery('.js-posts-selectbox-content'));
	this.radioButtonAcordion(jQuery("[name='allPosts']"),jQuery("[name='allPosts']:checked"),"allCategories", jQuery(".js-all-categories-content"));
	this.radioButtonAcordion(jQuery("[name='allCustomPosts']"),jQuery("[name='allCustomPosts']:checked"),"selected", jQuery(".js-all-custompost-content"));
	this.radioButtonAcordion(jQuery("[name='content-click-behavior']"),jQuery("[name='content-click-behavior']:checked"),"redirect",jQuery(".js-readio-buttons-acordion-content"));

	this.radioButtonAcordion(jQuery("[name='subs-success-behavior']"),jQuery("[name='subs-success-behavior']:checked"),"showMessage", jQuery('.js-subs-success-message-content'));
	this.radioButtonAcordion(jQuery("[name='subs-success-behavior']"),jQuery("[name='subs-success-behavior']:checked"),"redirectToUrl", jQuery('.js-subs-success-redirect-content'));
	this.radioButtonAcordion(jQuery("[name='subs-success-behavior']"),jQuery("[name='subs-success-behavior']:checked"),"openPopup", jQuery('.js-subs-success-popups-list-content'));
};

beckend.prototype.radioButtonAcordion = function(element, checkedElement,value, toggleContnet) {
	element.on("change", function() {

		if(jQuery(this).is(":checked") && jQuery(this).val() == value) {
			jQuery(this).after(toggleContnet.css({'display':'inline-block'}));

		}
		else {
			toggleContnet.css({'display': 'none'});
		}
	});
	if(checkedElement.val() == value) {
		checkedElement.after(toggleContnet.css({'display':'inline-block'}));
	}
	else {
		toggleContnet.css({'display': 'none'});
	}
};

beckend.prototype.initAccordions = function() {
	var radioButtonsList = [
		jQuery("[name='contact-success-behavior']"),
		jQuery("[name='popup-dimension-mode']")
	];

	for(var radioButtonIndex in radioButtonsList) {

		var radioButton = radioButtonsList[radioButtonIndex];

		var that = this;
		radioButton.each(function () {
			that.buildAccordionActions(jQuery(this));
		});
		radioButton.on("change", function () {
			that.buildAccordionActions(jQuery(this), 'change');
		});
	}
};

beckend.prototype.buildAccordionActions = function (currentRadioButton, event) {

	if(event == 'change') {
		currentRadioButton.parents('.sg-radio-option-behavior').first().find('.js-radio-accordion').css({'display': 'none'});
	}

	var value = currentRadioButton.val();
	var toggleContent = jQuery('.js-accordion-'+value);

	if(currentRadioButton.is(':checked')) {
		currentRadioButton.after(toggleContent.css({'display':'inline-block'}));
	}
	else {
		toggleContent.css({'display': 'none'});
	}
};

beckend.prototype.addCountries = function() {
	var countyNames = [];
	if(!jQuery('#countryIso').length) {
		return;
	}
	var countryIsos = jQuery('#countryIso').val().split(',');
	function addCountry(name,iso) {
		countyNames.push(name);
		countryIsos.push(iso);
		jQuery("#countryIso").val(countryIsos.join(','));
		jQuery('#countryName').tagsinput('add', countyNames.join(','));
	}
	jQuery(".addCountry").bind('click',function(){
		var optionCountryName = jQuery(".optionsCountry option:selected").text();
		var optionCountryIso = jQuery(".optionsCountry option:selected").val();
		addCountry(optionCountryName,optionCountryIso);
	});
	jQuery('input').on('itemRemoved', function(event) {
		var removeCountryName = event.item;
		var countryNameIso = countyNames.indexOf(removeCountryName);
		countryIsos.splice(countryNameIso,1);
		countyNames.splice(countryNameIso,1);
		jQuery("#countryIso").val(countryIsos.join(','));
	});

	if(typeof popupCountries != "undefined" && typeof popupCountries != "undefined"){
		var sgCountryNameArray = popupCountries.sgCountryName.split(",");
		var sgCountryIsoArray = popupCountries.sgCountryIso.split(",");
		for(var i=0; i <= sgCountryIsoArray.length; i++) {
			addCountry(sgCountryNameArray[i],sgCountryIsoArray[i]);
		}
	}
};

beckend.prototype.showCloseTextFieldForTheme = function() {
	var that = this;
	jQuery("[name='theme']").each(function() {
		if(jQuery(this).prop("checked")) {
			that.sgAllowCustomizedThemes(jQuery(this));
		}
	});

	jQuery("[name='theme']").bind("change", function() {
		that.sgAllowCustomizedThemes(jQuery(this))
	});

};

beckend.prototype.sgAllowCustomizedThemes = function(cureentRadioButton) {
	var customizedThemes = ['2','3','4'];
	var themeNumber = cureentRadioButton.attr("sgpoupnumber");
	var isInCustomThemes = customizedThemes.indexOf(themeNumber);
	jQuery(".themes-suboptions").addClass("sg-hide");
	if(isInCustomThemes != -1) {

		if(cureentRadioButton.prop( "checked" )) {
			jQuery(".sg-popup-theme-"+themeNumber).removeClass("sg-hide");
		}
		else {
			jQuery(".sg-popup-theme-"+themeNumber).addClass("sg-hide");
		}
	}
};

beckend.prototype.updateQueryStringParameter = function (uri, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
};

beckend.prototype.popupPreview = function () {
	var that = this;
	jQuery('.sg-popup-preview').bind('click', function (e) {
		e.preventDefault();
		var previewButton = jQuery(this);
		/*checking if it's not null*/
		if(typeof tinymce != 'undefined' && !!tinymce.activeEditor) {
			jQuery("[name='"+tinymce.activeEditor.id+"']").html(tinymce.activeEditor.getContent());
		}

		var data = {
			action: 'save_popup_preview_data',
			ajaxNonce: backendLocalizedData.ajaxNonce,
			beforeSend: function () {
				previewButton.prop('disabled', true);
				previewButton.val('loading');
			},
			popupDta: jQuery("#add-form").serialize()
		};
		var newWindow = window.open('');
		jQuery.post(ajaxurl, data, function(response,d) {
			var popupId = parseInt(response);
			if(isNaN(popupId)) {
				console.log("it's not number");
				return;
			}
			previewButton.prop('disabled', false);
			previewButton.val('Preview');
			var pageUrl  = previewButton.attr('data-page-url');
			var redirectUrl = that.updateQueryStringParameter(pageUrl, 'sg_popup_preview_id', popupId);
			newWindow.location = redirectUrl;
		});
	})
};

jQuery(document).ready(function($){
	var sgBeckeendObj = new  beckend();
	sgBeckeendObj.sgInit();
});
