<?php
/**
 * Settings File
*/

add_action('admin_menu', 'wp_subtitle_settings_menu');

function wp_subtitle_settings_menu() {
    add_options_page(
        'WP Subtitle Settings',
        'Subtitle Settings',
        'manage_options',
        'wp-subtitle-settings',
        'wp_subtitle_settings_page'
    );
}

function wp_subtitle_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['wp_subtitle_save_settings'])) {
        check_admin_referer('wp_subtitle_settings_nonce_action', 'wp_subtitle_settings_nonce');
        update_option('wp_subtitle_post_types', $_POST['wp_subtitle_post_types'] ?? []);
        update_option('wp_subtitle_custom_css', sanitize_textarea_field($_POST['wp_subtitle_custom_css']));
    }

    $enabled_post_types = get_option('wp_subtitle_post_types', ['post', 'page']);
    $custom_css = get_option('wp_subtitle_custom_css', '');

    ?>
    <div class="wrap">
        <h1>WP Subtitle Settings</h1>
        <form method="post">
            <?php wp_nonce_field('wp_subtitle_settings_nonce_action', 'wp_subtitle_settings_nonce'); ?>
            <h2>Select Post Types</h2>
            <p>Select the post types where the subtitle field should appear.</p>
            <?php
            $post_types = get_post_types(['public' => true], 'objects');
            foreach ($post_types as $post_type) {
                ?>
                <label>
                    <input type="checkbox" name="wp_subtitle_post_types[]" value="<?php echo esc_attr($post_type->name); ?>" 
                        <?php checked(in_array($post_type->name, $enabled_post_types)); ?> />
                    <?php echo esc_html($post_type->label); ?>
                </label><br/>
                <?php
            }
            ?>

            <h2>Custom CSS</h2>
            <textarea name="wp_subtitle_custom_css" style="width:100%;height:150px;"><?php echo esc_textarea($custom_css); ?></textarea>

            <p><input type="submit" name="wp_subtitle_save_settings" value="Save Settings" class="button-primary"></p>
        </form>
    </div>
    <?php
}

// Output custom CSS
add_action('wp_head', 'wp_subtitle_custom_css');
function wp_subtitle_custom_css() {
    $custom_css = get_option('wp_subtitle_custom_css', '');
    if (!empty($custom_css)) {
        echo '<style>' . esc_html($custom_css) . '</style>';
    }
}