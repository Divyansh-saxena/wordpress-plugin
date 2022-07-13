<?php
class Poet_Public {
	private $poet;
	private $version;
	public function __construct( $plugin_name, $version ) {
		include_once dirname( __FILE__ ) . '/partials/class-poet-consumer.php';
		$this->poet    = $plugin_name;
		$this->version = $version;
	}
	public function enqueue_styles() {
		wp_enqueue_style( $this->poet, plugin_dir_url( __FILE__ ) . 'css/poet-public.css', array(), $this->version, 'all' );
		wp_register_style( 'poet-badge-font', 'https://fonts.googleapis.com/css?family=Roboto' );
		wp_enqueue_style( 'poet-badge-font' );
	}
	public function enqueue_scripts() {
	}
	public function poet_badge_handler( $content ) {
		$post                  = get_post();
		$quill_image_url       = plugin_dir_url( __FILE__ ) . '/images/quill.svg';
		$post_publication_date = get_the_modified_time( 'F jS Y, H:i', $post );
		$frost_last_updated    = date( 'F jS Y, H:i', (int) get_post_meta( $post->ID, 'poet_last_updated', true ) );
		$work_id               = get_post_meta( $post->ID, 'poet_work_id', true );
		$poet_badge            = '';
		if ( strlen( $work_id ) === 64 ) {
			include_once dirname( __FILE__ ) . '/partials/poet-badge-template.php';
			$poet_badge = print_poet_template( $quill_image_url, $work_id, $frost_last_updated );
		}
		return $content . $poet_badge;
	}
	public static function post_article( $post_id ) {

		$active  = isset( get_option( 'poet_option' )['active'] ) ? 1 : 0;
		$api_url = ! empty( get_option( 'poet_option' )['api_url'] ) ? 1 : 0;
		$token   = ! empty( get_option( 'poet_option' )['token'] ) ? 1 : 0;
		$post    = get_post( $post_id );
		if ( 'publish' !== $post->post_status || ! $active || ! $api_url || ! $token ) {
			return;
		}
		$author = isset( get_option( 'poet_option' )['author'] ) ? get_option( 'poet_option' )['author'] : '';
		$url    = isset( get_option( 'poet_option' )['api_url'] ) ? get_option( 'poet_option' )['api_url'] : '';
		$token  = isset( get_option( 'poet_option' )['token'] ) ? get_option( 'poet_option' )['token'] : '';
		$consumer = new Poet_Consumer( $author, $url, $token, $post );
		try {
			$response              = $consumer->consume();
			$decoded_response_body = json_decode( $response['body'] );
			update_post_meta( $post_id, 'poet_work_id', '' );
			if ( json_last_error() !== JSON_ERROR_SYNTAX
				&& is_object( $decoded_response_body )
				&& property_exists( $decoded_response_body, 'workId' ) ) {
				update_post_meta( $post_id, 'poet_work_id', $decoded_response_body->{'workId'} );
				update_post_meta( $post_id, 'poet_last_updated', time() );
			}
		} catch ( Exception $e ) {
			update_post_meta( $post_id, 'poet_work_id', 'fail in post call' );
		}
	}
}