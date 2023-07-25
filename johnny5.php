<?php
/**
 * Plugin Name: Johnny5
 * Description: Guided AI Prompts for WordPress. 
 * Author: Sorta Rad
 * Author URI: https://sortarad.io
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
