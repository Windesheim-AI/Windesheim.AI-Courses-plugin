<?php

/**
 * Plugin Name: WingAI
 * Plugin URI: https://github.com/Windesheim-AI-App/WINsight
 * GitHub Plugin URI: https://github.com/Windesheim-AI-App/WINsight
 * Description: WingAI plugin for WordPress
 * Author: WingAI
 * Author URI: https://windesheim.tech/
 * Version: 1.0.1
 * Text Domain: wingai
 * Requires at least: 6.2
 * Tested up to: 6.4
 * Requires PHP: 7.1
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package  WingAI
 * @category Core
 * @author   Windesheim
 * @version  1.0.1
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class WingAI
{
    /**
     * Instance of the main WPGatsby class
     */
    private static WingAI|null $instance = null;

    /**
     * Returns instance of the main WPGatsby class
     */
    public static function instance(): WingAI
    {
        if (self::$instance) {
            return self::$instance;
        }

        self::$instance = new WingAI();
        self::$instance->setup_constants();
        self::$instance->include();
        self::$instance->init();

        return self::$instance;
    }

    private function setup_constants(): void
    {
        if (!defined('WingAI_PLUGIN_DIR')) {
            define('WingAI_PLUGIN_DIR', plugin_dir_path(__FILE__));
        }

        if (!defined('WingAI_PLUGIN_URL')) {
            define('WingAI_PLUGIN_URL', plugin_dir_url(__FILE__));
        }

        if (!defined('WingAI_PLUGIN_FILE')) {
            define('WingAI_PLUGIN_FILE', __FILE__);
        }

        if (!defined('WingAI_PLUGIN_BASENAME')) {
            define('WingAI_PLUGIN_BASENAME', plugin_basename(__FILE__));
        }

        if (!defined('WingAI_VERSION')) {
            define('WingAI_VERSION', '1.0.1');
        }

        if (!defined('WingAI_TEXT_DOMAIN')) {
            define('WingAI_TEXT_DOMAIN', 'wingai');
        }
    }

    public function include(): void
    {
        require_once WingAI_PLUGIN_DIR . 'includes/index.php';
        require_once WingAI_PLUGIN_DIR . 'pages/index.php';
        require_once WingAI_PLUGIN_DIR . 'components/index.php';
        require_once WingAI_PLUGIN_DIR . 'utils/index.php';
    }

    /**
     * Initialize plugin functionality
     */
    public function init(): void
    {
        register_activation_hook(__FILE__, array('WingAI_Activator', 'activate'));
        register_deactivation_hook(__FILE__, array('WingAI_Deactivator', 'deactivate'));

        new WingAI_Endpoints();
    }
}

if (!function_exists('wingai')) {
    /**
     * Returns instance of the main WingAI class
     *
     * @return WingAI
     * @throws Exception
     */
    function wingai()
    {
        return WingAI::instance();
    }
}
function wingai_enqueue_admin_styles()
{
    wp_enqueue_style('wingai-admin-styles', WingAI_PLUGIN_URL . 'stylesheets/admin-styles.css');
}


function wingai_enqueue_admin_scripts()
{
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
}

add_action('admin_enqueue_scripts', 'wingai_enqueue_admin_styles');
add_action('admin_enqueue_scripts', 'wingai_enqueue_admin_scripts');


wingai();

