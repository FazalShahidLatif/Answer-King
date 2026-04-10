<?php
/**
 * SaaSSkul Plugin Settings Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Settings {

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_settings_pages' ) );
		add_action( 'admin_init', array( $this, 'initialize_settings' ) );
	}

	/**
	 * Create the SaaSSkul admin menu
	 */
	public function register_settings_pages() {
		add_menu_page(
			'SaaSSkul Settings',
			'SaaSSkul',
			'manage_options',
			'saasskul-settings',
			array( $this, 'render_settings_page' ),
			'dashicons-admin-generic',
			30
		);
	}

	/**
	 * Register settings and sections
	 */
	public function initialize_settings() {
		register_setting( 'saasskul_settings_group', 'ak_api_timeout' );
		register_setting( 'saasskul_settings_group', 'ak_license_check_interval' );
		register_setting( 'saasskul_settings_group', 'ak_ai_provider' );
		register_setting( 'saasskul_settings_group', 'ak_ai_api_key' );

		// Core Section
		add_settings_section(
			'saasskul_main_section',
			'Core Ecosystem Settings',
			null,
			'saasskul-settings'
		);

		add_settings_field(
			'ak_api_timeout',
			'API Timeout (seconds)',
			array( $this, 'render_number_field' ),
			'saasskul-settings',
			'saasskul_main_section',
			array( 'label_for' => 'ak_api_timeout', 'default' => 30 )
		);

		// AI Engine Section
		add_settings_section(
			'saasskul_ai_section',
			'AI Content Bridge Settings',
			null,
			'saasskul-settings'
		);

		add_settings_field(
			'ak_ai_provider',
			'AI Provider',
			array( $this, 'render_select_field' ),
			'saasskul-settings',
			'saasskul_ai_section',
			array(
				'label_for' => 'ak_ai_provider',
				'options'   => array(
					'openai' => 'OpenAI (GPT-4)',
					'gemini' => 'Google Gemini (Pro)',
				),
			)
		);

		add_settings_field(
			'ak_ai_api_key',
			'API Key',
			array( $this, 'render_password_field' ),
			'saasskul-settings',
			'saasskul_ai_section',
			array( 'label_for' => 'ak_ai_api_key' )
		);
	}

	public function render_number_field( $args ) {
		$value = get_option( $args['label_for'], $args['default'] );
		echo "<input type='number' name='{$args['label_for']}' value='{$value}' class='small-text'>";
	}

	public function render_select_field( $args ) {
		$value = get_option( $args['label_for'] );
		echo "<select name='{$args['label_for']}'>";
		foreach ( $args['options'] as $val => $label ) {
			echo "<option value='{$val}' " . selected( $value, $val, false ) . ">{$label}</option>";
		}
		echo "</select>";
	}

	public function render_password_field( $args ) {
		$value = get_option( $args['label_for'] );
		echo "<input type='password' name='{$args['label_for']}' value='{$value}' class='regular-text'>";
	}

	/**
	 * Render the Settings HTML
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1>SaaSSkul Ecosystem Control Panel</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'saasskul_settings_group' );
				do_settings_sections( 'saasskul-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
AK_Settings::get_instance();
