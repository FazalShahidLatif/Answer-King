<?php
/**
 * Answer King Keyword Research Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Keyword_Engine {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes
	 */
	public function register_routes() {
		register_rest_route( 'answer-king/v1', '/research', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_research_results' ),
			'permission_callback' => '__return_true', // In production, add capability check
		) );
	}

	/**
	 * REST API Callback
	 */
	public function get_research_results( $request ) {
		$keyword = $request->get_param( 'q' );
		if ( empty( $keyword ) ) {
			return new WP_Error( 'no_keyword', 'Search query is required', array( 'status' => 400 ) );
		}

		return self::fetch_suggestions( $keyword );
	}

	/**
	 * Fetch suggestions from Google Autocomplete
	 */
	public static function fetch_suggestions( $keyword ) {
		$url = 'https://suggestqueries.google.com/complete/search?client=chrome&q=' . urlencode( $keyword );
		
		$response = wp_remote_get( $url );
		
		if ( is_wp_error( $response ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );

		if ( isset( $data[1] ) ) {
			return self::group_results( $data[1] );
		}

		return array();
	}

	/**
	 * Smart Grouping Algorithm with SEO Metrics
	 */
	private static function group_results( $suggestions ) {
		$grouped = array(
			'questions'    => array(),
			'prepositions' => array(),
			'comparisons'  => array(),
			'other'        => array(),
		);

		$patterns = array(
			'questions'    => '/\b(who|what|where|when|why|how|are|can|is)\b/i',
			'prepositions' => '/\b(for|is|near|to|without|with|can)\b/i',
			'comparisons'  => '/\b(and|like|or|versus|vs)\b/i',
		);

		foreach ( $suggestions as $phrase ) {
			$matched = false;
			
			// Generate Mock SEO Metrics for visualization
			$metrics = array(
				'volume'     => rand( 100, 15000 ),
				'difficulty' => rand( 1, 100 ),
				'cpc'        => (float) rand( 10, 500 ) / 100,
			);

			$node_data = array(
				'name'    => $phrase,
				'metrics' => $metrics,
			);

			foreach ( $patterns as $key => $pattern ) {
				if ( preg_match( $pattern, $phrase ) ) {
					$grouped[$key][] = $node_data;
					$matched = true;
					break;
				}
			}
			if ( ! $matched ) {
				$grouped['other'][] = $node_data;
			}
		}

		return $grouped;
	}
}
