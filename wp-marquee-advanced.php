<?php
/*
Plugin Name: WP Marquee Advanced
Plugin URI:  https://github.com/vadikonline1/wp-marquee-advanced/
Description: Banner animat cu scroll infinit și setări complete în admin (text, culoare, font, dimensiune, fundal, viteza, margini).
Version:     1.3
Author:      Steel..xD
Author URI:  https://github.com/vadikonline1/wp-marquee-advanced/
License:     GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// =======================
// Plugin Meta Links
// =======================
add_filter('plugin_row_meta', 'wp_marquee_plugin_row_meta', 10, 4);
function wp_marquee_plugin_row_meta($links, $file, $plugin_data, $status) {
    if ($file === plugin_basename(__FILE__)) {
        $links[] = '<a href="https://github.com/vadikonline1/wp-marquee-advanced/" target="_blank" rel="noopener noreferrer">📖 Documentație</a>';
        $links[] = '<a href="https://github.com/vadikonline1/wp-marquee-advanced/issues" target="_blank" rel="noopener noreferrer">🐛 Raportează Problemă</a>';
        $links[] = '<a href="https://github.com/vadikonline1/wp-marquee-advanced/" target="_blank" rel="noopener noreferrer">⭐ Rating</a>';
    }
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wp_marquee_plugin_action_links');
function wp_marquee_plugin_action_links($actions) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=marquee-settings') . '" style="font-weight:bold;color:#2271b1;">⚙️ Setări</a>';
    array_unshift($actions, $settings_link);
    return $actions;
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
        'default' => 'after_menu',
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
    register_setting('marquee_options_group', 'marquee_click_action', array(
        'default' => 'none',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('marquee_options_group', 'marquee_redirect_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    register_setting('marquee_options_group', 'marquee_redirect_page', array(
        'default' => '',
        'sanitize_callback' => 'absint'
    ));
    register_setting('marquee_options_group', 'marquee_redirect_target', array(
        'default' => '_self',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('marquee_options_group', 'marquee_zindex', array(
        'default' => '999',
        'sanitize_callback' => 'absint'
    ));
    
    add_settings_section('marquee_main_section', 'Setări Marquee Avansate', null, 'marquee-settings');
    add_settings_section('marquee_design_section', '🎨 Setări Design Avansate', null, 'marquee-settings');
    add_settings_section('marquee_position_section', '📍 Poziție & Comportament', null, 'marquee-settings');
    add_settings_section('marquee_click_section', '🔗 Acțiune la Click', null, 'marquee-settings');
    
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
    add_settings_field('marquee_zindex', 'Z-Index', 'wp_marquee_zindex_field', 'marquee-settings', 'marquee_design_section');
    
    // Position settings
    add_settings_field('marquee_position', 'Poziție Afișare', 'wp_marquee_position_field', 'marquee-settings', 'marquee_position_section');
    
    // Click action settings
    add_settings_field('marquee_click_action', 'Acțiune la Click', 'wp_marquee_click_action_field', 'marquee-settings', 'marquee_click_section');
    add_settings_field('marquee_redirect_url', 'URL Redirect', 'wp_marquee_redirect_url_field', 'marquee-settings', 'marquee_click_section');
    add_settings_field('marquee_redirect_page', 'Pagina Redirect', 'wp_marquee_redirect_page_field', 'marquee-settings', 'marquee_click_section');
    add_settings_field('marquee_redirect_target', 'Target Redirect', 'wp_marquee_redirect_target_field', 'marquee-settings', 'marquee_click_section');
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
                    <?php submit_button('💾 Salvează Setări', 'primary', 'submit', true); ?>
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
                            <li>Poziție: <span id="preview-position-info"><?php echo esc_html(ucfirst(str_replace('_', ' ', get_option('marquee_position', 'after_menu')))); ?></span></li>
                            <li>Click Action: <span id="preview-click-info"><?php 
                                $click_action = get_option('marquee_click_action', 'none');
                                echo $click_action === 'none' ? 'Niciuna' : ucfirst($click_action);
                            ?></span></li>
                            <li>Bannerul are z-index: <span id="preview-zindex"><?php echo esc_html(get_option('marquee_zindex', '999')); ?></span></li>
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
        const positionField = document.querySelector('select[name="marquee_position"]');
        const clickActionField = document.querySelector('select[name="marquee_click_action"]');
        const redirectUrlField = document.querySelector('input[name="marquee_redirect_url"]');
        const redirectPageField = document.querySelector('select[name="marquee_redirect_page"]');
        const redirectTargetField = document.querySelector('select[name="marquee_redirect_target"]');
        const zindexField = document.querySelector('input[name="marquee_zindex"]');
        
        const preview = document.getElementById('marquee-preview');
        const previewTrack = document.getElementById('marquee-preview-track');
        const previewText = document.getElementById('marquee-preview-text');
        const previewText2 = document.getElementById('marquee-preview-text2');
        const previewPositionInfo = document.getElementById('preview-position-info');
        const previewClickInfo = document.getElementById('preview-click-info');
        const previewZindex = document.getElementById('preview-zindex');

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
            let position = positionField ? positionField.value : '<?php echo esc_js(get_option("marquee_position", "after_menu")); ?>';
            let clickAction = clickActionField ? clickActionField.value : '<?php echo esc_js(get_option("marquee_click_action", "none")); ?>';
            let zindex = zindexField ? zindexField.value : <?php echo esc_js(get_option("marquee_zindex", "999")); ?>;

            // Update preview element styles
            preview.style.background = bg;
            preview.style.padding = padding + 'px';
            preview.style.boxShadow = shadow ? '0 4px 12px rgba(0,0,0,0.1)' : 'none';
            preview.style.border = border ? '1px solid ' + borderColor : 'none';
            preview.style.zIndex = zindex;
            
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
            
            // Update cursor based on click action
            if (clickAction !== 'none') {
                preview.style.cursor = 'pointer';
            } else {
                preview.style.cursor = 'default';
            }
            
            // Add/remove hover effect class
            if (hoverEffect) {
                preview.classList.add('has-hover-effect');
            } else {
                preview.classList.remove('has-hover-effect');
            }
            
            // Update info texts
            previewPositionInfo.textContent = position.replace('_', ' ');
            previewClickInfo.textContent = clickAction === 'none' ? 'Niciuna' : clickAction;
            previewZindex.textContent = zindex;
            
            // Restart animation
            previewTrack.style.animation = 'none';
            void previewTrack.offsetWidth; // Trigger reflow
            previewTrack.style.animation = 'marqueeScroll ' + speed + 's linear infinite';
        }

        // Add event listeners to all fields
        const fields = [
            textField, speedField, colorField, bgField, fontField, 
            sizeField, paddingField, shadowField, borderField, 
            borderColorField, textShadowField, hoverEffectField,
            positionField, clickActionField, redirectUrlField, 
            redirectPageField, redirectTargetField, zindexField
        ];
        
        fields.forEach(function(field) {
            if (field) {
                if (field.type === 'checkbox' || field.tagName === 'SELECT') {
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

        // Toggle redirect fields based on click action
        const redirectFields = document.querySelectorAll('.marquee-redirect-field');
        const clickActionFields = document.querySelector('.marquee-click-action-fields');
        
        if (clickActionField && clickActionFields) {
            function toggleRedirectFields() {
                const action = clickActionField.value;
                if (action === 'redirect_url' || action === 'redirect_page') {
                    clickActionFields.style.display = 'block';
                    redirectFields.forEach(function(field) {
                        field.style.display = action === 'redirect_page' && field.id.includes('url') ? 'none' : 'block';
                        if (field.id.includes('page')) {
                            field.style.display = action === 'redirect_page' ? 'block' : 'none';
                        }
                    });
                } else {
                    clickActionFields.style.display = 'none';
                }
            }
            
            clickActionField.addEventListener('change', toggleRedirectFields);
            toggleRedirectFields(); // Initial state
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

function wp_marquee_zindex_field() {
    echo '<input type="number" name="marquee_zindex" value="' . esc_attr(get_option('marquee_zindex', '999')) . '" min="1" max="9999" class="small-text">';
    echo '<p class="description">Controlul stratului de afișare (z-index). Valori mai mari = deasupra altor elemente.</p>';
}

// Position field
function wp_marquee_position_field() {
    $position = get_option('marquee_position', 'after_menu');
    ?>
    <select name="marquee_position" class="regular-text">
        <option value="body_open" <?php selected($position, 'body_open'); ?>>Începutul paginii (după tag-ul body)</option>
        <option value="after_menu" <?php selected($position, 'after_menu'); ?>>După meniu (în header, sub meniu)</option>
        <option value="before_content" <?php selected($position, 'before_content'); ?>>Înainte de conținut</option>
        <option value="shortcode" <?php selected($position, 'shortcode'); ?>>Doar prin shortcode [marquee]</option>
    </select>
    <p class="description">
        <strong>Începutul paginii:</strong> Bannerul apare la început, imediat după deschiderea tag-ului body.<br>
        <strong>După meniu:</strong> Bannerul apare în header, imediat după meniul principal (sub meniu).<br>
        <strong>Înainte de conținut:</strong> Bannerul apare înainte de conținutul principal al paginii.<br>
        <strong>Doar prin shortcode:</strong> Bannerul apare doar unde este inserat manual shortcode-ul [marquee].
    </p>
    <?php
}

// Click action fields
function wp_marquee_click_action_field() {
    $click_action = get_option('marquee_click_action', 'none');
    ?>
    <select name="marquee_click_action" class="regular-text">
        <option value="none" <?php selected($click_action, 'none'); ?>>Niciuna</option>
        <option value="redirect_url" <?php selected($click_action, 'redirect_url'); ?>>Redirect către URL</option>
        <option value="redirect_page" <?php selected($click_action, 'redirect_page'); ?>>Redirect către pagină</option>
    </select>
    <p class="description">Alege ce se întâmplă când utilizatorul apasă pe banner.</p>
    <?php
}

function wp_marquee_redirect_url_field() {
    echo '<div class="marquee-redirect-field" id="marquee-redirect-url">';
    echo '<input type="url" name="marquee_redirect_url" value="' . esc_url(get_option('marquee_redirect_url', '')) . '" class="regular-text" placeholder="https://exemplu.com/pagina">';
    echo '<p class="description">URL-ul către care va fi redirecționat utilizatorul. Exemplu: https://site.com/oferta</p>';
    echo '</div>';
}

function wp_marquee_redirect_page_field() {
    $pages = get_pages(array(
        'post_status' => 'publish',
        'sort_column' => 'post_title',
        'sort_order' => 'ASC'
    ));
    
    echo '<div class="marquee-redirect-field" id="marquee-redirect-page">';
    echo '<select name="marquee_redirect_page" class="regular-text">';
    echo '<option value="">-- Selectează o pagină --</option>';
    foreach ($pages as $page) {
        $selected = selected(get_option('marquee_redirect_page', ''), $page->ID, false);
        echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">Selectează o pagină din site către care va fi redirecționat utilizatorul.</p>';
    echo '</div>';
}

function wp_marquee_redirect_target_field() {
    $redirect_target = get_option('marquee_redirect_target', '_self');
    ?>
    <div class="marquee-redirect-field marquee-click-action-fields" style="margin-top: 10px;">
        <select name="marquee_redirect_target" class="regular-text">
            <option value="_self" <?php selected($redirect_target, '_self'); ?>>Aceeași filă (_self)</option>
            <option value="_blank" <?php selected($redirect_target, '_blank'); ?>>Filă nouă (_blank)</option>
        </select>
        <p class="description">Cum să se deschidă linkul: în aceeași filă sau în filă nouă.</p>
    </div>
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
    $zindex = get_option('marquee_zindex', '999');
    
    // Click action settings
    $click_action = get_option('marquee_click_action', 'none');
    $redirect_url = get_option('marquee_redirect_url', '');
    $redirect_page = get_option('marquee_redirect_page', '');
    $redirect_target = get_option('marquee_redirect_target', '_self');
    
    // Prepare redirect URL
    $final_redirect_url = '';
    if ($click_action === 'redirect_url' && !empty($redirect_url)) {
        $final_redirect_url = esc_url($redirect_url);
    } elseif ($click_action === 'redirect_page' && !empty($redirect_page)) {
        $final_redirect_url = get_permalink($redirect_page);
    }
    
    // Prepare styles
    $container_style = array();
    $container_style[] = 'background:' . esc_attr($bg);
    $container_style[] = 'padding:' . absint($padding) . 'px';
    $container_style[] = 'z-index:' . absint($zindex);
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
    
    // Prepare click attributes
    $click_attributes = '';
    if (!empty($final_redirect_url)) {
        $click_attributes = 'onclick="window.open(\'' . esc_js($final_redirect_url) . '\', \'' . esc_attr($redirect_target) . '\')" style="cursor:pointer;"';
    }

    // Build output
    $output = sprintf(
        '<div class="%s" style="%s" %s>
            <div class="marquee-track" style="%s">
                <span class="marquee-text" style="%s">%s</span>
                <span class="marquee-text" style="%s">%s</span>
            </div>
        </div>',
        esc_attr($container_class),
        implode(';', $container_style),
        $click_attributes,
        esc_attr($track_style),
        implode(';', $text_style),
        esc_html($text),
        implode(';', $text_style),
        esc_html($text)
    );
    
    // Add click event via JavaScript for better compatibility
    if (!empty($final_redirect_url)) {
        $output .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var marquee = document.querySelector(".marquee-container");
            if (marquee) {
                marquee.addEventListener("click", function() {
                    window.open("' . esc_js($final_redirect_url) . '", "' . esc_js($redirect_target) . '");
                });
            }
        });
        </script>';
    }
    
    return $output;
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
    
    $zindex = get_option('marquee_zindex', '999');
    
    echo '<style>
    /* WP Marquee Advanced - Professional Styles */
    .marquee-container { 
        width: 100%; 
        overflow: hidden; 
        cursor: pointer; 
        box-sizing: border-box;
        position: relative;
        z-index: ' . absint($zindex) . ';
        transition: all 0.3s ease;
        margin: 0;
        clear: both;
    }
    
    /* Specific positioning for different locations */
    .marquee-position-after-menu {
        position: relative;
        z-index: ' . (absint($zindex) - 1) . ';
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
    
    /* Clickable cursor */
    .marquee-clickable {
        cursor: pointer !important;
    }
    
    .marquee-clickable:hover {
        opacity: 0.95;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .marquee-text {
            padding-right: 30px;
            font-size: calc(100% - 2px) !important;
        }
        
        .marquee-container {
            padding: 8px !important;
        }
    }
    
    @media (max-width: 480px) {
        .marquee-text {
            padding-right: 20px;
            font-size: calc(100% - 4px) !important;
        }
        
        .marquee-container {
            padding: 6px !important;
        }
    }
    
    /* Ensure proper positioning after menu */
    body.admin-bar .marquee-container {
        top: 0;
    }
    
    /* Fix for theme conflicts */
    .marquee-container * {
        box-sizing: border-box;
    }
    </style>';
}
add_action('wp_head', 'wp_marquee_adv_css');

