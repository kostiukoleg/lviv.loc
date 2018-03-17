<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package freddo
 */

?>

	</div><!-- #content -->
	<?php $showSearchButton = freddo_options('_search_button', '1');
	if ($showSearchButton) : ?>
	<!-- Start: Search Form -->
	<div class="opacityBoxSearch"></div>
	<div class="search-container">
		<?php get_search_form(); ?>
	</div>
	<!-- End: Search Form -->
	<?php endif; ?>

	<footer id="colophon" class="site-footer">
		<div class="mainFooter">
			<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
				<div class="footerArea">
					<div class="freddoFooterWidget">
						<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
							<aside id="footer-1" class="widget-area footer" role="complementary">
								<?php dynamic_sidebar( 'footer-1' ); ?>
							</aside><!-- #footer-1 -->
						<?php endif; ?>
						<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
							<aside id="footer-2" class="widget-area footer" role="complementary">
								<?php dynamic_sidebar( 'footer-2' ); ?>
							</aside><!-- #footer-2 -->
						<?php endif; ?>
						<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
							<aside id="footer-3" class="widget-area footer" role="complementary">
								<?php dynamic_sidebar( 'footer-3' ); ?>
							</aside><!-- #footer-3 -->
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="site-copy-down">
				<div class="site-info">
					<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'freddo' ) ); ?>"><?php
						/* translators: %s: CMS name, i.e. WordPress. */
						printf( esc_html__( 'Proudly powered by %s', 'freddo' ), 'WordPress' );
					?></a>
					<span class="sep"> | </span>
					<?php 
						/* translators: 1: theme name, 2: theme developer */
						printf( esc_html__( 'Theme: %1$s by %2$s.', 'freddo' ), '<a target="_blank" href="https://crestaproject.com/downloads/freddo/" rel="nofollow" title="Freddo Theme">Freddo</a>', 'CrestaProject' );
					?>
				</div><!-- .site-info -->
				<div class="site-social">
					<?php freddo_show_social_network(); ?>
				</div><!-- .site-social -->
			</div><!-- .site-copy-down -->
		</div><!-- .mainFooter -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<?php $scrollToTopMobile = freddo_options('_scroll_top', ''); ?>
<a href="#top" id="toTop" class="<?php echo $scrollToTopMobile ? 'scrolltop_on' : 'scrolltop_off' ?>"><i class="fa fa-angle-up fa-lg"></i></a>
<?php wp_footer(); ?>

</body>
</html>
