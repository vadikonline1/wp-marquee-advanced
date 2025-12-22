<?php
/*
Plugin Name: WP Marquee Advanced
Plugin URI:  https://github.com/vadikonline1/wp-marquee-advanced/
Description: Banner animat cu scroll infinit și setări complete în admin (text, culoare, font, dimensiune, fundal, viteza, margini).
Version:     1.0
Author:      Steel..xD
Author URI:  https://github.com/vadikonline1/wp-marquee-advanced/
License:     GPL2
*/

// =======================
// Înregistrare opțiuni
// =======================
function wp_marquee_adv_register_settings() {
    add_option('marquee_text', 'Acesta este textul meu animat!');
    add_option('marquee_speed', 15); 
    add_option('marquee_color', '#333333');
    add_option('marquee_bg', '#ffffff');
    add_option('marquee_font', 'Arial, sans-serif');
    add_option('marquee_size', '20'); 
    add_option('marquee_padding', '10');

    add_settings_section('marquee_main_section', 'Setări Marquee Avansate', null, 'marquee-settings');

    add_settings_field('marquee_text', 'Text Banner', 'wp_marquee_text_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_speed', 'Viteza (secunde)', 'wp_marquee_speed_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_color', 'Culoare Text', 'wp_marquee_color_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_bg', 'Culoare Fundal', 'wp_marquee_bg_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_font', 'Font Text', 'wp_marquee_font_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_size', 'Dimensiune Text (px)', 'wp_marquee_size_field', 'marquee-settings', 'marquee_main_section');
    add_settings_field('marquee_padding', 'Padding Banner (px)', 'wp_marquee_padding_field', 'marquee-settings', 'marquee_main_section');

    register_setting('marquee_options_group', 'marquee_text');
    register_setting('marquee_options_group', 'marquee_speed');
    register_setting('marquee_options_group', 'marquee_color');
    register_setting('marquee_options_group', 'marquee_bg');
    register_setting('marquee_options_group', 'marquee_font');
    register_setting('marquee_options_group', 'marquee_size');
    register_setting('marquee_options_group', 'marquee_padding');
}
add_action('admin_init', 'wp_marquee_adv_register_settings');

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
</div>
<?php
}

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
    <div id="marquee-preview" style="border:1px solid #333; padding:10px; overflow:hidden; width:100%;">
        <div id="marquee-preview-track" style="display:flex; width:max-content; animation-name:marqueeScroll; animation-timing-function:linear; animation-iteration-count:infinite;">
            <span id="marquee-preview-text" style="white-space:nowrap; padding-right:50px;">
                <?php echo esc_html(get_option('marquee_text')); ?>
            </span>
            <span id="marquee-preview-text2" style="white-space:nowrap; padding-right:50px;">
                <?php echo esc_html(get_option('marquee_text')); ?>
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
        let text = textField.value;
        let speed = speedField.value;
        let color = colorField.value;
        let bg = bgField.value;
        let font = fontField.value;
        let size = sizeField.value;
        let padding = paddingField.value;

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
        previewTrack.style.animationDuration = speed + 's';
    }

    [textField, speedField, colorField, bgField, fontField, sizeField, paddingField].forEach(function(field){
        field.addEventListener('input', updatePreview);
    });

    updatePreview(); // inițializare
});
</script>
<?php
}



// =======================
// Field-uri admin
// =======================
function wp_marquee_text_field() { echo '<input type="text" name="marquee_text" value="'.esc_attr(get_option('marquee_text')).'" style="width:50%;">'; }
function wp_marquee_speed_field() { echo '<input type="number" name="marquee_speed" value="'.esc_attr(get_option('marquee_speed')).'" min="1">'; }
function wp_marquee_color_field() { echo '<input type="color" name="marquee_color" value="'.esc_attr(get_option('marquee_color')).'">'; }
function wp_marquee_bg_field() { echo '<input type="color" name="marquee_bg" value="'.esc_attr(get_option('marquee_bg')).'">'; }
function wp_marquee_font_field() { echo '<input type="text" name="marquee_font" value="'.esc_attr(get_option('marquee_font')).'" style="width:50%;">'; }
function wp_marquee_size_field() { echo '<input type="number" name="marquee_size" value="'.esc_attr(get_option('marquee_size')).'" min="10">'; }
function wp_marquee_padding_field() { echo '<input type="number" name="marquee_padding" value="'.esc_attr(get_option('marquee_padding')).'" min="0">'; }

// =======================
// Shortcode cu setări dinamice
// =======================
function wp_marquee_adv_shortcode($atts, $content = null) {
    $text = $content ? $content : get_option('marquee_text');
    $speed = get_option('marquee_speed', 15);
    $color = get_option('marquee_color', '#333');
    $bg = get_option('marquee_bg', '#fff');
    $font = get_option('marquee_font', 'Arial, sans-serif');
    $size = get_option('marquee_size', '20');
    $padding = get_option('marquee_padding', '10');

    return '<div class="marquee-container" style="background:'.$bg.'; padding:'.$padding.'px;">
                <div class="marquee-track" style="animation-duration:'.$speed.'s;">
                    <span class="marquee-text" style="color:'.$color.'; font-family:'.$font.'; font-size:'.$size.'px;">'.esc_html($text).'</span>
                    <span class="marquee-text" style="color:'.$color.'; font-family:'.$font.'; font-size:'.$size.'px;">'.esc_html($text).'</span>
                </div>
            </div>';
}
add_shortcode('marquee', 'wp_marquee_adv_shortcode');

// =======================
// CSS dinamic
// =======================
function wp_marquee_adv_css() {
    echo '<style>
    .marquee-container { width:100%; overflow:hidden; cursor:pointer; box-sizing:border-box; }
    .marquee-track { display:flex; width:max-content; animation-name:marqueeScroll; animation-timing-function:linear; animation-iteration-count:infinite; }
    .marquee-text { white-space:nowrap; padding-right:50px; }
    @keyframes marqueeScroll { 0% { transform:translateX(0); } 100% { transform:translateX(-50%); } }
    .marquee-container:hover .marquee-track { animation-play-state:paused; }
    </style>';
}
add_action('wp_head', 'wp_marquee_adv_css');

// =======================
// Afișare automată sub header
// =======================
function wp_marquee_adv_display() {
    echo do_shortcode('[marquee]');
}
add_action('wp_body_open', 'wp_marquee_adv_display');


