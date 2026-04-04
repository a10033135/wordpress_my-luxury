<?php

if (!function_exists('newstwenty_banner_trending_posts')):
    /**
     *
     * @since NewsTwenty 1.0.0
     *
     */
    function newstwenty_banner_trending_posts() { ?>
        <div class="col-md-3">
            <div class="trending-posts small-list-post">
            <?php
            if (is_front_page() || is_home()) {
                $number_of_posts = '1';
                $newsup_slider_category = newsup_get_option('select_trending_news_category');
                $newsup_all_posts_main = newsup_get_posts($number_of_posts, $newsup_slider_category);
                if ($newsup_all_posts_main->have_posts()) :
                    while ($newsup_all_posts_main->have_posts()) : $newsup_all_posts_main->the_post();
                    global $post;
                    $url = newsup_get_freatured_image_url($post->ID, 'newsup-slider-full');
                    ?>                 
                     <div class="mg-blog-post-box"> 
                    <?php newsup_post_image_display_type($post); ?>
                    <article class="small">
                        <?php newsup_post_categories(); ?> 
                        <h4 class="entry-title title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>  
                        <?php newsup_post_meta(); ?>
                        <?php $newsup_excerpt = newsup_the_excerpt( absint( 15 ) );
                            if ( !empty( $newsup_excerpt ) ) { echo wp_kses_post( wpautop( $newsup_excerpt ) ); } ?>
                    </article>
                </div>
                    <?php
                    endwhile;
                endif;
                wp_reset_postdata();
            }
            ?>
            </div>
        </div>
        <?php 
    }
endif;

add_action('newstwenty_action_banner_trending_posts', 'newstwenty_banner_trending_posts', 10);

// Banner 專用圖片函式，讀取 banner_image_type 設定（與文章區塊的 post_image_type 分開）
if (!function_exists('newstwenty_banner_image_display_type')) :
    function newstwenty_banner_image_display_type($post) {
        $banner_image_type = get_theme_mod('banner_image_type', 'newsup_post_img_hei');
        $url = newsup_get_freatured_image_url($post->ID, 'newsup-medium');
        if ($banner_image_type == 'newsup_post_img_hei') {
            if ($url) { ?>
            <div class="col-12 col-md-6">
                <div class="mg-post-thumb back-img md" style="background-image: url('<?php echo esc_url($url); ?>');">
                    <?php echo newsup_post_format_type($post); ?>
                    <a class="link-div" href="<?php the_permalink(); ?>"></a>
                </div>
            </div>
            <?php }
        } elseif ($banner_image_type == 'newsup_post_img_acc') {
            if (has_post_thumbnail()) { ?>
            <div class="col-12 col-md-6">
                <div class="mg-post-thumb img">
                    <?php echo '<a href="' . esc_url(get_the_permalink()) . '">';
                    the_post_thumbnail('', array('class' => 'img-responsive'));
                    echo '</a>'; ?>
                    <?php echo newsup_post_format_type($post); ?>
                </div>
            </div>
            <?php }
        }
    }
endif;

//Front Page Banner
if (!function_exists('newstwenty_front_page_banner_section')) :
    /**
     *
     * @since Newsup
     *
     */
    function newstwenty_front_page_banner_section() {
        if (is_front_page() || is_home()) {
           
            $newsup_enable_main_slider = newsup_get_option('show_main_news_section');
            $select_vertical_slider_news_category = newsup_get_option('select_vertical_slider_news_category');
            $vertical_slider_number_of_slides = newsup_get_option('vertical_slider_number_of_slides');
            $all_posts_vertical = newsup_get_posts($vertical_slider_number_of_slides, $select_vertical_slider_news_category);
            if ($newsup_enable_main_slider):  

                $main_banner_section_background_image = newsup_get_option('main_banner_section_background_image');
                $main_banner_section_background_image_url = wp_get_attachment_image_src($main_banner_section_background_image, 'full');
                if(!empty($main_banner_section_background_image)){ ?>
                    <section class="mg-fea-area over" style="background-image:url('<?php echo esc_url($main_banner_section_background_image_url[0]); ?>');">
                <?php }else{ ?>
                    <section class="mg-fea-area">
                <?php  } ?>
                    <div class="overlay">
                        <div class="container-fluid">
                            <div class="row">
                                <?php
                                $show_trending = get_theme_mod('newstwenty_show_trending_post_section', false);
                                $col_class = $show_trending ? 'col-md-9' : 'col-md-12';
                                ?>
                                <div class="<?php echo esc_attr($col_class); ?>">
                                    <div id="homemain"class="homemain owl-carousel">                                         
                                    <?php
                                        $newsup_slider_category = newsup_get_option('select_slider_news_category');
                                        $newsup_number_of_slides = newsup_get_option('number_of_slides');
                                        $newsup_all_posts_main = newsup_get_posts($newsup_number_of_slides, $newsup_slider_category);
                                        $newsup_count = 1;

                                        if ($newsup_all_posts_main->have_posts()) :
                                            while ($newsup_all_posts_main->have_posts()) : $newsup_all_posts_main->the_post();
                                            global $post;
                                            $newsup_url = newsup_get_freatured_image_url($post->ID, 'newsup-slider-full');  ?>
                                            <div class="item">
                                                <article class='d-md-flex mg-posts-sec-post align-items-center'>
                                                    <div class="mg-sec-top-post py-3 col">
                                                        <?php newsup_post_categories(); ?> 
                                                        <h4 class="entry-title title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                                                        <?php newsup_post_meta(); ?>
                                                        <div class="mg-content">
                                                            <?php $newsup_excerpt = newsup_the_excerpt( absint( 60 ) );
                                                            if ( !empty( $newsup_excerpt ) ) {  echo wp_kses_post( wpautop( $newsup_excerpt ) ); } ?>
                                                        </div>
                                                    </div>
                                                    <?php if(!empty($newsup_url)){ newstwenty_banner_image_display_type($post); }
                                                    else { ?> <div class="col-12 col-md-6"><div class="mg-post-thumb back-img md"></div></div> <?php } ?>
                                                </article>
                                            </div>
                                            <?php
                                            endwhile;
                                        endif;
                                        wp_reset_postdata();
                                        ?>
                                    </div>
                                </div>
                                <?php if ($show_trending) { do_action('newstwenty_action_banner_trending_posts'); } ?>
                            </div>
                        </div>
                    </div>
                </section>
                <!--==/ Home Slider ==-->
            <?php endif; ?>
            <!-- end slider-section -->
        <?php }
    }
endif;
add_action('newstwenty_action_front_page_main_section_1', 'newstwenty_front_page_banner_section', 40);