<?php
/**
 * GENERAL ARRAY OPTIONS
 */

$general = array(

	'general'  => array(

		array(
	        'title'		=> __( 'General', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wapo_settings_type'
	    ),
	    array(
			'title'     => __( 'Show add-ons', 'yith-woocommerce-product-add-ons' ),
			'id'        => 'yith_wapo_settings_formposition',
			'type'      => 'select',
			'options'   => array(
				'before'       => __( 'Before "Add to cart" button', 'yith-woocommerce-product-add-ons' ),
				'after'    => __( 'After "Add to cart" button', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'before'
		),
	    array(
	        'title'		=> __( '"Add to cart" button label', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'text',
	        'desc'		=> __( 'Change button label.', 'yith-woocommerce-product-add-ons' ),
	        'id'  		=> 'yith_wapo_settings_addtocartlabel',
	        'default' 	=> 'Select Options',
	        'css'     	=> 'min-width: 350px;',
		    'desc_tip'	=> true,
	    ),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wapo_settings_end'
	    ),

		array(
	        'title'		=> __( 'Add-ons', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wapo_settings_type'
	    ),
	    array(
	        'title'		=> __( 'Show add-on titles', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'checkbox',
	        'id'  		=> 'yith_wapo_settings_showlabeltype',
	        'default' 	=> 'yes',
	    ),
	    array(
	        'title'		=> __( 'Show add-on descriptions', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'checkbox',
	        'id'  		=> 'yith_wapo_settings_showdescrtype',
	        'default' 	=> 'yes',
	    ),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wapo_settings_end'
	    ),

		array(
	        'title'		=> __( 'Options', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wapo_settings_options'
	    ),
	    array(
	        'title'		=> __( 'Show option descriptions', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'checkbox',
	        'id'  		=> 'yith_wapo_settings_showdescropt',
	        'default' 	=> 'yes',
	    ),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wapo_settings_end'
	    ),


		array(
	        'title'		=> __( 'Tooltip', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wapo_settings_upload'
	    ),
	    array(
			'id'        => 'yith-wapo-enable-tooltip',
			'title'     => __( 'Enable tooltip', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Enable tooltip on options', 'yith-woocommerce-product-add-ons' ),
			'default'   => 'yes'
		),
		array(
			'id'        => 'yith-wapo-tooltip-position',
			'title'     => __( 'Tooltip position', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Select tooltip position', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'select',
			'options'   => array(
				'top'       => __( 'Top', 'yith-woocommerce-product-add-ons' ),
				'bottom'    => __( 'Bottom', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'top'
		),
		array(
			'id'        => 'yith-wapo-tooltip-animation',
			'title'     => __( 'Tooltip animation', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Select tooltip animation', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'select',
			'options'   => array(
				'fade'     => __( 'Fade in', 'yith-woocommerce-product-add-ons' ),
				'slide'    => __( 'Slide in', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'fade'
		),
		array(
			'id'        => 'yith-wapo-tooltip-background',
			'title'     => __( 'Tooltip background', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Pick a color', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'color',
			'default'   => '#222222'
		),
		array(
			'id'        => 'yith-wapo-tooltip-text-color',
			'title'     => __( 'Tooltip text color', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Pick a color', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'color',
			'default'   => '#ffffff'
		),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wapo_settings_end'
	    ),
	    
		array(
	        'title'		=> __( 'Uploading options', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wapo_settings_upload'
	    ),
	    array(
	        'title'		=> __( 'Uploading folder name', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'text',
	        'desc'		=> __( 'Changes will only affect future uploads.', 'yith-woocommerce-product-add-ons' ),
	        'id'  		=> 'yith_wapo_settings_uploadfolder',
	        'default' 	=> 'yith_advanced_product_options',
	        'css'     	=> 'min-width: 350px;',
		    'desc_tip'	=> true,
	    ),
	    array(
	        'title'		=> __( 'Uploading file types', 'yith-woocommerce-product-add-ons' ),
	        'type'		=> 'text',
	        'desc'		=> __( 'Separate file extensions using commas. Ex: .gif, .jpg, .png', 'yith-woocommerce-product-add-ons' ),
	        'id'  		=> 'yith_wapo_settings_filetypes',
	        'default' 	=> '.gif, .jpg, .png, .rar, .txt, .zip',
	        'css'     	=> 'min-width: 350px;',
		    'desc_tip'	=>  true,
	    ),
	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wapo_settings_end'
	    ),

	)

);

return apply_filters( 'yith_wapo_panel_general_options', $general );