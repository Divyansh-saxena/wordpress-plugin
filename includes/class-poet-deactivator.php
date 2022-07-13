<?php
class Poet_Deactivator {
	public static function deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		unregister_setting(
			'poet',
			'poet_option',
			array( self, 'sanitize' )
		);
	}
}
