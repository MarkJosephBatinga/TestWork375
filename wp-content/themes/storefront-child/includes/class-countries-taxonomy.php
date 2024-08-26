<?php

/**
 * Handles the registration of the 'countries' taxonomy.
 *
 * This class is responsible for creating and managing the hierarchical 'countries' taxonomy,
 * which is associated with the 'cities' custom post type.
 */
class Countries_Taxonomy {

    /**
     * Constructor method for the Countries_Taxonomy class.
     *
     * Sets up the action to initialize the 'countries' taxonomy.
     */
    public function __construct() {
        add_action('init', [$this, 'register_countries_taxonomy']);
    }

    /**
     * Registers the 'countries' taxonomy.
     *
     * Defines the labels and arguments for the taxonomy and registers it with WordPress.
     *
     * @return void
     */
    public function register_countries_taxonomy() {
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
}

// Instantiate the Countries_Taxonomy class
new Countries_Taxonomy();
