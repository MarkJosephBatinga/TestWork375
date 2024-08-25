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
