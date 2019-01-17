<?php
/**
 * Plugin Name: Sales Pop
 * Plugin URI: https://beeketing.com/sales-pop?utm_channel=appstore&utm_medium=woolisting&utm_term=shortdesc&utm_fromapp=spop
 * Description: Show recent sales notification popups to create the sense of a busy store and motivate customers to start buying. 1 in 10+ built-in features in Beeketing - <strong>#1 Marketing Automation</strong> platform for WooCommerce.
 * Version: 1.4.6
 * Author: Beeketing
 * Author URI: https://beeketing.com
 * WC tested up to: 3.5.0
 * Requires at least: 4.4
 * Tested up to: 5.0
 */

use BeeketingConnect_beeketing_woocommerce_salespop\Platforms\WooCommerce\PluginConfig;
use BeeketingConnect_beeketing_woocommerce_salespop\Platforms\WooCommerce\Plugin\SalesPop\SalesPopLoader;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Require plugin autoload
require_once __DIR__  . '/vendor/autoload.php';

$config = new PluginConfig(
    'sales_pop.js',
    'Sales Pop',
    'sales_pop_menu',
    __FILE__,
    plugin_basename(__FILE__)
);

SalesPopLoader::makeInstance($config);

require_once __DIR__ . '/sentry.php';

register_activation_hook(__FILE__, array(SalesPopLoader::class, 'activateHook'));
register_deactivation_hook(__FILE__, array(SalesPopLoader::class, 'deactivateHook'));
register_uninstall_hook(__FILE__, array(SalesPopLoader::class, 'uninstallHook'));

$pluginLoader = SalesPopLoader::instance();
$pluginLoader->init();
