<?php
function sgPopupMediaButton()
{
	global $pagenow, $typenow;

	$showCurrentUser = SGFunctions::isShowMenuForCurrentUser();
	if(!$showCurrentUser) {return;}
	$buttonTitle = 'Insert popup';
	$output = '';

	$pages = array(
		'post.php',
		'page.php',
		'post-new.php',
		'post-edit.php',
		'widgets.php'
	);


	/* For show in plugins page when package is pro */
	if(POPUP_BUILDER_PKG !== POPUP_BUILDER_PKG_FREE) {
		array_push($pages, "admin.php");
	}

	$checkPage = in_array(
		$pagenow,
		$pages
	);

	if ($checkPage && $typenow != 'download') {

		wp_enqueue_script('jquery-ui-dialog');
		wp_register_style('sg_jQuery_ui', SG_APP_POPUP_URL . "/style/jQueryDialog/jquery-ui.css");
		wp_enqueue_style('sg_jQuery_ui');
		$img = '<span class="dashicons dashicons-welcome-widgets-menus" id="sg-popup-media-button" style="padding: 3px 2px 0px 0px"></span>';
		$output = '<a href="javascript:void(0);" onclick="jQuery(\'#sgpb-thickbox\').dialog({ width: 450, modal: true, title: \'Insert the shortcode\', dialogClass: \'sg-popup-builder\' });"  class="button" title="'.$buttonTitle.'" style="padding-left: .4em;">'. $img.$buttonTitle.'</a>';
	}
	echo $output;
}

add_action('media_buttons', 'sgPopupMediaButton', 11);

function sgpbPopupVariable()
{
	$showCurrentUser = SGFunctions::isShowMenuForCurrentUser();
	if (!$showCurrentUser) {
		return;
	}

	$buttonTitle = 'Insert custom JS variable';
	$output = '';

	require_once(ABSPATH .'wp-admin/includes/screen.php');
	$currentScreen = get_current_screen();
	$currentPageParams = @get_object_vars($currentScreen);

	if ($currentPageParams['id'] != 'popup-builder_page_edit-popup') {
		return '';
	}
	wp_enqueue_script('jquery-ui-dialog');
	wp_register_style('sg_jQuery_ui', SG_APP_POPUP_URL . "/style/jQueryDialog/jquery-ui.css");
	wp_enqueue_style('sg_jQuery_ui');

	$img = '<span class="dashicons dashicons-welcome-widgets-menus" id="sg-popup-js-variable" style="padding: 3px 2px 0px 0px"></span>';
	$output = '<a href="javascript:void(0);" onclick="jQuery(\'#sgpb-js-variable-thickbox\').dialog({ width: 500, modal: true, title: \'Insert JS variable\', dialogClass: \'sg-popup-builder\' });"  class="button" title="'.$buttonTitle.'" style="padding-left: .4em;">'. $img.$buttonTitle.'</a>';

	echo $output;
	return '';
}

add_action('media_buttons', 'sgpbPopupVariable', 11);

function sgJsVariableThickbox() {

	require_once(ABSPATH .'wp-admin/includes/screen.php');
	$currentScreen = get_current_screen();
	$currentPageParams = get_object_vars($currentScreen);

	if($currentPageParams['id'] != 'popup-builder_page_edit-popup') {
		return '';
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$('#sgpb-insert-variable').on('click', function (e) {
				var jsVariableSelector = jQuery('.sgpb-js-variable-selector').val();
				var jsVariableAttribute = jQuery('.sgpb-js-variable-attribute').val();

				if (jsVariableSelector == '' || jsVariableAttribute == '') {
					alert('Please, fill in all the fields.');
					return;
				}
				window.send_to_editor('[pbvariable selector="' + jsVariableSelector + '" attribute="'+jsVariableAttribute+'"]');
				jQuery('#sgpb-js-variable-thickbox').dialog('close')
			});
		});
	</script>
	<div id="sgpb-js-variable-thickbox" style="display: none;">
		<div class="wrap">
			<p>Insert JS variable inside the popup.</p>
			<div>
				<div style="margin-bottom: 5px;">
					<span>Selector</span>
					<input type="text" class="sgpb-js-variable-selector">
					<span>Ex. #myselector or .myselector</span>
				</div>
				<div>
					<span>Attribute</span>
					<input type="text" class="sgpb-js-variable-attribute">
					<span>Ex. value or data-name</span>
				</div>
			</div>
			<p class="submit">
				<input type="button" id="sgpb-insert-variable" class="button-primary dashicons-welcome-widgets-menus" value="Insert"/>
				<a id="sgpb-cancel" class="button-secondary" onclick="jQuery('#sgpb-js-variable-thickbox').dialog( 'close' )" title="Cancel">Cancel</a>
			</p>
		</div>
	</div>
	<?php
}

