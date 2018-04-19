<?php
function sgPopupMeta()
{
	$showCurrentUser = SGFunctions::isShowMenuForCurrentUser();
	if(!$showCurrentUser) {return;}

	$screens = array('post', 'page');
	foreach ( $screens as $screen ) {
		add_meta_box( 'prfx_meta', __('Select popup on page load', 'prfx-textdomain'), 'sgPopupCallback', $screen, 'normal');
	}
}
add_action('add_meta_boxes', 'sgPopupMeta');

function sgPopupCallback($post)
{
	wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
	$prfx_stored_meta = get_post_meta( $post->ID );
	?>
	<p class="preview-paragaraph">
		<?php
		global $wpdb;
		$proposedTypes = array();
		$orderBy = 'id DESC';

		$proposedTypes = SGPopup::findAll($orderBy);
		function sgCreateSelect($options,$name,$selecteOption) {

			$popupPreviewId = get_option('popupPreviewId');
			$str = "";
			$str .= "<select class=\"choose-popup-type\" name=\"$name\">";
			$str .= "<option value='-1'>Not selected</option>";
			foreach($options as $option) {

				$selected ='';

				if ($option) {
					$title = $option->getTitle();
					$type = $option->getType();
					$id = $option->getId();
					if($id == $popupPreviewId) {
						continue;
					}
					if ($selecteOption == $id) {
						$selected = "selected";
					}
					$str .= "<option value='".$id."' disable='".$id."' ".esc_attr($selected)." >".esc_html($title .'-'. $type)."</option>";
				}
			}
			$str .="</select>" ;
			return $str;
		}
		global $post;
		$page = (int)$post->ID;
		$popup = "sg_promotional_popup";

		$popupId = 0;
		$postMetaSavedValue = get_post_meta($post->ID, 'sg_promotional_popup');
		if(!empty($postMetaSavedValue[0])) $popupId = (int)$postMetaSavedValue[0];

		echo sgCreateSelect($proposedTypes,'sg_promotional_popup',$popupId);
		$SG_APP_POPUP_URL = SG_APP_POPUP_URL;
		?>
	</p>
	<input type="hidden" value="<?php echo $SG_APP_POPUP_URL;?>" id="SG_APP_POPUP_URL">
	<?php
}

function sgSelectPopupSaved($post_id)
{
	$post_id = (int)$post_id;
	if(isset($_POST['sg_promotional_popup']) && $_POST['sg_promotional_popup'] == -1) {
		delete_post_meta($post_id, 'sg_promotional_popup');
		return false;
	}
	else if(isset($_POST['sg_promotional_popup'])) {
		update_post_meta($post_id, 'sg_promotional_popup' , (int)$_POST['sg_promotional_popup']);
	}
}

add_action('save_post','sgSelectPopupSaved');