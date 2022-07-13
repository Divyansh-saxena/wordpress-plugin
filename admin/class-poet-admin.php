<?php
class Poet_Admin {
	private $poet;
	private $version;
	private $plugin;
	public function __construct( $plugin_name, $version ) {
		$this->poet    = $plugin_name;
		$this->version = $version;
		$this->plugin  = plugin_basename( __FILE__ );
	}
	public function enqueue_styles() {
	}
	public function enqueue_scripts() {
	}
	public function add_settings_link( $links ) {
		$url           = menu_page_url( plugin_basename( __FILE__ ), false );
		$settings_link = '<a href="' . $url . '">' . __( 'Settings' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}
	public function register_setting() {
		register_setting( 'poet', 'poet_option', array( $this, 'sanitize' ) );
		add_settings_section( 'poet_setting_section_id', __( 'Po.et Settings' ), array( $this, 'print_section_info' ), $this->plugin );
		add_settings_field(
			'author', // ID.
			__( 'Author Name' ), // Title.
			array( $this, 'author_callback' ), // Callback.
			$this->plugin, // Page.
			'poet_setting_section_id' // Section.
		);
		add_settings_field(
			'api_url', // ID.
			__( 'API URL' ), // Title.
			array( $this, 'api_url_callback' ), // Callback.
			$this->plugin, // Page.
			'poet_setting_section_id' // Section.
		);
		add_settings_field(
			'token', // ID.
			__( 'API Token' ), // Title.
			array( $this, 'token_callback' ), // Callback.
			$this->plugin, // Page.
			'poet_setting_section_id' // Section.
		);
		add_settings_field(
			'active', // ID.
			__( 'Post articles automatically on insert or update?' ), // Title.
			array( $this, 'active_callback' ), // Callback.
			$this->plugin, // Page.
			'poet_setting_section_id' // Section.
		);
		add_settings_field(
			'backfill', // ID.
			__( 'Backfill all posts' ), // Title.
			array( $this, 'backfill_callback' ), // Callback.
			$this->plugin, // Page.
			'poet_setting_section_id' // Section.
		);
	}
	public function print_section_info() {
		echo esc_html( 'Enter Author Name, API URL, and Token (this will return to default value if the plugin deactivated and reactivated again):' );
	}
	public function sanitize( $input ) {
		$new_input = array();

		if ( isset( $input['author'] ) ) {
			$new_input['author'] = sanitize_text_field( $input['author'] );
		}

		if ( isset( $input['api_url'] ) ) {
			$new_input['api_url'] = esc_url_raw( $input['api_url'] );
		}
		if ( isset( $input['token'] ) ) {
			$new_input['token'] = sanitize_text_field( $input['token'] );
		}
		if ( isset( $input['active'] ) ) {
			$new_input['active'] = (int) $input['active'];
		}
		if ( isset( $input['backfill'] ) ) {
			$new_input['backfill'] = (int) $input['backfill'];
		}

		return $new_input;
	}
	public function author_callback() {
		printf(
			'<input type="text" id="author" name="poet_option[author]" value="%s" />',
			isset( get_option( 'poet_option' )['author'] ) ? esc_attr( get_option( 'poet_option' )['author'] ) : ''
		);
	}
	public function api_url_callback() {
		printf(
			'<input type="text" id="api_url" name="poet_option[api_url]" value="%s" required />',
			isset( get_option( 'poet_option' )['api_url'] ) ? esc_attr( get_option( 'poet_option' )['api_url'] ) : ''
		);
	}
	public function token_callback() {
		printf(
			'<input type="text" id="token" name="poet_option[token]" value="%s" required />',
			isset( get_option( 'poet_option' )['token'] ) ? esc_attr( get_option( 'poet_option' )['token'] ) : ''
		);
	}
	public function active_callback() {
		$checked = isset( get_option( 'poet_option' )['active'] ) ? 1 : 0;
		echo '<input type="checkbox" id="active" name="poet_option[active]" ' . checked( 1, $checked, false ) . ' />';
	}
	public function backfill_callback() {
		$checked = isset( get_option( 'poet_option' )['backfill'] ) ? 1 : 0;
		echo '<input type="checkbox" id="backfill" name="poet_option[backfill]" ' . checked( 1, $checked, false ) . ' />';
	}
	public function add_options_page() {
		add_options_page(
			__( 'Po.et' ),
			__( 'Po.et' ),
			'manage_options',
			$this->plugin,
			array( $this, 'create_options_page' )
		);
	}
	public function create_options_page() {
		?>
		<div class="wrap">

			<form method="post" action="options.php">
				<?php
				settings_fields( 'poet' );
				do_settings_sections( $this->plugin );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}