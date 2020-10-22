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
 * Class TestEllipse
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @group   writer
 */
class TestEllipse extends WP_UnitTestCase {

	/**
	 * @var Ellipse
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Ellipse();

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testExceptions() {
		try {
			$this->class->ellipse();
		} catch ( \BadMethodCallException $e ) {
			$this->assertEquals( '$position needs to include an array with four elements: $x, $y, $width, $height', $e->getMessage() );
		}

		try {
			$this->class->ellipse( [ 1, 2 ] );
		} catch ( \BadMethodCallException $e ) {
			$this->assertEquals( '$position needs to include an array with four elements: $x, $y, $width, $height', $e->getMessage() );
		}

		try {
			$this->class->ellipse( [ 1, 2, 3, 5, 6 ] );
		} catch ( \BadMethodCallException $e ) {
			$this->assertEquals( '$position needs to include an array with four elements: $x, $y, $width, $height', $e->getMessage() );
		}
	}

	/**
	 * @since 1.0
	 */
	public function testEllipse() {
		$e = null;

        $mpdf = \Spies\mock_object( new Mpdf( [ 'mode' => 'c' ] ) );
        $spy = $mpdf->spy_on_method('Ellipse' );

		$this->class->setMpdf( $mpdf );

		try {
			$this->class->ellipse( [ 1, 1, 2, 4 ] );
			$this->class->ellipse( [ 1, 1, 2 ] );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertNull( $e );

        $expectation = \Spies\expect_spy( $spy )->to_be_called->twice();
        $this->assertTrue( $expectation->met_expectations() );
	}
}
