<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use WP_UnitTestCase;
use mPDF;

/**
 * @package     Gravity PDF Developer Toolkit
 * @copyright   Copyright (c) 2018, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF Developer Toolkit.

    Copyright (C) 2018, Blue Liquid Designs

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

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
		$this->class->setMpdf( new mPDF() );

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
		$mpdf = $this->getMock( mPDF::class );
		$mpdf->expects( $this->exactly( 3 ) )
		     ->method( 'WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->tick( [ 10, 10 ] );
		$this->class->tick( [ 10, 10 ], [ 'font' => '' ] );
		$this->class->tick( [ 10, 10 ] );
	}
}
