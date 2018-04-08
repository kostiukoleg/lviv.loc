<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$general_options = array(

	'general' => array(

		'section_general_settings'             => array(
			'name' => __( 'General settings', 'yith-woocommerce-additional-uploads' ),
			'type' => 'title',
			'id'   => 'ywau_section_general'
		),
		'ywau_max_file_size'                   => array(
			'name'              => __( 'Maximum size of the file', 'yith-woocommerce-additional-uploads' ),
			'type'              => 'number',
			'desc'              => __( 'Maximum size in MB of the file that customers can send. Set to 0 to have no limits for the file size.', 'yith-woocommerce-additional-uploads' ),
			'id'                => 'ywau_max_file_size',
			'default'           => '1',
			'custom_attributes' => array(
				'min'      => 0,
				'step'     => 0.1,
				'required' => 'required'
			)
		),
		'ywau_allowed_extension'               => array(
			'name'    => __( 'Allowed extensions', 'yith-woocommerce-additional-uploads' ),
			'type'    => 'text',
			'desc'    => __( 'Set the file formats allowed, writing the extensions divided by comma (e.g., jpg,png,gif).', 'yith-woocommerce-additional-uploads' ),
			'id'      => 'ywau_allowed_extension',
			'default' => 'jpg,png',
		),
		'ywau_allowed_order_status_completed'  => array(
			"name"          => __( 'Order status', 'yith-woocommerce-additional-uploads' ),
			"desc"          => __( 'Completed', 'yith-woocommerce-additional-uploads' ),
			"id"            => "ywau_allow_wc-completed",
			"type"          => "checkbox",
			'checkboxgroup' => 'start'
		),
		'ywau_allowed_order_status_on_hold'    => array(
			'desc'          => __( 'On Hold', 'yith-woocommerce-additional-uploads' ),
			'id'            => 'ywau_allow_wc-on-hold',
			'default'       => 'yes',
			'type'          => 'checkbox',
			'checkboxgroup' => ''
		),
		'ywau_allowed_order_status_pending'    => array(
			'desc'          => __( 'Pending payment', 'yith-woocommerce-additional-uploads' ),
			'id'            => 'ywau_allow_wc-pending',
			'default'       => 'no',
			'type'          => 'checkbox',
			'checkboxgroup' => ''
		),
		'ywau_allowed_order_status_processing' => array(
			'desc'          => __( 'Processing', 'yqau' ),
			'id'            => 'ywau_allow_wc-processing',
			'default'       => 'no',
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
		),
		'ywau_allow_upload_on_cart'            => array(
			'name'    => __( 'Allow cart attachment', 'yith-woocommerce-additional-uploads' ),
			'desc'    => __( 'Use this option to allow users to attach a file even from the cart', 'yqau' ),
			'id'      => 'ywau_allow_upload_on_cart',
			'default' => 'no',
			'type'    => 'checkbox',
		),
		'section_general_settings_end'         => array(
			'type' => 'sectionend',
			'id'   => 'ywau_section_general_end'
		)
	)
);

return $general_options;

