<?php
/*
Plugin Name: WP Marquee Advanced Pro
Plugin URI:  https://github.com/vadikonline1/wp-marquee-advanced/
Description: Animated banner with infinite scroll and complete admin settings. Supports multiple texts with custom URLs and colored icons.
Version:     2.5
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
        $links[] = '<a href="https://github.com/vadikonline1/wp-marquee-advanced/" target="_blank" rel="noopener noreferrer">📖 Documentation</a>';
        $links[] = '<a href="https://github.com/vadikonline1/wp-marquee-advanced/issues" target="_blank" rel="noopener noreferrer">🐛 Report Issue</a>';
        $links[] = '<a href="https://github.com/vadikonline1/wp-marquee-advanced/" target="_blank" rel="noopener noreferrer">⭐ Rating</a>';
    }
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wp_marquee_plugin_action_links');
function wp_marquee_plugin_action_links($actions) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=marquee-settings') . '" style="font-weight:bold;color:#2271b1;">⚙️ Settings</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}

// =======================
// Register settings
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
                'text' => '🎁 Special offer! Click here for details',
                'url' => '',
                'icon_color' => '#ff6b6b',
                'border_color' => '#ff6b6b',
                'text_color' => '#333333'
            ),
            array(
                'text' => '🔥 Limited promotion! 50% discount',
                'url' => '',
                'icon_color' => '#ffa726',
                'border_color' => '#ffa726',
                'text_color' => '#333333'
            ),
            array(
                'text' => '📢 News! See the latest products',
                'url' => '',
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
    register_setting('marquee_options_group', 'marquee_icon_enabled', array(
        'default' => '1',
        'sanitize_callback' => 'wp_marquee_adv_sanitize_checkbox'
    ));
    
    add_settings_section('marquee_main_section', 'General Settings', null, 'marquee-settings');
    add_settings_section('marquee_items_section', '📋 Banner Elements (Text + URL)', null, 'marquee-settings');
    add_settings_section('marquee_design_section', '🎨 Advanced Design Settings', null, 'marquee-settings');
    add_settings_section('marquee_position_section', '📍 Position & Behavior', null, 'marquee-settings');
    
    add_settings_field('marquee_enabled', 'Enable Banner', 'wp_marquee_enabled_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_speed', 'Animation Speed', 'wp_marquee_speed_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_direction', 'Animation Direction', 'wp_marquee_direction_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_bg', 'Banner Background Color', 'wp_marquee_bg_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_font', 'Text Font', 'wp_marquee_font_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_size', 'Text Size (px)', 'wp_marquee_size_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_padding', 'Banner Padding (px)', 'wp_marquee_padding_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_display_type', 'Display on Pages', 'wp_marquee_display_type_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_selected_pages', 'Select Pages', 'wp_marquee_selected_pages_field', 'marquee-settings', 'marquee_main_section');
    
    // Items settings
    add_settings_field('marquee_text_items', 'Banner Elements', 'wp_marquee_text_items_field', 'marquee-settings', 'marquee_items_section');
    add_settings_field('marquee_item_padding', 'Element Padding (px)', 'wp_marquee_item_padding_field', 'marquee-settings', 'marquee_items_section');
    add_settings_field('marquee_item_spacing', 'Space Between Elements (px)', 'wp_marquee_item_spacing_field', 'marquee-settings', 'marquee_items_section');
    add_settings_field('marquee_icon_enabled', 'Show Icon', 'wp_marquee_icon_enabled_field', 'marquee-settings', 'marquee_items_section');
    add_settings_field('marquee_icon_type', 'Icon Type', 'wp_marquee_icon_type_field', 'marquee-settings', 'marquee_items_section');
    
    // Design settings
    add_settings_field('marquee_shadow', 'Banner Shadow', 'wp_marquee_shadow_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_border', 'Banner Border', 'wp_marquee_border_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_border_color', 'Border Color', 'wp_marquee_border_color_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_text_shadow', 'Text Shadow', 'wp_marquee_text_shadow_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_hover_effect', 'Hover Effect', 'wp_marquee_hover_effect_field', 'marquee-settings', 'marquee_design_section');
    add_settings_field('marquee_zindex', 'Z-Index', 'wp_marquee_zindex_field', 'marquee-settings', 'marquee_design_section');
    
    // Position settings
    add_settings_field('marquee_position', 'Display Position', 'wp_marquee_position_field', 'marquee-settings', 'marquee_position_section');
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
                'text' => '🎁 Special offer! Click here for details',
                'url' => '',
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
        $item['icon_color'] = sanitize_hex_color($item['icon_color']);
        $item['border_color'] = sanitize_hex_color($item['border_color']);
        $item['text_color'] = sanitize_hex_color($item['text_color']);
    }
    
    return wp_json_encode($decoded);
}

// =======================
// Admin settings page
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
                'text' => '🎁 Special offer! Click here for details',
                'url' => '',
                'icon_color' => '#ff6b6b',
                'border_color' => '#ff6b6b',
                'text_color' => '#333333'
            )
        );
    }
    ?>
    <div class="wrap">
        <h1>🎯 Marquee Advanced Pro - Advanced Settings</h1>
        
        <!-- Live Preview -->
        <div class="wp-marquee-preview-section">
            <h2><span class="dashicons dashicons-visibility"></span> Live Preview</h2>
            <div class="preview-container">
                <div id="marquee-preview" class="marquee-preview-wrapper">
                    <div id="marquee-preview-track" class="marquee-preview-track">
                        <?php 
                        // Display only one copy of elements for preview
                        foreach ($text_items as $item): 
                            $item_padding = get_option('marquee_item_padding', 10);
                            $item_spacing = get_option('marquee_item_spacing', 20);
                            $icon_type = get_option('marquee_icon_type', 'circle');
                            $icon_enabled = get_option('marquee_icon_enabled', '1') === '1';
                            $icon_border_radius = $icon_type === 'circle' ? '50%' : '2px';
                            $font = get_option('marquee_font', 'Arial, sans-serif');
                            $size = get_option('marquee_size', 20);
                        ?>
                        <div class="marquee-item-preview" style="
                            border-left: 2px solid <?php echo esc_attr($item['border_color']); ?>;
                            padding: <?php echo absint($item_padding); ?>px;
                            margin-right: <?php echo absint($item_spacing); ?>px;
                            display: inline-flex;
                            align-items: center;
                            flex-shrink: 0;
                            <?php if (!empty($item['url'])): ?>cursor: pointer;<?php endif; ?>
                        ">
                            <?php if ($icon_enabled): ?>
                            <span class="marquee-icon-preview" style="
                                display: inline-block;
                                width: 8px;
                                height: 8px;
                                background: <?php echo esc_attr($item['icon_color']); ?>;
                                border-radius: <?php echo $icon_border_radius; ?>;
                                margin-right: 6px;
                                flex-shrink: 0;
                            "></span>
                            <?php endif; ?>
                            <span class="marquee-text-preview" style="
                                color: <?php echo esc_attr($item['text_color']); ?>;
                                font-family: <?php echo esc_attr($font); ?>;
                                font-size: <?php echo absint($size); ?>px;
                                white-space: nowrap;
                            ">
                                <?php echo esc_html($item['text']); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="preview-info" style="margin-top: 15px; padding: 12px; background: #f8f9fa; border-radius: 6px; font-size: 14px;">
                    <p style="margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: bold;">💡 Information:</span>
                        <span style="margin-left: 5px;">Position: <span id="preview-position-info"><?php echo esc_html(ucfirst(str_replace('_', ' ', get_option('marquee_position', 'after_menu')))); ?></span> • 
                        Speed: <span id="preview-speed-info"><?php echo esc_html(get_option('marquee_speed', 20)); ?>s</span> • 
                        Elements: <span id="preview-items-count"><?php echo count($text_items); ?></span> • 
                        Icons: <span id="preview-icon-type"><?php 
                            $icon_enabled = get_option('marquee_icon_enabled', '1') === '1';
                            $icon_type = get_option('marquee_icon_type', 'circle');
                            if (!$icon_enabled) {
                                echo 'Disabled';
                            } else {
                                echo $icon_type === 'circle' ? '● Circle' : '■ Square';
                            }
                        ?></span></span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Settings form -->
        <div class="wp-marquee-admin-main">
            <form method="post" action="options.php" id="marquee-settings-form">
                <?php settings_fields('marquee_options_group'); ?>
                <?php do_settings_sections('marquee-settings'); ?>
                <?php submit_button('💾 Save Settings', 'primary', 'submit', true); ?>
            </form>
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
        const iconEnabledField = document.querySelector('input[name="marquee_icon_enabled"]');
        
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
            let iconEnabled = iconEnabledField ? iconEnabledField.checked : true;
            let iconBorderRadius = iconType === 'circle' ? '50%' : '2px';

            // Update preview container styles
            preview.style.background = bg;
            preview.style.padding = padding + 'px';
            preview.style.boxShadow = shadow ? '0 2px 8px rgba(0,0,0,0.08)' : 'none';
            preview.style.border = border ? '1px solid ' + borderColor : 'none';
            
            // Update all items in preview
            const items = previewTrack.querySelectorAll('.marquee-item-preview');
            items.forEach(function(item) {
                item.style.padding = itemPadding + 'px';
                item.style.marginRight = itemSpacing + 'px';
                
                const icon = item.querySelector('.marquee-icon-preview');
                if (icon) {
                    icon.style.display = iconEnabled ? 'inline-block' : 'none';
                    icon.style.borderRadius = iconBorderRadius;
                }
                
                const text = item.querySelector('.marquee-text-preview');
                if (text) {
                    text.style.fontFamily = font;
                    text.style.fontSize = size + 'px';
                    text.style.textShadow = textShadow ? '1px 1px 2px rgba(0,0,0,0.15)' : 'none';
                }
            });
            
            // Update animation
            let animationName = direction === 'right' ? 'marqueeScrollRight' : 'marqueeScrollLeft';
            previewTrack.style.animation = animationName + ' ' + speed + 's linear infinite';
            
            // Apply hover effect correctly
            const allItems = previewTrack.querySelectorAll('.marquee-item-preview');
            allItems.forEach(function(item) {
                if (hoverEffect) {
                    item.addEventListener('mouseenter', function() {
                        previewTrack.style.animationPlayState = 'paused';
                    });
                    item.addEventListener('mouseleave', function() {
                        previewTrack.style.animationPlayState = 'running';
                    });
                } else {
                    item.removeEventListener('mouseenter', function() {});
                    item.removeEventListener('mouseleave', function() {});
                    previewTrack.style.animationPlayState = 'running';
                }
            });
            
            // Update info texts
            previewPositionInfo.textContent = positionField ? positionField.value.replace('_', ' ') : 'after menu';
            previewSpeedInfo.textContent = speed + 's';
            
            let iconText = '';
            if (!iconEnabled) {
                iconText = 'Disabled';
            } else {
                iconText = iconType === 'circle' ? '● Circle' : '■ Square';
            }
            previewIconType.textContent = iconText;
            
            // Get item count from JSON field
            try {
                const jsonField = document.getElementById('marquee-text-items-json');
                if (jsonField && jsonField.value) {
                    const items = JSON.parse(jsonField.value);
                    previewItemsCount.textContent = items.length;
                }
            } catch(e) {
                // Fallback to counting visible items
                previewItemsCount.textContent = items.length;
            }
            
            // Restart animation to ensure it runs
            previewTrack.style.animation = 'none';
            setTimeout(() => {
                previewTrack.style.animation = animationName + ' ' + speed + 's linear infinite';
                previewTrack.style.animationPlayState = 'running';
            }, 10);
        }

        // Add event listeners to all fields
        const fields = [
            speedField, directionField, bgField, fontField, 
            sizeField, paddingField, shadowField, borderField, 
            borderColorField, textShadowField, hoverEffectField,
            positionField, zindexField, itemPaddingField,
            itemSpacingField, iconTypeField, iconEnabledField
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

        updatePreview(); // initialization
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
    `;
    document.head.appendChild(style);
    </script>
    <?php
}

// =======================
// Admin fields
// =======================
function wp_marquee_enabled_field() {
    $enabled = get_option('marquee_enabled', '1');
    echo '<label><input type="checkbox" name="marquee_enabled" value="1" ' . checked('1', $enabled, false) . '> Enable banner display</label>';
    echo '<p class="description">If disabled, the banner will not appear anywhere.</p>';
}

function wp_marquee_speed_field() { 
    $speed = get_option('marquee_speed', 20);
    echo '<input type="range" name="marquee_speed" min="5" max="60" value="' . esc_attr($speed) . '" class="marquee-speed-slider" oninput="document.getElementById(\'speed-value\').innerHTML = this.value + \'s\'">';
    echo '<span id="speed-value" style="margin-left:10px; font-weight:bold;">' . esc_html($speed) . 's</span>';
    echo '<p class="description">Animation speed (lower values = faster, higher values = slower). Recommended: 15-30 seconds.</p>';
}

function wp_marquee_direction_field() {
    $direction = get_option('marquee_direction', 'left');
    ?>
    <select name="marquee_direction" class="regular-text">
        <option value="left" <?php selected($direction, 'left'); ?>>Left ← (default)</option>
        <option value="right" <?php selected($direction, 'right'); ?>>Right →</option>
    </select>
    <p class="description">Direction in which the text moves.</p>
    <?php
}

function wp_marquee_bg_field() { 
    echo '<input type="color" name="marquee_bg" value="' . esc_attr(get_option('marquee_bg', '#ffffff')) . '">';
    echo '<p class="description">Banner background color.</p>';
}

function wp_marquee_font_field() { 
    echo '<input type="text" name="marquee_font" value="' . esc_attr(get_option('marquee_font', 'Arial, sans-serif')) . '" class="regular-text">';
    echo '<p class="description">Text font (ex: Arial, Helvetica, sans-serif).</p>';
}

function wp_marquee_size_field() { 
    echo '<input type="number" name="marquee_size" value="' . esc_attr(get_option('marquee_size', 20)) . '" min="10" max="100" class="small-text"> px';
    echo '<p class="description">Text size in pixels.</p>';
}

function wp_marquee_padding_field() { 
    echo '<input type="number" name="marquee_padding" value="' . esc_attr(get_option('marquee_padding', 10)) . '" min="0" max="100" class="small-text"> px';
    echo '<p class="description">Banner inner space.</p>';
}

function wp_marquee_display_type_field() {
    $display_type = get_option('marquee_display_type', 'all');
    ?>
    <select name="marquee_display_type" class="regular-text">
        <option value="all" <?php selected($display_type, 'all'); ?>>All pages</option>
        <option value="selected" <?php selected($display_type, 'selected'); ?>>Only selected pages</option>
    </select>
    <p class="description">Select where the banner will appear.</p>
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
        echo '<p style="margin-top:0; font-weight:bold;">Select pages:</p>';
        foreach ($pages as $page) {
            $checked = in_array($page->ID, $selected_pages) ? 'checked' : '';
            echo '<label style="display:block; margin-bottom:5px; padding:2px 0;">';
            echo '<input type="checkbox" name="marquee_selected_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . '> ';
            echo esc_html($page->post_title) . ' <span style="color:#666;">(ID: ' . $page->ID . ')</span>';
            echo '</label>';
        }
    } else {
        echo '<p>No published pages found.</p>';
    }
    echo '</div>';
    echo '<p class="description">Only appears when "Only selected pages" option is enabled.</p>';
}

// Text items field
function wp_marquee_text_items_field() {
    $text_items = json_decode(get_option('marquee_text_items', '[]'), true);
    if (empty($text_items)) {
        $text_items = array(
            array(
                'text' => '🎁 Special offer! Click here for details',
                'url' => '',
                'icon_color' => '#ff6b6b',
                'border_color' => '#ff6b6b',
                'text_color' => '#333333'
            )
        );
    }
    
    // Get all pages for dropdown
    $all_pages = get_pages(array(
        'post_status' => 'publish',
        'sort_column' => 'post_title',
        'sort_order' => 'ASC'
    ));
    
    echo '<div class="marquee-items-container" style="margin-bottom: 15px;">';
    echo '<input type="hidden" name="marquee_text_items" id="marquee-text-items-json" value=\'' . esc_attr(get_option('marquee_text_items')) . '\'>';
    
    // Collapsible container
    echo '<div class="marquee-items-collapsible" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">';
    
    foreach ($text_items as $index => $item) {
        echo '<div class="marquee-item" data-index="' . $index . '" style="border-bottom: 1px solid #eee; padding: 12px 15px; background: #fff; transition: all 0.3s ease;">';
        
        // Header with collapse toggle
        echo '<div class="marquee-item-header" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer; padding-bottom: 8px;" onclick="toggleMarqueeItem(this)">';
        echo '<strong style="font-size: 14px;">Element #' . ($index + 1) . '</strong>';
        echo '<div>';
        echo '<button type="button" class="button button-small marquee-remove-item" style="background: #dc3545; border-color: #dc3545; color: white; margin-left: 5px;">Delete</button>';
        echo '<span class="dashicons dashicons-arrow-down" style="margin-left: 8px; transition: transform 0.3s;"></span>';
        echo '</div>';
        echo '</div>';
        
        // Fields container (initially hidden)
        echo '<div class="marquee-item-fields" style="display: none; padding-top: 15px; border-top: 1px solid #f0f0f0; margin-top: 8px;">';
        
        // Text field
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px;">Text:</label>';
        echo '<input type="text" class="regular-text marquee-item-text" value="' . esc_attr($item['text']) . '" placeholder="Enter text..." style="width: 100%; padding: 8px;">';
        echo '</div>';
        
        // URL field with page selector
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px;">URL (optional):</label>';
        echo '<div style="display: flex; gap: 10px;">';
        echo '<input type="text" class="regular-text marquee-item-url" value="' . esc_attr($item['url']) . '" placeholder="https://... or select a page" style="flex: 1; padding: 8px;">';
        
        // Dropdown for pages
        echo '<select class="marquee-page-selector" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; background: white; cursor: pointer;" onchange="selectPage(this)">';
        echo '<option value="">Select a page</option>';
        if (!empty($all_pages)) {
            foreach ($all_pages as $page) {
                $page_url = get_permalink($page->ID);
                $selected = ($item['url'] == $page_url) ? 'selected' : '';
                echo '<option value="' . esc_attr($page_url) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
            }
        }
        echo '</select>';
        echo '</div>';
        echo '</div>';
        
        // Colors compact row
        echo '<div style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 12px;">';
        echo '<label style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 13px;">Colors:</label>';
        echo '<div style="display: flex; gap: 15px; align-items: center;">';
        
        // Icon color
        echo '<div style="flex: 1;">';
        echo '<label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Icon:</label>';
        echo '<div style="display: flex; align-items: center; gap: 8px;">';
        echo '<input type="color" class="marquee-item-icon-color" value="' . esc_attr($item['icon_color']) . '" style="width: 30px; height: 30px; padding: 0; border-radius: 4px;">';
        echo '<span style="font-size: 12px; color: #888;">' . esc_html($item['icon_color']) . '</span>';
        echo '</div>';
        echo '</div>';
        
        // Border color
        echo '<div style="flex: 1;">';
        echo '<label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Border:</label>';
        echo '<div style="display: flex; align-items: center; gap: 8px;">';
        echo '<input type="color" class="marquee-item-border-color" value="' . esc_attr($item['border_color']) . '" style="width: 30px; height: 30px; padding: 0; border-radius: 4px;">';
        echo '<span style="font-size: 12px; color: #888;">' . esc_html($item['border_color']) . '</span>';
        echo '</div>';
        echo '</div>';
        
        // Text color
        echo '<div style="flex: 1;">';
        echo '<label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Text:</label>';
        echo '<div style="display: flex; align-items: center; gap: 8px;">';
        echo '<input type="color" class="marquee-item-text-color" value="' . esc_attr($item['text_color']) . '" style="width: 30px; height: 30px; padding: 0; border-radius: 4px;">';
        echo '<span style="font-size: 12px; color: #888;">' . esc_html($item['text_color']) . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>'; // End colors row
        echo '</div>'; // End colors container
        
        echo '</div>'; // End fields container
        echo '</div>'; // End marquee-item
    }
    
    echo '</div>'; // End collapsible container
    echo '</div>'; // End items container
    
    echo '<button type="button" class="button button-secondary marquee-add-item" style="margin-top: 10px;">+ Add Element</button>';
    echo '<p class="description">Each element will appear in the banner with colored icon and border. You can add as many elements as you want.</p>';
    
    // JavaScript for handling dynamic items
    echo '<script>
    function toggleMarqueeItem(header) {
        const fields = header.nextElementSibling;
        const arrow = header.querySelector(".dashicons");
        
        if (fields.style.display === "none") {
            fields.style.display = "block";
            arrow.style.transform = "rotate(180deg)";
        } else {
            fields.style.display = "none";
            arrow.style.transform = "rotate(0deg)";
        }
    }
    
    function selectPage(selectElement) {
        const urlField = selectElement.parentElement.querySelector(".marquee-item-url");
        if (selectElement.value) {
            urlField.value = selectElement.value;
            
            // Trigger change event to update JSON
            const event = new Event("change", { bubbles: true });
            urlField.dispatchEvent(event);
        }
        // Reset dropdown
        selectElement.value = "";
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.querySelector(".marquee-items-container");
        const jsonField = document.getElementById("marquee-text-items-json");
        const addButton = document.querySelector(".marquee-add-item");
        
        // Function to update JSON field
        function updateJSON() {
            const items = [];
            document.querySelectorAll(".marquee-item").forEach(function(item) {
                const text = item.querySelector(".marquee-item-text").value;
                const url = item.querySelector(".marquee-item-url").value;
                const iconColor = item.querySelector(".marquee-item-icon-color").value;
                const borderColor = item.querySelector(".marquee-item-border-color").value;
                const textColor = item.querySelector(".marquee-item-text-color").value;
                
                if (text.trim() !== "") {
                    items.push({
                        text: text,
                        url: url,
                        icon_color: iconColor,
                        border_color: borderColor,
                        text_color: textColor
                    });
                }
            });
            
            jsonField.value = JSON.stringify(items);
            
            // Update preview if function exists
            if (typeof updatePreview === "function") {
                updatePreview();
            }
        }
        
        // Add new item
        addButton.addEventListener("click", function() {
            const items = container.querySelectorAll(".marquee-item");
            const index = items.length;
            
            // Get pages for dropdown
            const pages = ' . json_encode($all_pages) . ';
            
            const newItem = document.createElement("div");
            newItem.className = "marquee-item";
            newItem.setAttribute("data-index", index);
            newItem.style.borderBottom = "1px solid #eee";
            newItem.style.padding = "12px 15px";
            newItem.style.background = "#fff";
            
            let pagesOptions = "";
            if (pages && Array.isArray(pages)) {
                pages.forEach(function(page) {
                    pagesOptions += `<option value="${page.guid}">${page.post_title}</option>`;
                });
            }
            
            newItem.innerHTML = `
                <div class="marquee-item-header" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer; padding-bottom: 8px;" onclick="toggleMarqueeItem(this)">
                    <strong style="font-size: 14px;">Element #${index + 1}</strong>
                    <div>
                        <button type="button" class="button button-small marquee-remove-item" style="background: #dc3545; border-color: #dc3545; color: white; margin-left: 5px;">Delete</button>
                        <span class="dashicons dashicons-arrow-down" style="margin-left: 8px; transition: transform 0.3s;"></span>
                    </div>
                </div>
                <div class="marquee-item-fields" style="display: none; padding-top: 15px; border-top: 1px solid #f0f0f0; margin-top: 8px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px;">Text:</label>
                        <input type="text" class="regular-text marquee-item-text" placeholder="Enter text..." style="width: 100%; padding: 8px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px;">URL (optional):</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" class="regular-text marquee-item-url" placeholder="https://... or select a page" style="flex: 1; padding: 8px;">
                            <select class="marquee-page-selector" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; background: white; cursor: pointer;" onchange="selectPage(this)">
                                <option value="">Select a page</option>
                                ${pagesOptions}
                            </select>
                        </div>
                    </div>
                    <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 12px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 13px;">Colors:</label>
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <div style="flex: 1;">
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Icon:</label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="color" class="marquee-item-icon-color" value="#ff6b6b" style="width: 30px; height: 30px; padding: 0; border-radius: 4px;">
                                    <span style="font-size: 12px; color: #888;">#ff6b6b</span>
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Border:</label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="color" class="marquee-item-border-color" value="#ff6b6b" style="width: 30px; height: 30px; padding: 0; border-radius: 4px;">
                                    <span style="font-size: 12px; color: #888;">#ff6b6b</span>
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Text:</label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="color" class="marquee-item-text-color" value="#333333" style="width: 30px; height: 30px; padding: 0; border-radius: 4px;">
                                    <span style="font-size: 12px; color: #888;">#333333</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Insert before the add button
            const collapsible = container.querySelector(".marquee-items-collapsible");
            collapsible.appendChild(newItem);
            
            // Add event listeners to new fields
            newItem.querySelectorAll("input, select").forEach(function(field) {
                if (field.classList.contains("marquee-page-selector")) {
                    // Page selector already has onchange handler
                } else {
                    field.addEventListener("change", function() {
                        // Update color span
                        if (field.type === "color") {
                            const span = field.nextElementSibling;
                            if (span) {
                                span.textContent = field.value;
                            }
                        }
                        updateJSON();
                    });
                    field.addEventListener("input", updateJSON);
                }
            });
            
            // Add remove event listener
            newItem.querySelector(".marquee-remove-item").addEventListener("click", function() {
                if (confirm("Are you sure you want to delete this element?")) {
                    newItem.remove();
                    renumberItems();
                    updateJSON();
                }
            });
            
            updateJSON();
        });
        
        // Remove item function
        function setupRemoveButtons() {
            document.querySelectorAll(".marquee-remove-item").forEach(function(button) {
                button.addEventListener("click", function() {
                    const item = this.closest(".marquee-item");
                    if (item && confirm("Are you sure you want to delete this element?")) {
                        item.remove();
                        renumberItems();
                        updateJSON();
                    }
                });
            });
        }
        
        // Renumber items
        function renumberItems() {
            document.querySelectorAll(".marquee-item").forEach(function(item, index) {
                item.setAttribute("data-index", index);
                item.querySelector(".marquee-item-header strong").textContent = "Element #" + (index + 1);
            });
        }
        
        // Setup existing remove buttons
        setupRemoveButtons();
        
        // Add event listeners to existing fields
        document.querySelectorAll(".marquee-item input, .marquee-item select").forEach(function(field) {
            if (field.classList.contains("marquee-page-selector")) {
                // Skip page selectors - they have inline onchange
            } else {
                field.addEventListener("change", function() {
                    if (field.type === "color") {
                        const span = field.nextElementSibling;
                        if (span && span.tagName === "SPAN") {
                            span.textContent = field.value;
                        }
                    }
                    updateJSON();
                });
                field.addEventListener("input", updateJSON);
            }
        });
        
        // Initial update
        updateJSON();
    });
    </script>';
}

function wp_marquee_item_padding_field() {
    echo '<input type="number" name="marquee_item_padding" value="' . esc_attr(get_option('marquee_item_padding', 10)) . '" min="0" max="50" class="small-text"> px';
    echo '<p class="description">Inner space of each banner element.</p>';
}

function wp_marquee_item_spacing_field() {
    echo '<input type="number" name="marquee_item_spacing" value="' . esc_attr(get_option('marquee_item_spacing', 20)) . '" min="0" max="100" class="small-text"> px';
    echo '<p class="description">Distance between banner elements.</p>';
}

function wp_marquee_icon_enabled_field() {
    $icon_enabled = get_option('marquee_icon_enabled', '1');
    echo '<label><input type="checkbox" name="marquee_icon_enabled" value="1" ' . checked('1', $icon_enabled, false) . '> Show icon next to text</label>';
    echo '<p class="description">Display colored icon before each text element.</p>';
}

function wp_marquee_icon_type_field() {
    $icon_type = get_option('marquee_icon_type', 'circle');
    ?>
    <select name="marquee_icon_type" class="regular-text">
        <option value="none" <?php selected($icon_type, 'none'); ?>>No icon</option>
        <option value="circle" <?php selected($icon_type, 'circle'); ?>>● Circle (default)</option>
        <option value="square" <?php selected($icon_type, 'square'); ?>>■ Square</option>
    </select>
    <p class="description">Shape of the icon next to each text.</p>
    <?php
}

// Design fields
function wp_marquee_shadow_field() {
    $shadow = get_option('marquee_shadow', '0');
    echo '<label><input type="checkbox" name="marquee_shadow" value="1" ' . checked('1', $shadow, false) . '> Add banner shadow</label>';
    echo '<p class="description">Add a subtle shadow effect to the banner.</p>';
}

function wp_marquee_border_field() {
    $border = get_option('marquee_border', '0');
    echo '<label><input type="checkbox" name="marquee_border" value="1" ' . checked('1', $border, false) . '> Add border</label>';
    echo '<p class="description">Add a subtle border to the banner.</p>';
}

function wp_marquee_border_color_field() {
    echo '<input type="color" name="marquee_border_color" value="' . esc_attr(get_option('marquee_border_color', '#dddddd')) . '">';
    echo '<p class="description">Border color (only applies if border is enabled).</p>';
}

function wp_marquee_text_shadow_field() {
    $text_shadow = get_option('marquee_text_shadow', '0');
    echo '<label><input type="checkbox" name="marquee_text_shadow" value="1" ' . checked('1', $text_shadow, false) . '> Add text shadow</label>';
    echo '<p class="description">Add a subtle shadow effect to the text.</p>';
}

function wp_marquee_hover_effect_field() {
    $hover_effect = get_option('marquee_hover_effect', '1');
    echo '<label><input type="checkbox" name="marquee_hover_effect" value="1" ' . checked('1', $hover_effect, false) . '> Enable hover effect</label>';
    echo '<p class="description">When user hovers over text, animation pauses.</p>';
}

function wp_marquee_zindex_field() {
    echo '<input type="number" name="marquee_zindex" value="' . esc_attr(get_option('marquee_zindex', '999')) . '" min="1" max="9999" class="small-text">';
    echo '<p class="description">Display layer control (z-index). Higher values = above other elements.</p>';
}

// Position field
function wp_marquee_position_field() {
    $position = get_option('marquee_position', 'after_menu');
    ?>
    <select name="marquee_position" class="regular-text">
        <option value="body_open" <?php selected($position, 'body_open'); ?>>Beginning of page (after body tag)</option>
        <option value="after_menu" <?php selected($position, 'after_menu'); ?>>After menu (in header, under menu)</option>
        <option value="before_content" <?php selected($position, 'before_content'); ?>>Before content</option>
        <option value="shortcode" <?php selected($position, 'shortcode'); ?>>Only via shortcode [marquee]</option>
    </select>
    <p class="description">
        <strong>Beginning of page:</strong> Banner appears at the beginning, immediately after opening the body tag.<br>
        <strong>After menu:</strong> Banner appears in header, immediately after the main menu (under menu).<br>
        <strong>Before content:</strong> Banner appears before the main page content.<br>
        <strong>Only via shortcode:</strong> Banner appears only where the [marquee] shortcode is manually inserted.
    </p>
    <?php
}

// =======================
// Shortcode with dynamic settings - MODIFIED TO USE <a href>
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
    $icon_enabled = get_option('marquee_icon_enabled', '1') === '1' && $icon_type !== 'none';
    
    // Get text items
    $text_items = json_decode(get_option('marquee_text_items', '[]'), true);
    if (empty($text_items)) {
        $text_items = array(
            array(
                'text' => '🎁 Special offer! Click here for details',
                'url' => '',
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
        $container_style .= 'box-shadow:0 2px 8px rgba(0,0,0,0.08);';
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
    
    // Build items HTML - USING <a href> FOR LINKS
    $items_html = '';
    foreach ($text_items as $item) {
        $item_style = 'border-left: 2px solid ' . esc_attr($item['border_color']) . ';';
        $item_style .= 'padding: ' . absint($item_padding) . 'px;';
        $item_style .= 'margin-right: ' . absint($item_spacing) . 'px;';
        $item_style .= 'display: inline-flex;';
        $item_style .= 'align-items: center;';
        $item_style .= 'flex-shrink: 0;';
        
        $text_style = 'color:' . esc_attr($item['text_color']) . ';';
        $text_style .= 'font-family:' . esc_attr($font) . ';';
        $text_style .= 'font-size:' . absint($size) . 'px;';
        if ($text_shadow) {
            $text_style .= 'text-shadow:1px 1px 2px rgba(0,0,0,0.15);';
        }
        
        $item_html = '';
        
        // If URL exists, wrap in <a> tag
        if (!empty($item['url'])) {
            $item_html .= '<a href="' . esc_url($item['url']) . '" class="marquee-item-link" style="' . $item_style . 'text-decoration: none; color: inherit;">';
        } else {
            $item_html .= '<div class="marquee-item" style="' . $item_style . '">';
        }
        
        // Add icon if enabled
        if ($icon_enabled) {
            $icon_style = 'background:' . esc_attr($item['icon_color']) . ';';
            $icon_style .= 'border-radius:' . $icon_border_radius . ';';
            $icon_style .= 'display: inline-block;';
            $icon_style .= 'width: 8px;';
            $icon_style .= 'height: 8px;';
            $icon_style .= 'margin-right: 6px;';
            $icon_style .= 'flex-shrink: 0;';
            $item_html .= '<span class="marquee-icon" style="' . $icon_style . '"></span>';
        }
        
        $item_html .= '<span class="marquee-text" style="' . $text_style . '">' . esc_html($item['text']) . '</span>';
        
        // Close tag
        if (!empty($item['url'])) {
            $item_html .= '</a>';
        } else {
            $item_html .= '</div>';
        }
        
        $items_html .= $item_html;
    }
    
    // Build output
    $output = '<div class="' . esc_attr($container_class) . '" style="' . $container_style . '">';
    $output .= '<div class="marquee-track" style="' . $track_style . '">';
    $output .= $items_html;
    $output .= '</div>';
    $output .= '</div>';
    
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
// Dynamic CSS - MODIFIED FOR <a> TAGS
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
    
    .marquee-item,
    .marquee-item-link {
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        border-left-width: 2px;
        border-left-style: solid;
        flex-shrink: 0;
        cursor: pointer;
    }
    
    .marquee-item-link {
        text-decoration: none !important;
        color: inherit !important;
    }
    
    .marquee-item:hover,
    .marquee-item-link:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    
    .marquee-icon {
        display: inline-block;
        width: 8px;
        height: 8px;
        margin-right: 6px;
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
    
    /* Responsive design */
    @media (max-width: 768px) {
        .marquee-item,
        .marquee-item-link {
            padding: 8px !important;
            margin-right: 15px !important;
        }
        
        .marquee-icon {
            width: 6px;
            height: 6px;
            margin-right: 4px;
        }
        
        .wp-marquee-banner {
            padding: 8px !important;
        }
    }
    
    @media (max-width: 480px) {
        .marquee-item,
        .marquee-item-link {
            padding: 6px !important;
            margin-right: 10px !important;
        }
        
        .marquee-icon {
            width: 5px;
            height: 5px;
            margin-right: 3px;
        }
        
        .wp-marquee-banner {
            padding: 6px !important;
        }
    }
    </style>';
}
add_action('wp_head', 'wp_marquee_adv_css');

