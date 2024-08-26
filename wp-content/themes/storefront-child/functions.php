<?php

/**
 * Includes necessary class files for custom post types, taxonomies, widgets, and functions.
 */
require_once get_stylesheet_directory() . '/includes/class-city-post-type.php';
require_once get_stylesheet_directory() . '/includes/class-countries-taxonomy.php';
require_once get_stylesheet_directory() . '/includes/class-city-temperature-widget.php';
require_once get_stylesheet_directory() . '/includes/custom-functions.php';

/**
 * Initializes custom meta boxes for the theme.
 *
 * This function creates instances of custom post types and taxonomies.
 *
 * @return void
 */
function storefront_child_add_custom_meta_boxes() {
    new City_Post_Type();
    new Countries_Taxonomy();
}
add_action('after_setup_theme', 'storefront_child_add_custom_meta_boxes');

/**
 * Registers custom widgets for the theme.
 *
 * This function registers the City Temperature Widget.
 *
 * @return void
 */
function storefront_child_register_widgets() {
    register_widget('City_Temperature_Widget');
}
add_action('widgets_init', 'storefront_child_register_widgets');

/**
 * Handles AJAX requests for city searches.
 *
 * This function processes the AJAX request, sanitizes the search query,
 * and returns the filtered city table.
 *
 * @return void
 */
function storefront_child_ajax_search_cities() {
    // Sanitize the search query from the POST request
    $search_query = sanitize_text_field($_POST['search_query']);
    
    // Output the city table filtered by the search query
    echo storefront_child_display_city_table($search_query);
    
    wp_die();
}

add_action('wp_ajax_nopriv_search_cities', 'storefront_child_ajax_search_cities');
add_action('wp_ajax_search_cities', 'storefront_child_ajax_search_cities');
