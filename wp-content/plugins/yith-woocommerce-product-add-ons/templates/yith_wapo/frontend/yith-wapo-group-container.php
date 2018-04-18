<?php
/**
 * Group container template
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php
foreach( $types_list as $single_type ) {
    $yith_wapo_frontend->printSingleGroupType( $product , $single_type );
}

$display_price = function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $product ) : $product->get_display_price();
?>

<div class="yith_wapo_group_total" data-product-price="<?php echo esc_attr( $display_price ); ?>">
    <table>
        <tr><td><?php _e( 'Additional options total:' , 'yith-woocommerce-product-add-ons' ) ?></td><td><div class="yith_wapo_group_option_total"><span class="price amount"></span></div></td></tr>
        <tr><td><?php _e( 'Order total:' , 'yith-woocommerce-product-add-ons' ) ?></td><td><div class="yith_wapo_group_final_total"><span class="price amount"></span></div></td></tr>
    </table>
</div>
