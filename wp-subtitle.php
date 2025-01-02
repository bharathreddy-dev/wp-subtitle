<?php
/**
 * Plugin Name: WP Subtitle
 * Description: Adds subtitle field to posts, pages and custom post types with customizable settings.
 * Version: 1.0
 * Author: BHARATH KUMAR REDDY
*/

if(!defined('ABSPATH')) {
    exit; //Exit if accessed directly
}

/**
 * Include Admin Settings
 */
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';


/**
 * Enqueue Admin Scripts and Styles
 */
add_action('admin_enqueue_scripts', 'wp_subtitle_admin_scripts');

function wp_subtitle_admin_scripts() {
    wp_enqueue_style('wp-subtitle-admin-css', plugin_dir_url(__FILE__) . 'assets/css/admin.css');
}


// Add subtitle meta box
add_action('add_meta_boxes', 'wp_subtitle_add_meta_box');
function wp_subtitle_add_meta_box() {
    $enabled_post_types = get_option('wp_subtitle_post_types', ['post', 'page']);
    foreach ($enabled_post_types as $post_type) {
        add_meta_box(
            'wp_subtitle',
            'Subtitle',
            'wp_subtitle_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}

function wp_subtitle_meta_box_callback($post) {
    $subtitle = get_post_meta($post->ID, '_wp_subtitle', true);
    wp_nonce_field('wp_subtitle_nonce_action', 'wp_subtitle_nonce');
    echo '<input type="text" name="wp_subtitle" value="' . esc_attr($subtitle) . '" style="width:100%;"/>';
}

// Save subtitle meta data
add_action('save_post', 'wp_subtitle_save_meta');
function wp_subtitle_save_meta($post_id) {
    if (!isset($_POST['wp_subtitle_nonce']) || !wp_verify_nonce($_POST['wp_subtitle_nonce'], 'wp_subtitle_nonce_action')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $subtitle = sanitize_text_field($_POST['wp_subtitle']);
    update_post_meta($post_id, '_wp_subtitle', $subtitle);
}

// Add shortcode to display subtitle
add_shortcode('wp_subtitle', 'wp_subtitle_shortcode');
function wp_subtitle_shortcode($atts) {
    global $post;
    $subtitle = get_post_meta($post->ID, '_wp_subtitle', true);
    return esc_html($subtitle);
}