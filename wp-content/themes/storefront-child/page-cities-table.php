<?php
/**
 * Template Name: Cities Table
 */

get_header(); ?>

<div class="container">
    <?php
    // Custom action hook before the table
    do_action('before_cities_table');

    // Display the search form
    ?>
    <form id="city-search-form" method="post">
        <input type="text" id="city-search" name="city-search" placeholder="<?php _e('Search for a city...', 'storefront-child'); ?>" />
        <button type="submit"><?php _e('Search', 'storefront-child'); ?></button>
    </form>

    <div id="cities-table-container">
        <?php
        // Fetch and display the table
        display_cities_table();
        ?>
    </div>

    <?php
    // Custom action hook after the table
    do_action('after_cities_table');
    ?>
</div>

<?php get_footer(); ?>
