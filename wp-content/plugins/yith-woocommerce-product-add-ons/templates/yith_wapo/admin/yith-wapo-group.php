<?php
/**
 * Admin Products Options Group
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;

/*
 *	
 */

global $wpdb, $woocommerce;

$id = isset( $_GET['id'] ) && $_GET['id'] > 0 ? $_GET['id'] : ( isset( $_POST['id'] ) && $_POST['id'] > 0 ? $_POST['id'] : 0 );
$group = new YITH_WAPO_Group( $id );

?>

<div id="group" class="wrap wapo-plugin">

	<h1>
		<?php echo $group->id != '' ? __( 'Group', 'yith-woocommerce-product-add-ons' ) . ': ' . $group->name : __( 'New group', 'yith-woocommerce-product-add-ons' ); ?>
		<a href="edit.php?post_type=product&page=yith_wapo_group" class="page-title-action"><?php echo __( 'Add new', 'yith-woocommerce-product-add-ons' ); ?></a>
	</h1>

	<form id="group-form" action="edit.php?post_type=product&page=yith_wapo_group" method="post">

		<input type="hidden" name="id" value="<?php echo $group->id; ?>">
		<input type="hidden" name="act" value="<?php echo $group->id > 0 ? 'update' : 'new'; ?>">
		<input type="hidden" name="class" value="YITH_WAPO_Group">
		<input type="hidden" name="types-order" value="">

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="name"><?php echo __( 'Group name', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td><input name="name" type="text" value="<?php echo $group->name; ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="products_id"><?php echo __( 'Products', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td>
						<?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
							<input type="hidden" class="wc-product-search" style="width: 350px;" id="products_id" name="products_id"
								data-placeholder="<?php esc_attr_e( 'Applied to...', 'yith-woocommerce-product-add-ons' ); ?>"
								data-action="woocommerce_json_search_products"
								data-multiple="true"
								data-exclude=""
								data-selected="<?php
									$product_ids = array_filter( array_map( 'absint', explode( ',', $group->products_id ) ) );
									$json_ids    = array();
									foreach ( $product_ids as $product_id ) {
										$product = wc_get_product( $product_id );
										if ( is_object( $product ) ) {
											$json_ids[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
										}
									}
									echo esc_attr( json_encode( $json_ids ) );
									?>"
								value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>"
							/>
						<?php else : ?>
							<select name="products_id[]" id="products_id" class="wc-product-search"
						    	data-placeholder="<?php esc_attr_e( 'Applied to...', 'yith-woocommerce-product-add-ons' ); ?>"
						    	multiple="multiple">
						        <?php
						        $products_array = explode( ',', $group->products_id );
						        foreach ( $products_array as $key => $value ) :
						        	if ( $value > 0 ) :
							        	$base_product = wc_get_product( $value ); ?>
							        	<option selected="selected" value="<?php echo $value; ?>"><?php echo $base_product->get_title(); ?> (#<?php echo $value; ?>)</option>
						        	<?php endif; ?>
						        <?php endforeach; ?>
						    </select>
						<?php endif; ?>

					</td>
				</tr>
				
				<tr>
					<th scope="row"><label for="categories_id"><?php echo __( 'Categories', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td>
						<select name="categories_id[]" class="categories_id-select2"
							placeholder="<?php esc_attr_e( 'Applied to...', 'yith-woocommerce-product-add-ons' ); ?>"
							multiple="multiple">
							<?php

							$categories_array = explode( ',', $group->categories_id );
							echo_product_categories_childs_of( 0, 0, $categories_array );

							function echo_product_categories_childs_of( $id = 0, $tabs = 0, $categories_array = array() ) {
								$categories = get_categories( array( 'taxonomy'=>'product_cat', 'parent'=>$id, 'orderby'=>'name', 'order'=>'ASC' ) );
								foreach ( $categories as $key => $value ) {
									echo '<option value="' . $value->term_id . '" ' . ( in_array( $value->term_id, $categories_array ) ? 'selected="selected"' : '' ) . '>' . str_repeat( '&#8212;', $tabs ) . ' ' . $value->name . '</option>';
									$childs = get_categories( array( 'taxonomy'=>'product_cat', 'parent'=>$value->term_id, 'orderby'=>'name', 'order'=>'ASC' ) );
									if ( count( $childs ) > 0 ) { echo_product_categories_childs_of( $value->term_id, $tabs + 1, $categories_array ); }
								}
							}

						?></select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="priority"><?php echo __( 'Priority', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td><input name="priority" type="number" value="<?php echo $group->priority; ?>" class="small-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="visibility"><?php echo __( 'Visibility', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td>
						<select name="visibility">
							<option value="0" <?php selected( $group->visibility, 0 ); ?>><?php echo __( 'Hidden', 'yith-woocommerce-product-add-ons' ); ?></option>
							<option value="1" <?php selected( $group->visibility, 1 ); ?>><?php echo __( 'Administrators only', 'yith-woocommerce-product-add-ons' ); ?></option>
							<option value="9" <?php selected( $group->visibility, 9 ); ?>><?php echo __( 'Public', 'yith-woocommerce-product-add-ons' ); ?></option>
						</select>
					</td>
				</tr>
				<?php if ( $group->id > 0 ) : ?>
					<tr>
						<th scope="row"><label for="types"><?php echo __( 'Add-ons', 'yith-woocommerce-product-add-ons' ); ?></label></th>
						<td></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

	</form>

	<?php if ( $group->id > 0 ) : ?>

		<?php

		if( function_exists( 'wp_enqueue_media' ) ) { wp_enqueue_media(); } else {
		    wp_enqueue_style( 'thickbox' );
		    wp_enqueue_script( 'media-upload' );
		    wp_enqueue_script( 'thickbox' );
		}

		?>

		<!-- TYPES TABLE -->
		<div id="wapo-types" class="wrap">

			<div id="type-form-add" class="type-row">

				<a href="#" class="button button-primary wapo-type-new"><?php echo __( 'Add new', 'yith-woocommerce-product-add-ons' );?></a>

				<?php echo YITH_WAPO_Type::printOptionTypeForm( $wpdb , $group ); ?>

			</div>

			<ul id="sortable-list" class="sortable">

				<?php

				$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}yith_wapo_types WHERE group_id='$group->id' AND del='0' ORDER BY priority ASC" );
				foreach ( $rows as $key => $value ) :

					$type_id = $value->id;
					$array_options = maybe_unserialize( $value->options );

					?>

					<li id="type-<?php echo $value->id; ?>" class="type-row">

						<a href="#type-form-<?php echo $value->id; ?>" class="wapo-type-edit">
							#<?php echo $value->id; ?> <?php echo $value->label; ?>
							<span>
								<strong><?php echo $value->type; ?></strong>
								<?php if ( isset($array_options['label']) && count( $array_options['label'] ) > 0 ) : ?>
									with <?php echo count( $array_options['label'] ) . ' ' . __( 'options', 'yith-woocommerce-product-add-ons' ); ?>
								<?php endif; ?>
							</span>
							<?php if ( $value->required ) : ?><span style=" text-transform: capitalize;">[<?php echo __( 'Required', 'yith-woocommerce-product-add-ons' ); ?>]</span><?php endif; ?>
							<span>
							<?php
							$rows_dep = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}yith_wapo_types WHERE id!='$value->id' AND group_id='$group->id' AND del='0' ORDER BY label ASC" );
							
							$depsinarray = array();
							foreach ( $rows_dep as $key_dep => $value_dep ) {
								$depend_array = explode( ',', $value->depend );
								if ( in_array( $value_dep->id, $depend_array ) ) { $depsinarray[] = '#' . $value_dep->id . ' ' . $value_dep->label; }
							}
							if ( count( $depsinarray ) > 0 ) {
								echo __( 'Dependencies: ', 'yith-woocommerce-product-add-ons' );
								foreach ( $depsinarray as $key_dep => $value_dep ) {
									echo '<i>' . $value_dep . '</i>';
								}
							}
							?>
							</span>
						</a>

						<?php echo YITH_WAPO_Type::printOptionTypeForm( $wpdb , $group , $value ); ?>

					</li>

				<?php endforeach; ?>

			</ul>

		</div>

	<?php endif; ?>

	<p class="submit">
		<input type="submit" name="submit" id="submit" form="group-form" class="button button-primary" value="<?php echo __( 'Save group', 'yith-woocommerce-product-add-ons' );?>">
		<input type="checkbox" name="del" value="1" form="group-form" style="margin-left: 20px;">
		<span style="color: #a00;"><?php echo __( 'Delete this group', 'yith-woocommerce-product-add-ons' );?> <span class="dashicons dashicons-trash" style="margin-top: 5px;"></span></span>
	</p>

	</form>

</div>

<script>

	// OPEN TYPE NEW
	jQuery('.wapo-type-edit').click( function() {
		jQuery(this).next('form').toggle('fast');
	});
	jQuery('.wapo-type-new').click( function() {
		jQuery(this).hide();
		jQuery(this).next('form').slideDown('fast');
	});
	jQuery('.cancel.button').click( function() {
		jQuery(this).parents('form').slideUp();
		jQuery('.wapo-type-new').fadeIn();
	});

	// MANAGE OPTIONS TABLE
	jQuery('.options table .option-label input').live( 'change', function(){

		var delete_button = jQuery( '.button.remove-row', jQuery(this).parents('tr') );
		if ( jQuery(this).val() ) { delete_button.fadeIn(); }
		else { delete_button.fadeOut(); }
		
		var empty_fields = jQuery( '.option-label input', jQuery(this).parents('table') ).filter( function(){ return ! jQuery(this).val(); }).length;
		if ( empty_fields < 1 ) {
			var tr = jQuery(this).parents('tr');
			var clone = tr.clone();
			clone.find(':text').val('');
			clone.find(':checkbox').removeAttr('checked');
			clone.find('.button.remove-row').css('display','none').css('opacity','1');
			var $default = clone.find('.new_default');
			$default.attr( 'value' , parseInt( $default.attr('value') ) + 1 );
			var $required = clone.find('.new_required');
			$required.attr( 'value' , parseInt( $required.attr('value') ) + 1 );
			tr.after( clone );
		}

	});
	jQuery('.button.remove-row').live( 'click', function(){
		jQuery(this).parents('tr').remove();
	});

	// CHANGE TYPE
	jQuery('.type select').live( 'change', function(){
		jQuery(this).parents('form').removeClass().addClass(jQuery(this).val());
		changeType( jQuery(this) );
	});

	function changeType( item ) {

		if ( item ) { var parent = item.parents('.type-row'); }
		else { var parent = jQuery('body'); }
		
		jQuery('form .option-min input', parent).val('-').attr('disabled','disabled');
		jQuery('form .option-max input', parent).val('-').attr('disabled','disabled');

		jQuery('form.number .option-min input', parent).val('').removeAttr('disabled');
		jQuery('form.number .option-max input', parent).val('').removeAttr('disabled');
		
		jQuery('form.price .option-min input', parent).val('').removeAttr('disabled');
		jQuery('form.price .option-max input', parent).val('').removeAttr('disabled');
		
		jQuery('form.range .option-min input', parent).val('').removeAttr('disabled');
		jQuery('form.range .option-max input', parent).val('').removeAttr('disabled');

		jQuery('form.textarea .option-min input', parent).val('').removeAttr('disabled');
		jQuery('form.textarea .option-max input', parent).val('').removeAttr('disabled');

	}

	changeType( );

	// SELECT 2
	jQuery(".products_id-select2").select2();
	jQuery(".categories_id-select2").select2();
	jQuery(".attributes_id-select2").select2();
	jQuery(".depend-select2").select2();

	// SORTABLE
	jQuery('.sortable').sortable({
		axis: 'y',
		update: function (event, ui) {
			var priority = 1;
			var types_order = '';
			jQuery('.sortable > li').each(function(i) {
				//jQuery( 'input[name="priority"]', this ).val( priority );
				var id = jQuery( 'input[name="id"]', this ).val();
				types_order += id + ',';
				jQuery('input[name="types-order"]').val( types_order );
				priority++;
			});
		}
	});
	
	// DEFAULT / CHECKED
	jQuery('form.select .option-default input[type=checkbox], form.radio .option-default input[type=checkbox]').on('click', function(){
		var form = jQuery(this).parents('form');
		if ( jQuery(this).is(':checked') ){
			jQuery('.option-default input[type=checkbox]', form).removeAttr('checked');
			jQuery(this).attr('checked', 'checked');
		}
	});

	// TITLE PAGE
	document.title = "YITH WooCommerce Product Add-Ons";

</script>