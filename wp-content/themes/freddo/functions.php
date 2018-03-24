<?php
/**
 * freddo functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package freddo
 */

if ( ! function_exists( 'freddo_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function freddo_setup() {
		/*
		 * Define the number of items in some onepage sections (real value plus 1)
		 */
		define( 'FREDDO_VALUE_FOR_SLIDER', '5' );
		define( 'FREDDO_VALUE_FOR_FEATURES', '5' );
		define( 'FREDDO_VALUE_FOR_CTA', '9' );
		define( 'FREDDO_VALUE_FOR_SKILLS', '11' );
		define( 'FREDDO_VALUE_FOR_SERVICES', '7' );
		define( 'FREDDO_VALUE_FOR_TEAM', '2' );
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on freddo, use a find and replace
		 * to change 'freddo' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'freddo', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'freddo-the-post-small' , 980, 600, true);
		add_image_size( 'freddo-the-post-big' , 1920, 99999);
		add_image_size( 'freddo-little-post' , 370, 220, true);

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'freddo' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 60,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
			'header-text' => array( 'site-title', 'site-description' ),
		) );
		
		/*
	 * Starter Content Support
	 */
	add_theme_support( 'starter-content', array(
		'posts' => array(
			'home' => array(
				'template' => 'template-onepage.php',
			),
			'blog',
		),
		'options' => array(
			'show_on_front'  => 'page',
			'page_on_front'  => '{{home}}',
			'page_for_posts' => '{{blog}}',
			'freddo_theme_options[_onepage_section_slider]' => '1',
			'freddo_theme_options[_onepage_image_1_slider]' => get_template_directory_uri().'/images/example/freddo_slider_example_1.jpg',
			'freddo_theme_options[_onepage_image_2_slider]' => get_template_directory_uri().'/images/example/freddo_slider_example_2.jpg',
			'freddo_theme_options[_onepage_text_1_slider]' => 'Welcome to Freddo Theme',
			'freddo_theme_options[_onepage_subtext_1_slider]' => 'Use the customizer to customize the onepage sections',
			'freddo_theme_options[_onepage_text_2_slider]' => 'Read the documentation',
			'freddo_theme_options[_onepage_subtext_2_slider]' => 'You can find the documentation in "Appearance-> About Freddo-> Documentation"',
			'freddo_theme_options[_onepage_section_skills]' => '1',
			'freddo_theme_options[_onepage_skillname_1_skills]' => 'Design',
			'freddo_theme_options[_onepage_skillvalue_1_skills]' => '84',
			'freddo_theme_options[_onepage_skillname_2_skills]' => 'WordPress',
			'freddo_theme_options[_onepage_skillvalue_2_skills]' => '93',
			'freddo_theme_options[_onepage_skillname_3_skills]' => 'SEO',
			'freddo_theme_options[_onepage_skillvalue_3_skills]' => '76',
			'freddo_theme_options[_onepage_skillname_4_skills]' => 'Support',
			'freddo_theme_options[_onepage_skillvalue_4_skills]' => '90',
			'freddo_theme_options[_onepage_skillname_5_skills]' => 'Customization',
			'freddo_theme_options[_onepage_skillvalue_5_skills]' => '89',
			'freddo_theme_options[_onepage_skillname_6_skills]' => 'Updates',
			'freddo_theme_options[_onepage_skillvalue_6_skills]' => '87',
			'freddo_theme_options[_onepage_section_cta]' => '1',
			'freddo_theme_options[_onepage_phrase_cta]' => 'Do you want more?',
			'freddo_theme_options[_onepage_desc_cta]' => 'Take a look at Freddo PRO version...',
			'freddo_theme_options[_onepage_textbutton_cta]' => 'PRO Version',
			'freddo_theme_options[_onepage_urlbutton_cta]' => 'https://crestaproject.com/demo/freddo-pro/',
		),
		'nav_menus' => array(
			'primary' => array(
				'name' => __( 'Primary', 'freddo' ),
				'items' => array(
					'link_home',
					'page_blog',
				),
			),
		),
	) );
	}
