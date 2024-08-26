<?php

/**
 * Widget that displays the temperature of a selected city.
 *
 * This widget fetches the temperature data for a city from the OpenWeatherMap API
 * and displays it in a WordPress widget area.
 */
class City_Temperature_Widget extends WP_Widget {

    /**
     * Constructor method for the City_Temperature_Widget class.
     *
     * Sets up the widget with its name, description, and other arguments.
     */
    public function __construct() {
        parent::__construct(
            'city_temperature_widget',
            __('City Temperature', 'storefront-child'),
            array('description' => __('Displays the temperature of a city', 'storefront-child'))
        );
    }

    /**
     * Outputs the widget content on the front-end.
     *
     * Retrieves the temperature for the selected city and displays it.
     *
     * @param array $args     The widget arguments, including before/after widget and title.
     * @param array $instance The widget instance settings.
     * @return void
     */
    public function widget($args, $instance) {
        $city_id = $instance['city_id'];
        $api_key = '5ded25051396a08198a88c595688a822';

        $latitude = get_post_meta($city_id, '_city_latitude', true);
        $longitude = get_post_meta($city_id, '_city_longitude', true);
        $city_name = get_the_title($city_id);

        $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$api_key}&units=metric";

        $response = wp_remote_get($api_url);
        $temperature = __('Unavailable', 'storefront-child');

        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($data['main']['temp'])) {
                $temperature = $data['main']['temp'] . '°C';
            }
        }

        echo $args['before_widget'];
        echo $args['before_title'] . __('Temperature in ', 'storefront-child') . $city_name . $args['after_title'];
        echo '<p>' . __('Current Temperature:', 'storefront-child') . ' ' . esc_html($temperature) . '</p>';
        echo $args['after_widget'];
    }

    /**
     * Outputs the widget form in the admin area.
     *
     * Allows the user to select a city from a dropdown list.
     *
     * @param array $instance The current widget instance settings.
     * @return void
     */
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

    /**
     * Updates the widget settings in the database.
     *
     * Sanitizes and saves the widget settings.
     *
     * @param array $new_instance The new widget instance settings.
     * @param array $old_instance The old widget instance settings.
     * @return array The updated widget instance settings.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['city_id'] = (!empty($new_instance['city_id'])) ? strip_tags($new_instance['city_id']) : '';
        return $instance;
    }
}

/**
 * Registers the City_Temperature_Widget widget.
 *
 * Hooked into the 'widgets_init' action to register the widget.
 *
 * @return void
 */
function register_city_temperature_widget() {
    register_widget('City_Temperature_Widget');
}
add_action('widgets_init', 'register_city_temperature_widget');
