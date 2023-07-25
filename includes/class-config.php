<?php

namespace Posty\Johnny5;

class Config {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
	}

	/**
	 * Get the values for all settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		return array(
			'api_key'    => get_option( 'johnny5_openai_api_key' ),
			'max_tokens' => (int) get_option( 'johnny5_openai_max_tokens' ),
			'model'      => get_option( 'johnny5_openai_model' ),
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			'johnny5_settings',
			'johnny5_openai_api_key',
			array(
				'type'              => 'string',
				'description'       => __( 'OpenAI API Key', 'johnny5' ),
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'johnny5_settings',
			'johnny5_openai_model',
			array(
				'type'              => 'string',
				'description'       => __( 'OpenAI Model', 'johnny5' ),
				'default'           => 'gpt-3.5-turbo',
				'show_in_rest'      => array(
					'name'   => 'johnny5_openai_model',
					'schema' => array(
						'enum' => array(
							'gpt-3.5-turbo',
							'gpt-4',
						),
					),
				),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'johnny5_settings',
			'johnny5_openai_max_tokens',
			array(
				'type'              => 'integer',
				'description'       => __( 'OpenAI Max Tokens', 'johnny5' ),
				'default'           => 2048,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
			)
		);
	}

	/**
	 * Add the menu page.
	 */
	public function add_menu_page() {
		$hook_suffix = add_options_page(
			__( 'Johnny5', 'johnny5' ),
			__( 'Johnny5', 'johnny5' ),
			'manage_options',
			'johnny5',
			function() {
				echo '<div id="johnny5-admin-page"></div>';
			}
		);

		add_action( 'admin_print_scripts-' . $hook_suffix, array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		Assets::add_script( 'admin' );
		Assets::add_style( 'admin' );
		wp_enqueue_style( 'wp-components' );
	}
}