endif;
add_action( 'after_setup_theme', 'freddo_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function freddo_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'freddo_content_width', 640 );
}
add_action( 'after_setup_theme', 'freddo_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function freddo_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Classic Sidebar', 'freddo' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'freddo' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<div class="widget-title"><h3>',
		'after_title'   => '</h3></div>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Push Sidebar', 'freddo' ),
		'id'            => 'sidebar-push',
		'description'   => esc_html__( 'Add widgets here.', 'freddo' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<div class="widget-title"><h3>',
		'after_title'   => '</h3></div>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Left', 'freddo' ),
		'id'            => 'footer-1',
		'description'   => esc_html__( 'Add widgets here.', 'freddo' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<div class="widget-title"><h3>',
		'after_title'   => '</h3></div>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Center', 'freddo' ),
		'id'            => 'footer-2',
		'description'   => esc_html__( 'Add widgets here.', 'freddo' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<div class="widget-title"><h3>',
		'after_title'   => '</h3></div>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Right', 'freddo' ),
		'id'            => 'footer-3',
		'description'   => esc_html__( 'Add widgets here.', 'freddo' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<div class="widget-title"><h3>',
		'after_title'   => '</h3></div>',
	) );
}
add_action( 'widgets_init', 'freddo_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function freddo_scripts() {
	wp_enqueue_style( 'freddo-style', get_stylesheet_uri() );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() .'/css/font-awesome.min.css');
	wp_enqueue_style( 'freddo-ie', get_template_directory_uri() . '/css/ie.css', array(), '1.0', null );
	wp_style_add_data( 'freddo-ie', 'conditional', 'IE' );
	$query_args = array(
		'family' => 'Poppins:400,700%7CMontserrat:400,700'
	);
	wp_enqueue_style( 'freddo-googlefonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );

	wp_enqueue_script( 'freddo-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
	wp_enqueue_script( 'freddo-custom', get_template_directory_uri() . '/js/jquery.freddo.js', array('jquery'), '1.0', true );
	if ( is_active_sidebar( 'sidebar-push' ) ) {
		wp_enqueue_script( 'freddo-nanoScroll', get_template_directory_uri() . '/js/jquery.nanoscroller.min.js', array('jquery'), '1.0', true );
	}
	if ( freddo_options('_smooth_scroll', '1') == 1) {
		wp_enqueue_script( 'freddo-smooth-scroll', get_template_directory_uri() . '/js/SmoothScroll.min.js', array('jquery'), '1.0', true );
	}
	if (is_page_template('template-onepage.php') && freddo_options('_onepage_section_slider', '') == 1) {
		wp_enqueue_script( 'freddo-flex-slider', get_template_directory_uri() . '/js/jquery.flexslider-min.js', array(), '20151215', true );
	}
	if (is_page_template('template-onepage.php')) {
		wp_enqueue_script( 'freddo-waypoints', get_template_directory_uri() . '/js/jquery.waypoints.min.js', array('jquery'), '1.0', true );
	}
	wp_enqueue_script( 'freddo-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
	/* Dequeue default WooCommerce Layout */
	wp_dequeue_style ( 'woocommerce-layout' );
	wp_dequeue_style ( 'woocommerce-smallscreen' );
	wp_dequeue_style ( 'woocommerce-general' );
}
add_action( 'wp_enqueue_scripts', 'freddo_scripts' );

/**
 * WooCommerce Support
 */
if ( ! function_exists( 'freddo_woocommerce_support' ) ) :
	function freddo_woocommerce_support() {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-lightbox' );
	}
	add_action( 'after_setup_theme', 'freddo_woocommerce_support' );
endif; // freddo_woocommerce_support

/**
 * WooCommerce: Chenge default max number of related products
 */
if ( function_exists( 'is_woocommerce' ) ) :
	if ( ! function_exists( 'freddo_related_products_args' ) ) :
		add_filter( 'woocommerce_output_related_products_args', 'freddo_related_products_args' );
		function freddo_related_products_args( $args ) {
			$args['posts_per_page'] = 3;
			return $args;
		}
	endif;
	if ( ! function_exists( 'freddo_wc_get_product_cat_class' ) ) :
		function freddo_wc_get_product_cat_class($classes, $class, $category) {
			$classes[] = 'four-columns';
			return array_unique( array_filter( $classes ) );
		}
		add_filter( 'product_cat_class' , 'freddo_wc_get_product_cat_class', 10, 3 );
	endif;
endif;

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load PRO Button in the customizer
 */
require_once( trailingslashit( get_template_directory() ) . 'inc/pro-button/class-customize.php' );

/* Calling in the admin area for the Welcome Page */
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/freddo-admin-page.php';
}

/*
 * "Хлебные крошки" для WordPress
 * автор: Dimox
 * версия: 2017.01.21
 * лицензия: MIT
*/
function dimox_breadcrumbs() {

  /* === ОПЦИИ === */
  $text['home'] = 'Home'; // текст ссылки "Главная"
  $text['category'] = '%s'; // текст для страницы рубрики
  $text['search'] = 'Результаты поиска по запросу "%s"'; // текст для страницы с результатами поиска
  $text['tag'] = 'Записи с тегом "%s"'; // текст для страницы тега
  $text['author'] = 'Статьи автора %s'; // текст для страницы автора
  $text['404'] = 'Ошибка 404'; // текст для страницы 404
  $text['page'] = 'Страница %s'; // текст 'Страница N'
  $text['cpage'] = 'Страница комментариев %s'; // текст 'Страница комментариев N'

  $wrap_before = '<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">'; // открывающий тег обертки
  $wrap_after = '</div><!-- .breadcrumbs -->'; // закрывающий тег обертки
  $sep = '›'; // разделитель между "крошками"
  $sep_before = '<span class="sep">'; // тег перед разделителем
  $sep_after = '</span>'; // тег после разделителя
  $show_home_link = 1; // 1 - показывать ссылку "Главная", 0 - не показывать
  $show_on_home = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
  $show_current = 1; // 1 - показывать название текущей страницы, 0 - не показывать
  $before = '<span class="current">'; // тег перед текущей "крошкой"
  $after = '</span>'; // тег после текущей "крошки"
  /* === КОНЕЦ ОПЦИЙ === */

  global $post;
  $home_url = home_url('/');
  $link_before = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
  $link_after = '</span>';
  $link_attr = ' itemprop="item"';
  $link_in_before = '<span itemprop="name">';
  $link_in_after = '</span>';
  $link = $link_before . '<a href="%1$s"' . $link_attr . '>' . $link_in_before . '%2$s' . $link_in_after . '</a>' . $link_after;
  $frontpage_id = get_option('page_on_front');
  $parent_id = ($post) ? $post->post_parent : '';
  $sep = ' ' . $sep_before . $sep . $sep_after . ' ';
  $home_link = $link_before . '<a href="' . $home_url . '"' . $link_attr . ' class="home">' . $link_in_before . $text['home'] . $link_in_after . '</a>' . $link_after;

  if (is_home() || is_front_page()) {

    if ($show_on_home) echo $wrap_before . $home_link . $wrap_after;

  } else {

    echo $wrap_before;
    if ($show_home_link) echo $home_link;

    if ( is_category() ) {
      $cat = get_category(get_query_var('cat'), false);
      if ($cat->parent != 0) {
        $cats = get_category_parents($cat->parent, TRUE, $sep);
        $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
        if ($show_home_link) echo $sep;
        echo $cats;
      }
      if ( get_query_var('paged') ) {
        $cat = $cat->cat_ID;
        echo $sep . sprintf($link, get_category_link($cat), get_cat_name($cat)) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_current) echo $sep . $before . sprintf($text['category'], single_cat_title('', false)) . $after;
      }

    } elseif ( is_search() ) {
      if (have_posts()) {
        if ($show_home_link && $show_current) echo $sep;
        if ($show_current) echo $before . sprintf($text['search'], get_search_query()) . $after;
      } else {
        if ($show_home_link) echo $sep;
        echo $before . sprintf($text['search'], get_search_query()) . $after;
      }

    } elseif ( is_day() ) {
      if ($show_home_link) echo $sep;
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $sep;
      echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F'));
      if ($show_current) echo $sep . $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      if ($show_home_link) echo $sep;
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y'));
      if ($show_current) echo $sep . $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      if ($show_home_link && $show_current) echo $sep;
      if ($show_current) echo $before . get_the_time('Y') . $after;

    } elseif ( is_single() && !is_attachment() ) {
      if ($show_home_link) echo $sep;
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        printf($link, $home_url . $slug['slug'] . '/', $post_type->labels->singular_name);
        if ($show_current) echo $sep . $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, $sep);
        if (!$show_current || get_query_var('cpage')) $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
        echo $cats;
        if ( get_query_var('cpage') ) {
          echo $sep . sprintf($link, get_permalink(), get_the_title()) . $sep . $before . sprintf($text['cpage'], get_query_var('cpage')) . $after;
        } else {
          if ($show_current) echo $before . get_the_title() . $after;
        }
      }

    // custom post type
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      if ( get_query_var('paged') ) {
        echo $sep . sprintf($link, get_post_type_archive_link($post_type->name), $post_type->label) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_current) echo $sep . $before . $post_type->label . $after;
      }

    } elseif ( is_attachment() ) {
      if ($show_home_link) echo $sep;
      $parent = get_post($parent_id);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      if ($cat) {
        $cats = get_category_parents($cat, TRUE, $sep);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
        echo $cats;
      }
      printf($link, get_permalink($parent), $parent->post_title);
      if ($show_current) echo $sep . $before . get_the_title() . $after;

    } elseif ( is_page() && !$parent_id ) {
      if ($show_current) echo $sep . $before . get_the_title() . $after;

    } elseif ( is_page() && $parent_id ) {
      if ($show_home_link) echo $sep;
      if ($parent_id != $frontpage_id) {
        $breadcrumbs = array();
        while ($parent_id) {
          $page = get_page($parent_id);
          if ($parent_id != $frontpage_id) {
            $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
          }
          $parent_id = $page->post_parent;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        for ($i = 0; $i < count($breadcrumbs); $i++) {
          echo $breadcrumbs[$i];
          if ($i != count($breadcrumbs)-1) echo $sep;
        }
      }
      if ($show_current) echo $sep . $before . get_the_title() . $after;

    } elseif ( is_tag() ) {
      if ( get_query_var('paged') ) {
        $tag_id = get_queried_object_id();
        $tag = get_tag($tag_id);
        echo $sep . sprintf($link, get_tag_link($tag_id), $tag->name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_current) echo $sep . $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
      }

    } elseif ( is_author() ) {
      global $author;
      $author = get_userdata($author);
      if ( get_query_var('paged') ) {
        if ($show_home_link) echo $sep;
        echo sprintf($link, get_author_posts_url($author->ID), $author->display_name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_home_link && $show_current) echo $sep;
        if ($show_current) echo $before . sprintf($text['author'], $author->display_name) . $after;
      }

    } elseif ( is_404() ) {
      if ($show_home_link && $show_current) echo $sep;
      if ($show_current) echo $before . $text['404'] . $after;

    } elseif ( has_post_format() && !is_singular() ) {
      if ($show_home_link) echo $sep;
      echo get_post_format_string( get_post_format() );
    }

    echo $wrap_after;

  }
}

function exclude_product_cat_children ($wp_query) {
	if ( isset ( $wp_query->query_vars['product_cat'] ) && $wp_query->is_main_query() ) {
		$wp_query->set('tax_query', array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'slug',
				'terms' => $wp_query->query_vars['product_cat'],
				'include_children' => false

				)
			)
		);
	}
}
add_filter('pre_get_posts', 'exclude_product_cat_children');

function wpa89819_wc_single_product(){

    $product_cats = wp_get_post_terms( get_the_ID(), 'product_cat' );

    if ( $product_cats && ! is_wp_error ( $product_cats ) ){

        $single_cat = array_shift( $product_cats ); 

        ?>

        <h2 itemprop="name" class="product_category_title"><span><?php echo $single_cat->name; ?></span></h2>

<?php }
}
add_action( 'woocommerce_single_product_summary', 'wpa89819_wc_single_product', 2 );