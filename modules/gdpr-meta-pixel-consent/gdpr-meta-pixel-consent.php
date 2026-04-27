<?php
/**
 * Plugin Name: GDPR Meta Pixel Consent for PixelYourSite
 * Plugin URI:  https://example.com/
 * Description: Lightweight GDPR-style cookie consent banner that gates PixelYourSite's Meta/Facebook browser pixel until marketing consent is granted.
 * Version:     2.0.0
 * Author:      ChatGPT
 * License:     GPL-2.0-or-later
 * Text Domain: gdpr-meta-pixel-consent
 */

if (!defined('ABSPATH')) {
    exit;
}

final class GDPR_Meta_Pixel_Consent_Lite {
    private const OPTION_NAME = 'gmpp_settings';
    private const VERSION = '2.0.0';
    private const CONSENT_COOKIE = 'gmpp_meta_marketing_consent';
    private const LEGACY_COOKIE = 'gmpp_consent';
    private const PYS_CONSENT_COOKIE = 'pys_consent';

    public function __construct() {
        add_action('plugins_loaded', [$this, 'register_pixelyoursite_filters'], 1);
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend'], 99);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'settings_link']);
        add_shortcode('gdpr_meta_pixel_cookie_settings', [$this, 'cookie_settings_shortcode']);
    }

    public static function defaults(): array {
        return [
            'enabled' => '1',
            'block_external_id' => '1',
            'block_advanced_data' => '1',
            'reload_on_change' => '1',
            'banner_title' => __('Privacy preferences', 'gdpr-meta-pixel-consent'),
            'banner_message' => __('We use strictly necessary cookies to run this website. With your permission, PixelYourSite may load the Meta/Facebook browser Pixel for ads measurement, remarketing and audience building.', 'gdpr-meta-pixel-consent'),
            'privacy_url' => '',
            'accent_color' => '#2563eb',
            'position' => 'bottom',
            'retention_days' => '180',
            'show_settings_button' => '1',
            'settings_button_text' => __('Cookie settings', 'gdpr-meta-pixel-consent'),
        ];
    }

    public function get_settings(): array {
        $saved = get_option(self::OPTION_NAME, []);
        if (!is_array($saved)) {
            $saved = [];
        }
        return wp_parse_args($saved, self::defaults());
    }

    private function is_setting_enabled($value): bool {
        return in_array($value, [1, '1', true, 'true', 'yes', 'on'], true);
    }

    private function enabled(): bool {
        $settings = $this->get_settings();
        return $this->is_setting_enabled($settings['enabled'] ?? null);
    }

    private function has_marketing_consent(): bool {
        if (!$this->enabled()) {
            return true;
        }

        $raw = isset($_COOKIE[self::CONSENT_COOKIE]) ? sanitize_text_field(wp_unslash($_COOKIE[self::CONSENT_COOKIE])) : '';
        if ($raw === 'yes') {
            return true;
        }
        if ($raw === 'no') {
            return false;
        }

        // Backward compatibility with v1 cookie structure.
        $legacy = isset($_COOKIE[self::LEGACY_COOKIE]) ? wp_unslash($_COOKIE[self::LEGACY_COOKIE]) : '';
        if ($legacy) {
            $decoded = json_decode(stripslashes($legacy), true);
            if (is_array($decoded) && array_key_exists('marketing', $decoded)) {
                return (bool) $decoded['marketing'];
            }
        }

        return false;
    }

    private function should_block_meta(): bool {
        return $this->enabled() && !$this->has_marketing_consent();
    }

    public function register_pixelyoursite_filters(): void {
        add_filter('pys_disable_facebook_by_gdpr', [$this, 'filter_disable_facebook'], 1);
        add_filter('pys_check_consent_by_gdpr', [$this, 'filter_check_consent'], 1, 2);
        add_filter('pys_disable_externalID_by_gdpr', [$this, 'filter_disable_external_id'], 1);
        add_filter('pys_disable_advanced_form_data_cookie', [$this, 'filter_disable_advanced_data'], 1);
        add_filter('pys_disable_advance_data_cookie', [$this, 'filter_disable_advanced_data'], 1);
    }

    public function filter_disable_facebook($disabled): bool {
        return $this->should_block_meta() ? true : (bool) $disabled;
    }

    public function filter_check_consent($allowed, $pixel): bool {
        if ($pixel === 'facebook') {
            return $this->has_marketing_consent();
        }
        return (bool) $allowed;
    }

