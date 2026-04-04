<?php 
// Admin Page
class NewsUp_Admin {

    public function __construct() {
        // Add admin page
        add_action('admin_menu', [$this, 'newsup_admin_page']);
        // Remove all third party notices and enqueue style and script
        add_action('admin_enqueue_scripts', [$this, 'admin_script_n_style'], 9999);

        // AJAX install + activate plugin
        add_action('wp_ajax_newsup_install_plugin', [$this, 'newsup_install_plugin_callback']);
        add_action('wp_ajax_newsup_activate_plugin', [$this, 'newsup_activate_plugin_callback']);
    }

    public function newsup_admin_page() {
        // $site_favi_icon = '';
        $site_favi_icon = NEWSUP_THEME_URI .'admin/images/siteicon.png';

        $customMenu = add_menu_page(
            esc_html( NEWSUP_THEME_NAME ),
            esc_html( NEWSUP_THEME_NAME ),
            'manage_options',
            'newsup_admin_menu',
            [ $this, 'newsup_admin_page_content' ],
            $site_favi_icon,
            30
        );

        add_submenu_page(
            'newsup_admin_menu',
            __('Customize', 'newsup'),
            __('Customize', 'newsup'),
            'manage_options',
            'customize.php'
        );
        add_submenu_page(
            'newsup_admin_menu',
            __('Footer Builder', 'newsup'),
            '<span id="newsup-upgrade-menu-item">' . __('Upgrade Now &nbsp;➤', 'newsup') . '</span>',
            'manage_options',
            esc_url('https://themeansar.com/themes/newsup-pro/')
        );
    }

