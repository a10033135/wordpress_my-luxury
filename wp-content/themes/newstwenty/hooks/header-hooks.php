<?php

if (!function_exists('newstwenty_banner_exclusive_posts')):
    /**
     *
     * @since newsup 1.0.0
     *
     */
    function newstwenty_banner_exclusive_posts() { ?>
        <section class="mg-latest-news-sec"> 
            <?php
            $show_flash_news_section = newsup_get_option('show_flash_news_section');
            if ($show_flash_news_section) {
                $category = newsup_get_option('select_flash_news_category');
                $number_of_posts = newsup_get_option('number_of_flash_news');
                $newsup_ticker_news_title = newsup_get_option('flash_news_title');

                $all_posts = newsup_get_posts($number_of_posts, $category);
                $show_trending = true;
                $count = 1;
                ?>
                <div class="container-fluid">
                    <div class="mg-latest-news">
                        <div class="bn_title">
                            <h2 class="title">
                                <?php if (!empty($newsup_ticker_news_title)): ?>
                                    <?php echo esc_html($newsup_ticker_news_title); ?><span></span>
                                <?php endif; ?>
                            </h2>
                        </div>
                        <?php if(is_rtl()){ ?> 
                        <div class="mg-latest-news-slider marquee" data-direction='right' dir="ltr">
                        <?php } else { ?> 
                        <div class="mg-latest-news-slider marquee">
                        <?php }
                            if ($all_posts->have_posts()) :
                                while ($all_posts->have_posts()) : $all_posts->the_post(); ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <span><?php the_title(); ?></span>
                                        </a>
                                    <?php
                                    $count++;
                                endwhile;
                            endif;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Excluive line END -->
            <?php }
        echo '</section>';
    }
endif;
add_action('newstwenty_action_banner_exclusive_posts', 'newstwenty_banner_exclusive_posts', 10);

if (!function_exists('newstwenty_header_section')) :
/**
 *  Header Top Bar
 *
 * @since NewsTwenty
 *
 */
    function newstwenty_header_section(){
        
        $header_social_icon_enable = esc_attr(get_theme_mod('header_social_icon_enable','true'));
        $header_data_enable = esc_attr(get_theme_mod('header_data_enable','true'));
        $header_time_enable = esc_attr(get_theme_mod('header_time_enable','true'));
        if(($header_data_enable == true) || ($header_time_enable == true) || ($header_social_icon_enable == true)) {
        ?>
            <div class="mg-head-detail hidden-xs">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-9 col-xs-12">
                            <?php do_action('newstwenty_action_banner_exclusive_posts'); ?>
                        </div>
                        <div class="col-md-3 col-xs-12">
                            <ul class="mg-social info-right">
                                <?php newsup_date_display_type(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
    }
endif;
add_action('newstwenty_action_header_section', 'newstwenty_header_section', 5);

if (!function_exists('newstwenty_header_menus_section')) :
/**
 *  Header Menus
 *
 * @since NewsTwenty
 *
 */
    function newstwenty_header_menus_section(){ ?>
        <nav class="navbar navbar-expand-lg navbar-wp">
            <!-- Right nav -->
            <div class="m-header align-items-center">
                <?php $home_url = home_url(); ?>
                <a class="mobilehomebtn" href="<?php echo esc_url($home_url); ?>"><span class="fa-solid fa-house-chimney"></span></a>
                <!-- navbar-toggle -->
                <button class="navbar-toggler mr-auto" type="button" data-toggle="collapse" data-target="#navbar-wp" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation','newsup'); ?>">
                    <span class="burger">
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    </span>
                </button>
                <!-- /navbar-toggle -->
                <?php do_action('newsup_action_header_search'); do_action('newsup_action_header_subscribe'); ?>
            </div>
            <!-- /Right nav --> 
            <div class="collapse navbar-collapse justify-content-lg-start" id="navbar-wp">
                <div class="d-md-block">
                <?php  
                    wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'  => 'nav-collapse collapse',
                    'menu_class' => 'nav navbar-nav mr-auto '.(is_rtl() ? 'sm-rtl' : ''),
                    'fallback_cb' => 'newsup_fallback_page_menu',
                    'walker' => new newsup_nav_walker()
                    ) ); 
                ?>
                </div>
            </div>
            <!-- Right nav -->
            <div class="desk-header d-lg-flex pl-3 ml-auto my-2 my-lg-0 position-relative align-items-center">
                <?php do_action('newsup_action_header_search'); do_action('newsup_action_header_subscribe'); ?>
            </div>
            <!-- /Right nav -->
        </nav>
      <?php        
    }
endif;
add_action('newstwenty_action_header_menus_section', 'newstwenty_header_menus_section', 5);