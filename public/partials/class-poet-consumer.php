<?php
defined( 'ABSPATH' ) || exit;
class Poet_Consumer {
	private $author;
	private $url;
	private $token;
	private $post;
	public function __construct( $author, $url, $token, $post ) {
		$this->author = $author;
		$this->url    = $url;
		$this->token  = $token;
		$this->post   = $post;
	}
	private function get_author_name() {
		$author = $this->author;

		if ( empty( $author ) ) {
			$user   = get_user_by( 'ID', $this->post->post_author );
			$author = $user->display_name;
			if ( ! empty( $user->first_name ) ) {
				$author = $user->first_name;
				if ( ! empty( $user->last_name ) ) {
					$author .= ' ' . $user->last_name;
				}
			} elseif ( ! empty( $user->last_name ) ) {
				$author = $user->last_name;
			}
		}
		return $author;
	}
	public function consume() {
		$tags_array = wp_get_post_tags( $this->post->ID, array( 'fields' => 'names' ) );
		$tags       = implode( ',', $tags_array );
		$body_array = array(
			'canonicalUrl'  => get_permalink( $this->post->ID ),
			'name'          => get_the_title( $this->post->ID ),
			'datePublished' => get_the_modified_time( 'c', $this->post ),
			'dateCreated'   => get_the_time( 'c', $this->post ),
			'author'        => $this->get_author_name(),
			'tags'          => $tags,
			'content'       => $this->post->post_content,
		);
		$body_json = wp_json_encode( $body_array );
		$response  = wp_remote_post(
			$this->url,
			array(
				'method'  => 'POST',
				'timeout' => 30,
				'headers' => array(
					'Content-Type' => 'application/json',
					'token'        => $this->token,
				),
				'body'    => $body_json,
			)
		);
		return $response;
	}
}