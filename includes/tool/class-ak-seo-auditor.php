<?php
/**
 * SaaSSkul Advanced SEO Auditor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_SEO_Auditor {

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
		register_rest_route( 'answer-king/v1', '/audit', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'api_run_audit' ),
			'permission_callback' => '__return_true',
		) );
	}

	/**
	 * Run an SEO Audit on a post
	 */
	public function api_run_audit( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$keyword = $request->get_param( 'keyword' );

		if ( empty( $post_id ) || empty( $keyword ) ) {
			return new WP_Error( 'missing_params', 'Post ID and Keyword are required', array( 'status' => 400 ) );
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'not_found', 'Post not found', array( 'status' => 404 ) );
		}

		return $this->perform_audit( $post, $keyword );
	}

	/**
	 * Audit Logic
	 */
	private function perform_audit( $post, $keyword ) {
		$content = $post->post_content;
		$title   = $post->post_title;
		$checks  = array();
		$score   = 0;

		// 1. Title Check
		$in_title = stripos( $title, $keyword ) !== false;
		$checks['title'] = array(
			'label'   => 'Keyword in Title',
			'passed'  => $in_title,
			'message' => $in_title ? 'Great! Keyword found in the title.' : 'Missing keyword in the post title.',
		);
		if ( $in_title ) $score += 25;

		// 2. Headings Check (H1/H2)
		$in_headings = preg_match( '/<h[1-2][^>]*>.*?' . preg_quote( $keyword, '/' ) . '.*?<\/h[1-2]>/i', $content );
		$checks['headings'] = array(
			'label'   => 'Keyword in Headings (H1-H2)',
			'passed'  => (bool) $in_headings,
			'message' => $in_headings ? 'Keyword used in subheadings.' : 'Try adding the keyword to an H2 tag.',
		);
		if ( $in_headings ) $score += 25;

		// 3. Density Check
		$count = substr_count( stripos( $content, $keyword ), $keyword );
		$word_count = str_word_count( strip_tags( $content ) );
		$density = $word_count > 0 ? ( $count / $word_count ) * 100 : 0;
		$checks['density'] = array(
			'label'   => 'Keyword Density',
			'passed'  => $density > 0.5 && $density < 2.5,
			'message' => sprintf( 'Current density: %.2f%%. Optimal is 1-2%%.', $density ),
		);
		if ( $checks['density']['passed'] ) $score += 25;

		// 4. First Paragraph
		$first_p = substr( strip_tags( $content ), 0, 300 );
		$in_intro = stripos( $first_p, $keyword ) !== false;
		$checks['intro'] = array(
			'label'   => 'Keyword in Intro',
			'passed'  => $in_intro,
			'message' => $in_intro ? 'Introduced early in the text.' : 'Try mentioning the keyword in the first paragraph.',
		);
		if ( $in_intro ) $score += 25;

		return array(
			'score'    => $score,
			'checks'   => $checks,
			'summary'  => sprintf( 'Optimization Score: %d/100', $score ),
		);
	}
}
AK_SEO_Auditor::get_instance();
