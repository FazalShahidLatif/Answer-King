<?php
/**
 * SaaSSkul GDPR & Legal Compliance Suite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AK_GDPR {

	/**
	 * Automatically generate legal pages if they don't exist
	 */
	public static function generate_compliance_pages() {
		$pages = array(
			'privacy-policy' => array(
				'title'   => 'Privacy Policy',
				'content' => 'This Privacy Policy describes how your personal information is collected, used, and shared when you visit or make a purchase from this site...',
			),
			'terms-of-service' => array(
				'title'   => 'Terms of Service',
				'content' => 'By accessing this website, you are agreeing to be bound by these web site Terms and Conditions of Use...',
			),
			'cookie-consent' => array(
				'title'   => 'Cookie Policy',
				'content' => 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.',
			),
		);

		foreach ( $pages as $slug => $data ) {
			if ( ! get_page_by_path( $slug ) ) {
				wp_insert_post( array(
					'post_title'   => $data['title'],
					'post_content' => $data['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_name'    => $slug,
				) );
			}
		}
	}

	/**
	 * Hook for cookie consent bar
	 */
	public function render_cookie_consent() {
		?>
		<div id="saasskul-cookie-notice" style="position:fixed; bottom:0; width:100%; background:#1a1a1a; color:#fff; padding:15px; text-align:center; z-index:9999; font-family: sans-serif;">
			<span>We use cookies to ensure you get the best experience on our website. <a href="/privacy-policy" style="color:#0073aa;">Learn more</a></span>
			<button onclick="document.getElementById('saasskul-cookie-notice').style.display='none';" style="margin-left:20px; padding:5px 15px; background:#0073aa; border:none; color:#fff; cursor:pointer; border-radius:3px;">Accept</button>
		</div>
		<?php
	}
}
