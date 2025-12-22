<?php
/*
Plugin Name: WP Marquee Advanced
Plugin URI:  https://github.com/vadikonline1/wp-marquee-advanced/
Description: Banner animat cu scroll infinit și setări complete în admin (text, culoare, font, dimensiune, fundal, viteza, margini).
Version:     1.2
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
    register_setting('marquee_options_group', 'marquee_position', array(
        'default' => 'body_open',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('marquee_options_group', 'marquee_shadow', array(
        'default' => '0',
        'sanitize_callback' => 'wp_marquee_adv_sanitize_checkbox'
    ));
    register_setting('marquee_options_group', 'marquee_border', array(
        'default' => '0',
        'sanitize_callback' => 'wp_marquee_adv_sanitize_checkbox'
    ));
    register_setting('marquee_options_group', 'marquee_border_color', array(
        'default' => '#dddddd',
        'sanitize_callback' => 'sanitize_hex_color'
    ));
    register_setting('marquee_options_group', 'marquee_text_shadow', array(
        'default' => '0',
        'sanitize_callback' => 'wp_marquee_adv_sanitize_checkbox'
    ));
    register_setting('marquee_options_group', 'marquee_hover_effect', array(
        'default' => '1',
        'sanitize_callback' => 'wp_marquee_adv_sanitize_checkbox'
    ));
    
    add_settings_section('marquee_main_section', 'Setări Marquee Avansate', null, 'marquee-settings');
    add_settings_section('marquee_design_section', 'Setări Design Avansate', null, 'marquee-settings');
    add_settings_section('marquee_position_section', 'Poziție Banner', null, 'marquee-settings');
    
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
    
    // Design settings
    add_settings_field('marquee_shadow', 'Umbra Banner', 'wp_marquee_shadow_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_border', 'Border Banner', 'wp_marquee_border_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_border_color', 'Culoare Border', 'wp_marquee_border_color_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_text_shadow', 'Umbra Text', 'wp_marquee_text_shadow_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_hover_effect', 'Efect Hover', 'wp_marquee_hover_effect_field', 'marquee-settings', 'marquee_design_section');
    
    // Position settings
    add_settings_field('marquee_position', 'Poziție Afișare', 'wp_marquee_position_field', 'marquee-settings', 'marquee_position_section');
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
        <h1>🎯 Marquee Advanced - Setări Avansate</h1>
        
        <div class="wp-marquee-admin-container">
            <div class="wp-marquee-admin-main">
                <form method="post" action="options.php" id="marquee-settings-form">
                    <?php settings_fields('marquee_options_group'); ?>
                    <?php do_settings_sections('marquee-settings'); ?>
                    <?php submit_button('Salvează Setări', 'primary', 'submit', true); ?>
                </form>
            </div>
            
            <div class="wp-marquee-admin-preview">
                <h2><span class="dashicons dashicons-visibility"></span> Previzualizare Live</h2>
                <div class="preview-container">
                    <div id="marquee-preview" class="marquee-preview-wrapper">
                        <div id="marquee-preview-track" class="marquee-preview-track">
                            <span id="marquee-preview-text" class="marquee-preview-text">
                                <?php echo esc_html(get_option('marquee_text', 'Acesta este textul meu animat!')); ?>
                            </span>
                            <span id="marquee-preview-text2" class="marquee-preview-text">
                                <?php echo esc_html(get_option('marquee_text', 'Acesta este textul meu animat!')); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="preview-info">
                        <p><strong>💡 Informații:</strong></p>
                        <ul>
                            <li>Bannerul se va afișa conform poziției selectate</li>
                            <li>Hover peste banner va pausa animația</li>
                            <li>Click pe banner pentru a-l copia în clipboard</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('marquee-settings-form');
        const textField = document.querySelector('input[name="marquee_text"]');
        const speedField = document.querySelector('input[name="marquee_speed"]');
        const colorField = document.querySelector('input[name="marquee_color"]');
        const bgField = document.querySelector('input[name="marquee_bg"]');
        const fontField = document.querySelector('input[name="marquee_font"]');
        const sizeField = document.querySelector('input[name="marquee_size"]');
        const paddingField = document.querySelector('input[name="marquee_padding"]');
        const shadowField = document.querySelector('input[name="marquee_shadow"]');
        const borderField = document.querySelector('input[name="marquee_border"]');
        const borderColorField = document.querySelector('input[name="marquee_border_color"]');
        const textShadowField = document.querySelector('input[name="marquee_text_shadow"]');
        const hoverEffectField = document.querySelector('input[name="marquee_hover_effect"]');
        
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
            let shadow = shadowField ? shadowField.checked : <?php echo esc_js(get_option("marquee_shadow", "0") === "1" ? 'true' : 'false'); ?>;
            let border = borderField ? borderField.checked : <?php echo esc_js(get_option("marquee_border", "0") === "1" ? 'true' : 'false'); ?>;
            let borderColor = borderColorField ? borderColorField.value : '<?php echo esc_js(get_option("marquee_border_color", "#dddddd")); ?>';
            let textShadow = textShadowField ? textShadowField.checked : <?php echo esc_js(get_option("marquee_text_shadow", "0") === "1" ? 'true' : 'false'); ?>;
            let hoverEffect = hoverEffectField ? hoverEffectField.checked : <?php echo esc_js(get_option("marquee_hover_effect", "1") === "1" ? 'true' : 'false'); ?>;

            // Update preview element styles
            preview.style.background = bg;
            preview.style.padding = padding + 'px';
            preview.style.boxShadow = shadow ? '0 4px 12px rgba(0,0,0,0.1)' : 'none';
            preview.style.border = border ? '1px solid ' + borderColor : 'none';
            
            previewText.textContent = text;
            previewText2.textContent = text;
            
            previewText.style.color = color;
            previewText2.style.color = color;
            previewText.style.fontFamily = font;
            previewText2.style.fontFamily = font;
            previewText.style.fontSize = size + 'px';
            previewText2.style.fontSize = size + 'px';
            previewText.style.textShadow = textShadow ? '1px 1px 2px rgba(0,0,0,0.2)' : 'none';
            previewText2.style.textShadow = textShadow ? '1px 1px 2px rgba(0,0,0,0.2)' : 'none';
            
            // Update animation duration
            previewTrack.style.animationDuration = speed + 's';
            
            // Add/remove hover effect class
            if (hoverEffect) {
                preview.classList.add('has-hover-effect');
            } else {
                preview.classList.remove('has-hover-effect');
            }
            
            // Restart animation
            previewTrack.style.animation = 'none';
            void previewTrack.offsetWidth; // Trigger reflow
            previewTrack.style.animation = 'marqueeScroll ' + speed + 's linear infinite';
        }

        // Add event listeners to all fields
        const fields = [
            textField, speedField, colorField, bgField, fontField, 
            sizeField, paddingField, shadowField, borderField, 
            borderColorField, textShadowField, hoverEffectField
        ];
        
        fields.forEach(function(field) {
            if (field) {
                if (field.type === 'checkbox') {
                    field.addEventListener('change', updatePreview);
                } else {
                    field.addEventListener('input', updatePreview);
                }
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

        // Copy to clipboard on click
        preview.addEventListener('click', function() {
            const textToCopy = textField ? textField.value : '<?php echo esc_js(get_option("marquee_text", "Acesta este textul meu animat!")); ?>';
            navigator.clipboard.writeText(textToCopy).then(function() {
                const originalTitle = preview.title;
                preview.title = 'Text copiat în clipboard!';
                setTimeout(function() {
                    preview.title = originalTitle;
                }, 2000);
            });
        });

        updatePreview(); // inițializare
    });
    
    // Define the animation in JavaScript too for preview
    const style = document.createElement('style');
    style.textContent = `
        @keyframes marqueeScroll { 
            0% { transform:translateX(0); } 
            100% { transform:translateX(-50%); } 
        }
        .has-hover-effect:hover .marquee-preview-track { 
            animation-play-state: paused !important;
            opacity: 0.9;
        }
    `;
    document.head.appendChild(style);
    </script>
    <?php
}

// =======================
// Field-uri admin
// =======================
function wp_marquee_enabled_field() {
    $enabled = get_option('marquee_enabled', '1');
    echo '<label><input type="checkbox" name="marquee_enabled" value="1" ' . checked('1', $enabled, false) . '> Activează afișarea bannerului</label>';
    echo '<p class="description">Dacă este dezactivat, bannerul nu va apărea nicăieri.</p>';
}

function wp_marquee_text_field() { 
    echo '<input type="text" name="marquee_text" value="' . esc_attr(get_option('marquee_text', 'Acesta este textul meu animat!')) . '" class="regular-text">';
    echo '<p class="description">Textul care va fi afișat în bannerul animat.</p>';
}

function wp_marquee_speed_field() { 
    echo '<input type="number" name="marquee_speed" value="' . esc_attr(get_option('marquee_speed', 15)) . '" min="1" max="60" class="small-text"> secunde';
    echo '<p class="description">Durata unei animații complete (valori mai mici = mai rapid).</p>';
}

function wp_marquee_color_field() { 
    echo '<input type="color" name="marquee_color" value="' . esc_attr(get_option('marquee_color', '#333333')) . '">';
    echo '<p class="description">Culoarea textului din banner.</p>';
}

function wp_marquee_bg_field() { 
    echo '<input type="color" name="marquee_bg" value="' . esc_attr(get_option('marquee_bg', '#ffffff')) . '">';
    echo '<p class="description">Culoarea de fundal a bannerului.</p>';
}

function wp_marquee_font_field() { 
    echo '<input type="text" name="marquee_font" value="' . esc_attr(get_option('marquee_font', 'Arial, sans-serif')) . '" class="regular-text">';
    echo '<p class="description">Fontul textului (ex: Arial, Helvetica, sans-serif).</p>';
}

function wp_marquee_size_field() { 
    echo '<input type="number" name="marquee_size" value="' . esc_attr(get_option('marquee_size', 20)) . '" min="10" max="100" class="small-text"> px';
    echo '<p class="description">Dimensiunea textului în pixeli.</p>';
}

function wp_marquee_padding_field() { 
    echo '<input type="number" name="marquee_padding" value="' . esc_attr(get_option('marquee_padding', 10)) . '" min="0" max="100" class="small-text"> px';
    echo '<p class="description">Spațiu interior al bannerului.</p>';
}

function wp_marquee_display_type_field() {
    $display_type = get_option('marquee_display_type', 'all');
    ?>
    <select name="marquee_display_type" class="regular-text">
        <option value="all" <?php selected($display_type, 'all'); ?>>Toate paginile</option>
        <option value="selected" <?php selected($display_type, 'selected'); ?>>Doar pagini selectate</option>
    </select>
    <p class="description">Selectează unde va apărea bannerul.</p>
    <?php
}

function wp_marquee_selected_pages_field() {
    $selected_pages = get_option('marquee_selected_pages', array());
    $pages = get_pages(array(
        'post_status' => 'publish',
        'sort_column' => 'post_title',
        'sort_order' => 'ASC'
    ));
    
    echo '<div id="marquee_page_selection" style="margin-top:10px; max-height:200px; overflow-y:auto; border:1px solid #ddd; padding:10px; border-radius:4px;">';
    if (!empty($pages)) {
        echo '<p style="margin-top:0; font-weight:bold;">Selectează paginile:</p>';
        foreach ($pages as $page) {
            $checked = in_array($page->ID, $selected_pages) ? 'checked' : '';
            echo '<label style="display:block; margin-bottom:5px; padding:2px 0;">';
            echo '<input type="checkbox" name="marquee_selected_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . '> ';
            echo esc_html($page->post_title) . ' <span style="color:#666;">(ID: ' . $page->ID . ')</span>';
            echo '</label>';
        }
    } else {
        echo '<p>Nu există pagini publicate.</p>';
    }
    echo '</div>';
    echo '<p class="description">Apare doar când opțiunea "Doar pagini selectate" este activată.</p>';
}

// Design fields
function wp_marquee_shadow_field() {
    $shadow = get_option('marquee_shadow', '0');
    echo '<label><input type="checkbox" name="marquee_shadow" value="1" ' . checked('1', $shadow, false) . '> Adaugă umbra bannerului</label>';
    echo '<p class="description">Adaugă un efect de umbră subtil bannerului.</p>';
}

function wp_marquee_border_field() {
    $border = get_option('marquee_border', '0');
    echo '<label><input type="checkbox" name="marquee_border" value="1" ' . checked('1', $border, false) . '> Adaugă bordură</label>';
    echo '<p class="description">Adaugă o bordură subtilă bannerului.</p>';
}

function wp_marquee_border_color_field() {
    echo '<input type="color" name="marquee_border_color" value="' . esc_attr(get_option('marquee_border_color', '#dddddd')) . '">';
    echo '<p class="description">Culoarea bordurii (se aplică doar dacă bordura este activată).</p>';
}

function wp_marquee_text_shadow_field() {
    $text_shadow = get_option('marquee_text_shadow', '0');
    echo '<label><input type="checkbox" name="marquee_text_shadow" value="1" ' . checked('1', $text_shadow, false) . '> Adaugă umbra textului</label>';
    echo '<p class="description">Adaugă un efect de umbră subtil textului.</p>';
}

function wp_marquee_hover_effect_field() {
    $hover_effect = get_option('marquee_hover_effect', '1');
    echo '<label><input type="checkbox" name="marquee_hover_effect" value="1" ' . checked('1', $hover_effect, false) . '> Activează efectul de hover</label>';
    echo '<p class="description">Când utilizatorul trece mouse-ul peste banner, animația se oprește.</p>';
}

// Position field
function wp_marquee_position_field() {
    $position = get_option('marquee_position', 'body_open');
    ?>
    <select name="marquee_position" class="regular-text">
        <option value="body_open" <?php selected($position, 'body_open'); ?>>Începutul paginii (după tag-ul body)</option>
        <option value="before_main_content" <?php selected($position, 'before_main_content'); ?>>Înainte de conținutul principal (înainte de #main-content)</option>
        <option value="shortcode" <?php selected($position, 'shortcode'); ?>>Doar prin shortcode [marquee]</option>
    </select>
    <p class="description">
        <strong>Începutul paginii:</strong> Bannerul apare la început, imediat după deschiderea tag-ului body.<br>
        <strong>Înainte de conținutul principal:</strong> Bannerul apare după header, înainte de div-ul cu id="main-content".<br>
        <strong>Doar prin shortcode:</strong> Bannerul apare doar unde este inserat manual shortcode-ul [marquee].
    </p>
    <?php
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
    
    // Get all settings
    $text = $content ? $content : get_option('marquee_text', 'Acesta este textul meu animat!');
    $speed = get_option('marquee_speed', 15);
    $color = get_option('marquee_color', '#333');
    $bg = get_option('marquee_bg', '#fff');
    $font = get_option('marquee_font', 'Arial, sans-serif');
    $size = get_option('marquee_size', 20);
    $padding = get_option('marquee_padding', 10);
    $shadow = get_option('marquee_shadow', '0') === '1';
    $border = get_option('marquee_border', '0') === '1';
    $border_color = get_option('marquee_border_color', '#dddddd');
    $text_shadow = get_option('marquee_text_shadow', '0') === '1';
    $hover_effect = get_option('marquee_hover_effect', '1') === '1';

    // Prepare styles
    $container_style = array();
    $container_style[] = 'background:' . esc_attr($bg);
    $container_style[] = 'padding:' . absint($padding) . 'px';
    if ($shadow) {
        $container_style[] = 'box-shadow:0 4px 12px rgba(0,0,0,0.1)';
    }
    if ($border) {
        $container_style[] = 'border:1px solid ' . esc_attr($border_color);
    }
    
    $track_style = 'animation-duration:' . absint($speed) . 's';
    
    $text_style = array();
    $text_style[] = 'color:' . esc_attr($color);
    $text_style[] = 'font-family:' . esc_attr($font);
    $text_style[] = 'font-size:' . absint($size) . 'px';
    if ($text_shadow) {
        $text_style[] = 'text-shadow:1px 1px 2px rgba(0,0,0,0.2)';
    }

    // Prepare classes
    $container_class = 'marquee-container';
    if ($hover_effect) {
        $container_class .= ' marquee-hover-effect';
    }

    return sprintf(
        '<div class="%s" style="%s">
            <div class="marquee-track" style="%s">
                <span class="marquee-text" style="%s">%s</span>
                <span class="marquee-text" style="%s">%s</span>
            </div>
        </div>',
        esc_attr($container_class),
        implode(';', $container_style),
        esc_attr($track_style),
        implode(';', $text_style),
        esc_html($text),
        implode(';', $text_style),
        esc_html($text)
    );
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
    
    // Check display conditions for automatic display
    // (shortcode will handle its own conditions)
    if (!wp_marquee_adv_should_display()) {
        return;
    }
    
    echo '<style>
    /* WP Marquee Advanced - Professional Styles */
    .marquee-container { 
        width: 100%; 
        overflow: hidden; 
        cursor: pointer; 
        box-sizing: border-box;
        position: relative;
        z-index: 999;
        transition: all 0.3s ease;
    }
    
    .marquee-track { 
        display: flex; 
        width: max-content; 
        animation-name: wpMarqueeScroll; 
        animation-timing-function: linear; 
        animation-iteration-count: infinite; 
        will-change: transform;
    }
    
    .marquee-text { 
        white-space: nowrap; 
        padding-right: 50px;
        font-weight: 500;
        letter-spacing: 0.02em;
        transition: all 0.3s ease;
    }
    
    @keyframes wpMarqueeScroll { 
        0% { 
            transform: translateX(0); 
        } 
        100% { 
            transform: translateX(-50%); 
        } 
    }
    
    .marquee-hover-effect:hover .marquee-track { 
        animation-play-state: paused; 
    }
    
    .marquee-hover-effect:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
    }
    
    /* Smooth animation */
    .marquee-container {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .marquee-text {
            padding-right: 30px;
            font-size: calc(100% - 2px) !important;
        }
    }
    
    @media (max-width: 480px) {
        .marquee-text {
            padding-right: 20px;
            font-size: calc(100% - 4px) !important;
        }
    }
    </style>';
}
add_action('wp_head', 'wp_marquee_adv_css');

