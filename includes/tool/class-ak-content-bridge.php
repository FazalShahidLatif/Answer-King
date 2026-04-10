<?php
/**
 * SaaSSkul Automated Content Generation Bridge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Content_Bridge {

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route( 'answer-king/v1', '/generate-content', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'api_generate_content' ),
			'permission_callback' => '__return_true',
		) );
	}

	/**
	 * REST API Callback for content generation
	 */
	public function api_generate_content( $request ) {
		$params = $request->get_json_params();
		$keyword = $params['keyword'] ?? '';
		$nodes   = $params['selectedNodes'] ?? array();
		
		if ( empty( $keyword ) ) {
			return new WP_Error( 'no_keyword', 'Seed keyword is required', array( 'status' => 400 ) );
		}

		// Mock AI Generation (for local version stability)
		$content = $this->mock_generate_content( $keyword, $nodes );

		// Create the Post Draft
		$post_id = wp_insert_post( array(
			'post_title'   => sprintf( 'Ultimate Guide to %s', ucwords( $keyword ) ),
			'post_content' => $content,
			'post_status'  => 'draft',
			'post_type'    => 'post',
		) );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return array(
			'success' => true,
			'post_id' => $post_id,
			'url'     => get_edit_post_link( $post_id, 'raw' ),
			'message' => 'SaaSSkul Content Bridge: Post draft generated successfully.',
		);
	}

	/**
	 * Mock content generation to ensure system stability
	 */
	private function mock_generate_content( $keyword, $nodes ) {
		$output = "<!-- wp:heading -->\n<h2>Introduction to " . ucwords( $keyword ) . "</h2>\n<!-- /wp:heading -->\n";
		$output .= "<!-- wp:paragraph -->\n<p>Exploring <strong>" . $keyword . "</strong> within the SaaSSkul Ecosystem provides deep insights into semantic search trends.</p>\n<!-- /wp:paragraph -->\n";

		if ( ! empty( $nodes ) ) {
			$output .= "<!-- wp:heading {\"level\":3} -->\n<h3>Key Questions & Insights</h3>\n<!-- /wp:heading -->\n";
			$output .= "<!-- wp:list -->\n<ul>";
			foreach ( $nodes as $node ) {
				$output .= "<li>" . esc_html( $node ) . "</li>";
			}
			$output .= "</ul>\n<!-- /wp:list -->\n";
		}

		$output .= "<!-- wp:paragraph -->\n<p><em>Selected research data was processed using the Answer King Automated Content Bridge.</em></p>\n<!-- /wp:paragraph -->";

		return $output;
	}
}
AK_Content_Bridge::get_instance();
