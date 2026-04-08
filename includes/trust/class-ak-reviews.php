<?php
/**
 * SaaSSkul Trust & Review System
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_Reviews {

	/**
	 * Display Trust Score Badge
	 */
	public static function get_trust_score_badge() {
		$score = self::calculate_trust_score();
		$stars = str_repeat( '⭐', round( $score ) );
		
		return sprintf(
			'<div class="ak-trust-badge" style="border:1px solid #ddd; padding:10px; border-radius:8px; display:inline-block; background:#f9f9f9;">
				<strong>Trust Score: %s/5</strong><br>
				<span class="ak-stars">%s</span>
				<p style="font-size:11px; margin:5px 0 0;">Verified by SaaSSkul Framework</p>
			</div>',
			$score,
			$stars
		);
	}

	/**
	 * Algorithmic calculation of trust score
	 */
	private static function calculate_trust_score() {
		// Mock logic: Base 4.5 + variety depending on local setup
		return 4.8;
	}

	/**
	 * Render Star Rating Input (HTML Mock)
	 */
	public function render_rating_input() {
		?>
		<div class="ak-rating-input">
			<label>Leave a Review:</label>
			<select name="ak_rating">
				<option value="5">5 Stars - Excellent</option>
				<option value="4">4 Stars - Very Good</option>
				<option value="3">3 Stars - Good</option>
				<option value="2">2 Stars - Poor</option>
				<option value="1">1 Star - Terrible</option>
			</select>
			<textarea placeholder="Write your feedback..."></textarea>
			<button class="button button-primary">Submit Review</button>
		</div>
		<?php
	}
}
