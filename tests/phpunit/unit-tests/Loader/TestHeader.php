<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Helper\Helper_Templates;
use WP_UnitTestCase;

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
 * Class TestHeader
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @group   loader
 */
class TestHeader extends WP_UnitTestCase {

	/**
	 * @var Header
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$template = $this->getMockBuilder( Helper_Templates::class )
						->setConstructorArgs(
							[
								\GPDFAPI::get_log_class(),
								\GPDFAPI::get_data_class(),
								\GPDFAPI::get_form_class(),
							]
						)
						 ->setMethods( [ 'get_template_path_by_id' ] )
						 ->getMock();

		$template->method( 'get_template_path_by_id' )
				->will(
					$this->onConsecutiveCalls(
						__DIR__ . '/../pdfs/sample1.php',
						__DIR__ . '/../pdfs/sample2.php'
					)
				);

		$this->class = new Header( $template );
		$this->class->init();

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testToolkitHeader() {
		$results = apply_filters( 'gfpdf_template_header_details', [] );

		$this->assertArrayHasKey( 'toolkit', $results );
	}

	/**
	 * @since 1.0
	 */
	public function testAddToolkitSettings() {
		$results = $this->class->addToolkitSetting( [ 'template' => 'zadani', 'toolkit' => true ] );
		$this->assertArrayNotHasKey( 'toolkit', $results );

		$results = $this->class->addToolkitSetting( [ 'template' => 'zadani' ] );
		$this->assertArrayHasKey( 'toolkit', $results );
	}
}
