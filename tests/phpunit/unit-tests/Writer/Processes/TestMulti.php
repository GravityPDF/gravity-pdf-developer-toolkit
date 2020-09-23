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
 * Class TestMulti
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @group   writer
 */
class TestMulti extends WP_UnitTestCase {

	/**
	 * @var Multi
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Multi();
		$this->class->setMpdf( new Mpdf( [ 'mode' => 'c' ] ) );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testConfigMulti() {
		$config = $this->class->getMultiConfig();

		$this->assertSame( 10, $config['font-size'] );
		$this->assertSame( 14, $config['line-height'] );
		$this->assertSame( false, $config['strip-br'] );

		$config['font-size']   = 20.0;
		$config['line-height'] = 25.0;
		$config['strip-br']    = true;

		$this->class->configMulti( $config );

		$this->assertSame( $config, $this->class->getMultiConfig() );
	}

	/**
	 * @since 1.0
	 */
	public function testAddMultiExceptions() {
		/* Test invalid position */
		try {
			$this->class->addMulti( '', '' );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( '$position needs to include an array with four elements: $x, $y, $width, $height', $e->getMessage() );

		/* Test invalid overflow */
		try {
			$this->class->addMulti( '', [ 1, 2, 3, 4 ], 'test' );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( '$overflow can only be "auto" or "visible".', $e->getMessage() );
	}

	/**
	 * @since 1.0
	 */
	public function testAddMulti() {
        $mpdf = \Spies\mock_object( new Mpdf( [ 'mode' => 'c' ] ) );
        $spy = $mpdf->spy_on_method('WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->addMulti( '', [ 10, 10, 10, 10 ] );
		$this->class->addMulti( '', [ 10, 10, 10, 10 ] );
		$this->class->addMulti( '', [ 10, 10, 10, 10 ] );

        $expectation = \Spies\expect_spy( $spy )->to_be_called->times(3 );
        $this->assertTrue( $expectation->met_expectations() );
	}

	/**
	 * @since 1.0
	 */
	public function testAddMultiCenter() {
        $mpdf = \Spies\mock_object( new Mpdf( [ 'mode' => 'c' ] ) );
        $spy = $mpdf->spy_on_method('WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->addMultiCenter( '', [ 10, 10, 10, 10 ] );
		$this->class->addMultiCenter( '', [ 10, 10, 10, 10 ] );
		$this->class->addMultiCenter( '', [ 10, 10, 10, 10 ] );

        $expectation = \Spies\expect_spy( $spy )->to_be_called->times(3 );
        $this->assertTrue( $expectation->met_expectations() );
	}

	/**
	 * @since 1.0
	 */
	public function testAddMultiRight() {
        $mpdf = \Spies\mock_object( new Mpdf( [ 'mode' => 'c' ] ) );
        $spy = $mpdf->spy_on_method('WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->addMultiRight( '', [ 10, 10, 10, 10 ] );
		$this->class->addMultiRight( '', [ 10, 10, 10, 10 ] );
		$this->class->addMultiRight( '', [ 10, 10, 10, 10 ] );

        $expectation = \Spies\expect_spy( $spy )->to_be_called->times(3 );
        $this->assertTrue( $expectation->met_expectations() );
	}
}
