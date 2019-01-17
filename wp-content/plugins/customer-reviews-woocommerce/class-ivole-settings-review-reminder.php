<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Ivole_Review_Reminder_Settings' ) ):

class Ivole_Review_Reminder_Settings {

    /**
     * @var Ivole_Settings_Admin_Menu The instance of the settings admin menu
     */
    protected $settings_menu;

    /**
     * @var string The slug of this tab
     */
    protected $tab;

    /**
     * @var array The fields for this tab
     */
    protected $settings;

    public function __construct( $settings_menu ) {
        $this->settings_menu = $settings_menu;

        $this->tab = 'review_reminder';

        add_filter( 'ivole_settings_tabs', array( $this, 'register_tab' ) );
        add_action( 'ivole_settings_display_' . $this->tab, array( $this, 'display' ) );
        add_action( 'ivole_save_settings_' . $this->tab, array( $this, 'save' ) );

        add_action( 'woocommerce_admin_field_email_from', array( $this, 'show_email_from' ) );
        add_action( 'woocommerce_admin_field_email_from_name', array( $this, 'show_email_from_name' ) );
        add_action( 'woocommerce_admin_field_footertext', array( $this, 'show_footertext' ) );

        add_action( 'woocommerce_admin_settings_sanitize_option_ivole_email_from', array( $this, 'save_email_from' ), 10, 3 );
        add_action( 'woocommerce_admin_settings_sanitize_option_ivole_email_footer', array( $this, 'save_footertext' ), 10, 3 );

        add_action( 'wp_ajax_ivole_check_license_email_ajax', array( $this, 'check_license_email_ajax' ) );
        add_action( 'wp_ajax_ivole_verify_email_ajax', array( $this, 'ivole_verify_email_ajax' ) );

        add_action( 'admin_footer', array( $this, 'output_page_javascript' ) );
    }

    public function register_tab( $tabs ) {
        $tabs[$this->tab] = __( 'Review Reminder', IVOLE_TEXT_DOMAIN );
        return $tabs;
    }

    public function display() {
        $this->init_settings();

        WC_Admin_Settings::output_fields( $this->settings );
    }

    public function save() {

        $this->init_settings();

        if ( ! empty( $_POST ) && isset( $_POST['ivole_attach_image_quantity'] ) ) {
            if ( $_POST['ivole_attach_image_quantity'] <= 0 ) {
                $_POST['ivole_attach_image_quantity'] = 1;
            }
        }

        // make sure that there the maximum size of attached image is larger than zero
        if( ! empty( $_POST ) && isset( $_POST['ivole_attach_image_size'] ) ) {
            if ( $_POST['ivole_attach_image_size'] <= 0 ) {
                $_POST['ivole_attach_image_size'] = 1;
            }
        }

        // make sure that we do not save "Checking license..." in the settings
        if( ! empty( $_POST ) && isset( $_POST['ivole_email_from'] ) ) {
            if ( __( 'Checking license...', IVOLE_TEXT_DOMAIN ) === $_POST['ivole_email_from'] ) {
                $_POST['ivole_email_from'] = get_option( 'ivole_email_from', '' );
            }
        }
        if( ! empty( $_POST ) && isset( $_POST['ivole_email_from_name'] ) ) {
            if ( __( 'Checking license...', IVOLE_TEXT_DOMAIN ) === $_POST['ivole_email_from_name'] ) {
                $_POST['ivole_email_from_name'] = get_option( 'ivole_email_from_name', Ivole_Email::get_blogname() );
            }
        }
        if( ! empty( $_POST ) && isset( $_POST['ivole_email_footer'] ) ) {
            if ( __( 'Checking license...', IVOLE_TEXT_DOMAIN ) === $_POST['ivole_email_footer'] ) {
                $_POST['ivole_email_footer'] = get_option( 'ivole_email_footer', '' );
            }
        }

        // validate colors (users sometimes remove # or provide invalid hex color codes)
        if ( ! empty( $_POST ) && isset( $_POST['ivole_email_color_bg'] ) ) {
          if( ! preg_match_all( '/#([a-f0-9]{3}){1,2}\b/i', $_POST['ivole_email_color_bg'] ) ) {
            $_POST['ivole_email_color_bg'] = '#0f9d58';
          }
        }
        if ( ! empty( $_POST ) && isset( $_POST['ivole_email_color_text'] ) ) {
          if( ! preg_match_all( '/#([a-f0-9]{3}){1,2}\b/i', $_POST['ivole_email_color_text'] ) ) {
            $_POST['ivole_email_color_text'] = '#ffffff';
          }
        }
        if ( ! empty( $_POST ) && isset( $_POST['ivole_form_color_bg'] ) ) {
          if( ! preg_match_all( '/#([a-f0-9]{3}){1,2}\b/i', $_POST['ivole_form_color_bg'] ) ) {
            $_POST['ivole_form_color_bg'] = '#0f9d58';
          }
        }
        if ( ! empty( $_POST ) && isset( $_POST['ivole_form_color_text'] ) ) {
          if( ! preg_match_all( '/#([a-f0-9]{3}){1,2}\b/i', $_POST['ivole_form_color_text'] ) ) {
            $_POST['ivole_form_color_text'] = '#ffffff';
          }
        }

        WC_Admin_Settings::save_fields( $this->settings );
    }

