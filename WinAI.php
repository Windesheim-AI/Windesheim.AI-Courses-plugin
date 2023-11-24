<?php

/**
 * Plugin Name: WinAI
 * Plugin URI: https://github.com/Windesheim-AI-App/WINsight
 * GitHub Plugin URI: https://github.com/Windesheim-AI-App/WINsight
 * Description: WinAI plugin for WordPress
 * Author: WinAI
 * Author URI: https://windesheim.tech/
 * Version: 1.0.2
 * Text Domain: winai
 * Requires at least: 6.2
 * Tested up to: 6.4
 * Requires PHP: 7.1
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package  WinAI
 * @category Core
 * @author   Windesheim
 * @version  1.0.2
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class WinAI
{
    /**
     * Instance of the main WPGatsby class
     */
    private static WinAI|null $instance = null;

    /**
     * Returns instance of the main WPGatsby class
     */
    public static function instance(): WinAI
    {
        if (self::$instance) {
            return self::$instance;
        }

        self::$instance = new WinAI();
        self::$instance->setup_constants();
        self::$instance->include();
        self::$instance->init();

        return self::$instance;
    }

    private function setup_constants(): void
    {
        if (!defined('WinAI_PLUGIN_DIR')) {
            define('WinAI_PLUGIN_DIR', plugin_dir_path(__FILE__));
        }

        if (!defined('WinAI_PLUGIN_URL')) {
            define('WinAI_PLUGIN_URL', plugin_dir_url(__FILE__));
        }

        if (!defined('WinAI_PLUGIN_FILE')) {
            define('WinAI_PLUGIN_FILE', __FILE__);
        }

        if (!defined('WinAI_PLUGIN_BASENAME')) {
            define('WinAI_PLUGIN_BASENAME', plugin_basename(__FILE__));
        }

        if (!defined('WinAI_VERSION')) {
            define('WinAI_VERSION', '1.0.2');
        }

        if (!defined('WinAI_TEXT_DOMAIN')) {
            define('WinAI_TEXT_DOMAIN', 'winai');
        }
    }

    public function include(): void
    {
        require_once WinAI_PLUGIN_DIR . 'includes/index.php';
        require_once WinAI_PLUGIN_DIR . 'pages/index.php';
        require_once WinAI_PLUGIN_DIR . 'components/index.php';
        require_once WinAI_PLUGIN_DIR . 'utils/index.php';
    }

    /**
     * Initialize plugin functionality
     */
    public function init(): void
    {
        register_activation_hook(__FILE__, array('WinAI_Activator', 'activate'));
        register_deactivation_hook(__FILE__, array('WinAI_Deactivator', 'deactivate'));
        register_uninstall_hook(__FILE__, array('WinAI_Uninstall', 'uninstall'));

        new WinAI_Endpoints();
    }
}

if (!function_exists('winai')) {
    /**
     * Returns instance of the main WinAI class
     *
     * @return WinAI
     * @throws Exception
     */
    function winai()
    {
        return WinAI::instance();
    }
}
function winai_enqueue_admin_styles()
{
    wp_enqueue_style('winai-admin-styles', WinAI_PLUGIN_URL . 'stylesheets/admin-styles.css');
}


function winai_enqueue_admin_scripts()
{
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-sortable');
}

add_action('admin_enqueue_scripts', 'winai_enqueue_admin_styles');
add_action('admin_enqueue_scripts', 'winai_enqueue_admin_scripts');


winai();

