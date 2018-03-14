<?php
global $polylang;

$profiles = Lingotek::get_profiles();
$profiles = $this->get_profiles_usage($profiles);
$settings = $this->get_profiles_settings();

if (isset($_GET['lingotek_action']) && 'delete-profile' == $_GET['lingotek_action']) {
	check_admin_referer('delete-profile');

	// check again that usage empty
	if (!empty($profiles[$_GET['profile']]) && empty($profiles[$_GET['profile']]['usage'])) {
		unset($profiles[$_GET['profile']]);
		update_option('lingotek_profiles', $profiles);
		add_settings_error('lingotek_profile', 'default', __('Your translation profile was sucessfully deleted.', 'lingotek-translation'), 'updated');
		set_transient('settings_errors', get_settings_errors(), 30);
		wp_redirect(admin_url('admin.php?page=lingotek-translation_manage&sm=profiles&settings-updated=1'));
		exit;
	}
}

if (!empty($_POST)) {
	check_admin_referer('lingotek-edit-profile', '_wpnonce_lingotek-edit-profile');

	$defaults = get_option('lingotek_defaults');

	if (empty($_POST['name']) && empty($_POST['profile'])) {
		add_settings_error('lingotek_profile', 'default', __('You must provide a name for your translation profile.', 'lingotek-translation'), 'error');
	}
	else {
		$profile_id = empty($_POST['profile']) ? uniqid(rand()) : $_POST['profile'];
		$profiles[$profile_id]['profile'] = $profile_id;
		if (!empty($_POST['name']))
			$profiles[$profile_id]['name'] = strip_tags($_POST['name']);

		foreach (array('upload', 'download', 'project_id', 'workflow_id', 'primary_filter_id', 'secondary_filter_id') as $key) {
			if (isset($_POST[$key]) && in_array($_POST[$key], array_keys($settings[$key]['options'])))
				$profiles[$profile_id][$key] = $_POST[$key];

			if (empty($_POST[$key]) || 'default' == $_POST[$key])
				unset($profiles[$profile_id][$key]);
		}

		foreach ($this->pllm->get_languages_list() as $language) {
			switch($_POST['targets'][$language->slug]) {
				case 'custom':
					foreach (array('download', 'project_id', 'workflow_id') as $key) {
						if (isset($_POST['custom'][$key][$language->slug]) && in_array($_POST['custom'][$key][$language->slug], array_keys($settings[$key]['options']))) {
							$profiles[$profile_id]['custom'][$key][$language->slug] = $_POST['custom'][$key][$language->slug];
						}

						if (empty($_POST['custom'][$key][$language->slug]) || 'default' == $_POST['custom'][$key][$language->slug]) {
							unset($profiles[$profile_id]['custom'][$key][$language->slug]);
						}
					}

				case 'disabled':
				case 'copy':
					$profiles[$profile_id]['targets'][$language->slug] = $_POST['targets'][$language->slug];
					break;

				case 'default':
					unset($profiles[$profile_id]['targets'][$language->slug]);
			}
		}

		// hardcode default values for automatic and manual profiles as the process above emptied them
		$profiles['automatic']['upload'] = $profiles['automatic']['download'] = 'automatic';
		$profiles['manual']['upload'] = $profiles['manual']['download'] = 'manual';
		$profiles['automatic']['name'] = 'Automatic'; $profiles['manual']['name'] = 'Manual'; $profiles['disabled']['name'] = 'Disabled';// do not localize names here

		update_option('lingotek_profiles', $profiles);
		add_settings_error('lingotek_profile', 'default', __('Your translation profile was sucessfully saved.', 'lingotek-translation'), 'updated');

		if (isset($_POST['update_callback'])) {
			$project_id = isset($profiles[$profile_id]['project_id']) ? $profiles[$profile_id]['project_id'] : $defaults['project_id'];
			$client = new Lingotek_API();
			if ($client->update_callback_url($project_id))
				add_settings_error('lingotek_profile', 'default', __('Your callback url was successfully updated.', 'lingotek-translation'), 'updated');
		}
	}
	settings_errors();
}

?>
<h3><?php _e('Translation Profiles', 'lingotek-translation'); ?></h3>
<p class="description"><?php _e('Translation profiles allow you to quickly configure and re-use translation settings.', 'lingotek-translation'); ?></p><?php

$table = new Lingotek_Profiles_Table();
$table->prepare_items($profiles);
?>
<style>
.tablenav {
	clear: none !important;
}
</style>
<?php
$table->display();
printf(
	'<a href="%s" class="button button-primary">%s</a>',
	admin_url('admin.php?page=lingotek-translation_manage&sm=edit-profile'),
	__('Add New Profile', 'lingotek-translation')
);

