<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Xophz_Compass_Gale_Boomerang
 * @subpackage Xophz_Compass_Gale_Boomerang/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Xophz_Compass_Gale_Boomerang
 * @subpackage Xophz_Compass_Gale_Boomerang/public
 * @author     Your Name <email@example.com>
 */
class Xophz_Compass_Gale_Boomerang_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Xophz_Compass_Gale_Boomerang_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Xophz_Compass_Gale_Boomerang_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/xophz-compass-gale-boomerang-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Xophz_Compass_Gale_Boomerang_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Xophz_Compass_Gale_Boomerang_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/xophz-compass-gale-boomerang-public.js', array(), $this->version, true );

	}

	/**
	 * Captures the UTM and referer data stored in the cookie when a new user registers (CRM Lead).
	 *
	 * @since    1.0.0
	 * @param    int    $user_id    The ID of the newly registered user.
	 */
	public function capture_wind_on_registration( $user_id ) {
		if ( isset( $_COOKIE['_xc_gale_wind'] ) ) {
			$wind_data = json_decode( stripslashes( $_COOKIE['_xc_gale_wind'] ), true );

			if ( is_array( $wind_data ) ) {
				// Sanitize and save each piece of data as user meta
				$fields = array( 'source', 'medium', 'campaign', 'term', 'content', 'referer', 'first_visit' );
				foreach ( $fields as $field ) {
					if ( ! empty( $wind_data[ $field ] ) ) {
						update_user_meta( $user_id, '_gale_wind_' . $field, sanitize_text_field( $wind_data[ $field ] ) );
					}
				}
			}
		}
	}

	/**
	 * The PHP Vanguard: Attempts to capture the wind server-side before falling back to JS.
	 * This ensures we capture attribution even if ad-blockers block the JS, provided the page isn't cached.
	 *
	 * @since    1.0.0
	 */
	public function capture_wind_early() {
		// Only track if the cookie doesn't already exist
		if ( ! isset( $_COOKIE['_xc_gale_wind'] ) && ! is_admin() ) {
			$windData = array(
				'source'      => isset( $_GET['utm_source'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_source'] ) ) : '',
				'medium'      => isset( $_GET['utm_medium'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_medium'] ) ) : '',
				'campaign'    => isset( $_GET['utm_campaign'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_campaign'] ) ) : '',
				'term'        => isset( $_GET['utm_term'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_term'] ) ) : '',
				'content'     => isset( $_GET['utm_content'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_content'] ) ) : '',
				'referer'     => isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '',
				'first_visit' => current_time( 'mysql', true )
			);

			// Only save if there's actual attribution data
			if ( ! empty( $windData['source'] ) || ! empty( $windData['referer'] ) ) {
				// Set cookie for 1 year
				setcookie( '_xc_gale_wind', wp_json_encode( $windData ), time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), false );
				// Manually inject into $_COOKIE array so it's immediately available to other PHP scripts on this exact load
				$_COOKIE['_xc_gale_wind'] = wp_json_encode( $windData );
			}
		}
	}

}

