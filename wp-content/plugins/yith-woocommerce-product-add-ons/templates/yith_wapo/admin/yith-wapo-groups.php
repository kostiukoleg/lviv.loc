<?php
/**
 * Admin Products Options Groups list
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;

/*
 *	
 */

global $wpdb;

$current_user = wp_get_current_user();

$vendor_user = YITH_WAPO::get_current_multivendor();

$vendor_check = isset( $vendor_user ) && is_object( $vendor_user ) && $vendor_user->has_limited_access() ? 'AND user_id='.$vendor_user->id : '' ;

$show_vendor_column = YITH_WAPO::$is_vendor_installed && ( !isset( $vendor_user ) || ( isset( $vendor_user ) && is_object( $vendor_user ) && ! $vendor_user->has_limited_access() ) );

?>

<div id="wapo-groups" class="wrap wapo-plugin">

	<h1>
		<?php echo __( 'Groups', 'yith-woocommerce-product-add-ons' ); ?>
		<a href="edit.php?post_type=product&page=yith_wapo_group" class="page-title-action"><?php echo __( 'Add new', 'yith-woocommerce-product-add-ons' ); ?></a>
	</h1>

	<p style="margin-bottom: 30px;"><?php echo __( 'Complete list of product option groups.', 'yith-woocommerce-product-add-ons' ); ?></p>

	<?php

	for ( $visibility = 9; $visibility >=0; $visibility-- ) :

        $query = "SELECT * FROM {$wpdb->prefix}yith_wapo_groups WHERE visibility='$visibility' $vendor_check AND del='0' ORDER BY priority ASC";

		$rows = $wpdb->get_results( $query );

		if ( count( $rows ) == 0 ) { continue; }
		
		?>

		<p><?php

		switch ( $visibility ) {
			case 0: echo __( '<span class="dashicons dashicons-hidden" style="margin: -1px 5px 0px 0px;"></span> <strong>Hidden groups</strong>', 'yith-woocommerce-product-add-ons' ); break;
			case 1: echo __( '<span class="dashicons dashicons-lock" style="margin: -1px 5px 0px 0px;"></span> <strong>Administrators only</strong>', 'yith-woocommerce-product-add-ons' ); break;
			case 9: echo __( '<span class="dashicons dashicons-visibility" style="margin: -1px 5px 0px 0px;"></span> <strong>Public groups</strong>', 'yith-woocommerce-product-add-ons' ); break;
			default: echo __( '<span class="dashicons dashicons-visibility" style="margin: -1px 5px 0px 0px;"></span> <strong>Public groups</strong>', 'yith-woocommerce-product-add-ons' ); break;
		}

		?></p>

		<table class="wp-list-table widefat fixed striped posts" style="margin-bottom: 30px;">
			<tr>
				<th style="width: 200px;"><?php echo __( 'Name', 'yith-woocommerce-product-add-ons' ); ?></th>
				<th style="width: 200px;"><?php echo __( 'Visibility', 'yith-woocommerce-product-add-ons' ); ?></th>
				<th><?php echo __( 'Products', 'yith-woocommerce-product-add-ons' ); ?></th>
				<th><?php echo __( 'Categories', 'yith-woocommerce-product-add-ons' ); ?></th>
				<!--<th><?php echo __( 'Attributes', 'yith-woocommerce-product-add-ons' ); ?></th>-->
				<th><?php echo __( 'Priority', 'yith-woocommerce-product-add-ons' ); ?></th>
                <?php if( $show_vendor_column ) : ?>
                    <th><?php echo __( 'Vendor', 'yith-woocommerce-product-add-ons' ); ?></th>
                <?php endif; ?>
				<th style="width: 60px;"><?php echo __( 'Actions', 'yith-woocommerce-product-add-ons' ); ?></th>
			</tr>

			<?php

			foreach ( $rows as $key => $value ) : ?>

				<tr>
					<td>
						<span class="dashicons dashicons-exerpt-view" style="margin: 5px 5px 0px 0px;"></span>
						<strong><a href="edit.php?post_type=product&page=yith_wapo_group&id=<?php echo $value->id; ?>" class="row-title"><?php echo $value->name; ?></a></strong>
					</td>
					<td><strong><?php

					switch ( $value->visibility ) {
						case 0: echo '<span style="color: rgba(0,0,0,0.1);"><span class="dashicons dashicons-hidden" style="margin: 5px 5px 0px 0px;"></span> Hidden</span>'; break;
						case 1: echo '<span class="dashicons dashicons-lock" style="margin: 5px 5px 0px 0px;"></span> Administrators only'; break;
						case 9: echo '<span class="dashicons dashicons-visibility" style="margin: 5px 5px 0px 0px;"></span> Public'; break;
						default: echo '<span style="color: rgba(0,0,0,0.1);"><span class="dashicons dashicons-hidden" style="margin: 5px 5px 0px 0px;"></span> Hidden</span>'; break;
					}

					?></strong></td>
					<td><?php

					if ( $value->products_id ) {

						$products_id = explode( ',', trim( $value->products_id, ',' ) );
						
						echo '<ul class="products_list">';
						foreach ( $products_id as $key_2 => $value_2 ) {
							if ( $value_2 > 0 ) {
								$result = $wpdb->get_row( "SELECT post_title FROM {$wpdb->prefix}posts WHERE ID='$value_2'" );
								echo '<li><a href="post.php?post=' . $value_2 . '&action=edit">' . $result->post_title . '</a></li>';
							}
						}
						echo '</ul>';

					} else { echo '<strong>' . __( 'All', 'yith-woocommerce-product-add-ons' ) . '</strong>'; }

					?></td>
					<td><?php

					if ( $value->categories_id ) {

						$categories_id = explode( ',', trim( $value->categories_id, ',' ) );

						echo '<ul class="categories_list">';
						foreach ( $categories_id as $key_2 => $value_2 ) {
							$result = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}terms WHERE term_id='$value_2'" );
							echo '<li><span>' . $result->name . '</span></li>';
						}
						echo '</ul>';

					} else { echo '<strong>' . __( 'All', 'yith-woocommerce-product-add-ons' ) . '</strong>'; }

					?></td>
					<!--<td><?php echo $value->attributes_id; ?></td>-->
					<td><?php echo $value->priority; ?></td>
                    <?php if( $show_vendor_column ) : ?>
                        <td>
                            <?php
                            $vendor_id = intval( $value->user_id );
                            if( $vendor_id > 0 ) {
                                $current_vendor = YITH_WAPO::get_multivendor_by_id( $vendor_id );
                                if( isset( $current_vendor ) && is_object( $current_vendor ) ) {
                                    echo $current_vendor->name;
                                }
                            }

                            ?>
                        </td>
                    <?php endif; ?>
					<td><a href="edit.php?post_type=product&page=yith_wapo_group&id=<?php echo $value->id; ?>" class="button"><?php echo __( 'Edit', 'yith-woocommerce-product-add-ons' ); ?></a></td>
				</tr>

			<?php endforeach; ?>

		</table>

	<?php endfor; ?>

</div>