    protected function init_settings() {
        $language_desc = __( 'Choose language that will be used for different elements of emails and review forms. If your language is not in the list, submit a translation by filling out <a href="https://goo.gl/forms/8D4poyIBRq2MtWEP2" target="_blank">this form</a>. Your translation will added with the next update of the plugin.', IVOLE_TEXT_DOMAIN );

        $available_languages = array(
            'AR'  => __( 'Arabic', IVOLE_TEXT_DOMAIN ),
            'BG'  => __( 'Bulgarian', IVOLE_TEXT_DOMAIN ),
            'ZH'  => __( 'Chinese', IVOLE_TEXT_DOMAIN ),
            'CS'  => __( 'Czech', IVOLE_TEXT_DOMAIN ),
            'DA'  => __( 'Danish', IVOLE_TEXT_DOMAIN ),
            'NL'  => __( 'Dutch', IVOLE_TEXT_DOMAIN ),
            'EN'  => __( 'English', IVOLE_TEXT_DOMAIN ),
            'ET'  => __( 'Estonian', IVOLE_TEXT_DOMAIN ),
            'FI'  => __( 'Finnish', IVOLE_TEXT_DOMAIN ),
            'FR'  => __( 'French', IVOLE_TEXT_DOMAIN ),
            'DE'  => __( 'German', IVOLE_TEXT_DOMAIN ),
            'HE'  => __( 'Hebrew', IVOLE_TEXT_DOMAIN ),
            'HU'  => __( 'Hungarian', IVOLE_TEXT_DOMAIN ),
            'ID'  => __( 'Indonesian', IVOLE_TEXT_DOMAIN ),
            'IT'  => __( 'Italian', IVOLE_TEXT_DOMAIN ),
            'JA'  => __( 'Japanese', IVOLE_TEXT_DOMAIN ),
            'KO'  => __( 'Korean', IVOLE_TEXT_DOMAIN ),
            'LT'  => __( 'Lithuanian', IVOLE_TEXT_DOMAIN ),
            'NO'  => __( 'Norwegian', IVOLE_TEXT_DOMAIN ),
            'PL'  => __( 'Polish', IVOLE_TEXT_DOMAIN ),
            'PT'  => __( 'Portuguese', IVOLE_TEXT_DOMAIN ),
            'RO'  => __( 'Romanian', IVOLE_TEXT_DOMAIN ),
            'RU'  => __( 'Russian', IVOLE_TEXT_DOMAIN ),
            'SR'  => __( 'Serbian', IVOLE_TEXT_DOMAIN ),
            'SL'  => __( 'Slovenian', IVOLE_TEXT_DOMAIN ),
            'ES'  => __( 'Spanish', IVOLE_TEXT_DOMAIN ),
            'SV'  => __( 'Swedish', IVOLE_TEXT_DOMAIN ),
            'TH'  => __( 'Thai', IVOLE_TEXT_DOMAIN ),
            'TR'  => __( 'Turkish', IVOLE_TEXT_DOMAIN ),
            'VI'  => __( 'Vietnamese', IVOLE_TEXT_DOMAIN )
        );

        // qTranslate integration
        if ( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
            $language_desc .= ' ' . __( 'It looks like you have qTranslate-X plugin activated. You might want to choose "qTranslate-X Automatic" option to enable automatic selection of language.', IVOLE_TEXT_DOMAIN );
            $available_languages = array( 'QQ' => __( 'qTranslate-X Automatic', IVOLE_TEXT_DOMAIN ) ) + $available_languages;
        }

        // WPML integration
        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $language_desc .= ' ' . __( 'It looks like you have WPML plugin activated. You might want to choose "WPML Automatic" option to enable automate selection of language.', IVOLE_TEXT_DOMAIN );
            $available_languages = array( 'WPML' => __( 'WPML Automatic', IVOLE_TEXT_DOMAIN ) ) + $available_languages;
        }

