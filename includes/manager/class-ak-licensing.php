<?php
/**
 * SaaSSkul Licensing Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Licensing {

	private static $instance;

	/**
	 * Default Author (Fazal Shahid Latif)
	 */
	const SUPER_ADMIN_EMAIL = 'info@saasskul.com';

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_shortcode( 'saasskul_license_status', array( $this, 'display_license_status' ) );
		add_filter( 'ak_is_pro_active', array( $this, 'check_pro_status' ) );
	}

	/**
	 * Check if current user has an active SaaSSkul License
	 */
	public function check_pro_status( $status = false ) {
		$user = wp_get_current_user();
		
		// Priority 1: Super Admin (Fazal) always has Pro
		if ( $user->user_email === self::SUPER_ADMIN_EMAIL ) {
			return true;
		}

		// Priority 2: Check database for license key
		$license_key = get_option( 'ak_saasskul_license_key' );
		if ( ! empty( $license_key ) ) {
			// Mock verification logic for local testing
			if ( strpos( $license_key, 'SAAS-' ) === 0 ) {
				return true;
			}
		}

		return $status;
	}

	/**
	 * Display status via shortcode
	 */
	public function display_license_status() {
		if ( $this->check_pro_status() ) {
			return '<div class="ak-license-badge ak-pro">SaaSSkul License Active (Pro Edition)</div>';
		}
		return '<div class="ak-license-badge ak-free">SaaSSkul License Inactive (Free Features Only)</div>';
	}
}
