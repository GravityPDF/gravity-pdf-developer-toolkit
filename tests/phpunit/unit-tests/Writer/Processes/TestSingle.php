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
 * Class TestSingle
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @group   writer
 */
class TestSingle extends WP_UnitTestCase {

	/**
	 * @var Single
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Single();
		$this->class->setMpdf( new Mpdf( [ 'mode' => 'c' ] ) );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testaddExceptions() {

		/* Test invalid position */
		try {
			$this->class->add( '', '' );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( '$position needs to include an array with four elements: $x, $y, $width, $height', $e->getMessage() );

		/* Test invalid overflow */
		try {
			$this->class->add( '', [ 1, 2, 3, 4 ], 'test' );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( '$overflow can only be "auto" or "visible".', $e->getMessage() );
	}

	/**
	 * @since 1.0
	 */
	public function testadd() {
		$mpdf = $this->getMockBuilder( mPDF::class )->getMock();
		$mpdf->expects( $this->exactly( 3 ) )
		     ->method( 'WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->add( '', [ 10, 10, 10, 10 ] );
		$this->class->add( '', [ 10, 10, 10, 10 ] );
		$this->class->add( '', [ 10, 10, 10, 10 ] );
	}

	/**
	 * @since 1.0.0-beta2
	 */
	public function testaddCenter() {
		$mpdf = $this->getMockBuilder( mPDF::class )->getMock();
		$mpdf->expects( $this->exactly( 3 ) )
		     ->method( 'WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->addCenter( '', [ 10, 10, 10, 10 ] );
		$this->class->addCenter( '', [ 10, 10, 10, 10 ] );
		$this->class->addCenter( '', [ 10, 10, 10, 10 ] );
	}

	/**
	 * @since 1.0.0-beta2
	 */
	public function testaddRight() {
		$mpdf = $this->getMockBuilder( mPDF::class )->getMock();
		$mpdf->expects( $this->exactly( 3 ) )
		     ->method( 'WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->addRight( '', [ 10, 10, 10, 10 ] );
		$this->class->addRight( '', [ 10, 10, 10, 10 ] );
		$this->class->addRight( '', [ 10, 10, 10, 10 ] );
	}
}
