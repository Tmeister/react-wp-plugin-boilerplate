<?php
/**
 * Admin registration class.
 *
 * @package VajraStarterPlugin
 */

namespace SmallTownDev\VajraStarter\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\Jetpack\Assets;

/**
 * Class RegisterAdmin
 */
class RegisterAdmin {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Register Admin Dashboard.
		add_action( 'admin_menu', array( $this, 'register_admin_dashboard' ) );
	}

	/**
	 * Register admin dashboard.
	 */
	public function register_admin_dashboard() {
		$primary_slug = 'vajra-starter-dashboard';

		add_menu_page(
			__( 'Vajra Starter Dashboard', 'vajra-starter' ),
			_x( 'Vajra Starter', 'The Vajra Plugin product name, without the Vajra prefix', 'vajra-starter' ),
			'manage_options',
			$primary_slug,
			'',
			'dashicons-superhero',
			30
		);

		$dashboard_page_suffix = add_submenu_page( $primary_slug, 'Vajra Dashboard', 'Dashboard', 'manage_options', $primary_slug, array( $this, 'plugin_dashboard_page' ) );
		// Register dashboard hooks.
		add_action( 'load-' . $dashboard_page_suffix, array( $this, 'dashboard_admin_init' ) );

		$onboarding_page_suffix = add_submenu_page( $primary_slug, 'Vajra Dashboard', 'Getting Started', 'manage_options', 'vajra-starter-onboarding', array( $this, 'plugin_onboarding_page' ) );
		// Register onboarding hooks.
		add_action( 'load-' . $onboarding_page_suffix, array( $this, 'onboarding_admin_init' ) );

	}

	/**
	 * Initialize the Dashboard admin resources.
	 */
	public function dashboard_admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_admin_scripts' ) );
	}

	/**
	 * Enqueue plugin admin scripts and styles.
	 */
	public function enqueue_dashboard_admin_scripts() {
		Assets::register_script(
			'vajra-starter-dashboard',
			'build/dashboard/index.js',
			VAJRA_STARTER_ROOT_FILE,
			array(
				'in_footer'  => true,
				'textdomain' => 'vajra-starter',
			)
		);
		Assets::enqueue_script( 'vajra-starter-dashboard' );
		// Initial JS state.
		wp_add_inline_script( 'vajra-starter-dashboard', $this->render_dashboard_initial_state(), 'before' );
	}

	/**
	 * Initialize the Onboarding admin resources.
	 */
	public function onboarding_admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_onboarding_admin_scripts' ) );
	}

	/**
	 * Enqueue plugin onboarding admin scripts and styles.
	 */
	public function enqueue_onboarding_admin_scripts() {
		Assets::register_script(
			'vajra-starter-onboarding',
			'build/onboarding/index.js',
			VAJRA_STARTER_ROOT_FILE,
			array(
				'in_footer'  => true,
				'textdomain' => 'vajra-starter',
			)
		);
		Assets::enqueue_script( 'vajra-starter-onboarding' );
		// Initial JS state.
		wp_add_inline_script( 'vajra-starter-onboarding', $this->render_onboarding_initial_state(), 'before' );
	}

	/**
	 * Render the initial state into a JavaScript variable.
	 *
	 * @return string
	 */
	public function render_dashboard_initial_state() {
		return 'var vajraStarterPluginInitialState=JSON.parse(decodeURIComponent("' . rawurlencode( wp_json_encode( $this->initial_dashboard_state() ) ) . '"));';
	}

	/**
	 * Render the initial state into a JavaScript variable.
	 *
	 * @return string
	 */
	public function render_onboarding_initial_state() {
		return 'var vajraStarterPluginInitialState=JSON.parse(decodeURIComponent("' . rawurlencode( wp_json_encode( $this->initial_onboarding_state() ) ) . '"));';
	}

	/**
	 * Get the initial state data for hydrating the React UI.
	 *
	 * @return array
	 */
	public function initial_dashboard_state() {
		return array(
			'apiRoot'           => esc_url_raw( rest_url() ),
			'registrationNonce' => wp_create_nonce( 'vajra-registration-nonce' ),
		);
	}

	/**
	 * Get the initial state data for hydrating the React UI.
	 *
	 * @return array
	 */
	public function initial_onboarding_state() {
		return array(
			'apiRoot'           => esc_url_raw( rest_url() ),
			'registrationNonce' => wp_create_nonce( 'vajra-registration-nonce' ),
		);
	}

	/**
	 * Plugin Dashboard page.
	 */
	public function plugin_dashboard_page() {
		?>
			<div id="vajra-starter-dashboard-root"></div>
		<?php
	}

	/**
	 * Plugin Onboarding page.
	 */
	public function plugin_onboarding_page() {
		?>
			<div id="vajra-starter-onboarding-root"></div>
		<?php
	}
}