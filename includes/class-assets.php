<?php

namespace Posty\Johnny5;

class Assets {
	/**
	 * Registers and enqueues a style.
	 *
	 * @param string $name Name of the style.
	 * @param array  $dependencies Array of dependencies for the style.
	 */
	public static function add_style( $name, $dependencies = array() ) {
		wp_enqueue_style(
			"johnny5-{$name}-style",
			JOHNNY5_ASSETS_URL . $name . '.css',
			$dependencies,
			JOHNNY5_VERSION
		);
	}

	/**
	 * Registers and enqueues a script.
	 *
	 * @param string $name Name of the script.
	 * @param array  $data Array of parameters to add to the script.
	 * @param array  $dependencies Array of dependencies for the script.
	 */
	public static function add_script( $name, $data = array(), $dependencies = array() ) {
		$asset_filepath = JOHNNY5_ASSETS_PATH . $name . '.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : array(
			'dependencies' => array(),
			'version'      => JOHNNY5_VERSION,
		);

		wp_register_script(
			"johnny5-{$name}-script",
			JOHNNY5_ASSETS_URL . $name . '.js',
			array_merge( $asset_file['dependencies'], $dependencies ),
			$asset_file['version'],
			true
		);

		if ( ! empty( $data ) && is_array( $data ) ) {
			wp_add_inline_script( "johnny5-{$name}-script", 'var johnny5 = ' . wp_json_encode( $data ), 'before' );
		}

		wp_enqueue_script( "johnny5-{$name}-script" );
	}
}