    public function admin_script_n_style() {
      $screen = get_current_screen();
      if (isset( $screen->base ) && $screen->base == 'toplevel_page_newsup_admin_menu') {

        remove_all_actions('admin_notices');

        wp_enqueue_script('newsup-admin', NEWSUP_THEME_URI . 'js/admin.js', ['jquery'], NEWSUP_THEME_VERSION, true);

        wp_localize_script('newsup-admin', 'newsup_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('newsup_plugin_nonce'),
        ]);

        wp_enqueue_style('newsup-admin-styles', NEWSUP_THEME_URI . 'css/admin.css', array(), NEWSUP_THEME_VERSION);

        // Add Gooogle Font
        wp_enqueue_style( 
            'admin-google-fonts', 
            'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap',
            [], 
            NEWSUP_THEME_VERSION
        );

        add_filter('admin_footer_text', [$this, 'newsup_admin_footer_text']);
      }
    }

    function newsup_admin_footer_text() {
        return sprintf(
            wp_kses_post(
                __( 'Enjoyed <span class="newsup-footer-thankyou"><strong>%s</strong>? Please leave us a <a href="https://wordpress.org/support/theme/newsup/reviews/" target="_blank">★★★★★</a></span> rating.', 'newsup' )
            ),
            esc_html( NEWSUP_THEME_NAME )
        );
    }

    public function newsup_admin_page_content() {
        $change_log = NEWSUP_THEME_DIR . 'change-log.txt';
        if ( ! file_exists( $change_log ) ) {
            $change_log = esc_html__( 'Change log file not found.', 'newsup' );
        } elseif ( ! is_readable( $change_log ) ) {
            $change_log = esc_html__( 'Change log file not readable.', 'newsup' );
        } else {
            global $wp_filesystem;

            // Check if the the global filesystem isn't setup yet.
            if ( is_null( $wp_filesystem ) ) {
                WP_Filesystem();
            }

            $change_log = $wp_filesystem->get_contents( $change_log );
        }
        ?>

        <div class="newsup-page-content">
            <div class="newsup-tabbed">
                <?php
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'welcome';
                ?>
                <input type="radio" id="tab1" name="css-tabs" <?php checked( $active_tab, 'welcome' ); ?> >
                <input type="radio" id="tab2" name="css-tabs" <?php checked( $active_tab, 'starter-sites' ); ?> >
                <input type="radio" id="tab3" name="css-tabs" <?php checked( $active_tab, 'useful-plugin' ); ?> >
                <input type="radio" id="tab4" name="css-tabs" <?php checked( $active_tab, 'compare' ); ?> >
                <input type="radio" id="tab5" name="css-tabs" <?php checked( $active_tab, 'change-log' ); ?> >
                <div class="newsup-head-top-items">
                    <div class="newsup-head-item">
                        <a href="<?php echo esc_url( add_query_arg( [ 'tab'   => 'welcome'] ) ); ?>" class="newsup-site-icon"><img src=<?php echo esc_url(NEWSUP_THEME_URI) .'admin/images/mainlogo-1.png'; ?>  alt="mainlogo"></a>
                    </div>
                    <ul class="newsup-tabs">
                        <li class="newsup-tab">
                            <label for="tab1">
                            <a href="<?php echo esc_url( add_query_arg( [ 'tab'   => 'welcome'] ) ); ?>">
                                <?php esc_attr_e('Dashboard','newsup'); ?>
                            </a>
                            </label>
                        </li>
                        <?php if ( is_plugin_active( 'ansar-import/ansar-import.php' ) ) : ?>
                            <li class="newsup-tab">
                                <label for="tab2">
                                <a href="<?php echo esc_url(admin_url( 'admin.php?page=ansar-demo-import' )); ?>">
                                    <?php esc_attr_e('Starter Sites','newsup'); ?>
                                </a>
                                </label>
                            </li>
                        <?php else : ?>
                            <li class="newsup-tab">
                                <label for="tab2">
                                <a href="<?php echo esc_url( add_query_arg( [ 'tab'   => 'starter-sites'] )); ?>">
                                    <?php esc_attr_e('Starter Sites','newsup'); ?>
                                </a>
                                </label>
                            </li>
                        <?php endif; ?>
                        <li class="newsup-tab">
                            <label for="tab3">
                            <a href="<?php echo esc_url( add_query_arg( [ 'tab'   => 'useful-plugin'] ) ); ?>">
                                <?php esc_attr_e('Useful Plugin','newsup'); ?>
                            </a>
                            </label>
                        </li>
                        <li class="newsup-tab">
                            <label for="tab4">
                            <a href="<?php echo esc_url( add_query_arg( [ 'tab'   => 'compare'] ) ); ?>">
                                <?php esc_attr_e('Free Vs Pro','newsup'); ?>
                            </a>
                            </label>
                        </li>
                        <li class="newsup-tab">
                            <label for="tab5">
                            <a href="<?php echo esc_url( add_query_arg( [ 'tab'   => 'change-log'] ) ); ?>">
                                <?php esc_attr_e('Change Log','newsup'); ?>
                            </a>
                            </label>
                        </li>
                    </ul>
                    <div class="newsup-right-top-area">
                        <div class="newsup-ask-help">            
                            <div class="newsup-ask-icon">
                                <svg class="svg-inline--fa fa-book" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="book" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16zm16 48H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z"></path></svg>
                            </div>                            
                            <a href="https://docs.themeansar.com/docs/newsup-lite/" target="_blank" class="newsup-btn-link">Docs </a>
                        </div>
                        <div class="newsup-feature-pro">
                            <span><?php echo esc_html(NEWSUP_THEME_VERSION); ?> Current Version</span>
                        </div>
                    </div>
                </div>
                <div class="newsup-main-area">
                    <div class="newsup-tab-contents">
                        <div class="newsup-tab-content newsup-welcome">
                            <div class="newsup-getstart newsup-d-grid column6 gap-30">
                                <!--  -->
                                <!-- <div class="newsup-getstart-inner newsup-col-span-4">
                                    
                                </div>  -->
                                <div class="newsup-getstart-inner newsup-col-span-4">
                                    <div class="newsup-wrapper first">
                                        <div class="newsup-getstart-content">
                                                <h1 class="newsup-content-title"><?php esc_html_e('Welcome, ','newsup');   $current_user = wp_get_current_user();
                                                echo esc_html( $current_user->display_name );?></h1>
                                                <p class="newsup-content-description">
                                                    <?php printf(
                                                            esc_html__(
                                                                'Thank you for installing %s — your complete solution for creating modern, dynamic, engaging News, and Magazine websites.',
                                                                'newsup'
                                                            ),
                                                            esc_html( NEWSUP_THEME_NAME )
                                                        );
                                                    ?>
                                                </p>
                                            <?php if ( is_plugin_active( 'ansar-import/ansar-import.php' ) ) : ?>
                                                <a href="<?php echo esc_url(admin_url( 'admin.php?page=ansar-demo-import' )); ?>" class="newsup-content-btn newsup-str-sites"><?php esc_html_e('Start with Demo Sites','newsup'); ?></a>
                                            <?php else : ?>
                                                <a href="#" class="newsup-content-btn newsup-str-sites load"><?php esc_html_e('Start with Demo Sites','newsup'); ?></a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="newsup-getstart-image">
                                            <iframe src="https://www.youtube.com/embed/255CSHjfFJU?si=a2zBoFRrIbP44EB9" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                    <div class="newsup-wrapper second">
                                        <div class="newsup-feature-area newsup-d-grid column3 align-start">
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Theme Option</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=theme_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Header Option</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=theme_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Footer Option</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=theme_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Site Identity</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[section]=title_tagline') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Banner Advertisement</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=frontpage_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Top Tags</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=frontpage_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">News Ticker</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=frontpage_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Slider Section</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=frontpage_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Content Layout Settings</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=theme_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Single Post Settings</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=theme_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">You Missed Section</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=theme_option_panel') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <h3 class="newsup-feature-area-title">Widgets Settings</h3>
                                                <p class="newsup-feature-area-desc"><a href="<?php echo esc_url('customize.php?autofocus[panel]=widgets') ?>" target="_blank">Go to Customizer</a></p>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Animation Effects</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Post Content Settings</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Post Pagination Settings</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Breadcrumb Settings</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Theme Style Setting</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Color Settings</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Popup Advertisement</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Typography Settings</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Advanced Widgets Settings</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Slider Layouts</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Header Layouts</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Preloader</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Random Post</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Live Search / Ajax Search</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Header Toggle Offcanvas</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Maintenance Mode</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Schema Markup</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">SEO Setting</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Cursor Dot</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Post Like Setting</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Load more Posts</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Infinity Scroll</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Single Post Layouts</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Social Icon Repeater</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Light Dark Mode</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                            <div class="newsup-feature-box">
                                                <span class="ribbon pro">Pro</span>
                                                <h3 class="newsup-feature-area-title">Gradient Color</h3>
                                                <?php echo $this->newsup_upgrade_callback(); ?>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="newsup-wrapper four details-more">
                                        <div class="newsup-d-grid column2 gap-30">
                                            <?php echo $this->plugin_box(
                                                'ansar-import',
                                                'ansar-import/ansar-import.php',
                                                'Ansar Import – One Click Demo Import for WordPress Themes',
                                                'Ansar Import is a simple yet powerful one-click demo importer plugin for WordPress.',
                                                NEWSUP_THEME_URI . 'admin/images/ansar-icon.png',
                                                'https://wordpress.org/plugins/ansar-import/'
                                            ); ?>

                                            <?php echo $this->plugin_box(
                                                'blognews-for-elementor',
                                                'blognews-for-elementor/blognews-for-elementor.php',
                                                'Blog News Addons For Elementor (News, Magazine and Blog Addons)',
                                                'Blog News for Elementor is a complete solution for bloggers, news portals, and magazine-style websites using Elementor.',
                                                NEWSUP_THEME_URI . 'admin/images/bn-icon.png',
                                                'https://wordpress.org/plugins/blognews-for-elementor/'
                                            ); ?>
                                        </div>
                                    </div>
                                </div> 
                                <?php echo $this->newsup_admin_right_sidebar() ?>                          
                            </div>
                        </div>
                        <div class="newsup-tab-content starter-sites">
                                <div class="newsup-modal-main">
                                    <div class="newsup-modal-image overlay">
                                        <img src="<?php echo esc_url(NEWSUP_THEME_URI) . 'admin/images/demos.jpg' ?>" alt="">
                                    </div>
                                    <div class="newsup-modal-popup">
                                        <div class="newsup-modal-popup-content">
                                            <div class="newsup-modal-icon">
                                                <img src="<?php echo esc_url(NEWSUP_THEME_URI) . 'admin/images/ansar-import-logo.png' ?>" alt="">
                                            </div>
                                            <div>
                                                <h4><?php esc_html_e("Ansar Import","newsup"); ?></h4>
                                                <p><?php esc_html_e("Click View Demo Button to install a ready-made News & Magazine Demos — fast, simple, and customizable.","newsup"); ?></p>
                                                <a href="#" class="newsup-btn-ins newsup-str-sites load" >
                                                    <?php 
                                                        esc_html_e( 'View Demos', 'newsup' );
                                                    ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="newsup-tab-content newsup-useful-plugin">
                            <div class="newsup-plugins-tab newsup-d-grid column6 gap-30">

                                <div class="newsup-getstart-inner newsup-col-span-4">
                                    <div class="newsup-wrapper four details-more">
                                        <div class="newsup-d-grid column2 gap-30">
                                            <?php echo $this->plugin_box(
                                                'ansar-import',
                                                'ansar-import/ansar-import.php',
                                                'Ansar Import – One Click Demo Import for WordPress Themes',
                                                'Ansar Import is a simple yet powerful one-click demo importer plugin for WordPress.',
                                                NEWSUP_THEME_URI . 'admin/images/ansar-icon.png',
                                                'https://wordpress.org/plugins/ansar-import/'
                                            ); ?>

                                            <?php echo $this->plugin_box(
                                                'blognews-for-elementor',
                                                'blognews-for-elementor/blognews-for-elementor.php',
                                                'Blog News Addons For Elementor (News, Magazine and Blog Addons)',
                                                'Blog News for Elementor is a complete solution for bloggers, news portals, and magazine-style websites using Elementor.',
                                                NEWSUP_THEME_URI . 'admin/images/bn-icon.png',
                                                'https://wordpress.org/plugins/blognews-for-elementor/'
                                            ); ?>
                                        </div>
                                    </div>
                                </div> 
                                <?php echo $this->newsup_admin_right_sidebar() ?>                                
                            </div>
                        </div>
                        <div class="newsup-tab-content newsup-compare">
                            <div class="newsup-plugins-compare newsup-d-grid column6 gap-30">
                                <div class="newsup-getstart-inner newsup-col-span-4">
                                    <div class="newsup-table-main">
                                        <div class="newsup-admin-table">
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle pri">
                                                <div class="header">
                                                    <h4><?php esc_html_e('Features', 'newsup' ); ?></h4> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <h5><?php esc_html_e('Free', 'newsup' ); ?></h5>
                                                </div>
                                                <div class="checkable">
                                                    <h5 class="pro"><?php esc_html_e('Pro', 'newsup' ); ?></h5>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Live editing in Customizer', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Multiple Header Options', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Full Width Page Options', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Typography style and colors', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Preloader', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Animation Effects', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Load More Posts', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Infinity Scroll', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Social Icon Repeater', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Live Search / Ajax Search', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Light Dark Mode', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Posts Section Advertisements', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Advanced Posts Section Advertisements', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Basic Banner Featured Posts Controls', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Advanced Banner Featured Posts Controls', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Popup Advertisement', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Custom Widgets', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Advanced Custom Widgets', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Archive Layout', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class="newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Advanced Archive Layout', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Instagram Slider', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('View Related Post', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Advanced Footer Widgets', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Hide Theme Credit Link', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('WooCommerce Compatibility', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Responsive Layout', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Translations Ready', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Proper Documentation', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Updates', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Support', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Priority Support', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Prebuild Demos', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Advanced Prebuild Demos', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('SEO', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Gradient Color Option', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Breadcrumb Settings', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Random Post', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Header Layouts', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Slider Layouts', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Header Toggle Offcanvas', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Maintenance Mode', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Schema Markup', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Cursor Dot', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Post Like Setting', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                            <!-- newsup-admin-feature-table -->
                                            <div class="newsup-admin-tb-tittle">
                                                <div class=" newsup-admin-tb-list">
                                                    <span><?php esc_html_e('Single Post Layouts', 'newsup' ); ?></span> 
                                                </div>
                                                <div class="newsup-admin-tb-offer">
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </div>
                                                <div class="checkable">
                                                    <span class="dashicons dashicons-saved"></span>
                                                </div>
                                                </div> 
                                            </div>
                                            <!-- /newsup-admin-feature-table -->
                                        </div>
                                    </div>
                                </div> 
                                <?php echo $this->newsup_admin_right_sidebar() ?>                                
                            </div>
                        </div>
                        <div class="newsup-tab-content newsup-change-log">
                            <div class="newsup-change-log-main newsup-d-grid column6 gap-30">
                                <div class="newsup-getstart-inner newsup-col-span-4">
                                    <pre class="newsup-change-log-content"><?php echo esc_html( $change_log ); ?></pre>
                                </div> 
                                <?php echo $this->newsup_admin_right_sidebar() ?>  
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    <?php }

    public function newsup_install_plugin_callback() {
        check_ajax_referer('newsup_plugin_nonce', 'nonce');

        if (!current_user_can('install_plugins')) {
            wp_send_json_error(['msg' => 'Not allowed']);
        }

        $slug = sanitize_text_field($_POST['slug']);

        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';

        // Fetch plugin info
        $api = plugins_api('plugin_information', [
            'slug'   => $slug,
            'fields' => ['sections' => false],
        ]);

        if (is_wp_error($api)) {
            wp_send_json_error(['msg' => 'Plugin not found']);
        }

        // FAST AJAX UPGRADER (no screen output)
        $skin = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        $result   = $upgrader->install($api->download_link);

        if (is_wp_error($result)) {
            wp_send_json_error(['msg' => 'Install Failed']);
        }

        // Return TRUE instantly
        wp_send_json_success([
            'msg'    => 'installed',
            'plugin' => $slug,
        ]);
    }

    public function newsup_activate_plugin_callback() {
        check_ajax_referer('newsup_plugin_nonce', 'nonce');

        if (!current_user_can('activate_plugins')) {
            wp_send_json_error(['msg' => 'Not allowed']);
        }

        $plugin_file = sanitize_text_field($_POST['plugin_file']);

        $result = activate_plugin($plugin_file);

        if (is_wp_error($result)) {
            wp_send_json_error(['msg' => 'Activation failed']);
        }

        wp_send_json_success(['msg' => 'activated']);
    }

    private function plugin_box($slug, $plugin_file, $title, $desc, $image, $more_link) {

        if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
            $status = "install";
            $btn_text = "Install";
            $btn_class = "btn-install";
        } elseif (!is_plugin_active($plugin_file)) {
            $status = "activate";
            $btn_text = "Activate";
            $btn_class = "btn-activate";
        } else {
            $status = "activated";
            $btn_text = "Activated";
            $btn_class = "btn-disabled";
        }

        ob_start(); ?>

        <div class="bottom-item" data-status="<?php echo esc_attr($status); ?>"
             data-slug="<?php echo esc_attr($slug); ?>"
             data-plugin="<?php echo esc_attr($plugin_file); ?> 
             ">

            <div class="head-item">
                <div class="details-image">
                    <img src="<?php echo esc_url($image); ?>" />
                </div>
                <div class="details-heading">
                    <h4><?php echo esc_html($title); ?></h4>
                </div>
            </div>
            <p class="detail-description"><?php echo esc_html($desc); ?></p>
            <div class="foot-item">
                <div class="details-btn">
                    <a href="#" class="btn-active <?php echo $btn_class; ?>">
                        <?php echo esc_html($btn_text); ?>
                    </a>
                    <a href="<?php echo esc_url($more_link); ?>" target="_blank" class="more-detail-link">
                        More Details
                    </a>
                </div>
            </div>

        </div>

        <?php
        return ob_get_clean();
    }
    private function newsup_admin_right_sidebar(){ ?>
        <div class="newsup-right-area newsup-d-grid align-start">
            <div class="newsup-right-box sidebar-detail-area">
                <div class="newsup-img-sidebar"><img src="<?php echo esc_url(NEWSUP_THEME_URI) . 'images/newsup.customize.png' ?>"></div>
                <h3 class="newsup-right-area-title"><?php esc_html_e(' Go Pro with Newsup – Elevate Your News Experience','newsup'); ?></h3>
                <p class="newsup-right-area-desc"><?php esc_html_e('Access exclusive features, widgets, premium ready-made homepages, custom sections, smart controls, and powerful tools to make your website look sharper and work smarter.','newsup'); ?></p>
                <a href="https://themeansar.com/themes/newsup-pro/" target="_blank" class="newsup-btn-link">
                    <span class="head-icon"><svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                        <path d="M19.6872 14.0931L19.8706 12.3884C19.9684 11.4789 20.033 10.8783 19.9823 10.4999L20 10.5C20.8284 10.5 21.5 9.82843 21.5 9C21.5 8.17157 20.8284 7.5 20 7.5C19.1716 7.5 18.5 8.17157 18.5 9C18.5 9.37466 18.6374 9.71724 18.8645 9.98013C18.5384 10.1814 18.1122 10.606 17.4705 11.2451L17.4705 11.2451C16.9762 11.7375 16.729 11.9837 16.4533 12.0219C16.3005 12.043 16.1449 12.0213 16.0038 11.9592C15.7492 11.847 15.5794 11.5427 15.2399 10.934L13.4505 7.7254C13.241 7.34987 13.0657 7.03557 12.9077 6.78265C13.556 6.45187 14 5.77778 14 5C14 3.89543 13.1046 3 12 3C10.8954 3 10 3.89543 10 5C10 5.77778 10.444 6.45187 11.0923 6.78265C10.9343 7.03559 10.759 7.34984 10.5495 7.7254L8.76006 10.934C8.42056 11.5427 8.25081 11.847 7.99621 11.9592C7.85514 12.0213 7.69947 12.043 7.5467 12.0219C7.27097 11.9837 7.02381 11.7375 6.5295 11.2451C5.88787 10.606 5.46156 10.1814 5.13553 9.98012C5.36264 9.71724 5.5 9.37466 5.5 9C5.5 8.17157 4.82843 7.5 4 7.5C3.17157 7.5 2.5 8.17157 2.5 9C2.5 9.82843 3.17157 10.5 4 10.5L4.01771 10.4999C3.96702 10.8783 4.03162 11.4789 4.12945 12.3884L4.3128 14.0931C4.41458 15.0393 4.49921 15.9396 4.60287 16.75H19.3971C19.5008 15.9396 19.5854 15.0393 19.6872 14.0931Z" fill="none"></path>
                        <path d="M10.9121 21H13.0879C15.9239 21 17.3418 21 18.2879 20.1532C18.7009 19.7835 18.9623 19.1172 19.151 18.25H4.84896C5.03765 19.1172 5.29913 19.7835 5.71208 20.1532C6.65817 21 8.07613 21 10.9121 21Z" fill="none"></path>
                    </svg>
            </span><?php esc_html_e('Upgrade To Pro','newsup'); ?></a>
            </div>
            <div class="newsup-right-box">
                <h3 class="newsup-right-area-title"><div class="newsup-right-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M320 128C241 128 175.3 185.3 162.3 260.7C171.6 257.7 181.6 256 192 256L208 256C234.5 256 256 277.5 256 304L256 400C256 426.5 234.5 448 208 448L192 448C139 448 96 405 96 352L96 288C96 164.3 196.3 64 320 64C443.7 64 544 164.3 544 288L544 456.1C544 522.4 490.2 576.1 423.9 576.1L336 576L304 576C277.5 576 256 554.5 256 528C256 501.5 277.5 480 304 480L336 480C362.5 480 384 501.5 384 528L384 528L424 528C463.8 528 496 495.8 496 456L496 435.1C481.9 443.3 465.5 447.9 448 447.9L432 447.9C405.5 447.9 384 426.4 384 399.9L384 303.9C384 277.4 405.5 255.9 432 255.9L448 255.9C458.4 255.9 468.3 257.5 477.7 260.6C464.7 185.3 399.1 127.9 320 127.9z"/></svg>
                </div><?php esc_html_e('We’re Here to Help','newsup'); ?></h3>
                <p class="newsup-right-area-desc"><?php esc_html_e('Need help or have feedback? Join our Support Forum for quick answers and friendly advice!','newsup'); ?></p>
                <a href="https://themeansar.ticksy.com/" target="_blank" class="newsup-btn-link"><?php esc_html_e('Ask for Help','newsup'); ?> <span class="newsup-icon-btn"><svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                    <path d="M6 12H18M18 12L13 7M18 12L13 17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </span></a>
            </div>
            <div class="newsup-right-box">
                <h3 class="newsup-right-area-title"><div class="newsup-right-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                <path d="M9.15316 5.40838C10.4198 3.13613 11.0531 2 12 2C12.9469 2 13.5802 3.13612 14.8468 5.40837L15.1745 5.99623C15.5345 6.64193 15.7144 6.96479 15.9951 7.17781C16.2757 7.39083 16.6251 7.4699 17.3241 7.62805L17.9605 7.77203C20.4201 8.32856 21.65 8.60682 21.9426 9.54773C22.2352 10.4886 21.3968 11.4691 19.7199 13.4299L19.2861 13.9372C18.8096 14.4944 18.5713 14.773 18.4641 15.1177C18.357 15.4624 18.393 15.8341 18.465 16.5776L18.5306 17.2544C18.7841 19.8706 18.9109 21.1787 18.1449 21.7602C17.3788 22.3417 16.2273 21.8115 13.9243 20.7512L13.3285 20.4768C12.6741 20.1755 12.3469 20.0248 12 20.0248C11.6531 20.0248 11.3259 20.1755 10.6715 20.4768L10.0757 20.7512C7.77268 21.8115 6.62118 22.3417 5.85515 21.7602C5.08912 21.1787 5.21588 19.8706 5.4694 17.2544L5.53498 16.5776C5.60703 15.8341 5.64305 15.4624 5.53586 15.1177C5.42868 14.773 5.19043 14.4944 4.71392 13.9372L4.2801 13.4299C2.60325 11.4691 1.76482 10.4886 2.05742 9.54773C2.35002 8.60682 3.57986 8.32856 6.03954 7.77203L6.67589 7.62805C7.37485 7.4699 7.72433 7.39083 8.00494 7.17781C8.28555 6.96479 8.46553 6.64194 8.82547 5.99623L9.15316 5.40838Z" fill="#1C274C"/></svg>
                </div><?php esc_html_e(' Share your experience!','newsup'); ?></h3>
                <p class="newsup-right-area-desc">
                    <?php
                        printf(
                            esc_html__(
                                'Your feedback about %s — is greatly appreciated',
                                'newsup'
                            ),
                            esc_html( NEWSUP_THEME_NAME )
                        );
                    ?>                    
                </p>
                <a href="https://wordpress.org/support/theme/newsup/reviews/" target="_blank" class="newsup-btn-link"><?php esc_html_e('Rate Us','newsup'); ?> <span class="newsup-icon-btn"><svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                    <path d="M6 12H18M18 12L13 7M18 12L13 17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </span></a>
            </div>
            <div class="newsup-right-box">
                <h3 class="newsup-right-area-title"><div class="newsup-right-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                <path d="M16.5189 16.5013C16.6939 16.3648 16.8526 16.2061 17.1701 15.8886L21.1275 11.9312C21.2231 11.8356 21.1793 11.6708 21.0515 11.6264C20.5844 11.4644 19.9767 11.1601 19.4083 10.5917C18.8399 10.0233 18.5356 9.41561 18.3736 8.94849C18.3292 8.82066 18.1644 8.77687 18.0688 8.87254L14.1114 12.8299C13.7939 13.1474 13.6352 13.3061 13.4987 13.4811C13.3377 13.6876 13.1996 13.9109 13.087 14.1473C12.9915 14.3476 12.9205 14.5606 12.7786 14.9865L12.5951 15.5368L12.3034 16.4118L12.0299 17.2323C11.9601 17.4419 12.0146 17.6729 12.1708 17.8292C12.3271 17.9854 12.5581 18.0399 12.7677 17.9701L13.5882 17.6966L14.4632 17.4049L15.0135 17.2214L15.0136 17.2214C15.4394 17.0795 15.6524 17.0085 15.8527 16.913C16.0891 16.8004 16.3124 16.6623 16.5189 16.5013Z" fill="#1C274C"/>
                <path d="M22.3665 10.6922C23.2112 9.84754 23.2112 8.47812 22.3665 7.63348C21.5219 6.78884 20.1525 6.78884 19.3078 7.63348L19.1806 7.76071C19.0578 7.88348 19.0022 8.05496 19.0329 8.22586C19.0522 8.33336 19.0879 8.49053 19.153 8.67807C19.2831 9.05314 19.5288 9.54549 19.9917 10.0083C20.4545 10.4712 20.9469 10.7169 21.3219 10.847C21.5095 10.9121 21.6666 10.9478 21.7741 10.9671C21.945 10.9978 22.1165 10.9422 22.2393 10.8194L22.3665 10.6922Z" fill="#1C274C"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.17157 3.17157C3 4.34315 3 6.22876 3 10V14C3 17.7712 3 19.6569 4.17157 20.8284C5.34315 22 7.22876 22 11 22H13C16.7712 22 18.6569 22 19.8284 20.8284C20.9812 19.6756 20.9997 17.8316 21 14.1801L18.1817 16.9984C17.9119 17.2683 17.691 17.4894 17.4415 17.6841C17.1491 17.9121 16.8328 18.1076 16.4981 18.2671C16.2124 18.4032 15.9159 18.502 15.5538 18.6225L13.2421 19.3931C12.4935 19.6426 11.6682 19.4478 11.1102 18.8898C10.5523 18.3318 10.3574 17.5065 10.607 16.7579L10.8805 15.9375L11.3556 14.5121L11.3775 14.4463C11.4981 14.0842 11.5968 13.7876 11.7329 13.5019C11.8924 13.1672 12.0879 12.8509 12.316 12.5586C12.5106 12.309 12.7317 12.0881 13.0017 11.8183L17.0081 7.81188L18.12 6.70004L18.2472 6.57282C18.9626 5.85741 19.9003 5.49981 20.838 5.5C20.6867 4.46945 20.3941 3.73727 19.8284 3.17157C18.6569 2 16.7712 2 13 2H11C7.22876 2 5.34315 2 4.17157 3.17157ZM7.25 9C7.25 8.58579 7.58579 8.25 8 8.25H14.5C14.9142 8.25 15.25 8.58579 15.25 9C15.25 9.41421 14.9142 9.75 14.5 9.75H8C7.58579 9.75 7.25 9.41421 7.25 9ZM7.25 13C7.25 12.5858 7.58579 12.25 8 12.25H10.5C10.9142 12.25 11.25 12.5858 11.25 13C11.25 13.4142 10.9142 13.75 10.5 13.75H8C7.58579 13.75 7.25 13.4142 7.25 13ZM7.25 17C7.25 16.5858 7.58579 16.25 8 16.25H9.5C9.91421 16.25 10.25 16.5858 10.25 17C10.25 17.4142 9.91421 17.75 9.5 17.75H8C7.58579 17.75 7.25 17.4142 7.25 17Z" fill="#1C274C"/>
                </svg>
                </div><?php esc_html_e(' Discover the Features','newsup'); ?></h3>
                <p class="newsup-right-area-desc"><?php esc_html_e('Struggling to figure it out? Let our detailed guides be your ultimate problem-solver!','newsup'); ?></p>
                <a href="https://themeansar.com/free-themes/newsup/" target="_blank" class="newsup-btn-link"><?php esc_html_e('Explore Now','newsup'); ?> <span class="newsup-icon-btn"><svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                    <path d="M6 12H18M18 12L13 7M18 12L13 17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </span></a>
            </div>
        </div>      
    <?php 
    }
    private function newsup_upgrade_callback(){
        return ('<p class="newsup-feature-area-desc"><a href='. esc_url('https://themeansar.com/themes/newsup-pro/','newsup') . ' target="_blank">'. esc_html('Go to Pro','newsup') . '</a></p>');
    }
}

new NewsUp_Admin();