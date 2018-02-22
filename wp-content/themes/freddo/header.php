<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package freddo
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if(freddo_options('_show_loader', '0') == 1 ) : ?>
	<div class="freddoLoader">
		<?php freddo_loadingPage(); ?>
	</div>
<?php endif; ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'freddo' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="mainLogo">
			<div class="freddoSubHeader title">
				<div class="site-branding">
					<?php
					if ( function_exists( 'the_custom_logo' ) ) : ?>
					<div class="freddoLogo" itemscope itemtype="http://schema.org/Organization">
						<?php the_custom_logo(); ?>
					<?php endif; ?>
					<div class="freddoTitleText">
						<?php if ( is_front_page() && is_home() ) : ?>
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php else : ?>
							<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
						<?php
						endif;

						$description = get_bloginfo( 'description', 'display' );
						if ( $description || is_customize_preview() ) : ?>
							<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
						<?php
						endif; ?>
					</div>
					</div>
				</div><!-- .site-branding -->
			</div>
		</div>

		<div class="mainHeader">
			<?php if ( is_active_sidebar( 'sidebar-push' ) ) : ?>
				<div class="hamburger-menu">
					<div class="hamburger-box">
						<div class="hamburger-inner"></div>
					</div>
				</div>
			<?php endif; ?>
			<?php $showSearchButton = freddo_options('_search_button', '1');
			if ($showSearchButton) : ?>
			<div class="search-button">
				<div class="search-circle"></div>
				<div class="search-line"></div>
			</div>
			<?php endif; ?>
			<div class="freddoHeader">
				<div class="freddoSubHeader">
					<nav id="site-navigation" class="main-navigation">
						<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><i class="fa fa-lg fa-bars" aria-hidden="true"></i></button>
						<?php
							wp_nav_menu( array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
							) );
						?>
					</nav><!-- #site-navigation -->
				</div>
			</div>
		</div>
	</header><!-- #masthead -->
	
	<?php if (is_singular(array( 'post', 'page' )) && '' != get_the_post_thumbnail() && !is_page_template('template-onepage.php') ) : ?>
		<?php while ( have_posts() ) : 
		the_post(); ?>
		<?php 
			$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'freddo-the-post-big');
			$showScrollDownButton = freddo_options('_scrolldown_button', '1');
			$effectFeatImage = freddo_options('_effect_featimage', 'withZoom');
		?>
		<div class="freddoBox">
			<div class="freddoBigImage <?php echo esc_attr($effectFeatImage); ?>" style="background-image: url(<?php echo esc_url($src[0]); ?>);">
				<div class="freddoImageOp">
				</div>
			</div>
			<div class="freddoBigText">
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					<?php if ( 'post' === get_post_type() ) : ?>
					<div class="entry-meta">
						<?php freddo_posted_on(); ?>
					</div><!-- .entry-meta -->
					<?php if ($showScrollDownButton) : ?>
						<?php $scrollText = freddo_options('_post_scrolldown_text', __('Scroll Down', 'freddo')); ?>
						<div class="scrollDown"><span><?php echo esc_html($scrollText); ?></span></div>
					<?php endif; ?>
					<?php else: ?>
						<?php if ($showScrollDownButton) : ?>
							<?php $scrollText = freddo_options('_post_scrolldown_text', __('Scroll Down', 'freddo')); ?>
							<div class="scrollDown"><span><?php echo esc_html($scrollText); ?></span></div>
						<?php endif; ?>
					<?php endif; ?>
				</header><!-- .entry-header -->
			</div>
		</div>
		<?php endwhile; ?>
	<?php endif; ?>

	<div id="content" class="site-content">
