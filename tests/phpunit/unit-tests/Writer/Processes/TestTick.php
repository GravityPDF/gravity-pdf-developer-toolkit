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
 * Class TestTick
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @group   writer
 */
class TestTick extends WP_UnitTestCase {

	/**
	 * @var Tick
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Tick();
		$this->class->setMpdf( new Mpdf( [ 'mode' => 'c' ] ) );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testConfigTick() {
		$config = $this->class->getTickConfig();

		$this->assertSame( '&#10004;', $config['markup'] );
		$this->assertSame( 'DejavuSansCondensed', $config['font'] );
		$this->assertSame( 16, $config['font-size'] );
		$this->assertSame( 16, $config['line-height'] );

		$config['markup']      = 'X';
		$config['font']        = 'Times';
		$config['font-size']   = 20;
		$config['line-height'] = 15;

		$this->class->configTick( $config );

		$this->assertSame( $config, $this->class->getTickConfig() );
	}

	/**
	 * @since 1.0
	 */
	public function testTickExceptions() {
		/* Test invalid string */
		try {
			$this->class->tick( '' );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( '$position needs to include an array with two elements: $x, $y', $e->getMessage() );

		/* Test invalid array */
		try {
			$this->class->tick( [ 1, 2, 3 ] );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( '$position needs to include an array with two elements: $x, $y', $e->getMessage() );
	}

	/**
	 * @since 1.0
	 */
	public function testTick() {
        $mpdf = \Spies\mock_object( new Mpdf( [ 'mode' => 'c' ] ) );
        $spy = $mpdf->spy_on_method('WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->tick( [ 10, 10 ] );
		$this->class->tick( [ 10, 10 ], [ 'font' => '' ] );
		$this->class->tick( [ 10, 10 ] );

        $expectation = \Spies\expect_spy( $spy )->to_be_called->times(3 );
        $this->assertTrue( $expectation->met_expectations() );
	}
}
