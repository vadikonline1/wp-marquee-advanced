<?php
/*
Plugin Name: WP Marquee Advanced
Plugin URI:  https://github.com/vadikonline1/wp-marquee-advanced/
Description: Banner animat cu scroll infinit și setări complete în admin (text, culoare, font, dimensiune, fundal, viteza, margini).
Version:     1.1
Author:      Steel..xD
Author URI:  https://github.com/vadikonline1/wp-marquee-advanced/
License:     GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// =======================
// Înregistrare opțiuni
// =======================
function wp_marquee_adv_register_settings() {
    // Register options with default values
    register_setting('marquee_options_group', 'marquee_text', array(
        'default' => 'Acesta este textul meu animat!',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('marquee_options_group', 'marquee_speed', array(
        'default' => 15,
        'sanitize_callback' => 'absint'
    ));
    register_setting('marquee_options_group', 'marquee_color', array(
        'default' => '#333333',
        'sanitize_callback' => 'sanitize_hex_color'
    ));
    register_setting('marquee_options_group', 'marquee_bg', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color'
    ));
    register_setting('marquee_options_group', 'marquee_font', array(
        'default' => 'Arial, sans-serif',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('marquee_options_group', 'marquee_size', array(
        'default' => 20,
        'sanitize_callback' => 'absint'
    ));
    register_setting('marquee_options_group', 'marquee_padding', array(
        'default' => 10,
        'sanitize_callback' => 'absint'
    ));
    register_setting('marquee_options_group', 'marquee_enabled', array(
        'default' => '1',
        'sanitize_callback' => 'wp_marquee_adv_sanitize_checkbox'
    ));
    register_setting('marquee_options_group', 'marquee_display_type', array(
        'default' => 'all',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('marquee_options_group', 'marquee_selected_pages', array(
        'default' => array(),
        'sanitize_callback' => 'wp_marquee_adv_sanitize_array'
    ));
    
    add_settings_section('marquee_main_section', 'Setări Marquee Avansate', null, 'marquee-settings');
    
    add_settings_field('marquee_enabled', 'Activează Banner', 'wp_marquee_enabled_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_text', 'Text Banner', 'wp_marquee_text_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_speed', 'Viteza (secunde)', 'wp_marquee_speed_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_color', 'Culoare Text', 'wp_marquee_color_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_bg', 'Culoare Fundal', 'wp_marquee_bg_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_font', 'Font Text', 'wp_marquee_font_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_size', 'Dimensiune Text (px)', 'wp_marquee_size_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_padding', 'Padding Banner (px)', 'wp_marquee_padding_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_display_type', 'Afișare pe pagini', 'wp_marquee_display_type_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_selected_pages', 'Selectează Pagini', 'wp_marquee_selected_pages_field', 'marquee-settings', 'marquee_main_section');
}
add_action('admin_init', 'wp_marquee_adv_register_settings');

// Sanitization functions
function wp_marquee_adv_sanitize_checkbox($input) {
    return isset($input) ? '1' : '0';
}

function wp_marquee_adv_sanitize_array($input) {
    if (!is_array($input)) {
        return array();
    }
    return array_map('absint', $input);
}

// =======================
// Pagina de setări în admin
// =======================
function wp_marquee_adv_admin_page() {
    add_options_page('Marquee Advanced', 'Marquee Advanced', 'manage_options', 'marquee-settings', 'wp_marquee_adv_settings_page');
}
add_action('admin_menu', 'wp_marquee_adv_admin_page');

function wp_marquee_adv_settings_page() {
    ?>
    <div class="wrap">
        <h1>Setări Marquee Advanced</h1>
        <form method="post" action="options.php">
            <?php settings_fields('marquee_options_group'); ?>
            <?php do_settings_sections('marquee-settings'); ?>
            <?php submit_button(); ?>
        </form>

        <h2>Previzualizare Live</h2>
        <div id="marquee-preview" style="border:1px solid #333; padding:10px; overflow:hidden; width:100%; box-sizing:border-box;">
            <div id="marquee-preview-track" style="display:flex; width:max-content; animation-name:marqueeScroll; animation-timing-function:linear; animation-iteration-count:infinite;">
                <span id="marquee-preview-text" style="white-space:nowrap; padding-right:50px;">
                    <?php echo esc_html(get_option('marquee_text', 'Acesta este textul meu animat!')); ?>
                </span>
                <span id="marquee-preview-text2" style="white-space:nowrap; padding-right:50px;">
                    <?php echo esc_html(get_option('marquee_text', 'Acesta este textul meu animat!')); ?>
                </span>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const textField = document.querySelector('input[name="marquee_text"]');
        const speedField = document.querySelector('input[name="marquee_speed"]');
        const colorField = document.querySelector('input[name="marquee_color"]');
        const bgField = document.querySelector('input[name="marquee_bg"]');
        const fontField = document.querySelector('input[name="marquee_font"]');
        const sizeField = document.querySelector('input[name="marquee_size"]');
        const paddingField = document.querySelector('input[name="marquee_padding"]');
        
        const preview = document.getElementById('marquee-preview');
        const previewTrack = document.getElementById('marquee-preview-track');
        const previewText = document.getElementById('marquee-preview-text');
        const previewText2 = document.getElementById('marquee-preview-text2');

        function updatePreview() {
            let text = textField ? textField.value : '<?php echo esc_js(get_option("marquee_text", "Acesta este textul meu animat!")); ?>';
            let speed = speedField ? speedField.value : <?php echo esc_js(get_option("marquee_speed", 15)); ?>;
            let color = colorField ? colorField.value : '<?php echo esc_js(get_option("marquee_color", "#333333")); ?>';
            let bg = bgField ? bgField.value : '<?php echo esc_js(get_option("marquee_bg", "#ffffff")); ?>';
            let font = fontField ? fontField.value : '<?php echo esc_js(get_option("marquee_font", "Arial, sans-serif")); ?>';
            let size = sizeField ? sizeField.value : <?php echo esc_js(get_option("marquee_size", 20)); ?>;
            let padding = paddingField ? paddingField.value : <?php echo esc_js(get_option("marquee_padding", 10)); ?>;

            preview.style.background = bg;
            preview.style.padding = padding + 'px';
            previewText.textContent = text;
            previewText2.textContent = text;
            previewText.style.color = color;
            previewText2.style.color = color;
            previewText.style.fontFamily = font;
            previewText2.style.fontFamily = font;
            previewText.style.fontSize = size + 'px';
            previewText2.style.fontSize = size + 'px';
            
            // Update animation duration
            previewTrack.style.animationDuration = speed + 's';
            
            // Restart animation
            previewTrack.style.animation = 'none';
            void previewTrack.offsetWidth; // Trigger reflow
            previewTrack.style.animation = 'marqueeScroll ' + speed + 's linear infinite';
        }

        // Add event listeners to all fields
        const fields = [textField, speedField, colorField, bgField, fontField, sizeField, paddingField];
        fields.forEach(function(field) {
            if (field) {
                field.addEventListener('input', updatePreview);
            }
        });

        // Toggle page selection based on display type
        const displayTypeField = document.querySelector('select[name="marquee_display_type"]');
        const pageSelectionDiv = document.querySelector('#marquee_page_selection');
        
        if (displayTypeField && pageSelectionDiv) {
            function togglePageSelection() {
                if (displayTypeField.value === 'selected') {
                    pageSelectionDiv.style.display = 'block';
                } else {
                    pageSelectionDiv.style.display = 'none';
                }
            }
            
            displayTypeField.addEventListener('change', togglePageSelection);
            togglePageSelection(); // Initial state
        }

        updatePreview(); // inițializare
    });
    
    // Define the animation in JavaScript too for preview
    const style = document.createElement('style');
    style.textContent = '@keyframes marqueeScroll { 0% { transform:translateX(0); } 100% { transform:translateX(-50%); } }';
    document.head.appendChild(style);
    </script>
    <?php
}

// =======================
// Field-uri admin
// =======================
function wp_marquee_enabled_field() {
    $enabled = get_option('marquee_enabled', '1');
    echo '<input type="checkbox" name="marquee_enabled" value="1" ' . checked('1', $enabled, false) . '> Activează afișarea bannerului';
}

function wp_marquee_text_field() { 
    echo '<input type="text" name="marquee_text" value="' . esc_attr(get_option('marquee_text', 'Acesta este textul meu animat!')) . '" style="width:50%;">'; 
}

function wp_marquee_speed_field() { 
    echo '<input type="number" name="marquee_speed" value="' . esc_attr(get_option('marquee_speed', 15)) . '" min="1" max="60">'; 
}

function wp_marquee_color_field() { 
    echo '<input type="color" name="marquee_color" value="' . esc_attr(get_option('marquee_color', '#333333')) . '">'; 
}

function wp_marquee_bg_field() { 
    echo '<input type="color" name="marquee_bg" value="' . esc_attr(get_option('marquee_bg', '#ffffff')) . '">'; 
}

function wp_marquee_font_field() { 
    echo '<input type="text" name="marquee_font" value="' . esc_attr(get_option('marquee_font', 'Arial, sans-serif')) . '" style="width:50%;">'; 
}

function wp_marquee_size_field() { 
    echo '<input type="number" name="marquee_size" value="' . esc_attr(get_option('marquee_size', 20)) . '" min="10" max="100">'; 
}

function wp_marquee_padding_field() { 
    echo '<input type="number" name="marquee_padding" value="' . esc_attr(get_option('marquee_padding', 10)) . '" min="0" max="100">'; 
}

function wp_marquee_display_type_field() {
    $display_type = get_option('marquee_display_type', 'all');
    ?>
    <select name="marquee_display_type">
        <option value="all" <?php selected($display_type, 'all'); ?>>Toate paginile</option>
        <option value="selected" <?php selected($display_type, 'selected'); ?>>Doar pagini selectate</option>
    </select>
    <?php
}

function wp_marquee_selected_pages_field() {
    $selected_pages = get_option('marquee_selected_pages', array());
    $pages = get_pages(array(
        'post_status' => 'publish',
        'sort_column' => 'post_title',
        'sort_order' => 'ASC'
    ));
    
    echo '<div id="marquee_page_selection" style="margin-top:10px; max-height:200px; overflow-y:auto; border:1px solid #ddd; padding:10px;">';
    if (!empty($pages)) {
        foreach ($pages as $page) {
            $checked = in_array($page->ID, $selected_pages) ? 'checked' : '';
            echo '<label style="display:block; margin-bottom:5px;">';
            echo '<input type="checkbox" name="marquee_selected_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . '> ';
            echo esc_html($page->post_title) . ' (ID: ' . $page->ID . ')';
            echo '</label>';
        }
    } else {
        echo '<p>Nu există pagini publicate.</p>';
    }
    echo '</div>';
}

// =======================
// Shortcode cu setări dinamice
// =======================
function wp_marquee_adv_shortcode($atts, $content = null) {
    // Check if marquee is enabled
    if (get_option('marquee_enabled', '1') !== '1') {
        return '';
    }
    
    // Check display conditions
    if (!wp_marquee_adv_should_display()) {
        return '';
    }
    
    $text = $content ? $content : get_option('marquee_text', 'Acesta este textul meu animat!');
    $speed = get_option('marquee_speed', 15);
    $color = get_option('marquee_color', '#333');
    $bg = get_option('marquee_bg', '#fff');
    $font = get_option('marquee_font', 'Arial, sans-serif');
    $size = get_option('marquee_size', 20);
    $padding = get_option('marquee_padding', 10);

    // Sanitize and escape all outputs
    $bg = esc_attr($bg);
    $padding = absint($padding);
    $speed = absint($speed);
    $color = esc_attr($color);
    $font = esc_attr($font);
    $size = absint($size);
    $text = esc_html($text);

    return '<div class="marquee-container" style="background:' . $bg . '; padding:' . $padding . 'px; box-sizing:border-box;">
                <div class="marquee-track" style="animation-duration:' . $speed . 's;">
                    <span class="marquee-text" style="color:' . $color . '; font-family:' . $font . '; font-size:' . $size . 'px;">' . $text . '</span>
                    <span class="marquee-text" style="color:' . $color . '; font-family:' . $font . '; font-size:' . $size . 'px;">' . $text . '</span>
                </div>
            </div>';
}
add_shortcode('marquee', 'wp_marquee_adv_shortcode');

// =======================
// Check if marquee should display
// =======================
function wp_marquee_adv_should_display() {
    $display_type = get_option('marquee_display_type', 'all');
    
    if ($display_type === 'all') {
        return true;
    }
    
    if ($display_type === 'selected') {
        $selected_pages = get_option('marquee_selected_pages', array());
        
        // Check if we're on a single page
        if (is_page()) {
            $current_page_id = get_queried_object_id();
            return in_array($current_page_id, $selected_pages);
        }
        
        // For other types (posts, archives, etc.), don't display
        return false;
    }
    
    return false;
}

// =======================
// CSS dinamic
// =======================
function wp_marquee_adv_css() {
    // Check if marquee is enabled
    if (get_option('marquee_enabled', '1') !== '1') {
        return;
    }
    
    // Check display conditions
    if (!wp_marquee_adv_should_display()) {
        return;
    }
    
    echo '<style>
    .marquee-container { 
        width:100%; 
        overflow:hidden; 
        cursor:pointer; 
        box-sizing:border-box; 
    }
    .marquee-track { 
        display:flex; 
        width:max-content; 
        animation-name:marqueeScroll; 
        animation-timing-function:linear; 
        animation-iteration-count:infinite; 
    }
    .marquee-text { 
        white-space:nowrap; 
        padding-right:50px; 
    }
    @keyframes marqueeScroll { 
        0% { 
            transform:translateX(0); 
        } 
        100% { 
            transform:translateX(-50%); 
        } 
    }
    .marquee-container:hover .marquee-track { 
        animation-play-state:paused; 
    }
    </style>';
}
add_action('wp_head', 'wp_marquee_adv_css');

// =======================
// Afișare automată sub header
// =======================
function wp_marquee_adv_display() {
    // Check if marquee is enabled
    if (get_option('marquee_enabled', '1') !== '1') {
        return;
    }
    
    // Check display conditions
    if (!wp_marquee_adv_should_display()) {
        return;
    }
    
    echo do_shortcode('[marquee]');
}
add_action('wp_body_open', 'wp_marquee_adv_display');

// =======================
// Admin CSS
// =======================
function wp_marquee_adv_admin_styles() {
    echo '<style>
    #marquee_page_selection label {
        display: block;
        margin-bottom: 5px;
        padding: 3px 0;
    }
    #marquee_page_selection input[type="checkbox"] {
        margin-right: 8px;
    }
    </style>';
}
add_action('admin_head', 'wp_marquee_adv_admin_styles');
