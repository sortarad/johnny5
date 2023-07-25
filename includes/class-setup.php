<?php

namespace Posty\Johnny5;

class Setup {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_constants();
	}

	/**
	 * Set constants.
	 */
	private function set_constants() {
		define( 'JOHNNY5_VERSION', '1.0.0' );
		define( 'JOHNNY5_SLUG', 'johnny5' );
		define( 'JOHNNY5_PATH', plugin_dir_path( __DIR__ ) );
		define( 'JOHNNY5_ASSETS_PATH', JOHNNY5_PATH . 'build/' );
		define( 'JOHNNY5_BLOCKS_PATH', JOHNNY5_PATH . 'build/blocks/' );
		define( 'JOHNNY5_TEMPLATES_PATH', JOHNNY5_PATH . 'templates/' );
		define( 'JOHNNY5_LANGUAGES_PATH', JOHNNY5_PATH . 'languages/' );
		define( 'JOHNNY5_ASSETS_URL', plugin_dir_url( __DIR__ ) . 'build/' );
	}

	/**
	 * Initialize.
	 */
	public function init() {
		new Assets();
		new Blocks();
		new Filters();
		new Config();

		add_action( 'rest_api_init', array( new Endpoints\REST_Completion_Controller(), 'register_routes' ) );
	}
}
