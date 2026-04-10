<?php
/**
 * Answer King Tool Page Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Tool_Page {

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_tool_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register the Answer King Tool menu item
	 */
	public function register_tool_page() {
		add_submenu_page(
			'saasskul-settings',
			__( 'Answer King Tool', 'answer-king' ),
			__( 'Answer King', 'answer-king' ),
			'manage_options',
			'answer-king-tool',
			array( $this, 'render_tool_page' )
		);
	}

	/**
	 * Enqueue React and D3 scripts for the tool page
	 */
	public function enqueue_assets( $hook ) {
		if ( 'saasskul_page_answer-king-tool' !== $hook ) {
			return;
		}

		$asset_file = AK_PLUGIN_DIR . 'build/index.asset.php';
		
		if ( file_exists( $asset_file ) ) {
			$assets = include $asset_file;
			wp_enqueue_script(
				'answer-king-tool-js',
				AK_PLUGIN_URL . 'build/index.js',
				$assets['dependencies'],
				$assets['version'],
				true
			);
			wp_enqueue_style(
				'answer-king-tool-css',
				AK_PLUGIN_URL . 'build/index.css',
				array( 'wp-components' ),
				$assets['version']
			);
		} else {
			// Fallback for development if build folder doesn't exist yet
			wp_enqueue_script( 'wp-api-fetch' );
			wp_enqueue_script( 'wp-element' );
			wp_enqueue_script( 'wp-components' );
		}
	}

	/**
	 * Render the App Container
	 */
	public function render_tool_page() {
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">Answer King Keyword Research</h1>
			<hr class="wp-header-end">
			<div id="answer-king-app">
				<p>Loading SaaSSkul Ecosystem Visualization...</p>
			</div>
		</div>
		<style>
			#answer-king-app {
				margin-top: 20px;
				background: #fff;
				border: 1px solid #ccd0d4;
				box-shadow: 0 1px 1px rgba(0,0,0,.04);
				min-height: 600px;
				border-radius: 8px;
				padding: 20px;
			}
		</style>
		<?php
	}
}
AK_Tool_Page::get_instance();
