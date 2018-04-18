<?php

// a helper class to help us access the ACF api, and stub it when not available
class ACF_Woo_Display extends ACF_Woo_Singleton {
    // container for the acf core api functions to use
    protected $funcs = array();

    // container for the api helper
    protected $api = null;

    // generic function designed to call acf core functions, based on what is available
    public function __call($name, $args) {
        return isset($this->funcs[$name]) ? call_user_func_array($this->funcs[$name], $args) : '';
    }

    // during the first creation of this object register some hooks
    protected function __construct() {
        $this->api = ACF_Woo_API::get_instance();
        // once all plugins are loaded, figure out if we need to stub any functions
        add_action('plugins_loaded', array(&$this, 'initialize_functions'));
        add_action('acf/render_field_settings', array(&$this, 'add_field_display_label_pro'));
        add_action('acf/create_field_options', array(&$this, 'add_field_display_label'));
        add_action('woocommerce_admin_order_data_after_billing_address', array(&$this, 'acf_woocommerce_add_fields_to_order'));

    }

    // determind which functions to use, based on what is available
    public function initialize_functions() {
    }

    // NON-PRO ONLY: add a field to the admin interface, that decides whether this field's label gets displayed on the frontend or not
    public function add_field_display_label($field) {
        ?>
        <tr class="field_display_label">
            <td class="label"><label>Display on</label>
            <td>
                <?php
                do_action('acf/create_field', array(
                    'type' => 'checkbox',
                    'name' => 'fields[' . $field['name'] . '][show_fields_options]',
                    'value' => isset($field['show_fields_options']) ? $field['show_fields_options'] : 1,
                    'choices' => array(
                        'order' => 'Order field',
                        'email' => 'Email field',
                    ),
                    'layout' => 'horizontal',
                ));
                ?>
            </td>
        </tr>
        <?php
    }

    // PRO ONLY: add a field to the admin interface, that decides whether this field's label gets displayed on the frontend or not
    public function add_field_display_label_pro($field) {
        // required
        acf_render_field_wrap(array(
            'label' => 'Display on',
            'type' => 'checkbox',
            'name' => 'show_fields_options',
            'prefix' => $field['prefix'],
            'value' => isset($field['show_fields_options']) ? $field['show_fields_options'] : 1,
            'choices' => array(
                'order' => 'Order field',
                'email' => 'Email field',
            ),
            'layout' => 'horizontal',
            'class' => 'field-display_location'
        ), 'tr');
    }

    /**
     *
     */
    public function acf_woocommerce_add_fields_to_order() {
        $api = ACF_Woo_API::get_instance();
        $group_keys = wp_list_pluck($api->get_field_groups(), $api->acf_id_case_sensitive());
	   foreach ($group_keys as $group_key => $key) {
            $fields = $api->get_field_group_fields($key);
            foreach ($fields as $field => $value) {
                $field_label = $value['label'];
                $raw_meta = base64_decode(get_post_meta(get_the_ID(), $value['key'], true));
                $meta = unserialize($raw_meta);
		
				if (is_array($meta)) {
					
                    //handle repeater, flexible content
                    if (is_array(reset($meta))) {
                        echo '<table style="border-collapse: collapse; width: 100%">';
                        foreach ($meta as $row) {
                            echo '<tr>';
                            foreach ($row as $column) {
                                echo "<td style='border: 1px solid black;'>$column</td>";
                            }
                            echo '</tr>';
                        }
                        echo '</table>';
                    } //handle choice and select
                    else {
                        echo '<p><strong>' . $field_label . ': </strong>' . implode('; ', $meta) . '</p>';
                    }
                } else {
                    $meta = stripcslashes($meta);
                    echo '<p><strong>' . $field_label . ': </strong>' . $meta . '</p>';
                }
            }
        }
    }
}

ACF_Woo_Display::get_instance();
