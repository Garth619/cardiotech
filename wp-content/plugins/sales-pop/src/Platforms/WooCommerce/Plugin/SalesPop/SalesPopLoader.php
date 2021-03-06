<?php
/**
 * Created by PhpStorm.
 * User: admin1
 * Date: 05/09/2018
 * Time: 17:13
 */

namespace BeeketingConnect_beeketing_woocommerce_salespop\Platforms\WooCommerce\Plugin\SalesPop;

use BeeketingConnect_beeketing_woocommerce_salespop\Platforms\WooCommerce\PluginConfig;
use BeeketingConnect_beeketing_woocommerce_salespop\Platforms\WooCommerce\Plugin\Loader;

class SalesPopLoader extends Loader
{
    /**
     * The single instance of the class
     */
    private static $_instance = null;

    /**
     * Get instance
     *
     * @return static
     */
    public static function instance()
    {
        return static::$_instance;
    }

    /**
     * Make instance
     * @param PluginConfig $config
     */
    public static function makeInstance($config)
    {
        if (!is_null(static::$_instance)) {
            return;
        }

        static::$_instance = new static($config);
    }

    /**
     * @inheritdoc
     */
    protected function createHooks($config)
    {
        return new SalesPopHooks($config, $this);
    }


    public static function activateHook()
    {
        //  Set cookie for tracking in Connect Dashboard. This is quite hacky
        setcookie('activate_sale_pop', 1, time() + 60 * 60 *24);
        static::instance()
            ->handler
            ->activatePluginHook('Connect - Activate plugin', 'sale_notification');
    }

    public static function deactivateHook()
    {
        static::instance()
            ->handler
            ->deactivatePluginHook('Connect - Inactivate plugin', 'sale_notification');
    }

    public static function uninstallHook()
    {
        static::instance()
            ->handler
            ->uninstallHook('Connect - Delete plugin', 'sale_notification');
    }
}
