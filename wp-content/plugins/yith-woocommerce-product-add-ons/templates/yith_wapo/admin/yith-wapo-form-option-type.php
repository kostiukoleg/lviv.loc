<?php
/**
 * Admin Type Form
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;

$is_edit = isset( $type );
$act = 'new';
$field_priority = 0;
$field_type = '';
$field_image_url = YITH_WAPO_URL . '/assets/img/placeholder.png';
$field_image = '';
$field_id_img_class = 'form-add';
$field_label = '';
$field_description = '';
$field_required = false;
$field_qty_individually = false;
$field_description = '';

$dependencies_query = "SELECT * FROM {$wpdb->prefix}yith_wapo_types WHERE group_id='$group->id' AND del='0' ORDER BY label ASC";

if( $is_edit ) {
    $act = 'update';
    $field_priority = $type->priority;
    $field_type = $type->type;
    if(  $type->image ) {
        $field_image_url = $field_image = $type->image;
    }
    $field_id_img_class = $type->id;
    $field_label = $type->label;
    $field_required = $type->required;
    $field_description = $type->description;

    $dependencies_query = "SELECT * FROM {$wpdb->prefix}yith_wapo_types WHERE id!='$type->id' AND group_id='$group->id' AND del='0' ORDER BY label ASC";
}
?>

<form action="edit.php?post_type=product&page=yith_wapo_group" method="post" class="<?php echo $field_type; ?>">

    <?php if( $is_edit ) : ?>

        <input type="hidden" name="id" value="<?php echo $type->id; ?>">

    <?php endif; ?>
    <input type="hidden" name="act" value="<?php echo $act; ?>">
    <input type="hidden" name="class" value="YITH_WAPO_Type">
    <input type="hidden" name="group_id" value="<?php echo $group->id; ?>">
    <input type="hidden" name="priority" value="<?php echo $field_priority; ?>">

    <div class="form-left">
        <div class="form-row">
            <div class="type">
                <label for="label"><?php echo __( 'Add-on', 'yith-woocommerce-product-add-ons' ); ?></label>
                <select name="type">
                    <option value="checkbox" <?php selected( $field_type , 'checkbox' ); ?>><?php _e( 'Checkbox' , 'yith-woocommerce-product-add-ons' )  ?></option>
                    <option value="radio" <?php selected( $field_type , 'radio'); ?>><?php _e( 'Radio Button' , 'yith-woocommerce-product-add-ons' )  ?></option>
                    <option value="text" <?php selected( $field_type , 'text'); ?>><?php _e( 'Text' , 'yith-woocommerce-product-add-ons' )  ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-right">
        <div class="form-row">
            <div class="label">
                <label for="label"><?php _e( 'Title', 'yith-woocommerce-product-add-ons' ); ?></label>
                <input name="label" type="text" value="<?php echo $field_label; ?>" class="regular-text">
            </div>
            <div class="depend">
                <label for="depend"><?php _e( 'Requirements', 'yith-woocommerce-product-add-ons' ); ?><span class="woocommerce-help-tip" data-tip="<?php _e( 'Show this add-on only if users have first selected the following options.', 'yith-woocommerce-product-add-ons' ) ?>"></span></label>
                <select name="depend[]" class="depend-select2" multiple="multiple" placeholder="<?php echo __( 'Choose required add-ons', 'yith-woocommerce-product-add-ons' ); ?>..."><?php
                    $dependencies = $wpdb->get_results( $dependencies_query );
                    foreach ( $dependencies as $key => $item ) {
                        if( ! $is_edit ) {
                            echo '<option value="' . $item->id . '">' . $item->label . '</option>';
                        } else {
                            $depend_array = explode( ',', $type->depend );
                            echo '<option value="' . $item->id . '"' . ( in_array( $item->id, $depend_array ) ? 'selected="selected"' : '' ) . '>' . $item->label . '</option>';
                        }
                    }
                    ?>
                </select>

            </div>
            <div class="required">
                <label for="required"><?php echo __( 'Required', 'yith-woocommerce-product-add-ons' ); ?></label>
                <input type="checkbox" name="required" value="1" <?php echo $field_required ? 'checked="checked"' : ''; ?>>
            </div>
        </div>
        <div class="form-row">
            <div class="description">
                <label for="description"><?php echo __( 'Description', 'yith-woocommerce-product-add-ons' ); ?></label>
                <textarea name="description" id="description" rows="3" style="width: 100%;"><?php echo $field_description; ?></textarea>
            </div>
        </div>
        
        <?php if( $is_edit ) : ?>

            <div class="form-row">
                <div class="options">
                    <table class="wp-list-table widefat fixed">
                        <tr>
                            <th class="option-label" colspan="2"><?php echo __( 'Option label', 'yith-woocommerce-product-add-ons' );?></th>
                            <th class="option-type"><?php echo __( 'Type', 'yith-woocommerce-product-add-ons' );?></th>
                            <th class="option-price"><?php echo __( 'Price', 'yith-woocommerce-product-add-ons' );?></th>
                            <th class="option-min"><?php echo __( 'Min', 'yith-woocommerce-product-add-ons' );?></th>
                            <th class="option-max"><?php echo __( 'Max', 'yith-woocommerce-product-add-ons' );?></th>
                            <!--<th class="option-description"><?php echo __( 'Description', 'yith-woocommerce-product-add-ons' );?></th>-->
                            <th class="option-delete"></th>
                        </tr>
                        <?php
                        $i = 0;
                        $array_options = maybe_unserialize( $type->options );
                        if ( ! isset($array_options['description'] ) ) { $array_options['description'] = ''; }
                        if ( isset( $array_options['label'] ) && is_array( $array_options['label'] ) ) {
                            $array_default = isset( $array_options['default'] ) ? $array_options['default'] : array();
                            $array_required = isset( $array_options['required'] ) ? $array_options['required'] : array();
                            foreach ( $array_options['label'] as $key => $value ) : ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="option-label"><input type="text" name="options[label][]" value="<?php echo stripslashes( $array_options['label'][$i] ); ?>" placeholder="Label" /></div>
                                        <div class="option-type">
                                            <select name="options[type][]">
                                                <option value="fixed" <?php echo isset( $array_options['type'][$i] ) && $array_options['type'][$i] == 'fixed' ? 'selected="selected"' : ''; ?>><?php echo __( 'Fixed amount', 'yith-woocommerce-product-add-ons' ); ?></option>
                                            </select>
                                        </div>
                                        <div class="option-price"><input type="text" name="options[price][]" value="<?php echo $array_options['price'][$i]; ?>" placeholder="0" /></div>
                                        <div class="option-min"><input type="text" name="options[min][]" value="<?php echo $array_options['min'][$i]; ?>" placeholder="0" /></div>
                                        <div class="option-max"><input type="text" name="options[max][]" value="<?php echo $array_options['max'][$i]; ?>" placeholder="0" /></div>
                                        <div class="option-delete"><a class="button remove-row"><?php echo __( 'Delete', 'yith-woocommerce-product-add-ons' ); ?></a></div>
                                        <div class="option-description" colspan="6"><input type="text" name="options[description][]" value="<?php echo stripslashes( $array_options['description'][$i] ); ?>" placeholder="Description" /></div>
                                        <div class="option-default">
                                            <input type="checkbox" name="options[default][]" value="<?php echo $i; ?>"
                                                <?php foreach ( $array_default as $key_def => $value_def ) { echo $i == $value_def ? 'checked="checked"' : ''; } ?> />
                                            <?php echo __( 'Checked', 'yith-woocommerce-product-add-ons' );?>
                                        </div>
                                        <div class="option-required">
                                            <input type="checkbox" name="options[required][]" value="<?php echo $i; ?>"
                                                <?php foreach ( $array_required as $key_def => $value_def ) { echo $i == $value_def ? 'checked="checked"' : ''; } ?> />
                                            <?php echo __( 'Required', 'yith-woocommerce-product-add-ons' );?>
                                        </div>
                                    </td>
                                </tr>
                                <?php $i++;
                            endforeach;
                        }
                        ?>
                        <tr>
                            <td colspan="7">
                                <div class="option-label"><input type="text" name="options[label][]" value="" placeholder="Label" /></div>
                                <div class="option-type">
                                    <select name="options[type][]">
                                        <option value="fixed"><?php echo __( 'Fixed amount', 'yith-woocommerce-product-add-ons' ); ?></option>
                                    </select>
                                </div>
                                <div class="option-price"><input type="text" name="options[price][]" value="" placeholder="0" /></div>
                                <div class="option-min"><input type="text" name="options[min][]" value="" placeholder="0" /></div>
                                <div class="option-max"><input type="text" name="options[max][]" value="" placeholder="0" /></div>
                                <div class="option-delete"><a class="button" style="display: none;"><?php echo __( 'Delete', 'yith-woocommerce-product-add-ons' );?></a></div>
                                <div class="option-description"><input type="text" name="options[description][]" value="" placeholder="Description" /></div>
                                <div class="option-default"><input type="checkbox" name="options[default][]" value="<?php echo $i ;?>" class="new_default" /> <?php echo __( 'Checked', 'yith-woocommerce-product-add-ons' );?></div>
                                <div class="option-required"><input type="checkbox" name="options[required][]" value="<?php echo $i ;?>" class="new_required" /> <?php echo __( 'Required', 'yith-woocommerce-product-add-ons' );?></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7"><i><?php echo __( 'Choose an option to see more options ;)', 'yith-woocommerce-product-add-ons' );?></i></td>
                        </tr>
                    </table>
                </div>
            </div>

        <?php endif; ?>

        <div class="form-row">
            <div class="options">
                <i><?php echo __( 'Save the "Add-on" if you want to add new options.', 'yith-woocommerce-product-add-ons' );?></i>
            </div>
        </div>
        <div class="form-row">

            <?php if( $is_edit ) : ?>

                <div class="delete" style="color: #a00; float: right;">
                    <input type="checkbox" name="del" value="1"> <?php echo __( 'Delete this Add-on', 'yith-woocommerce-product-add-ons' );?>
                    <span class="dashicons dashicons-trash"></span>
                </div>

            <?php endif; ?>

            <div class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php $is_edit ? _e( 'Save add-on', 'yith-woocommerce-product-add-ons' ) : _e( 'Save new add-on', 'yith-woocommerce-product-add-ons' );?>">
                <?php if( ! $is_edit ) : ?>
                    <a href="#" class="button cancel"><?php echo __( 'Cancel', 'yith-woocommerce-product-add-ons' );?></a>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="clear"></div>

</form>
