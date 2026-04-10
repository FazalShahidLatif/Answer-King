<?php
/**
 * SaaSSkul Multi-tenant Client Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Client_Manager {

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'init', array( $this, 'register_client_cpt' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Register the Client Custom Post Type
	 */
	public function register_client_cpt() {
		$labels = array(
			'name'               => _x( 'Clients', 'post type general name', 'answer-king' ),
			'singular_name'      => _x( 'Client', 'post type singular name', 'answer-king' ),
			'menu_name'          => _x( 'Clients', 'admin menu', 'answer-king' ),
			'name_admin_bar'     => _x( 'Client', 'add new on admin bar', 'answer-king' ),
			'add_new'            => _x( 'Add New', 'client', 'answer-king' ),
			'add_new_item'       => __( 'Add New Client', 'answer-king' ),
			'new_item'           => __( 'New Client', 'answer-king' ),
			'edit_item'          => __( 'Edit Client', 'answer-king' ),
			'view_item'          => __( 'View Client', 'answer-king' ),
			'all_items'          => __( 'All Clients', 'answer-king' ),
			'search_items'       => __( 'Search Clients', 'answer-king' ),
			'not_found'          => __( 'No clients found.', 'answer-king' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => 'saasskul-settings', // Submenu of SaaSSkul
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'ak_client' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'thumbnail', 'excerpt' ),
			'show_in_rest'       => true,
		);

		register_post_type( 'ak_client', $args );
	}

	/**
	 * Register REST routes for client selection
	 */
	public function register_rest_routes() {
		register_rest_route( 'answer-king/v1', '/clients', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_clients' ),
			'permission_callback' => '__return_true',
		) );
	}

	/**
	 * Get list of clients for the tool dropdown
	 */
	public function get_clients() {
		$clients = get_posts( array(
			'post_type'      => 'ak_client',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		) );

		return array_map( function( $post ) {
			return array(
				'id'    => $post->ID,
				'name'  => $post->post_title,
				'site'  => get_post_meta( $post->ID, '_ak_client_website', true ),
			);
		}, $clients );
	}
}
AK_Client_Manager::get_instance();
