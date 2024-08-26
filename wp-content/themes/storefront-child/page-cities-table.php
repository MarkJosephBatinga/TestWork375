<?php
/**
 * Template Name: Cities Table Template
 */

get_header(); ?>

<div class="city-search-container">
    <form id="city-search-form" method="post">
        <input type="text" name="search_query" id="search-query" placeholder="<?php _e('Search City...', 'storefront-child'); ?>">
        <button type="submit"><?php _e('Search', 'storefront-child'); ?></button>
    </form>
</div>

<div id="city-results">
    <?php
    if (isset($_POST['search_query'])) {
        // Sanitize search input
        $search_query = sanitize_text_field($_POST['search_query']);
        echo storefront_child_display_city_table($search_query);
    } else {
        echo storefront_child_display_city_table();
    }
    ?>
</div>

<?php get_footer(); ?>
