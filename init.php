<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'XOPHZ_COMPASS_GALE_BOOMERANG_VERSION', '26.5.2.703' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-xophz-compass-gale-boomerang-activator.php
 */
function activate_xophz_compass_gale_boomerang() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-xophz-compass-gale-boomerang-activator.php';
  Xophz_Compass_Gale_Boomerang_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-xophz-compass-gale-boomerang-deactivator.php
 */
function deactivate_xophz_compass_gale_boomerang() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-xophz-compass-gale-boomerang-deactivator.php';
  Xophz_Compass_Gale_Boomerang_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_xophz_compass_gale_boomerang' );
register_deactivation_hook( __FILE__, 'deactivate_xophz_compass_gale_boomerang' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-xophz-compass-gale-boomerang.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_xophz_compass_gale_boomerang() {

  if ( !class_exists('Xophz_Compass') ) {
    add_action( 'admin_init', 'shutoff_xophz_compass_gale_boomerang' );
    add_action( 'admin_notices', 'admin_notice_xophz_compass_gale_boomerang' );

    function shutoff_xophz_compass_gale_boomerang() {
      if ( ! function_exists( 'deactivate_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      deactivate_plugins( plugin_basename( __FILE__ ) );
    }

    function admin_notice_xophz_compass_gale_boomerang() {
      echo '<div class="updated"><p><strong>Xophz_Compass_Gale_Boomerang</strong> requires Compass to run. It has self <strong>deactivated</strong>.</p></div>';
      if ( isset( $_GET['activate'] ) )
        unset( $_GET['activate'] );
    }
  } else {
    $plugin = new Xophz_Compass_Gale_Boomerang();
    $plugin->run();
  }
  
}
add_action( 'plugins_loaded', 'run_xophz_compass_gale_boomerang' );