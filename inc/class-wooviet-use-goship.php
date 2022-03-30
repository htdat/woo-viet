<?php
if (! defined('ABSPATH')) {
    exit;
}
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/misc.php';
include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
include_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
include_once ABSPATH . 'wp-admin/includes/class-plugin-installer-skin.php';

class Quiet_Skin extends Plugin_Installer_Skin
{
    public function feedback($feedback)
    {
        // just keep it quiet
    }
}

/**
 * The class connect to goship.io
 *
 * @see https://doc.goship.io 
 *
 * @author   KingDarkness
 * @since    v1.1
 *
 */
class WooViet_Use_Goship
{
    public $version;
    public function __construct($version = 'release')
    {
        $this->version = $version;
    }

    public function install_and_active($force = false)
    {
        $success = $this->install($force);
        $this->active();
        return $success;
    }

    public function install($force = false)
    {
        $installed_plugins = get_plugins();
        $goship_installed = array_key_exists('woo-goship/woo-goship.php', $installed_plugins) || in_array('woo-goship/woo-goship.php', $installed_plugins, true);
        if (!$goship_installed || $force) {
            $upgrader = new Plugin_Upgrader(new Quiet_Skin());
            $upgrader->install($this->download_url(), ['overwrite_package' => $force]);
            return true;
        }
        return false;
    }

    public function active()
    {
        if (!is_plugin_active('woo-goship/woo-goship.php')) {
            activate_plugin('woo-goship/woo-goship.php');
        }
    }

    public function deactive()
    {
        if (is_plugin_active('woo-goship/woo-goship.php')) {
            deactivate_plugins('woo-goship/woo-goship.php');
        }
    }

    public function redirect_to_setting()
    {
        wp_redirect(admin_url('admin.php?page=wc-settings&tab=shipping&section=woo_goship'));
    }

    private function download_url()
    {
        return 'https://github.com/KingDarkness/woo-goship/releases/download/' . $this->version . '/woo-goship.zip';
    }
}
