<?php
/**
 * This function ensures that the parent theme's stylesheet is loaded before the child theme's
 * stylesheet, allowing the child theme to override specific styles.
 */
function storefront_child_enqueue_styles() {
    // Enqueue the parent theme's style.css file.
    wp_enqueue_style('storefront-parent-style', get_template_directory_uri() . '/style.css');
}

// Hook the function into the 'wp_enqueue_scripts' action to ensure it runs at the right time.
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');


function create_cities_post_type() {
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
add_action('init', 'create_cities_post_type');

function add_cities_meta_boxes() {
    add_meta_box(
        'city_location',
        __('City Location', 'storefront-child'),
        'render_city_location_meta_box',
        'cities',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_cities_meta_boxes');

function render_city_location_meta_box($post) {
    // Add nonce for security and authentication.
    wp_nonce_field('city_location_nonce_action', 'city_location_nonce');

    $latitude = get_post_meta($post->ID, '_city_latitude', true);
    $longitude = get_post_meta($post->ID, '_city_longitude', true);

    echo '<label for="city_latitude">' . __('Latitude:', 'storefront-child') . '</label>';
    echo '<input type="text" id="city_latitude" name="city_latitude" value="' . esc_attr($latitude) . '" />';
    echo '<br/><br/>';
    echo '<label for="city_longitude">' . __('Longitude:', 'storefront-child') . '</label>';
    echo '<input type="text" id="city_longitude" name="city_longitude" value="' . esc_attr($longitude) . '" />';
}

function save_city_location_meta_box($post_id) {
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
add_action('save_post', 'save_city_location_meta_box');

function create_countries_taxonomy() {
    $labels = array(
        'name' => __('Countries', 'storefront-child'),
        'singular_name' => __('Country', 'storefront-child'),
        'search_items' => __('Search Countries', 'storefront-child'),
        'all_items' => __('All Countries', 'storefront-child'),
        'edit_item' => __('Edit Country', 'storefront-child'),
        'update_item' => __('Update Country', 'storefront-child'),
        'add_new_item' => __('Add New Country', 'storefront-child'),
        'new_item_name' => __('New Country Name', 'storefront-child'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'countries'),
        'show_in_rest' => true,
    );

    register_taxonomy('countries', array('cities'), $args);
}
add_action('init', 'create_countries_taxonomy');

class City_Temperature_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'city_temperature_widget',
            __('City Temperature', 'storefront-child'),
            array('description' => __('Displays the temperature of a city', 'storefront-child'))
        );
    }

    public function widget($args, $instance) {
        $city_id = $instance['city_id'];
        $api_key = '5ded25051396a08198a88c595688a822';

        $latitude = get_post_meta($city_id, '_city_latitude', true);
        $longitude = get_post_meta($city_id, '_city_longitude', true);
        $city_name = get_the_title($city_id);

        $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$api_key}&units=metric";

        // Fetch the temperature data from the API
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            $temperature = __('Unable to retrieve temperature', 'storefront-child');
        } else {
            $data = json_decode(wp_remote_retrieve_body($response), true);

            if (isset($data['main']['temp'])) {
                $temperature = $data['main']['temp'] . '°C';
            } else {
                $temperature = __('Unavailable', 'storefront-child');
            }
        }

        // Display the widget content
        echo $args['before_widget'];
        echo $args['before_title'] . __('Temperature in ', 'storefront-child') . $city_name . $args['after_title'];
        echo '<p>' . __('Current Temperature:', 'storefront-child') . ' ' . $temperature . '</p>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('city_id'); ?>"><?php _e('Select City:', 'storefront-child'); ?></label>
            <select id="<?php echo $this->get_field_id('city_id'); ?>" name="<?php echo $this->get_field_name('city_id'); ?>" class="widefat">
                <?php
                $cities = get_posts(array('post_type' => 'cities', 'posts_per_page' => -1));
                foreach ($cities as $city) {
                    ?>
                    <option value="<?php echo $city->ID; ?>" <?php selected($city_id, $city->ID); ?>>
                        <?php echo $city->post_title; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['city_id'] = (!empty($new_instance['city_id'])) ? strip_tags($new_instance['city_id']) : '';
        return $instance;
    }
}

function register_city_temperature_widget() {
    register_widget('City_Temperature_Widget');
}
add_action('widgets_init', 'register_city_temperature_widget');

function display_cities_table() {
    global $wpdb;

    // Query to get countries, cities, and temperatures
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

    echo '<table>';
    echo '<thead><tr><th>' . __('City', 'storefront-child') . '</th><th>' . __('Country', 'storefront-child') . '</th><th>' . __('Temperature', 'storefront-child') . '</th></tr></thead>';
    echo '<tbody>';

    foreach ($cities as $city) {
        // Fetch the temperature from the API
        $api_key = '5ded25051396a08198a88c595688a822';
        $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$city->latitude}&lon={$city->longitude}&appid={$api_key}&units=metric";

        $response = wp_remote_get($api_url);
        $temperature = __('Unavailable', 'storefront-child');

        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($data['main']['temp'])) {
                $temperature = $data['main']['temp'] . '°C';
            }
        }

        echo '<tr>';
        echo '<td>' . esc_html($city->city_name) . '</td>';
        echo '<td>' . esc_html($city->country_name) . '</td>';
        echo '<td>' . esc_html($temperature) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}

function search_cities() {
    global $wpdb;

    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

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

    $cities = $wpdb->get_results($query);

    ob_start();

    echo '<table>';
    echo '<thead><tr><th>' . __('City', 'storefront-child') . '</th><th>' . __('Country', 'storefront-child') . '</th><th>' . __('Temperature', 'storefront-child') . '</th></tr></thead>';
    echo '<tbody>';

    foreach ($cities as $city) {
        $api_key = '5ded25051396a08198a88c595688a822';
        $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$city->latitude}&lon={$city->longitude}&appid={$api_key}&units=metric";

        $response = wp_remote_get($api_url);
        $temperature = __('Unavailable', 'storefront-child');

        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($data['main']['temp'])) {
                $temperature = $data['main']['temp'] . '°C';
            }
        }

        echo '<tr>';
        echo '<td>' . esc_html($city->city_name) . '</td>';
        echo '<td>' . esc_html($city->country_name) . '</td>';
        echo '<td>' . esc_html($temperature) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    echo ob_get_clean();
    wp_die();
}
add_action('wp_ajax_search_cities', 'search_cities');
add_action('wp_ajax_nopriv_search_cities', 'search_cities');

function enqueue_city_search_script() {
    wp_enqueue_script('city-search', get_stylesheet_directory_uri() . '/js/city-search.js', array('jquery'), null, true);
    wp_localize_script('city-search', 'citySearch', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_city_search_script');


    


