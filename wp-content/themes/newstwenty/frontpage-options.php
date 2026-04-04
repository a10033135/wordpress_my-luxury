<?php

/**
 * Option Panel
 *
 * @package NewsTwenty
 */


function newstwenty_customize_register($wp_customize) {

    $newsup_default = newstwenty_get_default_theme_options();

    $wp_customize->remove_control('newsup_select_slider_setting');
    $wp_customize->remove_control('popular_tab_title');
    $wp_customize->remove_control('newsup_center_logo_title');
    $wp_customize->remove_control('tabbed_section_title');
    $wp_customize->remove_control('latest_tab_title');
    $wp_customize->remove_control('trending_tab_title');
    // $wp_customize->remove_control('select_trending_tab_news_category');

    $wp_customize->remove_section('newsup_popular_tags_section_settings');
    $wp_customize->remove_section('frontpage_advertisement_settings');

    $wp_customize->get_setting('newsup_title_font_size')->default  = '46'; 
    $wp_customize->get_setting('newsup_header_overlay_color')->default  = '#f9f9f9';
	$wp_customize->get_setting('header_textcolor')->default = '171717';

    //section title
    $wp_customize->add_setting('trending_post_section_title',
        array(
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        new newsup_Section_Title(
            $wp_customize,
            'trending_post_section_title',
            array(
                'label'             => esc_html__( 'Trending Post Section', 'newstwenty' ),
                'section'           => 'frontpage_main_banner_section_settings',
                'priority'          => 80,
                'active_callback' => 'newsup_main_banner_section_status'
            )
        )
    );

    // Slide Banner 摘要行數
    $wp_customize->add_setting('banner_excerpt_lines', array(
        'default'           => 3,
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('banner_excerpt_lines', array(
        'type'            => 'number',
        'label'           => esc_html__('Slide Banner - 內容摘要顯示行數', 'newstwenty'),
        'section'         => 'frontpage_main_banner_section_settings',
        'settings'        => 'banner_excerpt_lines',
        'priority'        => 78,
        'input_attrs'     => array('min' => 1, 'max' => 10, 'step' => 1),
        'active_callback' => 'newsup_main_banner_section_status',
    ));

    // 首頁文章區塊 摘要行數
    $wp_customize->add_setting('article_excerpt_lines', array(
        'default'           => 3,
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('article_excerpt_lines', array(
        'type'        => 'number',
        'label'       => esc_html__('首頁文章區塊 - 內容摘要顯示行數', 'newstwenty'),
        'section'     => 'post_image_options',
        'settings'    => 'article_excerpt_lines',
        'input_attrs' => array('min' => 1, 'max' => 10, 'step' => 1),
    ));

    // Banner image display type (separate from article blocks)
    $wp_customize->add_setting('banner_image_type', array(
        'default'           => 'newsup_post_img_hei',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'newsup_sanitize_select',
    ));
    $wp_customize->add_control('banner_image_type', array(
        'type'    => 'radio',
        'label'   => esc_html__('Slide Banner - Post Image display type:', 'newstwenty'),
        'choices' => array(
            'newsup_post_img_hei' => esc_html__('Fix Height Post Image', 'newstwenty'),
            'newsup_post_img_acc' => esc_html__('Auto Height Post Image', 'newstwenty'),
        ),
        'section'  => 'frontpage_main_banner_section_settings',
        'settings' => 'banner_image_type',
        'priority' => 79,
        'active_callback' => 'newsup_main_banner_section_status',
    ));

    // Dynamic toggle for Trending Post Section
    $wp_customize->add_setting('newstwenty_show_trending_post_section',
        array(
            'default'           => false,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'newsup_sanitize_checkbox',
        )
    );
    $wp_customize->add_control('newstwenty_show_trending_post_section',
        array(
            'label'           => esc_html__('Enable Trending Post Section', 'newstwenty'),
            'section'         => 'frontpage_main_banner_section_settings',
            'type'            => 'checkbox',
            'priority'        => 81,
            'active_callback' => 'newsup_main_banner_section_status'
        )
    );
}
add_action('customize_register', 'newstwenty_customize_register');
