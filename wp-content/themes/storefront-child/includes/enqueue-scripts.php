<?php

/**
 * Enqueues the city search script and localizes script parameters.
 */
function enqueue_city_search_script() {
    // Check if we're on a page where the script is needed
    if (is_page('search-cities')) {
        wp_enqueue_script(
            'city-search',
            get_stylesheet_directory_uri() . '/js/city-search.js',
            array('jquery'),
            filemtime(get_stylesheet_directory() . '/js/city-search.js'),
            true
        );

        wp_localize_script('city-search', 'citySearchParams', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }
}

add_action('wp_enqueue_scripts', 'enqueue_city_search_script');
