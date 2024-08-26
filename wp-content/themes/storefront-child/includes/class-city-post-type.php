<?php

/**
 * Handles the registration and management of the 'cities' custom post type.
 *
 * This class is responsible for creating the custom post type 'cities', adding meta boxes for location data,
 * and saving the location information when a city post is updated.
 */
class City_Post_Type {

    /**
     * Constructor method for the City_Post_Type class.
     *
     * Sets up the actions to initialize the custom post type, add meta boxes, and handle saving of meta box data.
     */
    public function __construct() {
        add_action('init', [$this, 'register_city_post_type']);
        add_action('add_meta_boxes', [$this, 'add_city_meta_boxes']);
        add_action('save_post', [$this, 'save_city_location_meta_box']);
    }

    /**
     * Registers the 'cities' custom post type.
     *
     * Defines the labels and arguments for the custom post type and registers it with WordPress.
     *
     * @return void
     */
    public function register_city_post_type() {
        $labels = array(
            'name' => __('Cities'),
            'singular_name' => __('City'),
            'menu_name' => __('Cities'),
            'name_admin_bar' => __('City'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'cities'),
            'supports' => array('title', 'editor', 'thumbnail'),
            'show_in_rest' => true,
        );

        register_post_type('cities', $args);
    }

    /**
     * Adds meta boxes to the 'cities' post type.
     *
     * Adds a meta box to the 'cities' post type for entering city location data (latitude and longitude).
     *
     * @return void
     */
    public function add_city_meta_boxes() {
        add_meta_box(
            'city_location',
            __('City Location', 'storefront-child'),
            [$this, 'render_city_location_meta_box'],
            'cities',
            'side',
            'default'
        );
    }

    /**
     * Renders the content of the city location meta box.
     *
     * Displays input fields for latitude and longitude in the meta box for city posts.
     *
     * @param WP_Post $post The current post object.
     * @return void
     */
    public function render_city_location_meta_box($post) {
        wp_nonce_field('city_location_nonce_action', 'city_location_nonce');

        $latitude = get_post_meta($post->ID, '_city_latitude', true);
        $longitude = get_post_meta($post->ID, '_city_longitude', true);

        echo '<label for="city_latitude">' . __('Latitude:', 'storefront-child') . '</label>';
        echo '<input type="text" id="city_latitude" name="city_latitude" value="' . esc_attr($latitude) . '" />';
        echo '<br/><br/>';
        echo '<label for="city_longitude">' . __('Longitude:', 'storefront-child') . '</label>';
        echo '<input type="text" id="city_longitude" name="city_longitude" value="' . esc_attr($longitude) . '" />';
    }

    /**
     * Saves the city location meta box data.
     *
     * Validates and saves the latitude and longitude data from the city location meta box.
     *
     * @param int $post_id The ID of the post being saved.
     * @return void
     */
    public function save_city_location_meta_box($post_id) {
        if (!isset($_POST['city_location_nonce']) || !wp_verify_nonce($_POST['city_location_nonce'], 'city_location_nonce_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['city_latitude'])) {
            update_post_meta($post_id, '_city_latitude', sanitize_text_field($_POST['city_latitude']));
        }

        if (isset($_POST['city_longitude'])) {
            update_post_meta($post_id, '_city_longitude', sanitize_text_field($_POST['city_longitude']));
        }
    }
}

// Instantiate the City_Post_Type class
new City_Post_Type();
