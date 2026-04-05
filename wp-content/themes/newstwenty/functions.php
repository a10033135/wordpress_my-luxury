<?php
/**
 * Theme functions and definitions
 *
 * @package NewsTwenty
 */
// 取得純文字內容
// $length = 0 → 完整文字（Slider Banner 用，由 CSS max-height 控制可見高度）
// $length > 0 → 截至指定字數（文章列表用，後台可調整）
function newstwenty_full_text( $post_obj = null, $length = 0 ) {
	global $post;
	if ( is_null( $post_obj ) ) {
		$post_obj = $post;
	}
	$content = trim( $post_obj->post_content );
	if ( empty( $content ) ) {
		$content = $post_obj->post_excerpt;
	}
	$content = preg_replace( '/\s*(&nbsp;|\xA0)\s*/u', ' ', $content );
	$content = preg_replace( '`\[[^\]]*\]`', '', $content );
	$content = wp_strip_all_tags( $content );
	$content = trim( $content );
	if ( $length > 0 ) {
		$content = wp_trim_words( $content, $length, '&hellip;' );
	}
	return $content;
}

// 覆寫父主題的摘要函式
// - 優先使用 post_content（API 的 post_excerpt 可能不完整）
// - length >= 20（文章型）→ 使用後台 article_excerpt_words 設定
// - length < 20（側欄/trending 等小元件）→ 保留原本短截斷
if ( ! function_exists( 'newsup_the_excerpt' ) ) :
	function newsup_the_excerpt( $length = 0, $post_obj = null ) {
		global $post;
		if ( is_null( $post_obj ) ) {
			$post_obj = $post;
		}
		$length = absint( $length );
		if ( 0 === $length ) {
			return;
		}
		// 文章型用途：改用後台設定的字數（中文 locale 下為字元數）
		if ( $length >= 20 ) {
			$length = max( 1, absint( get_theme_mod( 'article_excerpt_words', 100 ) ) );
		}
		// 優先使用 post_content；僅在 content 為空時才退回 post_excerpt
		$source_content = trim( $post_obj->post_content );
		if ( empty( $source_content ) ) {
			$source_content = $post_obj->post_excerpt;
		}
		$source_content = preg_replace( '/\s*(&nbsp;|\xA0)\s*/u', ' ', $source_content );
		$source_content = preg_replace( '`\[[^\]]*\]`', '', $source_content );
		return wp_trim_words( $source_content, $length, '&hellip;' );
	}
endif;

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
		$img_width             = max( 1, absint( get_theme_mod( 'banner_image_max_width',   480 ) ) );
		$img_height            = max( 1, absint( get_theme_mod( 'banner_image_max_height',  480 ) ) );
		$banner_excerpt_height = max( 1, absint( get_theme_mod( 'banner_excerpt_height',     80 ) ) );
		$article_img_width     = max( 1, absint( get_theme_mod( 'article_image_max_width',  300 ) ) );
		$article_img_height    = max( 1, absint( get_theme_mod( 'article_image_max_height', 300 ) ) );

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

			/* Slide Banner 摘要高度 */
			.mg-fea-area .mg-posts-sec-post .mg-content {
				max-height: {$banner_excerpt_height}px !important;
				overflow: hidden !important;
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

