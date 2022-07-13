<?php
class Poet {
	protected $loader;
	protected $poet;
	protected $version;
	public function __construct() {
		if ( defined( 'POET_VERSION' ) ) {
			$this->version = POET_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->poet = 'poet';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-poet-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-poet-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-poet-backfill.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-poet-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-poet-public.php';
		$this->loader = new Poet_Loader();
	}
	private function set_locale() {
		$plugin_i18n = new Poet_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	private function define_admin_hooks() {
		$plugin_admin = new Poet_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		$this->loader->add_filter( 'plugin_action_links_' . $this->poet, $plugin_admin, 'add_settings_link' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );
		$this->loader->add_action( 'admin_init', 'Poet_Backfill', 'init' );
	}
	private function define_public_hooks() {
		$plugin_public = new Poet_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'save_post', 'Poet_Public', 'post_article' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'poet_badge_handler' );
	}
	public function run() {
		$this->loader->run();
	}
	public function get_plugin_name() {
		return $this->poet;
	}
	public function get_loader() {
		return $this->loader;
	}
	public function get_version() {
		return $this->version;
	}
}
