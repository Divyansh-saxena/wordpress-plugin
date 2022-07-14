<?php

defined( 'ABSPATH' ) || exit;

define( 'POET_VERSION', '1.0.2-dev' );

function activate_poet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-poet-activator.php';
	Poet_Activator::activate();
}


function deactivate_poet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-poet-deactivator.php';
	Poet_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_poet' );
register_deactivation_hook( __FILE__, 'deactivate_poet' );


require plugin_dir_path( __FILE__ ) . 'includes/class-poet.php';


function run_poet() {
	$plugin = new Poet();
	$plugin->run();
}
run_poet();
