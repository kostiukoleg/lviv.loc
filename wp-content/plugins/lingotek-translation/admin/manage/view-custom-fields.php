<?php

global $polylang;

$items = array();
$default_setting = array('ignore' => 'Ignore', 'translate' => 'Translate', 'copy' => 'Copy');
$default_custom_fields = '';

if (!empty($_POST)) {
  check_admin_referer('lingotek-custom-fields', '_wpnonce_lingotek-custom-fields');

  if (!empty($_POST['submit'])) {
    $arr = empty($_POST['settings']) ? array() : $_POST['settings'];
    if (isset($_POST['default_custom_fields'])) {
      $default_custom_fields = $_POST['default_custom_fields'];
      update_option('lingotek_default_custom_fields', $default_custom_fields);
    }
    update_option('lingotek_custom_fields', $arr);
    add_settings_error('lingotek_custom_fields_save', 'custom_fields', __('Your <i>Custom Fields</i> were sucessfully saved.', 'lingotek-translation'), 'updated');
  }

  if (!empty($_POST['refresh'])) {
    Lingotek_Group_Post::get_updated_meta_values();
    add_settings_error('lingotek_custom_fields_refresh', 'custom_fields', __('Your <i>Custom Fields</i> were sucessfully identified.', 'lingotek-translation'), 'updated');
  }
  settings_errors();
}

$items = Lingotek_Group_Post::get_cached_meta_values();
$default_custom_fields = get_option('lingotek_default_custom_fields');

?>

<h3><?php _e('Custom Field Configuration', 'lingotek-translation'); ?></h3>
<p class="description"><?php _e('Custom Fields can be translated, copied, or ignored. Click "Refresh Custom Fields" to identify and enable your custom fields.', 'lingotek-translation'); ?></p>

<form id="lingotek-custom-fields" method="post" action="admin.php?page=lingotek-translation_manage&amp;sm=custom-fields" class="validate"><?php
wp_nonce_field('lingotek-custom-fields', '_wpnonce_lingotek-custom-fields'); ?>

<br>
<label for="default_custom_fields">Default configuration for new Custom Fields</label>
<select name="default_custom_fields"><?php
  foreach ($default_setting as $key => $title) {
    $selected = $key == $default_custom_fields ? 'selected="selected"' : '';
    echo "\n\t<option value='" . $key . "' $selected>" . $title . '</option>';
  } ?>
</select>

<?php
$table = new Lingotek_Custom_Fields_Table();
$table->prepare_items($items);
$table->display();
?>

  <p>
    <?php submit_button(__('Save Changes', 'lingotek-translation'), 'primary', 'submit', false); ?>
    <?php submit_button(__( 'Refresh Custom Fields', 'lingotek-translation'), 'secondary', 'refresh', false ); ?>
  </p>
</form>
