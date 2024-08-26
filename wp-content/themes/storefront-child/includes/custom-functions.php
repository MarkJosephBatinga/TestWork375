<?php

/**
 * Displays a table of cities filtered by a search query.
 *
 * Retrieves cities from the database, optionally filters them by a search query,
 * and outputs an HTML table with city names, country names, and temperatures.
 *
 * @param string $search_query Optional. The search query to filter cities by name. Default empty.
 * @return string HTML content of the city table or a message indicating no cities found.
 */
function storefront_child_display_city_table($search_query = '') {
    // Start the output buffer
    ob_start();
    global $wpdb;

    // Query to get countries, cities, and their coordinates
    $query = "
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
    ";

    $cities = $wpdb->get_results($query);

    // Filter cities if a search query is provided
    if (!empty($search_query)) {
        $cities = array_filter($cities, function ($city) use ($search_query) {
            return stripos($city->city_name, $search_query) !== false;
        });
    }

    // Display the table
    if (!empty($cities)) {
        echo '<table class="city-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' . __('City', 'storefront-child') . '</th>';
        echo '<th>' . __('Country', 'storefront-child') . '</th>';
        echo '<th>' . __('Temperature', 'storefront-child') . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($cities as $city) {
            // Get the temperature based on the coordinates
            $temperature = get_city_temperature($city->latitude, $city->longitude); // Implement this function

            echo '<tr>';
            echo '<td>' . esc_html($city->city_name) . '</td>';
            echo '<td>' . esc_html($city->country_name) . '</td>';
            echo '<td>' . esc_html($temperature) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>' . __('No cities found.', 'storefront-child') . '</p>';
    }

    // Return the content
    return ob_get_clean();
}

/**
 * Retrieves the current temperature for a given latitude and longitude.
 *
 * Makes an API request to OpenWeatherMap to get the temperature based on the provided
 * latitude and longitude.
 *
 * @param float $latitude Latitude of the location.
 * @param float $longitude Longitude of the location.
 * @return string Temperature in Celsius or a message indicating the data is unavailable.
 */
function get_city_temperature($latitude, $longitude) {
    $api_key = '5ded25051396a08198a88c595688a822';
    $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$api_key}&units=metric"; // Adjust units if needed

    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return __('Temperature data unavailable', 'storefront-child');
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if (!isset($data->main->temp)) {
        return __('Temperature data unavailable', 'storefront-child');
    }

    return esc_html($data->main->temp) . ' °C';
}