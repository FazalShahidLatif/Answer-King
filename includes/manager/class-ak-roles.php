<?php
/**
 * SaaSSkul Role Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Roles {

	/**
	 * Setup specialized roles upon activation
	 */
	public static function setup_super_admin() {
		// Ensure Fazal (Super Admin) has ultimate capability
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( 'saasskul_manage_ecosystem' );
		}

		// Add custom "Analyst" role for tiered users
		add_role(
			'ak_analyst',
			__( 'Answer King Analyst', 'answer-king' ),
			array(
				'read'         => true,
				'edit_posts'   => false,
				'upload_files' => true,
				'ak_research'  => true, // Custom capability
			)
		);
	}

	/**
	 * Define custom role definitions by author
	 */
	public static function get_author_defined_roles() {
		return array(
			'ak_manager' => array(
				'display_name' => 'SaaSSkul Manager',
				'capabilities' => array( 'read', 'ak_research', 'ak_export' ),
			),
			'ak_analyst' => array(
				'display_name' => 'SaaSSkul Analyst',
				'capabilities' => array( 'read', 'ak_research' ),
			),
		);
	}
}