        $this->settings = array(
            array(
                'title' => __( 'Reminders for Customer Reviews', IVOLE_TEXT_DOMAIN ),
                'type'  => 'title',
                'desc'  => __( 'Configure Customer Reviews for WooCommerce to send automatic follow-up emails (reminders) that gather shop and product reviews. Review reminders are sent via an independent service CR (Customer Reviews) integrated with the plugin because people commonly consider reviews managed by shops themselves to be biased or fake. Independent collection of customer feedback also enables other optional features of the plugin such as trust badges or verification of reviews.<br><br>Before enabling this feature you MUST update your terms and conditions and make sure that your customers consent to receive an invitation to review their order. By enabling and using this plugin, you agree to the <a href="https://www.cusrev.com/terms.html" target="_blank">terms and conditions</a>.', IVOLE_TEXT_DOMAIN ),
                'id'    => 'ivole_options'
            ),
            array(
                'title'   => __( 'Enable Automatic Reminders', IVOLE_TEXT_DOMAIN ),
                'desc'    => __( 'Enable automatic follow-up emails with a reminder to submit a review.', IVOLE_TEXT_DOMAIN ),
                'id'      => 'ivole_enable',
                'default' => 'no',
                'type'    => 'checkbox'
            ),
            array(
                'title'    => __( 'Sending Delay (Days)', IVOLE_TEXT_DOMAIN ),
                'type'     => 'number',
                'desc'     => __( 'Emails will be sent N days after order status is set to "Completed". N is a sending delay that needs to be defined in this field.', IVOLE_TEXT_DOMAIN ),
                'default'  => 5,
                'id'       => 'ivole_delay',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Enable for', IVOLE_TEXT_DOMAIN ),
                'type'     => 'select',
                'desc'     => __( 'Define if reminders will be send for all or only specific categories of products.', IVOLE_TEXT_DOMAIN ),
                'default'  => 'all',
                'id'       => 'ivole_enable_for',
                'desc_tip' => true,
                'class'    => 'wc-enhanced-select',
                'css'      => 'min-width:300px;',
                'options'  => array(
                    'all'        => __( 'All Categories', IVOLE_TEXT_DOMAIN ),
                    'categories' => __( 'Specific Categories', IVOLE_TEXT_DOMAIN )
                )
            ),
            array(
                'title'    => __( 'Categories', IVOLE_TEXT_DOMAIN ),
                'type'     => 'cselect',
                'desc'     => __( 'If reminders are enabled only for specific categories of products, this field enables you to choose these categories.', IVOLE_TEXT_DOMAIN ),
                'id'       => 'ivole_enabled_categories',
                'desc_tip' => true,
                'class'    => 'wc-enhanced-select',
                'css'      => 'min-width:300px;'
            ),
            array(
                'title' => __( 'Enable for Roles', IVOLE_TEXT_DOMAIN ),
                'type' => 'select',
                'desc' => __( 'Define if reminders will be send for all or only specific roles of users.', IVOLE_TEXT_DOMAIN ),
                'default'  => 'all',
                'id' => 'ivole_enable_for_role',
                'desc_tip' => true,
                'class'    => 'wc-enhanced-select',
                'css'      => 'min-width:300px;',
                'options'  => array(
                    'all'  => __( 'All Roles', IVOLE_TEXT_DOMAIN ),
                    'roles' => __( 'Specific Roles', IVOLE_TEXT_DOMAIN )
                )
            ),
            array(
                'title' => __( 'Roles', IVOLE_TEXT_DOMAIN ),
                'type' => 'cselect',
                'desc' => __( 'If reminders are enabled only for specific user roles, this field enables you to choose these roles.', IVOLE_TEXT_DOMAIN ),
                'id' => 'ivole_enabled_roles',
                'desc_tip' => true,
                'class'    => 'wc-enhanced-select',
                'css'      => 'min-width:300px;'
            ),
            array(
                'title'   => __( 'Enable Manual Reminders', IVOLE_TEXT_DOMAIN ),
                'desc'    => __( 'Enable manual sending of follow-up emails with a reminder to submit a review. Manual reminders can be sent for completed orders from <a href="' . admin_url( 'edit.php?post_type=shop_order' ) . '">Orders</a> page after enabling this option.', IVOLE_TEXT_DOMAIN ),
                'id'      => 'ivole_enable_manual',
                'default' => 'no',
                'type'    => 'checkbox'
            ),
            array(
                'title'   => __( 'Limit Number of Reminders', IVOLE_TEXT_DOMAIN ),
                'desc'    => __( 'Enable this checkbox to make sure that no more than one review reminder is sent for each order.', IVOLE_TEXT_DOMAIN ),
                'id'      => 'ivole_limit_reminders',
                'default' => 'yes',
                'type'    => 'checkbox'
            ),
            array(
                'title'   => __( 'Registered Customers', IVOLE_TEXT_DOMAIN ),
                'desc'    => __( 'By default, plugin sends review reminders to billing emails provided by customers during checkout. If you enable this option, the plugin will check if customers have accounts on your website and will send review reminders to email addresses associated with their accounts. It is recommended to keep this option disabled.', IVOLE_TEXT_DOMAIN ),
                'id'      => 'ivole_registered_customers',
                'default' => 'no',
                'type'    => 'checkbox'
            ),
            array(
                'title'   => __( 'Moderation of Reviews', IVOLE_TEXT_DOMAIN ),
                'desc'    => __( 'Enable manual moderation of reviews submitted by your verified customers. This setting applies only to reviews submitted in response to reminders sent by this plugin.', IVOLE_TEXT_DOMAIN ),
                'id'      => 'ivole_enable_moderation',
                'default' => 'no',
                'type'    => 'checkbox'
            ),
            array(
                'title'    => __( 'Shop Name', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'desc'     => __( 'Specify your shop name that will be used in emails and review forms generated by this plugin.', IVOLE_TEXT_DOMAIN ),
                'default'  => Ivole_Email::get_blogname(),
                'id'       => 'ivole_shop_name',
                'css'      => 'min-width:300px;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'From Address', IVOLE_TEXT_DOMAIN ),
                'type'     => 'email_from',
                'desc'     => __( 'Emails will be sent from the email address specified in this field. Modification of this field is possible in premium version of the plugin.', IVOLE_TEXT_DOMAIN ),
                'default'  => '',
                'id'       => 'ivole_email_from',
                'css'      => 'min-width:300px;display:none;vertical-align:middle;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'From Name', IVOLE_TEXT_DOMAIN ),
                'type'     => 'email_from_name',
                'desc'     => __( 'Name that will be used together with From Address to send emails. Modification of this field is possible in premium version of the plugin.', IVOLE_TEXT_DOMAIN ),
                'default'  => Ivole_Email::get_blogname(),
                'id'       => 'ivole_email_from_name',
                'css'      => 'min-width:300px;display:none;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'BCC Address', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'desc'     => __( 'Add a BCC recipient for emails with reminders. It can be useful to verify that emails are being sent properly.', IVOLE_TEXT_DOMAIN ),
                'default'  => '',
                'id'       => 'ivole_email_bcc',
                'css'      => 'min-width:300px;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Reply-To Address', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'desc'     => __( 'Add a Reply-To address for emails with reminders. If customers decide to reply to automatic emails, their replies will be sent to this address.', IVOLE_TEXT_DOMAIN ),
                'default'  => get_option( 'admin_email' ),
                'id'       => 'ivole_email_replyto',
                'css'      => 'min-width:300px;',
                'desc_tip' => true
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'ivole_options'
            ),
            array(
                'title' => __( 'Language', IVOLE_TEXT_DOMAIN ),
                'type'  => 'title',
                'desc'  => $language_desc,
                'id'    => 'ivole_options_language'
            ),
            array(
                'title'    => __( 'Language', IVOLE_TEXT_DOMAIN ),
                'type'     => 'select',
                'desc'     => __( 'Choose one of the available languages.', IVOLE_TEXT_DOMAIN ),
                'default'  => 'EN',
                'id'       => 'ivole_language',
                'class'    => 'wc-enhanced-select',
                'desc_tip' => true,
                'options'  => $available_languages
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'ivole_options_language'
            ),
            array(
                'title' => __( 'Email Template', IVOLE_TEXT_DOMAIN ),
                'type'  => 'title',
                'desc'  => __( 'Adjust template of the email that will be sent to customers.', IVOLE_TEXT_DOMAIN ),
                'id'    => 'ivole_options_email'
            ),
            array(
                'title'    => __( 'Email Subject', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'desc'     => __( 'Subject of the email that will be sent to customers.', IVOLE_TEXT_DOMAIN ),
                'default'  => '[{site_title}] Review Your Experience with Us',
                'id'       => 'ivole_email_subject',
                'css'      => 'min-width:600px;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Email Heading', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'desc'     => __( 'Heading of the email that will be sent to customers.', IVOLE_TEXT_DOMAIN ),
                'default'  => 'How did we do?',
                'id'       => 'ivole_email_heading',
                'css'      => 'min-width:600px;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Email Body', IVOLE_TEXT_DOMAIN ),
                'type'     => 'htmltext',
                'desc'     => __( 'Body of the email that will be sent to customers.', IVOLE_TEXT_DOMAIN ),
                'id'       => 'ivole_email_body',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Email Footer', IVOLE_TEXT_DOMAIN ),
                'type'     => 'footertext',
                'desc'     => __( 'Footer of the email that will be sent to customers. Modification of this field is possible in premium version of the plugin.', IVOLE_TEXT_DOMAIN ),
                'id'       => 'ivole_email_footer',
                'default'  => '',
                'css'      => 'min-width:600px;height:8em;display:none;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Email Color 1', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'id'       => 'ivole_email_color_bg',
                'default'  => '#0f9d58',
                'desc'     => __( 'Background color for heading of the email and review button.', IVOLE_TEXT_DOMAIN ),
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Email Color 2', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'id'       => 'ivole_email_color_text',
                'default'  => '#ffffff',
                'desc'     => __( 'Text color for heading of the email and review button.', IVOLE_TEXT_DOMAIN ),
                'desc_tip' => true
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'ivole_options_email'
            ),
            array(
                'title' => __( 'Review Form Template', IVOLE_TEXT_DOMAIN ),
                'type'  => 'title',
                'desc'  => __( 'Adjust template of the aggregated review form that will be sent to customers. Modifications will be applied to the next review form created after saving settings.', IVOLE_TEXT_DOMAIN ),
                'id'    => 'ivole_options_form'
            ),
            array(
                'title'    => __( 'Form Header', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'desc'     => __( 'Header of the review form that will be sent to customers.', IVOLE_TEXT_DOMAIN ),
                'default'  => 'How did we do?',
                'id'       => 'ivole_form_header',
                'css'      => 'min-width:600px;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Form Body', IVOLE_TEXT_DOMAIN ),
                'type'     => 'textarea',
                'desc'     => __( 'Body of the review form that will be sent to customers.', IVOLE_TEXT_DOMAIN ),
                'default'  => 'Please review your experience with products and services that you purchased at {site_title}.',
                'id'       => 'ivole_form_body',
                'css'      => 'min-width:600px;height:5em;',
                'desc_tip' => true
            ),
            array(
                'title'   => __( 'Shop Rating', IVOLE_TEXT_DOMAIN ),
                'type'    => 'checkbox',
                'id'      => 'ivole_form_shop_rating',
                'default' => 'no',
                'desc'    => __( 'Enable this option if you would like to include a separate question for a general shop review in addition to questions for product reviews.', IVOLE_TEXT_DOMAIN )
            ),
            array(
                'title'   => __( 'Comment Required', IVOLE_TEXT_DOMAIN ),
                'type'    => 'checkbox',
                'id'      => 'ivole_form_comment_required',
                'default' => 'no',
                'desc'    => __( 'Enable this option if you would like to make it mandatory for your customers to write something in their review.', IVOLE_TEXT_DOMAIN )
            ),
            array(
                'title'   => __( 'Attach Media', IVOLE_TEXT_DOMAIN ),
                'type'    => 'checkbox',
                'id'      => 'ivole_form_attach_media',
                'default' => 'no',
                'desc'    => __( 'Enable attachment of pictures and videos on aggregated review forms. Uploaded media files are stored on Amazon S3 and delivered using Amazon CloudFront CDN. Storage is free and sponsored by premium licenses. This option applies only to aggregated review forms. If you would like to enable attachment of pictures to reviews submitted on WooCommerce product pages, this can be done <a href="' . admin_url( 'admin.php?page=ivole-reviews-settings&tab=review_extensions' ) . '">here</a>.', IVOLE_TEXT_DOMAIN )
            ),
            array(
                'title'    => __( 'Form Color 1', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'id'       => 'ivole_form_color_bg',
                'default'  => '#0f9d58',
                'desc'     => __( 'Background color for heading of the form and product names.', IVOLE_TEXT_DOMAIN ),
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Form Color 2', IVOLE_TEXT_DOMAIN ),
                'type'     => 'text',
                'id'       => 'ivole_form_color_text',
                'default'  => '#ffffff',
                'desc'     => __( 'Text color for product names.', IVOLE_TEXT_DOMAIN ),
                'desc_tip' => true
            ),
            array(
                'title'       => __( 'Send Test', IVOLE_TEXT_DOMAIN ),
                'type'        => 'emailtest',
                'desc'        => __( 'Send a test email to this address. You must save changes before sending a test email.', IVOLE_TEXT_DOMAIN ),
                'default'     => '',
                'placeholder' => 'Email address',
                'id'          => 'ivole_email_test',
                'css'         => 'min-width:300px;',
                'desc_tip'    => true
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'ivole_options_form'
            )
        );
    }

    public function is_this_tab() {
        return $this->settings_menu->is_this_page() && ( $this->settings_menu->get_current_tab() === $this->tab );
    }

    /**
	 * Custom field type for from email
	 */
	public function show_email_from( $value ) {
		$tmp = Ivole_Admin::ivole_get_field_description( $value );
		$tooltip_html = $tmp['tooltip_html'];
		$description = $tmp['description'];
    ?>
    <tr valign="top">
		<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				<?php echo $tooltip_html; ?>
		</th>
		<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
				<input
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					type="text"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
					placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
        />
        <?php echo $description; ?>
				<span id="ivole_email_from_verify_status" style="display:none;padding:5px;margin-left:10px;margin-right:10px;vertical-align:middle;border-radius:3px;"></span>
				<input
					type="button"
					id="ivole_email_from_verify_button"
					value="Verify"
					class="button-primary"
					style="display:none;"
				/>
				<p id="ivole_email_from_status"></p>
			</td>
		</tr>
		<?php
    }

    /**
	 * Custom field type for from  name
	 */
	public function show_email_from_name( $value ) {
		$tmp = Ivole_Admin::ivole_get_field_description( $value );
		$tooltip_html = $tmp['tooltip_html'];
		$description = $tmp['description'];
        ?>
        <tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				<?php echo $tooltip_html; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
				<input
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					type="text"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
					placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
					/> <?php echo $description; ?>
				<p id="ivole_email_from_name_status"></p>
			</td>
		</tr>
		<?php
    }

    /**
	 * Custom field type for email footer text
	 */
	public function show_footertext( $value ) {
		$tmp = Ivole_Admin::ivole_get_field_description( $value );
		$tooltip_html = $tmp['tooltip_html'];
		$description = $tmp['description'];
    ?>
    <tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				<?php echo $tooltip_html; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
				<?php echo $description; ?>
				<textarea
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
          ></textarea>
				<p id="ivole_email_footer_status"></p>
			</td>
		</tr>
		<?php
	}

    /**
	 * Custom field type for body email save
	 */
	public function save_email_from( $value, $option, $raw_value ) {
		if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			return $value;
		}

        return;
    }

