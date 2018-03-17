<?php
/**
 * Plugin Name: Tutsplus display WooCommerce products and categories/subcategories separately in archive pages
 * Plugin URI: http://code.tutsplus.com/tutorials/woocommerce-display-product-categories-subcategories-and-products-in-two-separate-lists--cms-25479
 * Description: Display products and catgeories / subcategories as two separate lists in product archive pages
 * Version: 1.0
 * Author: Rachel McCollin
 * Author URI: http://rachelmccollin.co.uk
 *
 *
 */
 function tutsplus_product_subcategories( $args = array() ) {
     
    $parentid = get_queried_object_id();
         
	$args = array(
		'parent' => $parentid
	);
	 
	$terms = get_terms( 'product_cat', $args );
	 
	if ( $terms ) {
			 
		echo '<table id="mycat">';
		 
			foreach ( $terms as $term ): ?>
				<tr>
					<td class="dtsty">
						<a class="cattit" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo $term->name; ?></a>
						<a class="btn-choose" href="<?php echo esc_url( get_term_link( $term ) ); ?>">Подробнее</a>
                    </td>
					<td>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php woocommerce_subcategory_thumbnail( $term ); ?></a>
					</td>
					<td>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><img width="96" height="78" src="<?php echo Categories_Multiple_Images::get_image($term->term_id,1,'full')?>" class="attachment-post-thumbnail size-post-thumbnail"></a>
                    </td>
					<td>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><img width="96" height="78" src="<?php echo Categories_Multiple_Images::get_image($term->term_id,2,'full')?>" class="attachment-post-thumbnail size-post-thumbnail"></a>
                    </td>																	 
				</tr>
		<?php endforeach;
		 
		echo '</table>';
	 
	}
}
add_action( 'woocommerce_before_shop_loop', 'tutsplus_product_subcategories', 50 );

function tutsplus_product_cats_css() {
 
    /* register the stylesheet */
    wp_register_style( 'tutsplus_product_cats_css', plugins_url( 'css/style.css', __FILE__ ) );
     
    /* enqueue the stylsheet */
    wp_enqueue_style( 'tutsplus_product_cats_css' );
     
}
 
add_action( 'wp_enqueue_scripts', 'tutsplus_product_cats_css' );