<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package NewsTwenty
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> >
<?php wp_body_open(); ?>
<div id="page" class="site">
<a class="skip-link screen-reader-text" href="#content">
<?php _e( 'Skip to content', 'newstwenty' ); ?></a>
<div class="wrapper">
  <header class="mg-headwidget">
    <!--==================== TOP BAR ====================-->
    <?php do_action('newstwenty_action_header_section');  ?>
    <div class="clearfix"></div>

    <?php $background_image = get_theme_support( 'custom-header', 'default-image' );
    $newsup_center_logo_title = get_theme_mod('newsup_center_logo_title', false);
    if ( has_header_image() ) { $background_image = get_header_image(); } ?>

    <div class="mg-nav-widget-area-back" style='background-image: url("<?php echo esc_url( $background_image ); ?>" );'>
      <?php $remove_header_image_overlay = get_theme_mod('remove_header_image_overlay',false); ?>
      <div class="overlay">
        <div class="inner" <?php if($remove_header_image_overlay == false) { 
            $newsup_header_overlay_color = get_theme_mod('newsup_header_overlay_color','#f9f9f9');?> style="background-color:<?php echo esc_attr($newsup_header_overlay_color);?>;" <?php } ?>> 
            <div class="container-fluid">
                <div class="mg-nav-widget-area">
                  <div class="row align-items-center">
                    <div class="<?php echo esc_attr($newsup_center_logo_title == false ? 'col-md-3 text-center-xs' : 'col-md-12 text-center mx-auto') ?>">
                      <div class="navbar-header">
                        <div class="site-logo">
                          <?php if(get_theme_mod('custom_logo') !== ""){ the_custom_logo(); } ?>
                        </div>
                        <div class="site-branding-text <?php echo esc_attr(!display_header_text() ? 'd-none' : ''); ?>">
                          <?php  if (is_front_page() || is_home()) { ?>
                              <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html(get_bloginfo( 'name' )); ?></a></h1>
                          <?php } else { ?>
                              <p class="site-title"> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html(get_bloginfo( 'name' )); ?></a></p>
                          <?php } ?>
                              <p class="site-description"><?php echo esc_html(get_bloginfo( 'description' )); ?></p>
                        </div>    
                      </div>
                    </div>
                    <div class="col-md-9">
                    <?php do_action('newstwenty_action_header_menus_section'); ?>
                    </div>
                  </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </header>
  <div class="clearfix"></div>
  <?php
  do_action('newstwenty_action_front_page_main_section_1'); ?>