<?php
/**
 * Option group template
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Group Data

$type_id = $single_type['id'];
$title = $single_type['label'];
$description = $single_type['description'];
$conditional =  $yith_wapo_frontend->checkConditionalOptions( $single_type['depend'] );
$conditional_hidden = ! empty( $conditional ) ? 'ywapo_conditional_hidden' : '';
$disabled = ! empty( $conditional ) ? 'disabled' : '';
$image = $single_type['image'];
$type = strtolower( $single_type['type'] ) ;

$required = $single_type['required'];

$name = 'ywapo_'.$type.'_'.$type_id;

$value = 'ywapo_value_'.$type_id;

$empty_option_text = apply_filters( 'ywapo_empty_option_text' , __( 'Select an option...' , 'yith-woocommerce-product-add-ons' ) ) ;

// Options Data
$options = maybe_unserialize( $single_type['options'] );

if( !( isset( $options['label'] ) ) || count( $options['label'] ) <= 0 ) return;
?>

<div id="<?php echo $value ?>" class="ywapo_group_container ywapo_group_container_<?php echo $type; ?> form-row form-row-wide <?php echo $conditional_hidden; ?>" data-requested="<?php echo $required ? '1' : '0' ; ?>" data-type="<?php echo $type; ?>" data-id="<?php echo $type_id; ?>" data-condition="<?php echo esc_attr( $conditional ) ?>">
    <?php if( $title && $yith_wapo_frontend->_option_show_label_type == 'yes' ): ?>
    <h3><?php echo wptexturize( $title ); ?><?php echo ( $required ? '<abbr class="required" title="required">*</abbr>' : '' ) ?></h3>
    <?php endif; ?>
    <?php if( $image && $yith_wapo_frontend->_option_show_image_type == 'yes' ): ?>
        <?php echo '<div class="ywapo_product_option_image"><img src="'.esc_attr($image).'" alt="'.esc_attr($title).'"/></div>'; ?>
    <?php endif; ?>
    <?php if( $description && $yith_wapo_frontend->_option_show_description_type == 'yes' ): ?>
    <?php echo '<div class="ywapo_product_option_description">' .wpautop( wptexturize( $description ) ).'</div>'; ?>
    <?php endif; ?>
    <?php

    if( $type=='select' ) {
        echo '<select name="'.$name.'" class="ywapo_input" '.$disabled.' '.( $required ? 'required' : '').' >';
        echo '<option value="">'.$empty_option_text.'</option>';
    }

    if( is_array( $options ) ) {

        $options['label'] = array_map( 'stripslashes', $options['label'] );
        $options['description'] = array_map( 'stripslashes', $options['description'] );

        for( $i=0; $i< count($options['label']) ; $i++ ) {

            //--- WPML ----------
            if( YITH_WAPO::$is_wpml_installed ) {

                $options['label'][$i] = YITH_WAPO_WPML::string_translate( $options['label'][$i] );
                $options['description'][$i] = YITH_WAPO_WPML::string_translate( $options['description'][$i] );

            }
            //---END WPML---------

            $min = isset( $options['min'][$i] ) ? $options['min'][$i] : false;
            $max = isset( $options['max'][$i] ) ? $options['max'][$i] : false;
            $image = isset( $options['image'][$i] ) && $yith_wapo_frontend->_option_show_image_option == 'yes' ? $options['image'][$i] : '';
            $price_type = isset( $options['type'][$i] ) ? $options['type'][$i] : 'fixed';
            $description = isset( $options['description'][$i] ) && $yith_wapo_frontend->_option_show_description_option == 'yes' ? $options['description'][$i] : '';

            $checked = ( isset( $options['default'] ) ) ? ( in_array( $i , $options['default'] ) ) : false;

            $required_option = $required;

            if( ! $required_option ) {
                $required_option = ( isset( $options['required'] ) ) ? ( in_array( $i , $options['required'] ) ) : false;
            }

            $yith_wapo_frontend->printOptions( $i, $product, $type_id, $type, $name, $value, $options['price'][$i], $options['label'][$i], $image, $price_type, $description, $required_option, $checked, $disabled, 'before' , $min, $max );

        }

    }

    if( $type=='select' ) {
        echo '</select>';
    }

    ?>
</div>