    /**
	 * Custom field type for email footer text save
	 */
	public function save_footertext( $value, $option, $raw_value ) {
		return $raw_value;
    }

    /**
	 * Function to check status of the license and verification of email
	 */
	public function check_license_email_ajax() {
		$license = new Ivole_License();
		$lval = $license->check_license();

        if ( __( 'Active: Premium Version', IVOLE_TEXT_DOMAIN ) == $lval ) {
			// the license is active, so check if current from email address is verified
			$verify = new Ivole_Email_Verify();
			$vval = $verify->is_verified();

            wp_send_json( array( 'license' => $lval, 'email' => $vval ) );
		} else {
			wp_send_json( array( 'license' => $lval, 'email' => 0 ) );
		}
    }

    /**
	 * Function to verify an email
	 */
	public function ivole_verify_email_ajax() {
		$email = strval( $_POST['email'] );
		$verify = new Ivole_Email_Verify();
		$vval = $verify->verify_email( $email );

        wp_send_json( array( 'verification' => $vval['res'], 'email' => $email, 'message' => $vval['message'] ) );
	}

    public function output_page_javascript() {
        if ( $this->is_this_tab() ) {
        ?>
            <script type="text/javascript">
                jQuery(function($) {
                    // Load of Review Reminder page and check of From Email verification
                    if (jQuery('#ivole_email_from').length > 0) {
                        var data = {
                            'action': 'ivole_check_license_email_ajax',
                            'email': '<?php echo get_option( 'ivole_email_from', '' ); ?>'
                        };
                        jQuery('#ivole_email_from_status').text( '<?php echo __( 'Checking license...', IVOLE_TEXT_DOMAIN ); ?>' );
                        jQuery('#ivole_email_from_name_status').text( '<?php echo __( 'Checking license...', IVOLE_TEXT_DOMAIN ); ?>' );
                        jQuery('#ivole_email_footer_status').text( '<?php echo __( 'Checking license...', IVOLE_TEXT_DOMAIN ); ?>' );
                        jQuery.post(ajaxurl, data, function(response) {
                            jQuery('#ivole_email_footer_status').css('visibility', 'visible');

                            if ('<?php echo __( 'Active: Premium Version', IVOLE_TEXT_DOMAIN ); ?>' === response.license) {
                                jQuery('#ivole_email_from').val( '<?php echo get_option( 'ivole_email_from', '' ); ?>' );
                                jQuery('#ivole_email_from').show();
                                jQuery('#ivole_email_from_verify_status').show();
                                jQuery('#ivole_email_from_name').show();
                                jQuery('#ivole_email_from_name').val( <?php echo json_encode( get_option( 'ivole_email_from_name', Ivole_Email::get_blogname() ), JSON_HEX_APOS|JSON_HEX_QUOT ); ?> );
                                jQuery('#ivole_email_from_name_status').hide();
                                jQuery('#ivole_email_footer').show();
                                jQuery('#ivole_email_footer').val( <?php echo json_encode( get_option( 'ivole_email_footer', "" ), JSON_HEX_APOS|JSON_HEX_QUOT ); ?> );
                                jQuery('#ivole_email_footer_status').text( 'While editing footer text please make sure to keep unsubscribe link markup: <a href="{{unsubscribeLink}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">unsubscribe</a>.' );

                                if (1 == response.email){
                                    jQuery('#ivole_email_from_verify_status').css('background', '#00FF00');
                                    jQuery('#ivole_email_from_verify_status').text( 'Verified' );
                                    jQuery('#ivole_email_from_status').text( '' );
                                    jQuery('#ivole_email_from_status').hide();
                                } else {
                                    jQuery('#ivole_email_from_verify_status').css('background', '#FA8072');
                                    jQuery('#ivole_email_from_verify_status').text( 'Unverified' );
                                    jQuery('#ivole_email_from_verify_button').show();
                                    jQuery('#ivole_email_from_status').text( 'This email address is unverified. You must verify it to send emails.' );
                                }
                            } else {
                                jQuery('#ivole_email_from').val( '' );
                                jQuery('#ivole_email_from_status').html( 'Review reminders are sent by CR from \'feedback@cusrev.com\'. This indicates to customers that review process is independent and trustworthy. \'From Address\' can be modified in the <a href="<?php echo admin_url( 'admin.php?page=ivole-reviews-settings&tab=license-key' ); ?>">premium version</a> of the plugin.' );
                                jQuery('#ivole_email_from_name_status').html( 'Since review invitations are sent via CR, \'From Name\' will be based on \'Shop Name\' (see above) with a reference to CR. This field can be modified in the <a href="<?php echo admin_url( 'admin.php?page=ivole-reviews-settings&tab=license-key' ); ?>">premium version</a> of the plugin.' );
                                jQuery('#ivole_email_footer_status').html( 'To comply with the international laws about sending emails (CAN-SPAM act, CASL laws, etc), CR will automatically add a footer with address of the sender and an opt-out link. The footer can be modified in the <a href="<?php echo admin_url( 'admin.php?page=ivole-reviews-settings&tab=license-key' ); ?>">premium version</a> of the plugin.' );
                            }
                            // integration with qTranslate-X - add translation for elements that are loaded with a delay
                            if (typeof qTranslateConfig !== 'undefined' && typeof qTranslateConfig.qtx !== 'undefined') {
                                qTranslateConfig.qtx.addContentHook( document.getElementById( 'ivole_email_from_name' ), null, null );
                                qTranslateConfig.qtx.addContentHook( document.getElementById( 'ivole_email_footer' ), null, null );
                            }
                        });
                    }

                    // Click on Verify From Email button
                    jQuery('#ivole_email_from_verify_button').click(function(){
                        var data = {
                            'action': 'ivole_verify_email_ajax',
                            'email': jQuery('#ivole_email_from').val()
                        };
                        jQuery('#ivole_email_from_verify_button').prop('disabled', true);
                        jQuery('#ivole_email_from_status').text( 'Sending verification email...' );
                        jQuery.post(ajaxurl, data, function(response) {
                            if ( 1 === response.verification ) {
                                jQuery('#ivole_email_from_status').text( 'A verification email from Amazon Web Services has been sent to \'' + response.email + '\'. Please open the email and click the verification URL to confirm that you are the owner of this email address. After verification, reload this page to see updated status of verification.' );
                                jQuery('#ivole_email_from_verify_button').css('visibility', 'hidden');
                            } else if ( 2 === response.verification ) {
                                jQuery('#ivole_email_from_status').text( 'Verification error: ' + response.message + '.' );
                                jQuery('#ivole_email_from_verify_button').prop('disabled', false);
                            } else if ( 3 === response.verification ) {
                                jQuery('#ivole_email_from_status').text( 'Verification error: ' + response.message + '. Please refresh the page to see the updated verification status.' );
                                jQuery('#ivole_email_from_verify_button').prop('disabled', false);
                            } else if ( 99 === response.verification ) {
                                jQuery('#ivole_email_from_status').text( 'Verification error: please enter a valid email address.' );
                                jQuery('#ivole_email_from_verify_button').prop('disabled', false);
                            } else {
                                jQuery('#ivole_email_from_status').text( 'Verification error.' );
                                jQuery('#ivole_email_from_verify_button').prop('disabled', false);
                            }
                        });
                    });
                });
            </script>
        <?php
        }
    }

}

endif;
