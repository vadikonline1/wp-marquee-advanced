<?php
/*
Plugin Name: WP Marquee Advanced Pro
Plugin URI:  https://github.com/vadikonline1/wp-marquee-advanced/
Description: Banner animat cu scroll infinit și setări complete în admin. Suportă mai multe texte cu URL-uri personalizate și iconițe colorate.
Version:     2.0
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
    register_setting('marquee_options_group', 'marquee_enabled', array(
        'default' => '1',
        'sanitize_callback' => 'wp_marquee_adv_sanitize_checkbox'
    ));
    register_setting('marquee_options_group', 'marquee_speed', array(
        'default' => 20,
        'sanitize_callback' => 'absint'
    ));
    register_setting('marquee_options_group', 'marquee_direction', array(
        'default' => 'left',
        'sanitize_callback' => 'sanitize_text_field'
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
    register_setting('marquee_options_group', 'marquee_zindex', array(
        'default' => '999',
        'sanitize_callback' => 'absint'
    ));
    register_setting('marquee_options_group', 'marquee_text_items', array(
        'default' => wp_json_encode(array(
            array(
                'text' => '🎁 Oferta specială! Click aici pentru detalii',
                'url' => '',
                'target' => '_self',
                'icon_color' => '#ff6b6b',
                'border_color' => '#ff6b6b',
                'text_color' => '#333333'
            ),
            array(
                'text' => '🔥 Promoție limitată! Reducere 50%',
                'url' => '',
                'target' => '_self',
                'icon_color' => '#ffa726',
                'border_color' => '#ffa726',
                'text_color' => '#333333'
            ),
            array(
                'text' => '📢 Noutăți! Vezi cele mai recente produse',
                'url' => '',
                'target' => '_self',
                'icon_color' => '#42a5f5',
                'border_color' => '#42a5f5',
                'text_color' => '#333333'
            )
        )),
        'sanitize_callback' => 'wp_marquee_adv_sanitize_json'
    ));
    register_setting('marquee_options_group', 'marquee_item_padding', array(
        'default' => '10',
        'sanitize_callback' => 'absint'
    ));
    register_setting('marquee_options_group', 'marquee_item_spacing', array(
        'default' => '20',
        'sanitize_callback' => 'absint'
    ));
    register_setting('marquee_options_group', 'marquee_icon_type', array(
        'default' => 'circle',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    add_settings_section('marquee_main_section', 'Setări Generale', null, 'marquee-settings');
    add_settings_section('marquee_items_section', '📋 Elemente Banner (Text + URL)', null, 'marquee-settings');
    add_settings_section('marquee_design_section', '🎨 Setări Design Avansate', null, 'marquee-settings');
    add_settings_section('marquee_position_section', '📍 Poziție & Comportament', null, 'marquee-settings');
    
    add_settings_field('marquee_enabled', 'Activează Banner', 'wp_marquee_enabled_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_speed', 'Viteza animație', 'wp_marquee_speed_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_direction', 'Direcție animație', 'wp_marquee_direction_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_bg', 'Culoare Fundal Banner', 'wp_marquee_bg_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_font', 'Font Text', 'wp_marquee_font_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_size', 'Dimensiune Text (px)', 'wp_marquee_size_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_padding', 'Padding Banner (px)', 'wp_marquee_padding_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_display_type', 'Afișare pe pagini', 'wp_marquee_display_type_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_selected_pages', 'Selectează Pagini', 'wp_marquee_selected_pages_field', 'marquee-settings', 'marquee_main_section');
    
    // Items settings
    add_settings_field('marquee_text_items', 'Elemente Banner', 'wp_marquee_text_items_field', 'marquee-settings', 'marquee_items_section');
    add_settings_field('marquee_item_padding', 'Padding Elemente (px)', 'wp_marquee_item_padding_field', 'marquee-settings', 'marquee_items_section');
    add_settings_field('marquee_item_spacing', 'Spațiu între Elemente (px)', 'wp_marquee_item_spacing_field', 'marquee-settings', 'marquee_items_section');
    add_settings_field('marquee_icon_type', 'Tip Iconiță', 'wp_marquee_icon_type_field', 'marquee-settings', 'marquee_items_section');
    
    // Design settings
    add_settings_field('marquee_shadow', 'Umbra Banner', 'wp_marquee_shadow_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_border', 'Border Banner', 'wp_marquee_border_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_border_color', 'Culoare Border', 'wp_marquee_border_color_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_text_shadow', 'Umbra Text', 'wp_marquee_text_shadow_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_hover_effect', 'Efect Hover', 'wp_marquee_hover_effect_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_zindex', 'Z-Index', 'wp_marquee_zindex_field', 'marquee-settings', 'marquee_design_section');
    
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

function wp_marquee_adv_sanitize_json($input) {
    $decoded = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Return default if invalid JSON
        return wp_json_encode(array(
            array(
                'text' => '🎁 Oferta specială! Click aici pentru detalii',
                'url' => '',
                'target' => '_self',
                'icon_color' => '#ff6b6b',
                'border_color' => '#ff6b6b',
                'text_color' => '#333333'
            )
        ));
    }
    
    // Sanitize each item
    foreach ($decoded as &$item) {
        $item['text'] = sanitize_text_field($item['text']);
        $item['url'] = esc_url_raw($item['url']);
        $item['target'] = in_array($item['target'], array('_self', '_blank')) ? $item['target'] : '_self';
        $item['icon_color'] = sanitize_hex_color($item['icon_color']);
        $item['border_color'] = sanitize_hex_color($item['border_color']);
        $item['text_color'] = sanitize_hex_color($item['text_color']);
    }
    
    return wp_json_encode($decoded);
}

// =======================
// Pagina de setări în admin
// =======================
function wp_marquee_adv_admin_page() {
    add_options_page('Marquee Advanced Pro', 'Marquee Advanced Pro', 'manage_options', 'marquee-settings', 'wp_marquee_adv_settings_page');
}
add_action('admin_menu', 'wp_marquee_adv_admin_page');

function wp_marquee_adv_settings_page() {
    $text_items = json_decode(get_option('marquee_text_items', '[]'), true);
    if (empty($text_items)) {
        $text_items = array(
            array(
                'text' => '🎁 Oferta specială! Click aici pentru detalii',
                'url' => '',
                'target' => '_self',
                'icon_color' => '#ff6b6b',
                'border_color' => '#ff6b6b',
                'text_color' => '#333333'
            )
        );
    }
    ?>
    <div class="wrap">
        <h1>🎯 Marquee Advanced Pro - Setări Avansate</h1>
        
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
                            <?php foreach ($text_items as $item): ?>
                            <div class="marquee-item-preview" style="
                                border-left: 3px solid <?php echo esc_attr($item['border_color']); ?>;
                                padding: <?php echo absint(get_option('marquee_item_padding', 10)); ?>px;
                                margin-right: <?php echo absint(get_option('marquee_item_spacing', 20)); ?>px;
                                display: inline-flex;
                                align-items: center;
                            ">
                                <span class="marquee-icon-preview" style="
                                    display: inline-block;
                                    width: 12px;
                                    height: 12px;
                                    background: <?php echo esc_attr($item['icon_color']); ?>;
                                    border-radius: <?php echo get_option('marquee_icon_type', 'circle') === 'circle' ? '50%' : '2px'; ?>;
                                    margin-right: 8px;
                                "></span>
                                <span class="marquee-text-preview" style="
                                    color: <?php echo esc_attr($item['text_color']); ?>;
                                    font-family: <?php echo esc_attr(get_option('marquee_font', 'Arial, sans-serif')); ?>;
                                    font-size: <?php echo absint(get_option('marquee_size', 20)); ?>px;
                                    white-space: nowrap;
                                ">
                                    <?php echo esc_html($item['text']); ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php // Duplicate for continuous animation ?>
                            <?php foreach ($text_items as $item): ?>
                            <div class="marquee-item-preview" style="
                                border-left: 3px solid <?php echo esc_attr($item['border_color']); ?>;
                                padding: <?php echo absint(get_option('marquee_item_padding', 10)); ?>px;
                                margin-right: <?php echo absint(get_option('marquee_item_spacing', 20)); ?>px;
                                display: inline-flex;
                                align-items: center;
                            ">
                                <span class="marquee-icon-preview" style="
                                    display: inline-block;
                                    width: 12px;
                                    height: 12px;
                                    background: <?php echo esc_attr($item['icon_color']); ?>;
                                    border-radius: <?php echo get_option('marquee_icon_type', 'circle') === 'circle' ? '50%' : '2px'; ?>;
                                    margin-right: 8px;
                                "></span>
                                <span class="marquee-text-preview" style="
                                    color: <?php echo esc_attr($item['text_color']); ?>;
                                    font-family: <?php echo esc_attr(get_option('marquee_font', 'Arial, sans-serif')); ?>;
                                    font-size: <?php echo absint(get_option('marquee_size', 20)); ?>px;
                                    white-space: nowrap;
                                ">
                                    <?php echo esc_html($item['text']); ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="preview-info">
                        <p><strong>💡 Informații:</strong></p>
                        <ul>
                            <li>Poziție: <span id="preview-position-info"><?php echo esc_html(ucfirst(str_replace('_', ' ', get_option('marquee_position', 'after_menu')))); ?></span></li>
                            <li>Viteză: <span id="preview-speed-info"><?php echo esc_html(get_option('marquee_speed', 20)); ?>s</span></li>
                            <li>Elemente: <span id="preview-items-count"><?php echo count($text_items); ?></span></li>
                            <li>Iconițe: <span id="preview-icon-type"><?php echo get_option('marquee_icon_type', 'circle') === 'circle' ? '● Cerc' : '■ Pătrat'; ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('marquee-settings-form');
        const speedField = document.querySelector('input[name="marquee_speed"]');
        const directionField = document.querySelector('select[name="marquee_direction"]');
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
        const zindexField = document.querySelector('input[name="marquee_zindex"]');
        const itemPaddingField = document.querySelector('input[name="marquee_item_padding"]');
        const itemSpacingField = document.querySelector('input[name="marquee_item_spacing"]');
        const iconTypeField = document.querySelector('select[name="marquee_icon_type"]');
        
        const preview = document.getElementById('marquee-preview');
        const previewTrack = document.getElementById('marquee-preview-track');
        const previewPositionInfo = document.getElementById('preview-position-info');
        const previewSpeedInfo = document.getElementById('preview-speed-info');
        const previewItemsCount = document.getElementById('preview-items-count');
        const previewIconType = document.getElementById('preview-icon-type');

        function updatePreview() {
            let speed = speedField ? parseInt(speedField.value) : 20;
            let direction = directionField ? directionField.value : 'left';
            let bg = bgField ? bgField.value : '#ffffff';
            let font = fontField ? fontField.value : 'Arial, sans-serif';
            let size = sizeField ? sizeField.value : 20;
            let padding = paddingField ? paddingField.value : 10;
            let shadow = shadowField ? shadowField.checked : false;
            let border = borderField ? borderField.checked : false;
            let borderColor = borderColorField ? borderColorField.value : '#dddddd';
            let textShadow = textShadowField ? textShadowField.checked : false;
            let hoverEffect = hoverEffectField ? hoverEffectField.checked : true;
            let itemPadding = itemPaddingField ? parseInt(itemPaddingField.value) : 10;
            let itemSpacing = itemSpacingField ? parseInt(itemSpacingField.value) : 20;
            let iconType = iconTypeField ? iconTypeField.value : 'circle';

            // Update preview element styles
            preview.style.background = bg;
            preview.style.padding = padding + 'px';
            preview.style.boxShadow = shadow ? '0 4px 12px rgba(0,0,0,0.1)' : 'none';
            preview.style.border = border ? '1px solid ' + borderColor : 'none';
            
            // Update all items in preview
            const items = previewTrack.querySelectorAll('.marquee-item-preview');
            items.forEach(function(item) {
                item.style.padding = itemPadding + 'px';
                item.style.marginRight = itemSpacing + 'px';
                
                const icon = item.querySelector('.marquee-icon-preview');
                if (icon) {
                    icon.style.borderRadius = iconType === 'circle' ? '50%' : '2px';
                }
                
                const text = item.querySelector('.marquee-text-preview');
                if (text) {
                    text.style.fontFamily = font;
                    text.style.fontSize = size + 'px';
                    text.style.textShadow = textShadow ? '1px 1px 2px rgba(0,0,0,0.2)' : 'none';
                }
            });
            
            // Update animation
            let animationName = direction === 'right' ? 'marqueeScrollRight' : 'marqueeScrollLeft';
            previewTrack.style.animation = animationName + ' ' + speed + 's linear infinite';
            
            // Add/remove hover effect class
            if (hoverEffect) {
                preview.classList.add('has-hover-effect');
            } else {
                preview.classList.remove('has-hover-effect');
            }
            
            // Update info texts
            previewPositionInfo.textContent = positionField ? positionField.value.replace('_', ' ') : 'after menu';
            previewSpeedInfo.textContent = speed + 's';
            previewIconType.textContent = iconType === 'circle' ? '● Cerc' : '■ Pătrat';
            
            // Count items (excluding duplicates)
            const uniqueItems = Math.floor(items.length / 2);
            previewItemsCount.textContent = uniqueItems;
            
            // Restart animation to ensure it runs
            previewTrack.style.animation = 'none';
            setTimeout(() => {
                previewTrack.style.animation = animationName + ' ' + speed + 's linear infinite';
            }, 10);
        }

        // Add event listeners to all fields
        const fields = [
            speedField, directionField, bgField, fontField, 
            sizeField, paddingField, shadowField, borderField, 
            borderColorField, textShadowField, hoverEffectField,
            positionField, zindexField, itemPaddingField,
            itemSpacingField, iconTypeField
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

        // Handle dynamic items
        const addItemBtn = document.querySelector('.marquee-add-item');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', function() {
                const itemsContainer = document.querySelector('.marquee-items-container');
                const itemCount = itemsContainer.querySelectorAll('.marquee-item').length;
                
                const newItem = document.createElement('div');
                newItem.className = 'marquee-item';
                newItem.innerHTML = `
                    <div class="marquee-item-header">
                        <strong>Element #${itemCount + 1}</strong>
                        <button type="button" class="button button-small marquee-remove-item">Șterge</button>
                    </div>
                    <div class="marquee-item-fields">
                        <p>
                            <label>Text:<br>
                            <input type="text" name="marquee_item_text[]" class="regular-text" placeholder="Introdu textul...">
                            </label>
                        </p>
                        <p>
                            <label>URL (opțional):<br>
                            <input type="url" name="marquee_item_url[]" class="regular-text" placeholder="https://...">
                            </label>
                        </p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                            <div>
                                <label>Culoare Iconiță:<br>
                                <input type="color" name="marquee_item_icon_color[]" value="#ff6b6b">
                                </label>
                            </div>
                            <div>
                                <label>Culoare Border:<br>
                                <input type="color" name="marquee_item_border_color[]" value="#ff6b6b">
                                </label>
                            </div>
                            <div>
                                <label>Culoare Text:<br>
                                <input type="color" name="marquee_item_text_color[]" value="#333333">
                                </label>
                            </div>
                        </div>
                        <p>
                            <label>Target:
                            <select name="marquee_item_target[]">
                                <option value="_self">Aceeași filă</option>
                                <option value="_blank">Filă nouă</option>
                            </select>
                            </label>
                        </p>
                    </div>
                `;
                
                itemsContainer.appendChild(newItem);
                
                // Add event listener to remove button
                newItem.querySelector('.marquee-remove-item').addEventListener('click', function() {
                    newItem.remove();
                    updatePreview();
                });
                
                // Add event listeners to new fields
                const newFields = newItem.querySelectorAll('input, select');
                newFields.forEach(function(field) {
                    field.addEventListener('change', updatePreview);
                    if (field.type !== 'checkbox' && field.type !== 'select') {
                        field.addEventListener('input', updatePreview);
                    }
                });
                
                updatePreview();
            });
        }

        // Handle remove item buttons
        document.querySelectorAll('.marquee-remove-item').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const item = this.closest('.marquee-item');
                if (item) {
                    item.remove();
                    updatePreview();
                }
            });
        });

        // Update preview when item fields change
        document.querySelectorAll('.marquee-items-container input, .marquee-items-container select').forEach(function(field) {
            field.addEventListener('change', updatePreview);
            if (field.type !== 'checkbox') {
                field.addEventListener('input', updatePreview);
            }
        });

        updatePreview(); // inițializare
    });
    
    // Define the animations in JavaScript for preview
    const style = document.createElement('style');
    style.textContent = `
        @keyframes marqueeScrollLeft { 
            0% { transform: translateX(0); } 
            100% { transform: translateX(-50%); } 
        }
        @keyframes marqueeScrollRight { 
            0% { transform: translateX(-50%); } 
            100% { transform: translateX(0); } 
        }
        .has-hover-effect:hover .marquee-preview-track { 
            animation-play-state: paused !important;
            opacity: 0.9;
        }
        .marquee-item-preview:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

function wp_marquee_speed_field() { 
    $speed = get_option('marquee_speed', 20);
    echo '<input type="range" name="marquee_speed" min="5" max="60" value="' . esc_attr($speed) . '" class="marquee-speed-slider" oninput="document.getElementById(\'speed-value\').innerHTML = this.value + \'s\'">';
    echo '<span id="speed-value" style="margin-left:10px; font-weight:bold;">' . esc_html($speed) . 's</span>';
    echo '<p class="description">Viteza animației (valori mai mici = mai rapid, valori mai mari = mai lent). Recomandat: 15-30 secunde.</p>';
}

function wp_marquee_direction_field() {
    $direction = get_option('marquee_direction', 'left');
    ?>
    <select name="marquee_direction" class="regular-text">
        <option value="left" <?php selected($direction, 'left'); ?>>Stânga ← (default)</option>
        <option value="right" <?php selected($direction, 'right'); ?>>Dreapta →</option>
    </select>
    <p class="description">Direcția în care se mișcă textul.</p>
    <?php
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

// Text items field
function wp_marquee_text_items_field() {
    $text_items = json_decode(get_option('marquee_text_items', '[]'), true);
    if (empty($text_items)) {
        $text_items = array(
            array(
                'text' => '🎁 Oferta specială! Click aici pentru detalii',
                'url' => '',
                'target' => '_self',
                'icon_color' => '#ff6b6b',
                'border_color' => '#ff6b6b',
                'text_color' => '#333333'
            )
        );
    }
    
    echo '<div class="marquee-items-container">';
    echo '<input type="hidden" name="marquee_text_items" id="marquee-text-items-json" value=\'' . esc_attr(get_option('marquee_text_items')) . '\'>';
    
    foreach ($text_items as $index => $item) {
        echo '<div class="marquee-item" data-index="' . $index . '">';
        echo '<div class="marquee-item-header">';
        echo '<strong>Element #' . ($index + 1) . '</strong>';
        echo '<button type="button" class="button button-small marquee-remove-item" onclick="wpMarqueeRemoveItem(this)">Șterge</button>';
        echo '</div>';
        echo '<div class="marquee-item-fields">';
        
        // Text field
        echo '<p>';
        echo '<label>Text:<br>';
        echo '<input type="text" class="regular-text marquee-item-text" value="' . esc_attr($item['text']) . '" placeholder="Introdu textul...">';
        echo '</label>';
        echo '</p>';
        
        // URL field
        echo '<p>';
        echo '<label>URL (opțional):<br>';
        echo '<input type="url" class="regular-text marquee-item-url" value="' . esc_url($item['url']) . '" placeholder="https://...">';
        echo '</label>';
        echo '</p>';
        
        // Colors grid
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px;">';
        
        // Icon color
        echo '<div>';
        echo '<label>Culoare Iconiță:<br>';
        echo '<input type="color" class="marquee-item-icon-color" value="' . esc_attr($item['icon_color']) . '">';
        echo '</label>';
        echo '</div>';
        
        // Border color
        echo '<div>';
        echo '<label>Culoare Border:<br>';
        echo '<input type="color" class="marquee-item-border-color" value="' . esc_attr($item['border_color']) . '">';
        echo '</label>';
        echo '</div>';
        
        // Text color
        echo '<div>';
        echo '<label>Culoare Text:<br>';
        echo '<input type="color" class="marquee-item-text-color" value="' . esc_attr($item['text_color']) . '">';
        echo '</label>';
        echo '</div>';
        
        echo '</div>';
        
        // Target field
        echo '<p>';
        echo '<label>Target:';
        echo '<select class="marquee-item-target">';
        echo '<option value="_self" ' . selected($item['target'], '_self', false) . '>Aceeași filă</option>';
        echo '<option value="_blank" ' . selected($item['target'], '_blank', false) . '>Filă nouă</option>';
        echo '</select>';
        echo '</label>';
        echo '</p>';
        
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    
    echo '<button type="button" class="button button-secondary marquee-add-item" onclick="wpMarqueeAddItem()">+ Adaugă Element</button>';
    echo '<p class="description">Fiecare element va apărea în banner cu iconiță colorată și border. Poți să adaugi câte elemente dorești.</p>';
    
    // JavaScript for handling dynamic items
    echo '<script>
    function wpMarqueeUpdateJSON() {
        const items = [];
        document.querySelectorAll(".marquee-item").forEach(function(item, index) {
            const text = item.querySelector(".marquee-item-text").value;
            const url = item.querySelector(".marquee-item-url").value;
            const target = item.querySelector(".marquee-item-target").value;
            const iconColor = item.querySelector(".marquee-item-icon-color").value;
            const borderColor = item.querySelector(".marquee-item-border-color").value;
            const textColor = item.querySelector(".marquee-item-text-color").value;
            
            if (text.trim() !== "") {
                items.push({
                    text: text,
                    url: url,
                    target: target,
                    icon_color: iconColor,
                    border_color: borderColor,
                    text_color: textColor
                });
            }
        });
        
        document.getElementById("marquee-text-items-json").value = JSON.stringify(items);
        
        // Trigger preview update
        if (typeof updatePreview === "function") {
            updatePreview();
        }
    }
    
    function wpMarqueeAddItem() {
        const container = document.querySelector(".marquee-items-container");
        const items = container.querySelectorAll(".marquee-item");
        const index = items.length;
        
        const newItem = document.createElement("div");
        newItem.className = "marquee-item";
        newItem.setAttribute("data-index", index);
        
        newItem.innerHTML = `
            <div class="marquee-item-header">
                <strong>Element #${index + 1}</strong>
                <button type="button" class="button button-small marquee-remove-item" onclick="wpMarqueeRemoveItem(this)">Șterge</button>
            </div>
            <div class="marquee-item-fields">
                <p>
                    <label>Text:<br>
                    <input type="text" class="regular-text marquee-item-text" placeholder="Introdu textul...">
                    </label>
                </p>
                <p>
                    <label>URL (opțional):<br>
                    <input type="url" class="regular-text marquee-item-url" placeholder="https://...">
                    </label>
                </p>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <div>
                        <label>Culoare Iconiță:<br>
                        <input type="color" class="marquee-item-icon-color" value="#ff6b6b">
                        </label>
                    </div>
                    <div>
                        <label>Culoare Border:<br>
                        <input type="color" class="marquee-item-border-color" value="#ff6b6b">
                        </label>
                    </div>
                    <div>
                        <label>Culoare Text:<br>
                        <input type="color" class="marquee-item-text-color" value="#333333">
                        </label>
                    </div>
                </div>
                <p>
                    <label>Target:
                    <select class="marquee-item-target">
                        <option value="_self">Aceeași filă</option>
                        <option value="_blank">Filă nouă</option>
                    </select>
                    </label>
                </p>
            </div>
        `;
        
        container.appendChild(newItem);
        
        // Add event listeners to new fields
        newItem.querySelectorAll("input, select").forEach(function(field) {
            field.addEventListener("change", wpMarqueeUpdateJSON);
            field.addEventListener("input", wpMarqueeUpdateJSON);
        });
        
        wpMarqueeUpdateJSON();
    }
    
    function wpMarqueeRemoveItem(button) {
        const item = button.closest(".marquee-item");
        if (item && confirm("Sigur doriți să ștergeți acest element?")) {
            item.remove();
            
            // Renumber remaining items
            document.querySelectorAll(".marquee-item").forEach(function(item, index) {
                item.setAttribute("data-index", index);
                item.querySelector(".marquee-item-header strong").textContent = "Element #" + (index + 1);
            });
            
            wpMarqueeUpdateJSON();
        }
    }
    
    // Add event listeners to existing fields
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".marquee-item input, .marquee-item select").forEach(function(field) {
            field.addEventListener("change", wpMarqueeUpdateJSON);
            field.addEventListener("input", wpMarqueeUpdateJSON);
        });
    });
    </script>';
    
    // CSS for items
    echo '<style>
    .marquee-item {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .marquee-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .marquee-item-fields label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .marquee-add-item {
        margin-top: 10px;
    }
    
    .marquee-remove-item {
        background: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .marquee-remove-item:hover {
        background: #c82333;
        border-color: #bd2130;
    }
    </style>';
}

function wp_marquee_item_padding_field() {
    echo '<input type="number" name="marquee_item_padding" value="' . esc_attr(get_option('marquee_item_padding', 10)) . '" min="0" max="50" class="small-text"> px';
    echo '<p class="description">Spațiu interior al fiecărui element din banner.</p>';
}

function wp_marquee_item_spacing_field() {
    echo '<input type="number" name="marquee_item_spacing" value="' . esc_attr(get_option('marquee_item_spacing', 20)) . '" min="0" max="100" class="small-text"> px';
    echo '<p class="description">Distanța între elementele din banner.</p>';
}

function wp_marquee_icon_type_field() {
    $icon_type = get_option('marquee_icon_type', 'circle');
    ?>
    <select name="marquee_icon_type" class="regular-text">
        <option value="circle" <?php selected($icon_type, 'circle'); ?>>● Cerc (default)</option>
        <option value="square" <?php selected($icon_type, 'square'); ?>>■ Pătrat</option>
    </select>
    <p class="description">Forma iconiței de lângă fiecare text.</p>
    <?php
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
    $speed = get_option('marquee_speed', 20);
    $direction = get_option('marquee_direction', 'left');
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
    $item_padding = get_option('marquee_item_padding', 10);
    $item_spacing = get_option('marquee_item_spacing', 20);
    $icon_type = get_option('marquee_icon_type', 'circle');
    
    // Get text items
    $text_items = json_decode(get_option('marquee_text_items', '[]'), true);
    if (empty($text_items)) {
        $text_items = array(
            array(
                'text' => '🎁 Oferta specială! Click aici pentru detalii',
                'url' => '',
                'target' => '_self',
                'icon_color' => '#ff6b6b',
                'border_color' => '#ff6b6b',
                'text_color' => '#333333'
            )
        );
    }
    
    // Prepare styles
    $container_style = 'background:' . esc_attr($bg) . ';';
    $container_style .= 'padding:' . absint($padding) . 'px;';
    $container_style .= 'z-index:' . absint($zindex) . ';';
    if ($shadow) {
        $container_style .= 'box-shadow:0 4px 12px rgba(0,0,0,0.1);';
    }
    if ($border) {
        $container_style .= 'border:1px solid ' . esc_attr($border_color) . ';';
    }
    
    // Animation based on direction
    $animation_name = $direction === 'right' ? 'wpMarqueeScrollRight' : 'wpMarqueeScrollLeft';
    $track_style = 'animation: ' . $animation_name . ' ' . $speed . 's linear infinite;';
    
    // Prepare icon border radius
    $icon_border_radius = $icon_type === 'circle' ? '50%' : '2px';

    // Prepare classes
    $container_class = 'marquee-container wp-marquee-banner';
    if ($hover_effect) {
        $container_class .= ' marquee-hover-effect';
    }
    
    // Build items HTML
    $items_html = '';
    foreach ($text_items as $item) {
        $item_style = 'border-left: 3px solid ' . esc_attr($item['border_color']) . ';';
        $item_style .= 'padding: ' . absint($item_padding) . 'px;';
        $item_style .= 'margin-right: ' . absint($item_spacing) . 'px;';
        
        $text_style = 'color:' . esc_attr($item['text_color']) . ';';
        $text_style .= 'font-family:' . esc_attr($font) . ';';
        $text_style .= 'font-size:' . absint($size) . 'px;';
        if ($text_shadow) {
            $text_style .= 'text-shadow:1px 1px 2px rgba(0,0,0,0.2);';
        }
        
        $icon_style = 'background:' . esc_attr($item['icon_color']) . ';';
        $icon_style .= 'border-radius:' . $icon_border_radius . ';';
        
        $item_html = '<div class="marquee-item" style="' . $item_style . '">';
        $item_html .= '<span class="marquee-icon" style="' . $icon_style . '"></span>';
        $item_html .= '<span class="marquee-text" style="' . $text_style . '">' . esc_html($item['text']) . '</span>';
        $item_html .= '</div>';
        
        $items_html .= $item_html;
    }
    
    // Duplicate items for continuous animation
    $all_items_html = $items_html . $items_html;
    
    // Build output
    $output = '<div class="' . esc_attr($container_class) . '" style="' . $container_style . '">';
    $output .= '<div class="marquee-track" style="' . $track_style . '">';
    $output .= $all_items_html;
    $output .= '</div>';
    $output .= '</div>';
    
    // Add JavaScript for click handling
    $output .= '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var items = ' . wp_json_encode($text_items) . ';
        var bannerItems = document.querySelectorAll(".marquee-item");
        
        bannerItems.forEach(function(item, index) {
            var itemData = items[index % items.length];
            if (itemData.url && itemData.url.trim() !== "") {
                item.style.cursor = "pointer";
                item.addEventListener("click", function(e) {
                    e.preventDefault();
                    if (itemData.target === "_blank") {
                        window.open(itemData.url, "_blank");
                    } else {
                        window.location.href = itemData.url;
                    }
                });
                
                // Add hover effect
                item.addEventListener("mouseenter", function() {
                    this.style.opacity = "0.9";
                    this.style.transform = "translateY(-1px)";
                });
                
                item.addEventListener("mouseleave", function() {
                    this.style.opacity = "1";
                    this.style.transform = "translateY(0)";
                });
            }
        });
    });
    </script>';
    
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
    
    echo '<style>
    /* WP Marquee Advanced Pro - Professional Styles */
    .wp-marquee-banner { 
        width: 100%; 
        overflow: hidden; 
        box-sizing: border-box;
        position: relative;
        transition: all 0.3s ease;
        margin: 0;
        clear: both;
        display: block;
    }
    
    .marquee-track { 
        display: flex; 
        width: max-content; 
        animation-timing-function: linear; 
        animation-iteration-count: infinite; 
        will-change: transform;
        white-space: nowrap;
        align-items: center;
    }
    
    .marquee-item {
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        border-left-width: 3px;
        border-left-style: solid;
        flex-shrink: 0;
    }
    
    .marquee-item:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    
    .marquee-icon {
        display: inline-block;
        width: 12px;
        height: 12px;
        margin-right: 8px;
        flex-shrink: 0;
    }
    
    .marquee-text {
        white-space: nowrap;
        font-weight: 500;
        letter-spacing: 0.02em;
        transition: all 0.3s ease;
    }
    
    /* Left to right animation */
    @keyframes wpMarqueeScrollLeft { 
        0% { 
            transform: translateX(0); 
        } 
        100% { 
            transform: translateX(-50%); 
        } 
    }
    
    /* Right to left animation */
    @keyframes wpMarqueeScrollRight { 
        0% { 
            transform: translateX(-50%); 
        } 
        100% { 
            transform: translateX(0); 
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
    .wp-marquee-banner {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .marquee-item {
            padding: 8px !important;
            margin-right: 15px !important;
        }
        
        .marquee-icon {
            width: 10px;
            height: 10px;
            margin-right: 6px;
        }
        
        .wp-marquee-banner {
            padding: 8px !important;
        }
    }
    
    @media (max-width: 480px) {
        .marquee-item {
            padding: 6px !important;
            margin-right: 10px !important;
        }
        
        .marquee-icon {
            width: 8px;
            height: 8px;
            margin-right: 4px;
        }
        
        .wp-marquee-banner {
            padding: 6px !important;
        }
    }
    
    /* Ensure proper positioning */
    body.admin-bar .wp-marquee-banner {
        position: relative;
    }
    
    /* Fix for theme conflicts */
    .wp-marquee-banner * {
        box-sizing: border-box;
    }
    
    /* Performance optimization */
    .marquee-track {
        backface-visibility: hidden;
        perspective: 1000px;
    }
    </style>';
}
add_action('wp_head', 'wp_marquee_adv_css');

// =======================
// JavaScript pentru poziționare
// =======================
function wp_marquee_adv_js() {
    // Check if marquee is enabled
    if (get_option('marquee_enabled', '1') !== '1') {
        return;
    }
    
    // Check display conditions
    if (!wp_marquee_adv_should_display()) {
        return;
    }
    
    $position = get_option('marquee_position', 'after_menu');
    
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Position handling based on settings
        var position = '<?php echo esc_js($position); ?>';
        var banner = document.querySelector('.wp-marquee-banner');
        
        if (!banner) return;
        
        if (position === 'after_menu') {
            // Try to find the menu and insert banner after it
            var menus = document.querySelectorAll('nav, .nav, .navbar, .main-navigation, .menu, #menu, .site-navigation, header nav, #main-nav, .primary-menu');
            
            if (menus.length > 0) {
                // Find the most visible menu (usually has more width)
                var bestMenu = null;
                var maxWidth = 0;
                
                menus.forEach(function(menu) {
                    var width = menu.offsetWidth;
                    if (width > maxWidth && width > 100) {
                        maxWidth = width;
                        bestMenu = menu;
                    }
                });
                
                if (bestMenu && bestMenu.parentNode) {
                    // Insert banner after the menu
                    bestMenu.parentNode.insertBefore(banner, bestMenu.nextSibling);
                    return;
                }
            }
            
            // Fallback: insert after header
            var header = document.querySelector('header, .site-header, #header, .header, #masthead');
            if (header && header.parentNode) {
                header.parentNode.insertBefore(banner, header.nextSibling);
            }
        } else if (position === 'before_content') {
            // Try to find the main content and insert banner before it
            var content = document.querySelector('main, .main, #main, .site-main, .content, #content, .main-content, #main-content, .site-content, #primary');
            
            if (content && content.parentNode) {
                content.parentNode.insertBefore(banner, content);
            } else {
                // Fallback: insert before first .entry-content or article
                var entryContent = document.querySelector('.entry-content, article, .post, .page-content');
                if (entryContent && entryContent.parentNode) {
                    entryContent.parentNode.insertBefore(banner, entryContent);
                }
            }
        }
        
        // Ensure banner stays visible
        banner.style.position = 'relative';
        
        // Fix for animation not running
        setTimeout(function() {
            var tracks = document.querySelectorAll('.marquee-track');
            tracks.forEach(function(track) {
                // Force reflow to restart animation
                var currentAnimation = track.style.animation;
                track.style.animation = 'none';
                void track.offsetWidth; // Trigger reflow
                track.style.animation = currentAnimation;
            });
        }, 100);
    });
    
    // Fix for iOS Safari animation issues
    if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
        document.addEventListener('touchstart', function() {}, {passive: true});
    }
    </script>
    <?php
}
add_action('wp_footer', 'wp_marquee_adv_js');