function sgPopupMediaButtonThickboxs()
{
	global $pagenow, $typenow;
	require_once(ABSPATH .'wp-admin/includes/screen.php');
	$currentScreen = get_current_screen();
	$currentPageParams = get_object_vars($currentScreen);

	$showCurrentUser = SGFunctions::isShowMenuForCurrentUser();
	if(!$showCurrentUser) {return;}

	$pages = array(
		'post.php',
		'page.php',
		'post-new.php',
		'post-edit.php',
		'widgets.php'
	);

	if(POPUP_BUILDER_PKG !== POPUP_BUILDER_PKG_FREE) {
		array_push($pages, "admin.php");
	}

	$checkPage = in_array(
		$pagenow,
		$pages
	);


	if ($checkPage && $typenow != 'download') :
		$orderBy = 'id DESC';
		$allPopups = SGPopup::findAll($orderBy);
		$popupPreviewId = get_option('popupPreviewId');
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {

				$('#sg-ptp-popup-insert').on('click', function () {
					var id = $('#sg-insert-popup-id').val();
					if ('' === id) {
						alert('Select your popup');
						return;
					}
					var appearEvent = jQuery("#openEvent").val();

					var selectionText = '';
					if (typeof(tinyMCE.editors.content) != "undefined") {
						selectionText = (tinyMCE.activeEditor.selection.getContent()) ? tinyMCE.activeEditor.selection.getContent() : '';
					}
					/* For plugin editor selected text */
					else if(typeof(tinyMCE.editors[0]) != "undefined") {
						var pluginEditorId = tinyMCE.editors[0]['id'];
						selectionText = (tinyMCE['editors'][pluginEditorId].selection.getContent()) ? tinyMCE['editors'][pluginEditorId].selection.getContent() : '';
					}
					if(appearEvent == 'onload') {
						selectionText = '';
					}
					<?php if( $currentPageParams['id'] == 'popup-builder_page_edit-popup'){ ?>
					window.send_to_editor('[sg_popup id="' + id + '" insidePopup="on"]'+selectionText+"[/sg_popup]");
					<?php }
					else { ?>
						window.send_to_editor('[sg_popup id="' + id + '" event="'+appearEvent+'"]'+selectionText+"[/sg_popup]");
					<?php } ?>
					jQuery('#sgpb-thickbox').dialog( "close" );
				});
			});
		</script>

		<div id="sgpb-thickbox" style="display: none;">
			<div class="wrap">
				<p>Insert the shortcode for showing a Popup.</p>
				<div>
					<div class="sg-select-popup">
						<span>Select Popup</span>
						<select id="sg-insert-popup-id" style="margin-bottom: 5px;">
							<option value="">Please select...</option>
							<?php
								foreach ($allPopups as $popup) :

									if(empty($popup)) {
										continue;
									}
									$popupId = (int)$popup->getId();
									$popupType = $popup->getType();
									$popupTitle = $popup->getTitle();

									if(empty($popupId) || empty($popupType) || $popupId == $popupPreviewId) {
										continue;
									}

									/*Inside popup*/
									if((isset($_GET['id']) && $popupId == (int)@$_GET['id'] || $popupType == 'exitIntent') && $currentPageParams['id'] == 'popup-builder_page_edit-popup') {
										continue;
									}
								?>
									<option value="<?php echo $popupId; ?>"><?php echo $popupTitle;?><?php echo " - ".$popupType;?></option>;
								<?php endforeach; ?>
						</select>
					</div>
					<?php /* Becouse in popup content must be have only click */
					   		if($pagenow !== 'admin.php'): ?>
					<div class="sg-select-popup">
						<span>Select Event</span>
						<select id="openEvent">
							<option value="onload">On load</option>
							<option value="click">Click</option>
							<option value="hover">Hover</option>
						</select>
					</div>
				<?php endif;?>
				</div>
				<p class="submit">
					<input type="button" id="sg-ptp-popup-insert" class="button-primary dashicons-welcome-widgets-menus" value="Insert"/>
					<a id="sg_popup_cancel" class="button-secondary" onclick="jQuery('#sgpb-thickbox').dialog( 'close' )" title="Cancel">Cancel</a>
				</p>
			</div>
		</div>
	<?php endif;
}

add_action('admin_footer', 'sgPopupMediaButtonThickboxs');
add_action('admin_footer', 'sgJsVariableThickbox');
