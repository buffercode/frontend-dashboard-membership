<?php
/**
 * Plugin Name: Frontend Dashboard Membership
 * Plugin URI: https://buffercode.com/plugin/frontend-dashboard-membership
 * Description: Frontend Dashboard Membership.
 * Version: 1.0
 * Author: vinoth06
 * Author URI: https://buffercode.com/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: frontend-dashboard-membership
 * Domain Path: /languages
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Version Number
 */
define( 'BC_FED_M_PLUGIN_VERSION', '1.0' );
define( 'BC_FED_M_PLUGIN_VERSION_TYPE', 'FREE' );

/**
 * App Name
 */
define( 'BC_FED_M_APP_NAME', 'Frontend Dashboard Membership' );

/**
 * Root Path
 */
define( 'BC_FED_M_PLUGIN', __FILE__ );
/**
 * Plugin Base Name
 */
define( 'BC_FED_M_PLUGIN_BASENAME', plugin_basename( BC_FED_M_PLUGIN ) );
/**
 * Plugin Name
 */
define( 'BC_FED_M_PLUGIN_NAME', trim( dirname( BC_FED_M_PLUGIN_BASENAME ), '/' ) );
/**
 * Plugin Directory
 */
define( 'BC_FED_M_PLUGIN_DIR', untrailingslashit( dirname( BC_FED_M_PLUGIN ) ) );



require_once BC_FED_M_PLUGIN_DIR . '/fedm_autoload.php';