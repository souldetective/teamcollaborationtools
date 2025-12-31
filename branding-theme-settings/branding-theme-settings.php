<?php
/**
 * Plugin Name: Branding & Theme Settings
 * Description: Provides a full admin UI to manage global branding colors, typography, layout, buttons/forms, footer links, and advanced options with dynamic CSS output.
 * Version: 1.0.0
 * Author: aichatbotfree
 * License: GPL-2.0+
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ATBS_Branding_Settings')) {
    /**
     * Drop this file (and the assets folder) into wp-content/plugins/branding-theme-settings/ or include via your theme.
     */
    class ATBS_Branding_Settings {
        const OPTION_KEY = 'theme_branding_settings';
        const NONCE_FIELD = 'atbs_branding_nonce';
        const MENU_SLUG = 'atbs-branding-settings';

        /**
         * Initialize hooks.
         */
        public static function init() {
            add_action('admin_menu', [__CLASS__, 'add_menu']);
            add_action('admin_init', [__CLASS__, 'register_settings']);
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
            add_action('wp_head', [__CLASS__, 'output_dynamic_css']);
        }

        /**
         * Default settings to merge with saved options.
         */
        public static function defaults() {
            return [
                'colors' => [
                    'link_default' => '#0066FF',
                    'link_visited' => '#0A1A2F',
                    'link_hover' => '#0051CC',
                    'background' => '#FFFFFF',
                    'font' => '#2C3A4A',
                    'heading' => '#0A1A2F',
                    'border' => '#D5D9E0',
                    'success' => '#00C27A',
                    'warning' => '#F9A825',
                    'error' => '#E53935',
                ],
                'typography' => [
                    'font_family' => 'Poppins, Inter, Arial, sans-serif',
                    'heading_font_family' => '',
                    'base_font_size' => '16',
                    'line_height' => '1.6',
                    'h1_size' => '36',
                    'h2_size' => '30',
                    'h3_size' => '26',
                    'h4_size' => '22',
                    'h5_size' => '18',
                    'h6_size' => '16',
                ],
                'layout' => [
                    'space_1' => '4',
                    'space_2' => '8',
                    'space_3' => '12',
                    'space_4' => '16',
                    'space_5' => '24',
                    'space_6' => '32',
                    'space_7' => '48',
                    'space_8' => '64',
                    'container_sm' => '576',
                    'container_md' => '768',
                    'container_lg' => '1024',
                    'container_xl' => '1280',
                    'border_radius' => '6',
                    'box_shadow' => '0 10px 30px rgba(0,0,0,0.08)',
                ],
                'buttons' => [
                    'primary_bg' => '#0066FF',
                    'primary_text' => '#FFFFFF',
                    'primary_hover' => '#0051CC',
                    'secondary_bg' => '#FFFFFF',
                    'secondary_border' => '#0A1A2F',
                    'secondary_text' => '#0A1A2F',
                    'ghost_text' => '#0066FF',
                    'ghost_hover_bg' => 'rgba(0,102,255,0.08)',
                ],
                'forms' => [
                    'input_bg' => '#FFFFFF',
                    'input_border' => '#D5D9E0',
                    'input_focus' => '#0066FF',
                    'label_color' => '#0A1A2F',
                ],
                'footer' => [
                    'logo_id' => '',
                    'description' => '',
                    'col2_title' => 'Popular Links',
                    'col3_title' => 'Resources',
                    'col4_title' => 'Company',
                    'col2_links' => [],
                    'col3_links' => [],
                    'col4_links' => [],
                ],
                'advanced' => [
                    'enable_dark' => 0,
                    'dark_bg' => '#0A1A2F',
                    'dark_text' => '#FFFFFF',
                    'bp_sm' => '576',
                    'bp_md' => '768',
                    'bp_lg' => '1024',
                    'bp_xl' => '1280',
                ],
            ];
        }

        /**
         * Get merged settings.
         */
        public static function get_settings() {
            $saved = get_option(self::OPTION_KEY, []);
            $defaults = self::defaults();
            return wp_parse_args($saved, $defaults);
        }

        /**
         * Register menu.
         */
        public static function add_menu() {
            add_menu_page(
                __('Branding & Theme Settings', 'atbs'),
                __('Branding & Theme Settings', 'atbs'),
                'manage_options',
                self::MENU_SLUG,
                [__CLASS__, 'render_settings_page'],
                'dashicons-admin-customizer',
                60
            );
        }

        /**
         * Register settings, sections, and fields.
         */
        public static function register_settings() {
            register_setting('atbs_branding_settings_group', self::OPTION_KEY, [__CLASS__, 'sanitize_settings']);

            // Global Colors.
            add_settings_section('atbs_colors_section', __('Global Colors', 'atbs'), '__return_false', 'atbs_branding_tab_colors');
            self::add_color_field('link_default', __('Global Anchor Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('link_visited', __('Visited Link Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('link_hover', __('Hover Link Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('background', __('Website Background Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('font', __('Global Font Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('heading', __('Heading Text Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('border', __('Border Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('success', __('Success Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('warning', __('Warning Color', 'atbs'), 'atbs_colors_section');
            self::add_color_field('error', __('Error Color', 'atbs'), 'atbs_colors_section');

            // Typography.
            add_settings_section('atbs_typography_section', __('Typography', 'atbs'), '__return_false', 'atbs_branding_tab_typography');
            self::add_text_field('font_family', __('Global Font Family', 'atbs'), 'atbs_typography_section', 'typography');
            self::add_text_field('heading_font_family', __('Heading Font Family (optional)', 'atbs'), 'atbs_typography_section', 'typography');
            self::add_number_field('base_font_size', __('Base Font Size (px)', 'atbs'), 'atbs_typography_section', 'typography');
            self::add_number_field('line_height', __('Line Height', 'atbs'), 'atbs_typography_section', 'typography', ['step' => '0.1']);
            self::add_number_field('h1_size', __('H1 Size (px)', 'atbs'), 'atbs_typography_section', 'typography');
            self::add_number_field('h2_size', __('H2 Size (px)', 'atbs'), 'atbs_typography_section', 'typography');
            self::add_number_field('h3_size', __('H3 Size (px)', 'atbs'), 'atbs_typography_section', 'typography');
            self::add_number_field('h4_size', __('H4 Size (px)', 'atbs'), 'atbs_typography_section', 'typography');
            self::add_number_field('h5_size', __('H5 Size (px)', 'atbs'), 'atbs_typography_section', 'typography');
            self::add_number_field('h6_size', __('H6 Size (px)', 'atbs'), 'atbs_typography_section', 'typography');

            // Layout.
            add_settings_section('atbs_layout_section', __('Layout System', 'atbs'), '__return_false', 'atbs_branding_tab_layout');
            foreach (range(1, 8) as $i) {
                self::add_number_field('space_' . $i, sprintf(__('Spacing %s (px)', 'atbs'), $i), 'atbs_layout_section', 'layout');
            }
            self::add_number_field('container_sm', __('Container SM (px)', 'atbs'), 'atbs_layout_section', 'layout');
            self::add_number_field('container_md', __('Container MD (px)', 'atbs'), 'atbs_layout_section', 'layout');
            self::add_number_field('container_lg', __('Container LG (px)', 'atbs'), 'atbs_layout_section', 'layout');
            self::add_number_field('container_xl', __('Container XL (px)', 'atbs'), 'atbs_layout_section', 'layout');
            self::add_number_field('border_radius', __('Global Border Radius (px)', 'atbs'), 'atbs_layout_section', 'layout');
            self::add_text_field('box_shadow', __('Box Shadow Preset', 'atbs'), 'atbs_layout_section', 'layout');

            // Buttons & Forms.
            add_settings_section('atbs_buttons_section', __('Buttons & Forms', 'atbs'), '__return_false', 'atbs_branding_tab_buttons');
            self::add_color_field('primary_bg', __('Primary Button Background', 'atbs'), 'atbs_buttons_section', 'buttons');
            self::add_color_field('primary_text', __('Primary Button Text', 'atbs'), 'atbs_buttons_section', 'buttons');
            self::add_color_field('primary_hover', __('Primary Button Hover Background', 'atbs'), 'atbs_buttons_section', 'buttons');
            self::add_color_field('secondary_bg', __('Secondary Button Background', 'atbs'), 'atbs_buttons_section', 'buttons');
            self::add_color_field('secondary_border', __('Secondary Button Border', 'atbs'), 'atbs_buttons_section', 'buttons');
            self::add_color_field('secondary_text', __('Secondary Button Text', 'atbs'), 'atbs_buttons_section', 'buttons');
            self::add_color_field('ghost_text', __('Ghost Button Text', 'atbs'), 'atbs_buttons_section', 'buttons');
            self::add_color_field('ghost_hover_bg', __('Ghost Button Hover Background', 'atbs'), 'atbs_buttons_section', 'buttons');
            self::add_color_field('input_bg', __('Input Background', 'atbs'), 'atbs_buttons_section', 'forms', 'buttons');
            self::add_color_field('input_border', __('Input Border', 'atbs'), 'atbs_buttons_section', 'forms', 'buttons');
            self::add_color_field('input_focus', __('Input Focus Color', 'atbs'), 'atbs_buttons_section', 'forms', 'buttons');
            self::add_color_field('label_color', __('Label Color', 'atbs'), 'atbs_buttons_section', 'forms', 'buttons');

            // Footer builder.
            add_settings_section('atbs_footer_section', __('Footer Builder', 'atbs'), '__return_false', 'atbs_branding_tab_footer');
            add_settings_field('footer_logo', __('Footer Logo', 'atbs'), [__CLASS__, 'render_media_field'], 'atbs_branding_tab_footer', 'atbs_footer_section', ['key' => 'logo_id']);
            add_settings_field('footer_description', __('Footer Description', 'atbs'), [__CLASS__, 'render_textarea_field'], 'atbs_branding_tab_footer', 'atbs_footer_section', ['key' => 'description']);
            self::add_text_field('col2_title', __('Column 2 Title', 'atbs'), 'atbs_footer_section', 'footer');
            add_settings_field('col2_links', __('Column 2 Links', 'atbs'), [__CLASS__, 'render_links_repeater'], 'atbs_branding_tab_footer', 'atbs_footer_section', ['column' => 'col2_links']);
            self::add_text_field('col3_title', __('Column 3 Title', 'atbs'), 'atbs_footer_section', 'footer');
            add_settings_field('col3_links', __('Column 3 Links', 'atbs'), [__CLASS__, 'render_links_repeater'], 'atbs_branding_tab_footer', 'atbs_footer_section', ['column' => 'col3_links']);
            self::add_text_field('col4_title', __('Column 4 Title', 'atbs'), 'atbs_footer_section', 'footer');
            add_settings_field('col4_links', __('Column 4 Links', 'atbs'), [__CLASS__, 'render_links_repeater'], 'atbs_branding_tab_footer', 'atbs_footer_section', ['column' => 'col4_links']);

            // Advanced.
            add_settings_section('atbs_advanced_section', __('Advanced', 'atbs'), '__return_false', 'atbs_branding_tab_advanced');
            add_settings_field('enable_dark', __('Enable Dark Mode', 'atbs'), [__CLASS__, 'render_checkbox_field'], 'atbs_branding_tab_advanced', 'atbs_advanced_section', ['key' => 'enable_dark']);
            self::add_color_field('dark_bg', __('Dark Mode Background', 'atbs'), 'atbs_advanced_section', 'advanced');
            self::add_color_field('dark_text', __('Dark Mode Text', 'atbs'), 'atbs_advanced_section', 'advanced');
            self::add_number_field('bp_sm', __('Breakpoint SM (px)', 'atbs'), 'atbs_advanced_section', 'advanced');
            self::add_number_field('bp_md', __('Breakpoint MD (px)', 'atbs'), 'atbs_advanced_section', 'advanced');
            self::add_number_field('bp_lg', __('Breakpoint LG (px)', 'atbs'), 'atbs_advanced_section', 'advanced');
            self::add_number_field('bp_xl', __('Breakpoint XL (px)', 'atbs'), 'atbs_advanced_section', 'advanced');
        }

        private static function add_color_field($key, $label, $section, $group = 'colors', $page = null) {
            $page = $page ? $page : $group;
            add_settings_field($group . '_' . $key, $label, [__CLASS__, 'render_color_field'], 'atbs_branding_tab_' . $page, $section, [
                'group' => $group,
                'key' => $key,
            ]);
        }

        private static function add_text_field($key, $label, $section, $group = 'colors', $page = null) {
            $page = $page ? $page : $group;
            add_settings_field($group . '_' . $key, $label, [__CLASS__, 'render_text_field'], 'atbs_branding_tab_' . $page, $section, [
                'group' => $group,
                'key' => $key,
            ]);
        }

        private static function add_number_field($key, $label, $section, $group = 'colors', $args = [], $page = null) {
            $page = $page ? $page : $group;
            add_settings_field($group . '_' . $key, $label, [__CLASS__, 'render_number_field'], 'atbs_branding_tab_' . $page, $section, [
                'group' => $group,
                'key' => $key,
                'args' => $args,
            ]);
        }

        /**
         * Admin assets.
         */
        public static function enqueue_admin_assets($hook) {
            if ($hook !== 'toplevel_page_' . self::MENU_SLUG) {
                return;
            }
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_media();
            wp_enqueue_style('atbs-branding-admin', plugins_url('assets/css/admin-branding.css', __FILE__), [], '1.0.0');
            wp_enqueue_script('atbs-branding-admin', plugins_url('assets/js/admin-branding.js', __FILE__), ['jquery', 'wp-color-picker'], '1.0.0', true);
            wp_localize_script('atbs-branding-admin', 'ATBSBranding', [
                'addLink' => __('Add Link', 'atbs'),
                'remove' => __('Remove', 'atbs'),
                'moveUp' => __('Move Up', 'atbs'),
                'moveDown' => __('Move Down', 'atbs'),
                'upload' => __('Select Logo', 'atbs'),
            ]);
        }

        /**
         * Render settings page with tabs.
         */
        public static function render_settings_page() {
            if (!current_user_can('manage_options')) {
                return;
            }
            $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'colors';
            $tabs = [
                'colors' => __('Global Colors', 'atbs'),
                'typography' => __('Typography', 'atbs'),
                'layout' => __('Layout System', 'atbs'),
                'buttons' => __('Buttons & Forms', 'atbs'),
                'footer' => __('Footer Builder', 'atbs'),
                'advanced' => __('Advanced', 'atbs'),
            ];
            ?>
            <div class="wrap">
                <h1><?php esc_html_e('Branding & Theme Settings', 'atbs'); ?></h1>
                <h2 class="nav-tab-wrapper">
                    <?php foreach ($tabs as $tab => $label) : ?>
                        <?php $class = ($tab === $active_tab) ? ' nav-tab-active' : ''; ?>
                        <a class="nav-tab<?php echo esc_attr($class); ?>" href="<?php echo esc_url(admin_url('admin.php?page=' . self::MENU_SLUG . '&tab=' . $tab)); ?>"><?php echo esc_html($label); ?></a>
                    <?php endforeach; ?>
                </h2>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('atbs_branding_settings_group');
                    do_settings_sections('atbs_branding_tab_' . $active_tab);
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Sanitization callback for all options.
         */
        public static function sanitize_settings($input) {
            $defaults = self::defaults();
            $clean = wp_parse_args([], $defaults);

            // Colors.
            foreach ($defaults['colors'] as $key => $value) {
                if (isset($input['colors'][$key])) {
                    $sanitized = sanitize_hex_color($input['colors'][$key]);
                    $clean['colors'][$key] = $sanitized ? $sanitized : $value;
                }
            }

            // Typography.
            foreach ($defaults['typography'] as $key => $value) {
                if (!isset($input['typography'][$key])) {
                    continue;
                }
                if (in_array($key, ['font_family', 'heading_font_family'], true)) {
                    $clean['typography'][$key] = sanitize_text_field($input['typography'][$key]);
                } else {
                    $clean['typography'][$key] = floatval($input['typography'][$key]);
                }
            }

            // Layout.
            foreach ($defaults['layout'] as $key => $value) {
                if (!isset($input['layout'][$key])) {
                    continue;
                }
                $clean['layout'][$key] = ('box_shadow' === $key) ? sanitize_text_field($input['layout'][$key]) : floatval($input['layout'][$key]);
            }

            // Buttons.
            foreach ($defaults['buttons'] as $key => $value) {
                if (isset($input['buttons'][$key])) {
                    $clean['buttons'][$key] = sanitize_text_field($input['buttons'][$key]);
                }
            }

            // Forms.
            foreach ($defaults['forms'] as $key => $value) {
                if (isset($input['forms'][$key])) {
                    $clean['forms'][$key] = sanitize_text_field($input['forms'][$key]);
                }
            }

            // Footer.
            $footer = $defaults['footer'];
            $footer_input = isset($input['footer']) && is_array($input['footer']) ? $input['footer'] : [];
            $clean['footer']['logo_id'] = isset($footer_input['logo_id']) ? absint($footer_input['logo_id']) : '';
            $clean['footer']['description'] = isset($footer_input['description']) ? wp_kses_post($footer_input['description']) : '';
            $clean['footer']['col2_title'] = isset($footer_input['col2_title']) ? sanitize_text_field($footer_input['col2_title']) : $footer['col2_title'];
            $clean['footer']['col3_title'] = isset($footer_input['col3_title']) ? sanitize_text_field($footer_input['col3_title']) : $footer['col3_title'];
            $clean['footer']['col4_title'] = isset($footer_input['col4_title']) ? sanitize_text_field($footer_input['col4_title']) : $footer['col4_title'];

            foreach (['col2_links', 'col3_links', 'col4_links'] as $col) {
                $clean['footer'][$col] = [];
                if (empty($footer_input[$col]) || !is_array($footer_input[$col])) {
                    continue;
                }
                foreach ($footer_input[$col] as $row) {
                    if (empty($row['label']) && empty($row['url'])) {
                        continue;
                    }
                    $clean['footer'][$col][] = [
                        'label' => sanitize_text_field($row['label']),
                        'url' => esc_url_raw($row['url']),
                    ];
                }
            }

            // Advanced.
            foreach ($defaults['advanced'] as $key => $value) {
                if (!isset($input['advanced'][$key])) {
                    $clean['advanced'][$key] = ('enable_dark' === $key) ? 0 : $defaults['advanced'][$key];
                    continue;
                }
                if ('enable_dark' === $key) {
                    $clean['advanced'][$key] = !empty($input['advanced'][$key]) ? 1 : 0;
                } elseif (in_array($key, ['dark_bg', 'dark_text'], true)) {
                    $clean['advanced'][$key] = sanitize_hex_color($input['advanced'][$key]);
                } else {
                    $clean['advanced'][$key] = floatval($input['advanced'][$key]);
                }
            }

            return $clean;
        }

        /**
         * Field renderers.
         */
        public static function render_color_field($args) {
            $settings = self::get_settings();
            $group = isset($args['group']) ? $args['group'] : 'colors';
            $key = $args['key'];
            $value = isset($settings[$group][$key]) ? $settings[$group][$key] : '';
            printf('<input type="text" class="atbs-color-field" name="%1$s[%2$s][%3$s]" value="%4$s" data-default-color="%4$s" />', esc_attr(self::OPTION_KEY), esc_attr($group), esc_attr($key), esc_attr($value));
        }

        public static function render_text_field($args) {
            $settings = self::get_settings();
            $group = isset($args['group']) ? $args['group'] : 'colors';
            $key = $args['key'];
            $value = isset($settings[$group][$key]) ? $settings[$group][$key] : '';
            printf('<input type="text" class="regular-text" name="%1$s[%2$s][%3$s]" value="%4$s" />', esc_attr(self::OPTION_KEY), esc_attr($group), esc_attr($key), esc_attr($value));
        }

        public static function render_number_field($args) {
            $settings = self::get_settings();
            $group = isset($args['group']) ? $args['group'] : 'colors';
            $key = $args['key'];
            $value = isset($settings[$group][$key]) ? $settings[$group][$key] : '';
            $step = isset($args['args']['step']) ? $args['args']['step'] : '1';
            printf('<input type="number" step="%5$s" class="small-text" name="%1$s[%2$s][%3$s]" value="%4$s" />', esc_attr(self::OPTION_KEY), esc_attr($group), esc_attr($key), esc_attr($value), esc_attr($step));
        }

        public static function render_checkbox_field($args) {
            $settings = self::get_settings();
            $key = $args['key'];
            $checked = !empty($settings['advanced'][$key]);
            printf('<label><input type="checkbox" name="%1$s[advanced][%2$s]" value="1" %3$s /> %4$s</label>', esc_attr(self::OPTION_KEY), esc_attr($key), checked($checked, true, false), esc_html__('Enable', 'atbs'));
        }

        public static function render_media_field($args) {
            $settings = self::get_settings();
            $key = $args['key'];
            $value = isset($settings['footer'][$key]) ? absint($settings['footer'][$key]) : '';
            $image = $value ? wp_get_attachment_image($value, 'thumbnail') : '';
            ?>
            <div class="atbs-media-field" data-target="<?php echo esc_attr($key); ?>">
                <div class="preview"><?php echo $image ? wp_kses_post($image) : '<span class="placeholder">' . esc_html__('No logo selected', 'atbs') . '</span>'; ?></div>
                <input type="hidden" name="<?php echo esc_attr(self::OPTION_KEY); ?>[footer][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>" class="atbs-media-input" />
                <button type="button" class="button atbs-upload-media"><?php esc_html_e('Upload Logo', 'atbs'); ?></button>
                <button type="button" class="button atbs-remove-media"><?php esc_html_e('Remove', 'atbs'); ?></button>
            </div>
            <?php
        }

        public static function render_textarea_field($args) {
            $settings = self::get_settings();
            $key = $args['key'];
            $value = isset($settings['footer'][$key]) ? $settings['footer'][$key] : '';
            printf('<textarea name="%1$s[footer][%2$s]" rows="4" class="large-text">%3$s</textarea>', esc_attr(self::OPTION_KEY), esc_attr($key), esc_textarea($value));
        }

        public static function render_links_repeater($args) {
            $settings = self::get_settings();
            $column = $args['column'];
            $links = isset($settings['footer'][$column]) && is_array($settings['footer'][$column]) ? $settings['footer'][$column] : [];
            ?>
            <div class="atbs-links-repeater" data-column="<?php echo esc_attr($column); ?>">
                <div class="atbs-links-rows">
                    <?php if (!empty($links)) : ?>
                        <?php foreach ($links as $index => $link) : ?>
                            <?php self::render_link_row($column, $index, $link); ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <?php self::render_link_row($column, 0, []); ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="button button-secondary atbs-add-link" data-column="<?php echo esc_attr($column); ?>"><?php esc_html_e('Add Link', 'atbs'); ?></button>
            </div>
            <?php
        }

        private static function render_link_row($column, $index, $link = []) {
            $label = isset($link['label']) ? $link['label'] : '';
            $url = isset($link['url']) ? $link['url'] : '';
            ?>
            <div class="atbs-link-row" data-index="<?php echo esc_attr($index); ?>">
                <input type="text" class="regular-text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[footer][<?php echo esc_attr($column); ?>][<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($label); ?>" placeholder="<?php esc_attr_e('Link label', 'atbs'); ?>" />
                <input type="url" class="regular-text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[footer][<?php echo esc_attr($column); ?>][<?php echo esc_attr($index); ?>][url]" value="<?php echo esc_attr($url); ?>" placeholder="<?php esc_attr_e('Link URL', 'atbs'); ?>" />
                <button type="button" class="button atbs-move-up" aria-label="<?php esc_attr_e('Move up', 'atbs'); ?>">&#8593;</button>
                <button type="button" class="button atbs-move-down" aria-label="<?php esc_attr_e('Move down', 'atbs'); ?>">&#8595;</button>
                <button type="button" class="button atbs-remove-row">&times;</button>
            </div>
            <?php
        }

        /**
         * Output dynamic CSS in the head.
         */
        public static function output_dynamic_css() {
            $settings = self::get_settings();
            $colors = $settings['colors'];
            $typography = $settings['typography'];
            $layout = $settings['layout'];
            $buttons = $settings['buttons'];
            $forms = $settings['forms'];
            $advanced = $settings['advanced'];
            ?>
            <style id="atbs-branding-dynamic-css">
                :root {
                    --link-color: <?php echo esc_html($colors['link_default']); ?>;
                    --link-visited: <?php echo esc_html($colors['link_visited']); ?>;
                    --link-hover: <?php echo esc_html($colors['link_hover']); ?>;
                    --bg-color: <?php echo esc_html($colors['background']); ?>;
                    --text-color: <?php echo esc_html($colors['font']); ?>;
                    --heading-color: <?php echo esc_html($colors['heading']); ?>;
                    --border-color: <?php echo esc_html($colors['border']); ?>;
                    --success-color: <?php echo esc_html($colors['success']); ?>;
                    --warning-color: <?php echo esc_html($colors['warning']); ?>;
                    --error-color: <?php echo esc_html($colors['error']); ?>;
                    --font-family: <?php echo esc_html($typography['font_family']); ?>;
                    --heading-font-family: <?php echo esc_html($typography['heading_font_family'] ? $typography['heading_font_family'] : $typography['font_family']); ?>;
                    --base-font-size: <?php echo esc_html($typography['base_font_size']); ?>px;
                    --line-height: <?php echo esc_html($typography['line_height']); ?>;
                    --h1-size: <?php echo esc_html($typography['h1_size']); ?>px;
                    --h2-size: <?php echo esc_html($typography['h2_size']); ?>px;
                    --h3-size: <?php echo esc_html($typography['h3_size']); ?>px;
                    --h4-size: <?php echo esc_html($typography['h4_size']); ?>px;
                    --h5-size: <?php echo esc_html($typography['h5_size']); ?>px;
                    --h6-size: <?php echo esc_html($typography['h6_size']); ?>px;
                    --space-1: <?php echo esc_html($layout['space_1']); ?>px;
                    --space-2: <?php echo esc_html($layout['space_2']); ?>px;
                    --space-3: <?php echo esc_html($layout['space_3']); ?>px;
                    --space-4: <?php echo esc_html($layout['space_4']); ?>px;
                    --space-5: <?php echo esc_html($layout['space_5']); ?>px;
                    --space-6: <?php echo esc_html($layout['space_6']); ?>px;
                    --space-7: <?php echo esc_html($layout['space_7']); ?>px;
                    --space-8: <?php echo esc_html($layout['space_8']); ?>px;
                    --container-sm: <?php echo esc_html($layout['container_sm']); ?>px;
                    --container-md: <?php echo esc_html($layout['container_md']); ?>px;
                    --container-lg: <?php echo esc_html($layout['container_lg']); ?>px;
                    --container-xl: <?php echo esc_html($layout['container_xl']); ?>px;
                    --border-radius: <?php echo esc_html($layout['border_radius']); ?>px;
                    --box-shadow: <?php echo esc_html($layout['box_shadow']); ?>;
                    --btn-primary-bg: <?php echo esc_html($buttons['primary_bg']); ?>;
                    --btn-primary-text: <?php echo esc_html($buttons['primary_text']); ?>;
                    --btn-primary-hover: <?php echo esc_html($buttons['primary_hover']); ?>;
                    --btn-secondary-bg: <?php echo esc_html($buttons['secondary_bg']); ?>;
                    --btn-secondary-border: <?php echo esc_html($buttons['secondary_border']); ?>;
                    --btn-secondary-text: <?php echo esc_html($buttons['secondary_text']); ?>;
                    --btn-ghost-text: <?php echo esc_html($buttons['ghost_text']); ?>;
                    --btn-ghost-hover-bg: <?php echo esc_html($buttons['ghost_hover_bg']); ?>;
                    --input-bg: <?php echo esc_html($forms['input_bg']); ?>;
                    --input-border: <?php echo esc_html($forms['input_border']); ?>;
                    --input-focus: <?php echo esc_html($forms['input_focus']); ?>;
                    --label-color: <?php echo esc_html($forms['label_color']); ?>;
                    --bp-sm: <?php echo esc_html($advanced['bp_sm']); ?>px;
                    --bp-md: <?php echo esc_html($advanced['bp_md']); ?>px;
                    --bp-lg: <?php echo esc_html($advanced['bp_lg']); ?>px;
                    --bp-xl: <?php echo esc_html($advanced['bp_xl']); ?>px;
                }

                body {
                    background-color: var(--bg-color);
                    color: var(--text-color);
                    font-family: var(--font-family);
                    font-size: var(--base-font-size);
                    line-height: var(--line-height);
                }

                h1, h2, h3, h4, h5, h6 {
                    color: var(--heading-color);
                    font-family: var(--heading-font-family);
                    margin-top: var(--space-6);
                    margin-bottom: var(--space-3);
                }
                h1 { font-size: var(--h1-size); }
                h2 { font-size: var(--h2-size); }
                h3 { font-size: var(--h3-size); }
                h4 { font-size: var(--h4-size); }
                h5 { font-size: var(--h5-size); }
                h6 { font-size: var(--h6-size); }

                a {
                    color: var(--link-color);
                    text-decoration: none;
                }
                a:visited { color: var(--link-visited); }
                a:hover, a:focus { color: var(--link-hover); text-decoration: underline; }

                .container {
                    width: 100%;
                    margin: 0 auto;
                    max-width: var(--container-xl);
                    padding-left: var(--space-5);
                    padding-right: var(--space-5);
                }

                .btn-primary {
                    background: var(--btn-primary-bg);
                    color: var(--btn-primary-text);
                    padding: 12px 24px;
                    border-radius: var(--border-radius);
                    border: none;
                    transition: background 0.2s ease;
                }
                .btn-primary:hover {
                    background: var(--btn-primary-hover);
                }
                .btn-secondary {
                    background: var(--btn-secondary-bg);
                    color: var(--btn-secondary-text);
                    border: 1px solid var(--btn-secondary-border);
                    padding: 12px 24px;
                    border-radius: var(--border-radius);
                }
                .btn-secondary:hover { background: #f5f7fa; }
                .btn-ghost {
                    background: transparent;
                    color: var(--btn-ghost-text);
                    border: none;
                    padding: 12px 16px;
                    border-radius: var(--border-radius);
                }
                .btn-ghost:hover { background: var(--btn-ghost-hover-bg); }

                input[type="text"], input[type="email"], input[type="url"], input[type="number"], textarea, select {
                    background: var(--input-bg);
                    border: 1px solid var(--input-border);
                    border-radius: var(--border-radius);
                    padding: 10px 12px;
                    width: 100%;
                }
                input:focus, textarea:focus, select:focus {
                    outline: 2px solid var(--input-focus);
                    border-color: var(--input-focus);
                }
                label { color: var(--label-color); }

                .card {
                    background: #fff;
                    border: 1px solid var(--border-color);
                    border-radius: var(--border-radius);
                    box-shadow: var(--box-shadow);
                    padding: var(--space-5);
                }

                footer.site-footer {
                    background: #0A1A2F;
                    color: #fff;
                    padding: var(--space-8) 0;
                }
                footer .footer-grid {
                    display: grid;
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: var(--space-5);
                }
                footer .footer-col-title { font-weight: 600; margin-bottom: var(--space-3); }
                footer a { color: #fff; }
                footer a:hover { color: var(--link-color); }

                <?php if (!empty($advanced['enable_dark'])) : ?>
                [data-theme="dark"] {
                    background: <?php echo esc_html($advanced['dark_bg']); ?>;
                    color: <?php echo esc_html($advanced['dark_text']); ?>;
                }
                [data-theme="dark"] a { color: #66a3ff; }
                [data-theme="dark"] .card { background: rgba(255,255,255,0.06); }
                <?php endif; ?>

                @media (max-width: <?php echo esc_html($advanced['bp_md']); ?>px) {
                    footer .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                }
                @media (max-width: <?php echo esc_html($advanced['bp_sm']); ?>px) {
                    footer .footer-grid { grid-template-columns: 1fr; }
                }
            </style>
            <?php
        }
    }

    ATBS_Branding_Settings::init();
}
