<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use WP_UnitTestCase;
use \Mpdf\Mpdf;

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
 * Class TestHtml
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @group   writer
 */
class TestHtml extends WP_UnitTestCase {

	/**
	 * @var Html
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Html();

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testHtml() {
		$mpdf = $this->getMockBuilder( Mpdf::class )->getMock();
		$mpdf->expects( $this->once() )
			 ->method( 'WriteHTML' );

		$this->class->setMpdf( $mpdf );

		$this->assertTrue( method_exists( $this->class, 'addHtml' ) );

		$this->class->addHtml( '' );
	}
}
