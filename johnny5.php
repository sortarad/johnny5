<?php
/**
 * Plugin Name: Johnny 5
 * Description: Forms, now supercharged with AI.
 * Author: Posty Studio
 * Author URI: https://posty.studio
 * License: GPL-3.0
 * Version: 1.0.0
 *
 * @package posty/johnny5
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require __DIR__ . '/vendor/autoload.php';

$posty_setup = new Posty\Johnny5\Setup();
$posty_setup->init();