    public function filter_disable_external_id($disabled): bool {
        $settings = $this->get_settings();
        if ($this->is_setting_enabled($settings['block_external_id'] ?? null) && $this->should_block_meta()) {
            return true;
        }
        return (bool) $disabled;
    }

    public function filter_disable_advanced_data($disabled): bool {
        $settings = $this->get_settings();
        if ($this->is_setting_enabled($settings['block_advanced_data'] ?? null) && $this->should_block_meta()) {
            return true;
        }
        return (bool) $disabled;
    }

    public function settings_link(array $links): array {
        $url = admin_url('options-general.php?page=gmpp-settings');
        $links[] = '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'gdpr-meta-pixel-consent') . '</a>';
        return $links;
    }

    public function add_settings_page(): void {
        add_options_page(
            __('GDPR Meta Pixel Consent', 'gdpr-meta-pixel-consent'),
            __('GDPR Meta Pixel', 'gdpr-meta-pixel-consent'),
            'manage_options',
            'gmpp-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings(): void {
        register_setting('gmpp_settings_group', self::OPTION_NAME, [
            'sanitize_callback' => [$this, 'sanitize_settings'],
        ]);
    }

    public function sanitize_settings($input): array {
        $defaults = self::defaults();
        $input = is_array($input) ? $input : [];

        $retention_days = absint($input['retention_days'] ?? $defaults['retention_days']);
        $retention_days = max(1, min(365, $retention_days));

        $accent = sanitize_hex_color($input['accent_color'] ?? $defaults['accent_color']);
        if (!$accent) {
            $accent = $defaults['accent_color'];
        }

        return [
            'enabled' => !empty($input['enabled']) ? '1' : '0',
            'block_external_id' => !empty($input['block_external_id']) ? '1' : '0',
            'block_advanced_data' => !empty($input['block_advanced_data']) ? '1' : '0',
            'reload_on_change' => !empty($input['reload_on_change']) ? '1' : '0',
            'banner_title' => sanitize_text_field($input['banner_title'] ?? $defaults['banner_title']),
            'banner_message' => wp_kses_post($input['banner_message'] ?? $defaults['banner_message']),
            'privacy_url' => esc_url_raw($input['privacy_url'] ?? ''),
            'accent_color' => $accent,
            'position' => in_array(($input['position'] ?? 'bottom'), ['bottom', 'center'], true) ? $input['position'] : 'bottom',
            'retention_days' => (string) $retention_days,
            'show_settings_button' => !empty($input['show_settings_button']) ? '1' : '0',
            'settings_button_text' => sanitize_text_field($input['settings_button_text'] ?? $defaults['settings_button_text']),
        ];
    }

    public function render_settings_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = $this->get_settings();
        $pys_active = $this->is_plugin_active_file('pixelyoursite-pro/pixelyoursite-pro.php') || $this->is_plugin_active_file('pixelyoursite/facebook-pixel-master.php') || defined('PYS_VERSION');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('GDPR Meta Pixel Consent', 'gdpr-meta-pixel-consent'); ?></h1>
            <p><?php esc_html_e('This version integrates with PixelYourSite. Keep your Pixel ID and event settings inside PixelYourSite; this plugin only controls consent.', 'gdpr-meta-pixel-consent'); ?></p>

            <div class="notice <?php echo $pys_active ? 'notice-success' : 'notice-warning'; ?> inline">
                <p>
                    <?php if ($pys_active): ?>
                        <strong><?php esc_html_e('PixelYourSite detected.', 'gdpr-meta-pixel-consent'); ?></strong>
                        <?php esc_html_e('Meta/Facebook browser pixel output is gated by this consent plugin.', 'gdpr-meta-pixel-consent'); ?>
                    <?php else: ?>
                        <strong><?php esc_html_e('PixelYourSite was not detected.', 'gdpr-meta-pixel-consent'); ?></strong>
                        <?php esc_html_e('Activate PixelYourSite for this consent gate to control its Meta browser pixel.', 'gdpr-meta-pixel-consent'); ?>
                    <?php endif; ?>
                </p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields('gmpp_settings_group'); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e('Enable banner and PixelYourSite consent gate', 'gdpr-meta-pixel-consent'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[enabled]" value="1" <?php checked($settings['enabled'], '1'); ?>>
                                <?php esc_html_e('Enabled', 'gdpr-meta-pixel-consent'); ?>
                            </label>
                            <p class="description"><?php esc_html_e('When enabled, PixelYourSite Meta/Facebook browser pixel, noscript output and Meta server consent checks stay blocked until marketing consent is accepted.', 'gdpr-meta-pixel-consent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('PixelYourSite compatibility', 'gdpr-meta-pixel-consent'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[block_external_id]" value="1" <?php checked($settings['block_external_id'], '1'); ?>>
                                <?php esc_html_e('Block PixelYourSite external ID before marketing consent', 'gdpr-meta-pixel-consent'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[block_advanced_data]" value="1" <?php checked($settings['block_advanced_data'], '1'); ?>>
                                <?php esc_html_e('Block PixelYourSite advanced matching form-data cookies before marketing consent', 'gdpr-meta-pixel-consent'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[reload_on_change]" value="1" <?php checked($settings['reload_on_change'], '1'); ?>>
                                <?php esc_html_e('Reload page after consent changes so PixelYourSite receives the new server-side consent state', 'gdpr-meta-pixel-consent'); ?>
                            </label>
                            <p class="description"><?php esc_html_e('Recommended: in PixelYourSite GDPR settings, enable “AJAX filter values update” to avoid cache/plugin conflicts.', 'gdpr-meta-pixel-consent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gmpp_banner_title"><?php esc_html_e('Banner title', 'gdpr-meta-pixel-consent'); ?></label></th>
                        <td><input id="gmpp_banner_title" type="text" class="regular-text" name="<?php echo esc_attr(self::OPTION_NAME); ?>[banner_title]" value="<?php echo esc_attr($settings['banner_title']); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gmpp_banner_message"><?php esc_html_e('Banner message', 'gdpr-meta-pixel-consent'); ?></label></th>
                        <td>
                            <textarea id="gmpp_banner_message" class="large-text" rows="4" name="<?php echo esc_attr(self::OPTION_NAME); ?>[banner_message]"><?php echo esc_textarea($settings['banner_message']); ?></textarea>
                            <p class="description"><?php esc_html_e('Keep this clear and specific. Mention PixelYourSite, Meta/Facebook Pixel and marketing/ads measurement.', 'gdpr-meta-pixel-consent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gmpp_privacy_url"><?php esc_html_e('Privacy/Cookie policy URL', 'gdpr-meta-pixel-consent'); ?></label></th>
                        <td><input id="gmpp_privacy_url" type="url" class="regular-text" name="<?php echo esc_attr(self::OPTION_NAME); ?>[privacy_url]" value="<?php echo esc_url($settings['privacy_url']); ?>" placeholder="https://example.com/privacy-policy/"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gmpp_accent_color"><?php esc_html_e('Accent color', 'gdpr-meta-pixel-consent'); ?></label></th>
                        <td><input id="gmpp_accent_color" type="color" name="<?php echo esc_attr(self::OPTION_NAME); ?>[accent_color]" value="<?php echo esc_attr($settings['accent_color']); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Banner position', 'gdpr-meta-pixel-consent'); ?></th>
                        <td>
                            <label><input type="radio" name="<?php echo esc_attr(self::OPTION_NAME); ?>[position]" value="bottom" <?php checked($settings['position'], 'bottom'); ?>> <?php esc_html_e('Bottom card', 'gdpr-meta-pixel-consent'); ?></label><br>
                            <label><input type="radio" name="<?php echo esc_attr(self::OPTION_NAME); ?>[position]" value="center" <?php checked($settings['position'], 'center'); ?>> <?php esc_html_e('Centered modal', 'gdpr-meta-pixel-consent'); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gmpp_retention_days"><?php esc_html_e('Consent retention', 'gdpr-meta-pixel-consent'); ?></label></th>
                        <td>
                            <input id="gmpp_retention_days" type="number" min="1" max="365" name="<?php echo esc_attr(self::OPTION_NAME); ?>[retention_days]" value="<?php echo esc_attr($settings['retention_days']); ?>"> <?php esc_html_e('days', 'gdpr-meta-pixel-consent'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Persistent settings button', 'gdpr-meta-pixel-consent'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[show_settings_button]" value="1" <?php checked($settings['show_settings_button'], '1'); ?>>
                                <?php esc_html_e('Show a small button so visitors can change/withdraw consent later.', 'gdpr-meta-pixel-consent'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="gmpp_settings_button_text"><?php esc_html_e('Settings button text', 'gdpr-meta-pixel-consent'); ?></label></th>
                        <td><input id="gmpp_settings_button_text" type="text" class="regular-text" name="<?php echo esc_attr(self::OPTION_NAME); ?>[settings_button_text]" value="<?php echo esc_attr($settings['settings_button_text']); ?>"></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <hr>
            <h2><?php esc_html_e('How to verify', 'gdpr-meta-pixel-consent'); ?></h2>
            <ol>
                <li><?php esc_html_e('Open the site in a private/incognito window.', 'gdpr-meta-pixel-consent'); ?></li>
                <li><?php esc_html_e('Before accepting, confirm that connect.facebook.net/fbevents.js is not requested and _fbp/_fbc are not created.', 'gdpr-meta-pixel-consent'); ?></li>
                <li><?php esc_html_e('Click Accept all. The page reloads and PixelYourSite can then load the Meta browser pixel.', 'gdpr-meta-pixel-consent'); ?></li>
                <li><?php esc_html_e('Click Cookie settings → Reject all. The page reloads and Meta browser pixel is blocked again.', 'gdpr-meta-pixel-consent'); ?></li>
            </ol>
            <p><code>[gdpr_meta_pixel_cookie_settings]</code></p>
        </div>
        <?php
    }

    private function is_plugin_active_file(string $plugin_file): bool {
        if (!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return function_exists('is_plugin_active') && is_plugin_active($plugin_file);
    }

    public function enqueue_frontend(): void {
        if (is_admin()) {
            return;
        }

        $settings = $this->get_settings();
        if (!$this->is_setting_enabled($settings['enabled'] ?? null)) {
            return;
        }

        $asset_url = plugin_dir_url(__FILE__) . 'assets/';
        $asset_path = plugin_dir_path(__FILE__) . 'assets/';

        wp_enqueue_style(
            'gmpp-consent',
            $asset_url . 'consent.css',
            [],
            file_exists($asset_path . 'consent.css') ? filemtime($asset_path . 'consent.css') : self::VERSION
        );

        wp_enqueue_script(
            'gmpp-consent',
            $asset_url . 'consent.js',
            [],
            file_exists($asset_path . 'consent.js') ? filemtime($asset_path . 'consent.js') : self::VERSION,
            true
        );

        wp_localize_script('gmpp-consent', 'GMPPConsentConfig', [
            'consentCookie' => self::CONSENT_COOKIE,
            'legacyCookie' => self::LEGACY_COOKIE,
            'pysConsentCookie' => self::PYS_CONSENT_COOKIE,
            'title' => $settings['banner_title'],
            'message' => wp_kses_post($settings['banner_message']),
            'privacyUrl' => $settings['privacy_url'],
            'privacyText' => __('Privacy policy', 'gdpr-meta-pixel-consent'),
            'acceptAllText' => __('Accept all', 'gdpr-meta-pixel-consent'),
            'rejectAllText' => __('Reject all', 'gdpr-meta-pixel-consent'),
            'manageText' => __('Manage options', 'gdpr-meta-pixel-consent'),
            'saveText' => __('Save choices', 'gdpr-meta-pixel-consent'),
            'necessaryText' => __('Strictly necessary', 'gdpr-meta-pixel-consent'),
            'necessaryDescription' => __('Required for the website to work. These cannot be switched off.', 'gdpr-meta-pixel-consent'),
            'marketingText' => __('Marketing / Meta Pixel', 'gdpr-meta-pixel-consent'),
            'marketingDescription' => __('Allows PixelYourSite to load Meta/Facebook Pixel for ads measurement, remarketing and audience building.', 'gdpr-meta-pixel-consent'),
            'settingsButtonText' => $settings['settings_button_text'],
            'showSettingsButton' => $this->is_setting_enabled($settings['show_settings_button'] ?? null),
            'position' => $settings['position'],
            'retentionDays' => absint($settings['retention_days']),
            'accentColor' => $settings['accent_color'],
            'reloadOnChange' => $this->is_setting_enabled($settings['reload_on_change'] ?? null),
            'version' => self::VERSION,
        ]);
    }

    public function cookie_settings_shortcode(): string {
        return '<button type="button" class="gmpp-inline-settings" data-gmpp-open-settings>' . esc_html__('Open cookie settings', 'gdpr-meta-pixel-consent') . '</button>';
    }
}

new GDPR_Meta_Pixel_Consent_Lite();