// =======================
// Afișare automată în funcție de poziția selectată
// =======================

// Funcția principală pentru afișarea bannerului
function wp_marquee_adv_display_banner() {
    // Check if marquee is enabled
    if (get_option('marquee_enabled', '1') !== '1') {
        return;
    }
    
    // Check display conditions
    if (!wp_marquee_adv_should_display()) {
        return;
    }
    
    $position = get_option('marquee_position', 'after_menu');
    
    // For shortcode only, don't display automatically
    if ($position === 'shortcode') {
        return;
    }
    
    // Display the banner
    echo do_shortcode('[marquee]');
}

// Hook pentru începutul paginii (wp_body_open)
add_action('wp_body_open', function() {
    if (get_option('marquee_position', 'after_menu') === 'body_open') {
        wp_marquee_adv_display_banner();
    }
}, 5);

// Hook pentru după meniu
add_action('wp_footer', function() {
    if (get_option('marquee_position', 'after_menu') === 'after_menu') {
        // Adăugăm bannerul direct în footer pentru a fi mutat de JavaScript
        echo '<div id="wp-marquee-after-menu" style="display:none;">';
        wp_marquee_adv_display_banner();
        echo '</div>';
    }
}, 1);

// Hook pentru înainte de conținut - folosim the_content
add_filter('the_content', function($content) {
    if (is_main_query() && get_option('marquee_position', 'after_menu') === 'before_content') {
        ob_start();
        wp_marquee_adv_display_banner();
        $banner = ob_get_clean();
        return $banner . $content;
    }
    return $content;
}, 5);

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
        position: relative;
        border: 2px dashed #e0e0e0;
        min-height: 60px;
        display: flex;
        align-items: center;
    }
    
    .marquee-preview-wrapper:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        border-color: #4dabf7;
    }
    
    .marquee-preview-track {
        display: flex;
        width: max-content;
        animation-name: marqueeScrollLeft;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
        align-items: center;
    }
    
    .marquee-item-preview {
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        border-left-width: 3px;
        border-left-style: solid;
        flex-shrink: 0;
    }
    
    .marquee-item-preview:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .marquee-icon-preview {
        display: inline-block;
        width: 12px;
        height: 12px;
        margin-right: 8px;
        flex-shrink: 0;
    }
    
    .marquee-text-preview {
        white-space: nowrap;
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
    
    /* Slider styling */
    .marquee-speed-slider {
        width: 200px;
        height: 6px;
        border-radius: 3px;
        background: #ddd;
        outline: none;
        -webkit-appearance: none;
    }
    
    .marquee-speed-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #2271b1;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .marquee-speed-slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #2271b1;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
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
        
        .marquee-speed-slider {
            width: 150px;
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
    
    /* Animation for preview */
    @keyframes marqueeScrollLeft {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    
    @keyframes marqueeScrollRight {
        0% { transform: translateX(-50%); }
        100% { transform: translateX(0); }
    }
    
    /* Items styling in admin */
    .marquee-item {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .marquee-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .marquee-item-fields label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .marquee-add-item {
        margin-top: 10px;
    }
    
    .marquee-remove-item {
        background: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .marquee-remove-item:hover {
        background: #c82333;
        border-color: #bd2130;
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
                <strong>🎉 Nou în versiunea 2.0!</strong> Funcționalități avansate adăugate:
            </p>
            <ul>
                <li><strong>Mai multe texte:</strong> Adaugă câte elemente dorești</li>
                <li><strong>URL-uri personalizate:</strong> Fiecare text poate avea link propriu</li>
                <li><strong>Iconițe colorate:</strong> Personalizează culorile pentru fiecare element</li>
                <li><strong>Border stânga:</strong> Fiecare element are border colorat la început</li>
                <li><strong>Tip iconiță:</strong> Alege între cerc sau pătrat</li>
            </ul>
            <p>
                <strong>🔗 Link-uri utile:</strong> 
                <a href="https://github.com/vadikonline1/wp-marquee-advanced/" target="_blank">GitHub</a> | 
                <a href="https://github.com/vadikonline1/wp-marquee-advanced/issues" target="_blank">Support</a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'wp_marquee_adv_admin_notice');




// Auto-update from GitHub - METODA SIMPLĂ DIRECTĂ
add_filter('site_transient_update_plugins', function($transient){
    if(empty($transient->checked)) return $transient;

    $plugin_slug = plugin_basename(__FILE__);
    
    // Verifică dacă plugin-ul curent este în lista de checked
    if (!isset($transient->checked[$plugin_slug])) {
        return $transient;
    }

    // Folosim același URL ca în funcția de verificare
    $remote_url = 'https://raw.githubusercontent.com/vadikonline1/wp-marquee-advanced/main/wp-marquee-advanced.php';
    
    $response = wp_remote_get($remote_url, ['timeout' => 10]);
    if(is_wp_error($response)) return $transient;

    $remote_plugin_data = $response['body'];
    
    if(preg_match('/Version:\s*([0-9.]+)/', $remote_plugin_data, $matches)){
        $remote_version = trim($matches[1]);
        $current_version = $transient->checked[$plugin_slug];

        if(version_compare($remote_version, $current_version, '>')){
            $transient->response[$plugin_slug] = (object) [
                'slug' => dirname($plugin_slug),
                'plugin' => $plugin_slug,
                'new_version' => $remote_version,
                'url' => 'https://github.com/vadikonline1/wp-marquee-advanced/',
                'package' => 'https://github.com/vadikonline1/wp-marquee-advanced/archive/refs/heads/main.zip',
                'tested' => get_bloginfo('version')
            ];
        }
    }
    return $transient;
});
