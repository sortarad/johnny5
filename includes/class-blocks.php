<?php

namespace Posty\Johnny5;

class Blocks {
	/**
	 * Array of blocks to register.
	 *
	 * @var array
	 */
	private function get_blocks() {
		return array(
			array(
				'name' => 'johnny5',
				'data' => array(
					'restUrl' => esc_url_raw( rest_url() ),
				),
			),
			array(
				'name' => 'johnny5/text',
			),
			array(
				'name' => 'johnny5/dropdown',
			),
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Register blocks.
	 */
	public function register() {
		foreach ( $this->get_blocks() as $block ) {
			register_block_type( JOHNNY5_BLOCKS_PATH . $block['name'] );

			if ( isset( $block['data'] ) ) {
				add_action(
					'wp_enqueue_scripts',
					function() use ( $block ) {
						$handle = str_replace( '/', '-', $block['name'] );
						// TODO: Improve script handling.
						wp_add_inline_script( 'johnny5-' . $handle . '-view-script', 'var johnny5 = ' . wp_json_encode( $block['data'] ), 'before' );
					}
				);
			}
		}
	}
}
