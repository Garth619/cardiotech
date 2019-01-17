<?php
if (!defined('ABSPATH'))
    exit;

class WCPA_Settings {

    /**
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;
    private $settings = array();

    public function __construct() {
        $this->_version = WCPA_VERSION;
        $this->_token = WCPA_TOKEN;
        $this->file = WCPA_FILE;
        $this->dir = dirname($this->file);

        $this->settings = [
            'disp_show_field_price' => 'boolean',
            'disp_summ_show_total_price' => 'boolean',
            'disp_summ_show_product_price' => 'boolean',
            'disp_summ_show_option_price' => 'boolean',
            'show_meta_in_cart' => 'boolean',
            'show_meta_in_checkout' => 'boolean',
            'show_meta_in_order' => 'boolean',
            'show_price_in_cart' => 'boolean',
            'show_price_in_checkout' => 'boolean',
            'show_price_in_order' => 'boolean',
            'show_price_in_order_meta' => 'boolean',
            'pric_exc_product_base_price' => 'boolean',
            'enable_recaptcha' => 'boolean',
            'options_total_label' => 'text',
            'options_product_label' => 'text',
            'total_label' => 'text',
            'fee_label' => 'text',
            'add_to_cart_text' => 'text',
            'form_loading_order_by_date' => 'boolean',
            'hide_empty_data' => 'boolean',
            'google_map_api_key' => 'text',
            'recaptcha_site_key' => 'text',
            'recaptcha_secret_key' => 'text',
            'price_prefix_label' => 'text'
        ];

        add_action('admin_menu', array($this, 'register_options_page'));
        add_action('admin_menu', array($this, 'register_setting'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('init', array($this, 'check_migration'));
        $plugin = plugin_basename($this->file);
        add_filter("plugin_action_links_$plugin", array($this, 'add_settings_link'));
    }

    public function add_settings_link($links) {
        $settings = '<a href="' . admin_url('options-general.php?page=wcpa_settings') . '">' . __('Settings') . '</a>';
        $products = '<a href="' . admin_url('edit.php?post_type=' . WCPA_POST_TYPE) . '">' . __('Create Forms') . '</a>';
        array_push($links, $settings);
        array_push($links, $products);
        return $links;
        
       
    }

    public function check_migration() {
        $migration = new WCPA_Migration();
        $migration->check();
    }

    public function check_free_version_installed() {


        if (in_array('woo-custom-product-addons/start.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            return true;
        } else {
            return false;
        }
    }

    public function admin_notices() {
        if ($this->check_lcesnse() === FALSE) {
            ?>
            <div class="error">
                <p>You have invalid or expired license keys for<strong> <?php echo WCPA_PLUGIN_NAME; ?></strong>.
                    Please go to the <a href="<?php echo admin_url('options-general.php?page=wcpa_settings#wcpa-license'); ?>">
                        Licenses page</a> to correct this issue.

                </p>
            </div>
            <?php
        }
        if ($this->check_free_version_installed() == true) {
            $free_version_name = get_plugin_data(WP_PLUGIN_DIR . '/woo-custom-product-addons/start.php');
            ?>
            <div class="error">
                <p>Free version of plugin <strong> <?php echo WCPA_PLUGIN_NAME; ?></strong> has installed on this site.
                    Remove <strong> <?php echo $free_version_name['Name']; ?></strong> in order to function this plugin properly 

                </p>
            </div>
            <?php
        }



        if (isset($_GET['sl_activation']) && !empty($_GET['message'])) {
            switch ($_GET['sl_activation']) {
                case 'false':
                    $message = urldecode($_GET['message']);
                    ?>
                    <div class="error">
                        <p><?php echo $message; ?></p>
                    </div>
                    <?php
                    break;
                case 'true':
                default:
                    // Developers can put a custom success message here for when activation is successful if they way.
                    break;
            }
        }
    }

    function register_options_page() {
        add_options_page('Custom Product Addons', 'Custom Product Addons', 'manage_options', 'wcpa_settings', array($this, 'options_page'));
    }

    function register_setting() {
        register_setting('wcpa_license', 'wcpa_activation_license_key', array('type' => 'text',
            'sanitize_callback' => array($this, 'sanitize_license')));
        if (isset($_POST['wcpa_license_activate'])) {
            $this->activate_license();
        } else if (isset($_POST['wcpa_license_deactivate'])) {
            $this->deactivate_license();
        }
    }

    public function sanitize_license($new) {
        $old = get_option('wcpa_activation_license_key');
        if ($old && $old != $new) {
            delete_option('wcpa_activation_license_status'); // new license has been entered, so must reactivate
        }
        return $new;
    }

    public function deactivate_license() {
        // run a quick security check
        if (!check_admin_referer('wcpa_deactivate', 'wcpa_nounce')) {
            return; // get out if we didn't click the Activate button
        }

        $license = trim(sanitize_text_field($_POST['wcpa_activation_license_key']));
        $old = get_option('wcpa_activation_license_key');
        if ($old && $old != $license) {
            delete_option('wcpa_activation_license_status'); // new license has been entered, so must reactivate
        }

        update_option('wcpa_activation_license_key', $license);

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license' => $license,
            'item_id' => WCPA_ITEM_ID, // The ID of the item in EDD
            'url' => home_url()
        );
        // Call the custom API.
        $response = wp_remote_post(WCPA_STORE_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            if (is_wp_error($response)) {
                $temp = $response->get_error_message();
                if (empty($temp)) {
                    $message = $response->get_error_message();
                } else {
                    $message = __('An error occurred, please try again.');
                }
            }
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));
            if (false === $license_data->success) {

                switch ($license_data->error) {
                    case 'expired' :
                        $message = sprintf(
                                __('Your license key expired on %s.'), date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                        );
                        break;
                    case 'revoked' :
                        $message = __('Your license key has been disabled.');
                        break;
                    case 'missing' :
                        $message = __('Invalid license.');
                        break;
                    case 'invalid' :
                    case 'site_inactive' :
                        $message = __('Your license is not active for this URL.');
                        break;
                    case 'item_name_mismatch' :
                        $message = sprintf(__('This appears to be an invalid license key for %s.'), EDD_SAMPLE_ITEM_NAME);
                        break;
                    case 'no_activations_left':
                        $message = __('Your license key has reached its activation limit.');
                        break;
                    default :
                        $message = __('An error occurred, please try again.');
                        break;
                }
            }
        }
        // Check if anything passed on a message constituting a failure
        if (!empty($message)) {
            $base_url = admin_url('options-general.php?page=wcpa_settings');
            $redirect = add_query_arg(array('sl_activation' => 'false', 'message' => urlencode($message)), $base_url);
            wp_redirect($redirect);
            exit();
        }
        // $license_data->license will be either "valid" or "invalid"
        update_option('wcpa_activation_license_status', $license_data->license);
        wp_redirect(admin_url('options-general.php?page=wcpa_settings'));
        exit();
    }

    public function check_lcesnse() {
        $license_status = get_option('wcpa_activation_license_status');
        if ($license_status == 'valid') {
            return true;
        } else {
            return FALSE;
        }
    }

    public function activate_license() {

        // run a quick security check
        if (!check_admin_referer('wcpa_activate', 'wcpa_nounce')) {
            return; // get out if we didn't click the Activate button
        }
        $license = trim(sanitize_text_field($_POST['wcpa_activation_license_key']));
        update_option('wcpa_activation_license_key', $license);

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'activate_license',
            'license' => $license,
            'item_id' => WCPA_ITEM_ID, // The ID of the item in EDD
            'url' => home_url()
        );
        // Call the custom API.
        $response = wp_remote_post(WCPA_STORE_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));


        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            if (is_wp_error($response)) {
                $temp = $response->get_error_message();
                if (empty($temp)) {
                    $message = $response->get_error_message();
                } else {
                    $message = __('An error occurred, please try again.');
                }
            }
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));

            if (false === $license_data->success) {
                switch ($license_data->error) {
                    case 'expired' :
                        $message = sprintf(
                                __('Your license key expired on %s.'), date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                        );
                        break;
                    case 'revoked' :
                        $message = __('Your license key has been disabled.');
                        break;
                    case 'missing' :
                        $message = __('Invalid license.');
                        break;
                    case 'invalid' :
                    case 'site_inactive' :
                        $message = __('Your license is not active for this URL.');
                        break;
                    case 'item_name_mismatch' :
                        $message = sprintf(__('This appears to be an invalid license key for %s.'), EDD_SAMPLE_ITEM_NAME);
                        break;
                    case 'no_activations_left':
                        $message = __('Your license key has reached its activation limit.');
                        break;
                    default :
                        $message = __('An error occurred, please try again.');
                        break;
                }
            }
        }
        // Check if anything passed on a message constituting a failure
        if (!empty($message)) {
            $base_url = admin_url('options-general.php?page=wcpa_settings');
            $redirect = add_query_arg(array('sl_activation' => 'false', 'message' => urlencode($message)), $base_url);
            wp_redirect($redirect);
            exit();
        }
        // $license_data->license will be either "valid" or "invalid"
        update_option('wcpa_activation_license_status', $license_data->license);
        wp_redirect(admin_url('options-general.php?page=wcpa_settings'));
        exit();
    }

    public function save_settings() {
        if (isset($_POST['wcpa_save_settings']) && wp_verify_nonce($_POST['wcpa_nonce'], 'wcpa_save_settings')) {
            $settings = get_option(WCPA_SETTINGS_KEY);

            foreach ($this->settings as $key => $type) {
                if ($type == 'text') {
                    if (isset($_POST[$key])) {
                        $settings[$key] = sanitize_text_field($_POST[$key]);
                    }
                } else if ($type == 'boolean') {
                    if (isset($_POST[$key])) {
                        $settings[$key] = true;
                    } else {
                        $settings[$key] = false;
                    }
                }
            }

            update_option(WCPA_SETTINGS_KEY, $settings);
           $ml = new WCPA_Ml();
            if ($ml->is_active()) {
                $ml->settings_to_wpml();
            }
        }
    }

    public function options_page() {



        if (array_key_exists('wcpa_save_settings', $_POST)) {
            $this->save_settings();
        }
        if (array_key_exists('action', $_GET)) {
            if ($_GET['action'] == 'migrate') {
                if (isset($_GET['wcpa_nonce']) && wp_verify_nonce($_GET['wcpa_nonce'], 'wcpa_migration')) {
                    $migration = new WCPA_Migration();
                    $response = $migration->version_migration();
                    WCPA_Backend::view('settings-migration', ['response' => $response]);
                }
            }
        } else if (array_key_exists('view', $_GET)) {
            if ($_GET['view'] == 'migration') {
                WCPA_Backend::view('settings-migration', []);
            }
        } else {
            WCPA_Backend::view('settings-main', []);
        }
    }

    /**
     *
     *
     * Ensures only one instance of CPO is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main CPO instance
     */
    public static function instance($file = '', $version = '1.0.0') {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone() {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

}
