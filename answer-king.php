<?php
/**
 * Plugin Name: Answer King Core
 * Plugin URI:  https://saasskul.com/answer-king
 * Description: Premium local-first keyword research ecosystem and SaaSSkul SaaS Framework.
 * Version:     1.0.0
 * Author:      Fazal Shahid Latif (Founder & CEO SaaSSkul)
 * Author URI:  https://saasskul.com
 * License:     MIT
 * Text Domain: answer-king
 * 
 * © 2026 Fazal Shahid Latif. All rights reserved.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Answer King Class
 */
final class Answer_King {

	/**
	 * Plugin version
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Instance of this class
	 * @var Answer_King
	 */
	private static $instance;

	/**
	 * Get Class Instance
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Answer_King ) ) {
			self::$instance = new self();
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->init_hooks();
		}
		return self::$instance;
	}

	/**
	 * Setup plugin constants
	 */
	private function setup_constants() {
		define( 'AK_VERSION', $this->version );
		define( 'AK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'AK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'AK_BASENAME', plugin_basename( __FILE__ ) );
	}

	/**
	 * Include required files
	 */
	private function includes() {
		// Foundation Engine
		require_once AK_PLUGIN_DIR . 'includes/manager/class-ak-licensing.php';
		require_once AK_PLUGIN_DIR . 'includes/manager/class-ak-roles.php';
		require_once AK_PLUGIN_DIR . 'includes/manager/class-ak-client-manager.php';
		require_once AK_PLUGIN_DIR . 'includes/admin/class-ak-settings.php';
		require_once AK_PLUGIN_DIR . 'includes/admin/class-ak-tool.php';
		
		// Legal & Trust
		require_once AK_PLUGIN_DIR . 'includes/legal/class-ak-gdpr.php';
		require_once AK_PLUGIN_DIR . 'includes/trust/class-ak-reviews.php';
		
		// Tool Core
		require_once AK_PLUGIN_DIR . 'includes/tool/class-ak-keyword-engine.php';
		require_once AK_PLUGIN_DIR . 'includes/tool/class-ak-repository.php';
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}

	/**
	 * Run on plugins_loaded
	 */
	public function init() {
		// Fire off the licensing engine
		if ( class_exists( 'AK_Licensing' ) ) {
			AK_Licensing::get_instance();
		}

		// Initialize Tool UI
		if ( class_exists( 'AK_Tool_Page' ) ) {
			AK_Tool_Page::get_instance();
		}

		// Initialize Client Manager
		if ( class_exists( 'AK_Client_Manager' ) ) {
			AK_Client_Manager::get_instance();
		}

		// Initialize Repository
		if ( class_exists( 'AK_Repository' ) ) {
			AK_Repository::get_instance();
		}

		// Initialize Keyword Engine (REST Routes)
		if ( class_exists( 'AK_Keyword_Engine' ) ) {
			new AK_Keyword_Engine();
		}
	}

	/**
	 * Activation hook
	 */
	public function activate() {
		// Setup default roles and legal pages
		if ( class_exists( 'AK_Roles' ) ) {
			AK_Roles::setup_super_admin();
		}
		
		if ( class_exists( 'AK_GDPR' ) ) {
			AK_GDPR::generate_compliance_pages();
		}
		
		flush_rewrite_rules();
	}
}

/**
 * Initialize the plugin
 */
function answer_king_init() {
	return Answer_King::get_instance();
}
answer_king_init();