// =======================
// Afișare automată în funcție de poziția selectată
// =======================
function wp_marquee_adv_display_body_open() {
    $position = get_option('marquee_position', 'body_open');
    
    if ($position !== 'body_open') {
        return;
    }
    
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
add_action('wp_body_open', 'wp_marquee_adv_display_body_open');

// =======================
// Afișare înainte de #main-content
// =======================
function wp_marquee_adv_display_before_main_content() {
    $position = get_option('marquee_position', 'body_open');
    
    if ($position !== 'before_main_content') {
        return;
    }
    
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

// Hook pentru a afișa bannerul înainte de #main-content
function wp_marquee_adv_add_before_main_content() {
    add_action('wp_marquee_before_main_content', 'wp_marquee_adv_display_before_main_content');
    
    // Acest hook trebuie să fie apelat în tema ta, dar oferim și o soluție fallback
    if (!has_action('wp_marquee_before_main_content')) {
        // Încearcă să injecteze bannerul înainte de #main-content
        add_filter('the_content', 'wp_marquee_adv_inject_before_content', 999);
    }
}
add_action('init', 'wp_marquee_adv_add_before_main_content');

// Fallback pentru a injecta bannerul înainte de conținut
function wp_marquee_adv_inject_before_content($content) {
    // Verifică dacă suntem în loop-ul principal și dacă poziția este corectă
    if (is_main_query() && get_option('marquee_position', 'body_open') === 'before_main_content') {
        ob_start();
        wp_marquee_adv_display_before_main_content();
        $marquee = ob_get_clean();
        return $marquee . $content;
    }
    return $content;
}

// =======================
// Admin CSS
// =======================
function wp_marquee_adv_admin_styles() {
    ?>
    <style>
    /* Admin Styles */
    .wp-marquee-admin-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }
    
    @media (max-width: 1200px) {
        .wp-marquee-admin-container {
            grid-template-columns: 1fr;
        }
    }
    
    .wp-marquee-admin-main {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .wp-marquee-admin-preview {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        position: sticky;
        top: 20px;
    }
    
    .wp-marquee-admin-preview h2 {
        margin-top: 0;
        color: #23282d;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .preview-container {
        background: #fff;
        padding: 20px;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        margin-top: 15px;
    }
    
    .marquee-preview-wrapper {
        width: 100%;
        overflow: hidden;
        border-radius: 6px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    
    .marquee-preview-wrapper:hover {
        transform: translateY(-2px);
    }
    
    .marquee-preview-wrapper:active {
        transform: translateY(0);
    }
    
    .marquee-preview-track {
        display: flex;
        width: max-content;
        animation-name: marqueeScroll;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
    }
    
    .marquee-preview-text {
        white-space: nowrap;
        padding-right: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .preview-info {
        margin-top: 20px;
        padding: 15px;
        background: #f0f7ff;
        border-radius: 6px;
        border-left: 4px solid #2271b1;
    }
    
    .preview-info p {
        margin-top: 0;
    }
    
    .preview-info ul {
        margin-bottom: 0;
    }
    
    .preview-info li {
        margin-bottom: 5px;
    }
    
    /* Section styling */
    h2.section-title {
        background: #f1f1f1;
        padding: 12px 15px;
        border-left: 4px solid #2271b1;
        margin: 30px 0 20px 0;
    }
    
    /* Field styling */
    .form-table th {
        padding: 20px 10px 20px 0;
        width: 200px;
    }
    
    .form-table td {
        padding: 15px 10px;
    }
    
    /* Color picker styling */
    input[type="color"] {
        width: 50px;
        height: 40px;
        padding: 2px;
        border: 1px solid #8c8f94;
        border-radius: 4px;
        cursor: pointer;
    }
    
    /* Checkbox styling */
    input[type="checkbox"] {
        margin-right: 8px;
    }
    
    /* Description text */
    .description {
        color: #646970;
        font-style: normal;
        margin-top: 4px;
        display: block;
    }
    
    /* Responsive admin */
    @media (max-width: 782px) {
        .wp-marquee-admin-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .form-table th {
            width: auto;
            padding: 10px 0;
            display: block;
        }
        
        .form-table td {
            padding: 5px 0 15px 0;
            display: block;
        }
    }
    </style>
    <?php
}
add_action('admin_head', 'wp_marquee_adv_admin_styles');
