<?php

/**
 * Handles AJAX requests for city searches.
 *
 * This class registers AJAX actions to search for cities and returns the results
 * in JSON format.
 */
class City_Ajax_Search {

    /**
     * Constructor method for the City_Ajax_Search class.
     *
     * Registers the AJAX actions for both logged-in and non-logged-in users.
     */
    public function __construct() {
        add_action('wp_ajax_search_cities', [$this, 'search_cities']);
        add_action('wp_ajax_nopriv_search_cities', [$this, 'search_cities']);
    }

    /**
     * Handles the AJAX request to search for cities.
     *
     * Retrieves cities from the database based on the search query and returns the
     * results as a JSON response.
     *
     * @return void
     */
    public function search_cities() {
        global $wpdb;

        // Get and sanitize the search query
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        // Prepare the SQL query to search for cities
        $query = $wpdb->prepare("
            SELECT 
                c.ID as city_id,
                c.post_title as city_name,
                t.name as country_name,
                pm_lat.meta_value as latitude,
                pm_lon.meta_value as longitude
            FROM {$wpdb->prefix}posts c
            LEFT JOIN {$wpdb->prefix}postmeta pm_lat ON c.ID = pm_lat.post_id AND pm_lat.meta_key = '_city_latitude'
            LEFT JOIN {$wpdb->prefix}postmeta pm_lon ON c.ID = pm_lon.post_id AND pm_lon.meta_key = '_city_longitude'
            LEFT JOIN {$wpdb->prefix}term_relationships tr ON c.ID = tr.object_id
            LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN {$wpdb->prefix}terms t ON tt.term_id = t.term_id
            WHERE c.post_type = 'cities' AND c.post_status = 'publish'
            AND c.post_title LIKE %s
        ", '%' . $wpdb->esc_like($search) . '%');

        // Execute the query and get results
        $cities = $wpdb->get_results($query);

        if (!empty($cities)) {
            $result = array();
            foreach ($cities as $city) {
                $result[] = array(
                    'city_id' => $city->city_id,
                    'city_name' => $city->city_name,
                    'country_name' => $city->country_name,
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude
                );
            }
            wp_send_json_success($result);
        } else {
            wp_send_json_error(__('No cities found', 'storefront-child'));
        }
    }
}

// Instantiate the City_Ajax_Search class
new City_Ajax_Search();
