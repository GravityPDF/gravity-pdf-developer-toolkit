<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use WP_UnitTestCase;
use GPDFAPI;

/**
 * @package     Gravity PDF Developer Toolkit
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TestDeactivate
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Legacy
 *
 * @group   legacy
 */
class TestDeactivate extends WP_UnitTestCase {

	/**
	 * @var Deactivate
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Deactivate( GPDFAPI::get_options_class() );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testMaybeDeactivateLegacyPlugin() {
		$options = GPDFAPI::get_options_class();

		$settings = $options->get_settings();
		$this->assertArrayNotHasKey( 'advanced_templating', $settings );

		$this->class->maybeDeactivateLegacyPlugin();

		$settings = $options->get_settings();
		$this->assertTrue( $settings['advanced_templating'] );
	}
}

function is_plugin_active( $slug ) {
	return true;
}
