<?php
/**
 * SaaSSkul Core Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup Theme features
 */
function saasskul_core_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	
	// Register Navigation Menus
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'saasskul-core' ),
	) );
}
add_action( 'after_setup_theme', 'saasskul_core_setup' );

/**
 * Register Customizer Settings
 */
function saasskul_core_customize_register( $wp_customize ) {
	// Add SaaSSkul Branding Section
	$wp_customize->add_section( 'saasskul_branding', array(
		'title'    => __( 'SaaSSkul Branding', 'saasskul-core' ),
		'priority' => 30,
	) );

	// Header Color Setting
	$wp_customize->add_setting( 'header_color', array(
		'default'   => '#1a1a1a',
		'transport' => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_color', array(
		'label'    => __( 'Header Background Color', 'saasskul-core' ),
		'section'  => 'saasskul_branding',
		'settings' => 'header_color',
	) ) );

	// Primary Brand Color
	$wp_customize->add_setting( 'brand_color', array(
		'default'   => '#0073aa',
		'transport' => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'brand_color', array(
		'label'    => __( 'Primary Brand Color', 'saasskul-core' ),
		'section'  => 'saasskul_branding',
		'settings' => 'brand_color',
	) ) );
}
add_action( 'customize_register', 'saasskul_core_customize_register' );

/**
 * Output dynamic CSS in head
 */
function saasskul_core_customizer_css() {
	?>
	<style type="text/css">
		header { background: <?php echo get_theme_mod( 'header_color', '#1a1a1a' ); ?> !important; }
		.button-primary, .ak-btn { background-color: <?php echo get_theme_mod( 'brand_color', '#0073aa' ); ?> !important; }
	</style>
	<?php
}
add_action( 'wp_head', 'saasskul_core_customizer_css' );
