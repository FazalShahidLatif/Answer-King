<?php
/**
 * Answer King Keyword Research Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Keyword_Engine {

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
	 * Smart Grouping Algorithm
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
			foreach ( $patterns as $key => $pattern ) {
				if ( preg_match( $pattern, $phrase ) ) {
					$grouped[$key][] = $phrase;
					$matched = true;
					break;
				}
			}
			if ( ! $matched ) {
				$grouped['other'][] = $phrase;
			}
		}

		return $grouped;
	}
}
