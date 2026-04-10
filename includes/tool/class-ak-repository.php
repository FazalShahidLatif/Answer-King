<?php
/**
 * Answer King Research Repository
 * Handles saving and retrieving keyword maps.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Repository {

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'init', array( $this, 'register_research_cpt' ) );
		add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );
	}

	/**
	 * Register Research Map CPT (Private)
	 */
	public function register_research_cpt() {
		register_post_type( 'ak_research_map', array(
			'public'            => false,
			'show_ui'           => false,
			'supports'          => array( 'title', 'editor', 'author' ),
			'show_in_rest'      => true,
		) );
	}

	/**
	 * Register persistence routes
	 */
	public function register_api_routes() {
		register_rest_route( 'answer-king/v1', '/save-map', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'save_map' ),
			'permission_callback' => '__return_true',
		) );
	}

	/**
	 * Save research map data
	 */
	public function save_map( $request ) {
		$params = $request->get_json_params();
		
		if ( empty( $params['keyword'] ) || empty( $params['data'] ) ) {
			return new WP_Error( 'invalid_data', 'Keyword and data are required', array( 'status' => 400 ) );
		}

		$post_id = wp_insert_post( array(
			'post_title'   => sprintf( 'Research: %s', $params['keyword'] ),
			'post_content' => json_encode( $params['data'] ),
			'post_status'  => 'publish',
			'post_type'    => 'ak_research_map',
		) );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Link to client if provided
		if ( ! empty( $params['client_id'] ) ) {
			update_post_meta( $post_id, '_ak_client_id', (int) $params['client_id'] );
		}

		return array(
			'success' => true,
			'id'      => $post_id,
			'message' => 'Map saved successfully to SaaSSkul Ecosystem.',
		);
	}
}
AK_Repository::get_instance();
