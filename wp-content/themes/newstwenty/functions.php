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
