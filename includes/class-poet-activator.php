<?php
class Poet_Activator {
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$default = array(
			'api_url' => 'https://api.poetnetwork.net/works',
			'token'   => '',
			'active'  => 1,
		);
		update_option( 'poet_option', $default );
	}
}
