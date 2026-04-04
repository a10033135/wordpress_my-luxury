<?php
/**
 * Default theme options.
 *
 * @package NewsTwenty
 */

if (!function_exists('newstwenty_get_default_theme_options')):

/**
 * Get default theme options
 *
 * @since 1.0.0
 *
 * @return array Default theme options.
 */
function newstwenty_get_default_theme_options() {

    $defaults = array();

    $defaults['select_trending_news_category'] = 0;
    $defaults['select_recent_news_category'] = 0;

	return $defaults;

}
endif;