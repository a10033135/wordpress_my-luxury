<?php
/**
 * Theme functions and definitions
 *
 * @package NewsTwenty
 */
if ( ! function_exists( 'newstwenty_enqueue_styles' ) ) :
	/**
	 * @since 0.1
	 */
	function newstwenty_enqueue_styles() {
		wp_enqueue_style( 'newsup-style-parent', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'newstwenty-style', get_stylesheet_directory_uri() . '/style.css', array( 'newsup-style-parent' ), '1.0' );
		wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css');
		wp_enqueue_style( 'newstwenty-default-css', get_stylesheet_directory_uri()."/css/colors/default.css" );
		if(is_rtl()){
		wp_enqueue_style( 'newsup_style_rtl', trailingslashit( get_template_directory_uri() ) . 'style-rtl.css' );
	    }

		// 動態 CSS：緊接在 newstwenty-style 之後輸出，確保覆寫靜態值
		$img_width          = max( 1, absint( get_theme_mod( 'banner_image_max_width',   480 ) ) );
		$img_height         = max( 1, absint( get_theme_mod( 'banner_image_max_height',  480 ) ) );
		$banner_lines       = max( 1, absint( get_theme_mod( 'banner_excerpt_lines',       3 ) ) );
		$article_img_width  = max( 1, absint( get_theme_mod( 'article_image_max_width',  300 ) ) );
		$article_img_height = max( 1, absint( get_theme_mod( 'article_image_max_height', 300 ) ) );
		$article_lines      = max( 1, absint( get_theme_mod( 'article_excerpt_lines',      3 ) ) );

		$dynamic_css = "
			/* Slide Banner 圖片尺寸 */
			@media(min-width: 768px) {
				.mg-fea-area .mg-posts-sec-post > .col-12.col-md-6:has(.mg-post-thumb.back-img) {
					flex-grow: 0 !important;
					flex-shrink: 0 !important;
					flex-basis: {$img_width}px !important;
					max-width: {$img_width}px !important;
				}
			}
			.mg-fea-area .mg-posts-sec-post .mg-post-thumb.back-img {
				height: {$img_height}px !important;
				max-height: {$img_height}px !important;
			}

			/* 首頁文章區塊 圖片尺寸 */
			@media(min-width: 768px) {
				.mg-posts-modul-6 .mg-posts-sec-post > .col-12.col-md-6:has(.mg-post-thumb.back-img) {
					flex-grow: 0 !important;
					flex-shrink: 0 !important;
					flex-basis: {$article_img_width}px !important;
					max-width: {$article_img_width}px !important;
				}
			}
			.mg-posts-modul-6 .mg-posts-sec-post .mg-post-thumb.back-img {
				height: {$article_img_height}px !important;
				max-height: {$article_img_height}px !important;
				padding-top: 0 !important;
			}

			/* 摘要行數 */
			.mg-fea-area .mg-posts-sec-post .mg-content p {
				-webkit-line-clamp: {$banner_lines} !important;
				line-clamp: {$banner_lines} !important;
			}
			.mg-posts-modul-6 .mg-sec-top-post .mg-content p {
				-webkit-line-clamp: {$article_lines} !important;
				line-clamp: {$article_lines} !important;
			}
		";
		wp_add_inline_style( 'newstwenty-style', $dynamic_css );
	}

endif;
add_action( 'wp_enqueue_scripts', 'newstwenty_enqueue_styles', 9999 );

function newstwenty_theme_setup() {

	//Load text domain for translation-ready
	load_theme_textdomain('newstwenty', get_stylesheet_directory() . '/languages');

	require( get_stylesheet_directory() . '/hooks/hooks.php' );
	require( get_stylesheet_directory() . '/hooks/header-hooks.php' );
	require( get_stylesheet_directory() . '/customizer-default.php' );
	require( get_stylesheet_directory() . '/frontpage-options.php' );
	require( get_stylesheet_directory() . '/font.php' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );

	$args = array(
		'default-color' => '#f9f9f9',
		'default-image' => '',
	);
	add_theme_support( 'custom-background', $args );

	// custom header Support
} 
add_action( 'after_setup_theme', 'newstwenty_theme_setup' );


function newstwenty_widgets_init() {
	
	$newsup_footer_column_layout = esc_attr(get_theme_mod('newsup_footer_column_layout',3));
	
	$newsup_footer_column_layout = 12 / $newsup_footer_column_layout;
	
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Widget Area', 'newstwenty' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="mg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="mg-wid-title"><h6 class="wtitle">',
		'after_title'   => '</h6></div>',
	) );


	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget Area', 'newstwenty' ),
		'id'            => 'footer_widget_area',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="col-md-'.$newsup_footer_column_layout.' rotateInDownLeft animated mg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h6>',
		'after_title'   => '</h6>',
	) );

}
add_action( 'widgets_init', 'newstwenty_widgets_init' );

add_filter( 'theme_mod_header_textcolor', function() {
    return '171717';
});