// =======================
// JavaScript for animation and hover - SIMPLIFIED SINCE WE USE <a> TAGS
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
    
    $hover_effect = get_option('marquee_hover_effect', '1') === '1';
    
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle hover effect for pausing animation
        <?php if ($hover_effect): ?>
        document.querySelectorAll('.marquee-item, .marquee-item-link').forEach(function(item) {
            const track = item.closest('.marquee-track');
            if (track) {
                item.addEventListener('mouseenter', function() {
                    track.style.animationPlayState = 'paused';
                });
                
                item.addEventListener('mouseleave', function() {
                    track.style.animationPlayState = 'running';
                });
            }
        });
        <?php endif; ?>
        
        // Duplicate items for continuous animation
        const tracks = document.querySelectorAll('.marquee-track');
        tracks.forEach(function(track) {
            // Check if elements are already duplicated
            const items = track.querySelectorAll('.marquee-item, .marquee-item-link');
            if (items.length < 10) {
                const originalHTML = track.innerHTML;
                track.innerHTML = originalHTML + originalHTML;
            }
        });
        
        // Position handling based on settings
        var position = '<?php echo esc_js(get_option('marquee_position', 'after_menu')); ?>';
        var banner = document.querySelector('.wp-marquee-banner');
        
        if (!banner) return;
        
        if (position === 'after_menu') {
            // Try to find the menu and insert banner after it
            var menus = document.querySelectorAll('nav, .nav, .navbar, .main-navigation, .menu, #menu, .site-navigation, header nav, #main-nav, .primary-menu');
            
            if (menus.length > 0) {
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
                var currentAnimation = track.style.animation;
                track.style.animation = 'none';
                void track.offsetWidth;
                track.style.animation = currentAnimation;
                track.style.animationPlayState = 'running';
            });
        }, 100);
    });
    
    // Fix for iOS Safari animation issues
    if (/iPad|iPhone|iPod/.test(navigator_userAgent) && !window.MSStream) {
        document.addEventListener('touchstart', function() {}, {passive: true});
    }
    </script>
    <?php
}
add_action('wp_footer', 'wp_marquee_adv_js');