// =======================
// Afișare automată în funcție de poziția selectată
// =======================

// Hook pentru după meniu (acesta este noul hook principal)
function wp_marquee_adv_display_after_menu() {
    $position = get_option('marquee_position', 'after_menu');
    
    if ($position !== 'after_menu') {
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

// Hook pentru începutul paginii
function wp_marquee_adv_display_body_open() {
    $position = get_option('marquee_position', 'after_menu');
    
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

// Hook pentru înainte de conținut
function wp_marquee_adv_display_before_content() {
    $position = get_option('marquee_position', 'after_menu');
    
    if ($position !== 'before_content') {
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

// =======================
// Register display hooks
// =======================
add_action('wp_body_open', 'wp_marquee_adv_display_body_open');

// Hook pentru după meniu - acesta trebuie să fie adăugat în tema, dar oferim soluții
function wp_marquee_adv_init_hooks() {
    // Adaugă hook-ul după meniu - tema trebuie să aibă acest hook
    add_action('wp_marquee_after_menu', 'wp_marquee_adv_display_after_menu');
    
    // Adaugă hook-ul înainte de conținut
    add_action('wp_marquee_before_content', 'wp_marquee_adv_display_before_content');
    
    // Dacă tema nu are hook-ul după meniu, încercăm să injectăm bannerul
    if (!has_action('wp_marquee_after_menu')) {
        // Încearcă să injecteze bannerul după meniul WordPress
        add_filter('wp_nav_menu', 'wp_marquee_adv_inject_after_menu', 100, 2);
    }
}
add_action('init', 'wp_marquee_adv_init_hooks');

// Fallback pentru a injecta bannerul după meniu
function wp_marquee_adv_inject_after_menu($nav_menu, $args) {
    // Verifică dacă este meniul principal (de obicei 'primary' sau 'main')
    if (isset($args->theme_location) && in_array($args->theme_location, array('primary', 'main', 'header'))) {
        ob_start();
        wp_marquee_adv_display_after_menu();
        $marquee = ob_get_clean();
        return $nav_menu . $marquee;
    }
    return $nav_menu;
}

// Fallback pentru a injecta bannerul înainte de conținut
function wp_marquee_adv_inject_before_content($content) {
    // Verifică dacă suntem în loop-ul principal și dacă poziția este corectă
    if (is_main_query() && get_option('marquee_position', 'after_menu') === 'before_content') {
        ob_start();
        wp_marquee_adv_display_before_content();
        $marquee = ob_get_clean();
        return $marquee . $content;
    }
    return $content;
}
add_filter('the_content', 'wp_marquee_adv_inject_before_content', 5);

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
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        border: 1px solid #e0e0e0;
    }
    
    .wp-marquee-admin-preview {
        background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
        padding: 25px;
        border-radius: 10px;
        border: 1px solid #d0d7de;
        position: sticky;
        top: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .wp-marquee-admin-preview h2 {
        margin-top: 0;
        color: #2c3e50;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 20px;
    }
    
    .preview-container {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-top: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .marquee-preview-wrapper {
        width: 100%;
        overflow: hidden;
        border-radius: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        border: 2px dashed #e0e0e0;
    }
    
    .marquee-preview-wrapper:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        border-color: #4dabf7;
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
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .preview-info {
        margin-top: 25px;
        padding: 20px;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-radius: 8px;
        border-left: 4px solid #1976d2;
        color: #1565c0;
    }
    
    .preview-info p {
        margin-top: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .preview-info ul {
        margin-bottom: 0;
        padding-left: 20px;
    }
    
    .preview-info li {
        margin-bottom: 8px;
        line-height: 1.5;
    }
    
    /* Section styling */
    h2.section-title {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 15px 20px;
        border-left: 4px solid #4dabf7;
        margin: 35px 0 25px 0;
        border-radius: 0 8px 8px 0;
        color: #2c3e50;
        font-size: 18px;
    }
    
    /* Field styling */
    .form-table th {
        padding: 20px 15px 20px 0;
        width: 220px;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .form-table td {
        padding: 18px 15px;
        vertical-align: top;
    }
    
    /* Color picker styling */
    input[type="color"] {
        width: 60px;
        height: 45px;
        padding: 3px;
        border: 2px solid #c5c5c5;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    input[type="color"]:hover {
        border-color: #4dabf7;
        transform: scale(1.05);
    }
    
    /* Checkbox styling */
    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        vertical-align: middle;
    }
    
    /* Input styling */
    input[type="text"],
    input[type="number"],
    input[type="url"],
    select {
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        padding: 10px 12px;
        transition: all 0.3s ease;
    }
    
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="url"]:focus,
    select:focus {
        border-color: #4dabf7;
        box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.2);
        outline: none;
    }
    
    /* Submit button */
    .button-primary {
        background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(25, 118, 210, 0.2);
    }
    
    .button-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(25, 118, 210, 0.3);
    }
    
    /* Description text */
    .description {
        color: #6c757d;
        font-style: normal;
        margin-top: 8px;
        display: block;
        line-height: 1.5;
        font-size: 13px;
    }
    
    /* Responsive admin */
    @media (max-width: 782px) {
        .wp-marquee-admin-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .form-table th {
            width: auto;
            padding: 15px 0 5px 0;
            display: block;
            border-bottom: none;
        }
        
        .form-table td {
            padding: 5px 0 20px 0;
            display: block;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .form-table tr:last-child td {
            border-bottom: none;
        }
    }
    
    /* Plugin info box */
    .marquee-plugin-info {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border: 1px solid #81c784;
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
        color: #2e7d32;
    }
    
    .marquee-plugin-info a {
        color: #1b5e20;
        text-decoration: none;
        font-weight: 600;
    }
    
    .marquee-plugin-info a:hover {
        text-decoration: underline;
    }
    </style>
    <?php
}
add_action('admin_head', 'wp_marquee_adv_admin_styles');

// =======================
// Add plugin info box
// =======================
function wp_marquee_adv_admin_notice() {
    $screen = get_current_screen();
    if ($screen->id === 'settings_page_marquee-settings') {
        ?>
        <div class="notice notice-info marquee-plugin-info">
            <p>
                <strong>💡 Sugestie pentru dezvoltatori:</strong> Pentru a afișa bannerul <strong>după meniu</strong>, adaugă acest cod în fișierul header.php al temei tale, 
                imediat după codul meniului: <code>&lt;?php do_action('wp_marquee_after_menu'); ?&gt;</code>
            </p>
            <p>
                <strong>🔗 Link-uri utile:</strong> 
                <a href="https://github.com/vadikonline1/wp-marquee-advanced/" target="_blank">GitHub</a> | 
                <a href="https://github.com/vadikonline1/wp-marquee-advanced/issues" target="_blank">Support</a> | 
                <a href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=marquee-advanced&TB_iframe=true&width=772&height=800'); ?>" class="thickbox">Plugin Info</a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'wp_marquee_adv_admin_notice');