// =======================
// Automatic display based on selected position
// =======================

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

// Hook for beginning of page (wp_body_open)
add_action('wp_body_open', function() {
    if (get_option('marquee_position', 'after_menu') === 'body_open') {
        wp_marquee_adv_display_banner();
    }
}, 5);

// Hook for after menu
add_action('wp_footer', function() {
    if (get_option('marquee_position', 'after_menu') === 'after_menu') {
        echo '<div id="wp-marquee-after-menu" style="display:none;">';
        wp_marquee_adv_display_banner();
        echo '</div>';
    }
}, 1);

// Hook for before content
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
    .wp-marquee-preview-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
        padding: 25px;
        border-radius: 10px;
        border: 1px solid #d0d7de;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .wp-marquee-preview-section h2 {
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
    
    .wp-marquee-admin-main {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        border: 1px solid #e0e0e0;
    }
    
    .preview-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-top: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .marquee-preview-wrapper {
        width: 100%;
        overflow: hidden;
        border-radius: 6px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        border: 2px dashed #e0e0e0;
        min-height: 60px;
        display: flex;
        align-items: center;
    }
    
    .marquee-preview-wrapper:hover {
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
        border-left-width: 2px;
        border-left-style: solid;
        flex-shrink: 0;
        cursor: pointer;
    }
    
    .marquee-item-preview:hover {
        transform: translateY(-1px);
    }
    
    .marquee-icon-preview {
        display: inline-block;
        width: 8px;
        height: 8px;
        margin-right: 6px;
        flex-shrink: 0;
    }
    
    .marquee-text-preview {
        white-space: nowrap;
        font-weight: 600;
        transition: all 0.3s ease;
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
        width: 40px;
        height: 40px;
        padding: 2px;
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
    
    /* Animation for preview */
    @keyframes marqueeScrollLeft {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    
    @keyframes marqueeScrollRight {
        0% { transform: translateX(-50%); }
        100% { transform: translateX(0); }
    }
    </style>
    <?php
}
add_action('admin_head', 'wp_marquee_adv_admin_styles');

// Auto-update from GitHub
add_filter('site_transient_update_plugins', function($transient){
    if(empty($transient->checked)) return $transient;

    $plugin_slug = plugin_basename(__FILE__);
    
    if (!isset($transient->checked[$plugin_slug])) {
        return $transient;
    }

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
