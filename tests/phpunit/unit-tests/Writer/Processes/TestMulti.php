<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use WP_UnitTestCase;
use \Mpdf\Mpdf;

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

	Copyright (c) 2018, Blue Liquid Designs

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
		$mpdf = $this->getMockBuilder( mPDF::class )->getMock();
		$mpdf->expects( $this->exactly( 3 ) )
		     ->method( 'WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->addMulti( '', [ 10, 10, 10, 10 ] );
		$this->class->addMulti( '', [ 10, 10, 10, 10 ] );
		$this->class->addMulti( '', [ 10, 10, 10, 10 ] );
	}

	/**
	 * @since 1.0
	 */
	public function testAddMultiCenter() {
		$mpdf = $this->getMockBuilder( mPDF::class )->getMock();
		$mpdf->expects( $this->exactly( 3 ) )
		     ->method( 'WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->addMultiCenter( '', [ 10, 10, 10, 10 ] );
		$this->class->addMultiCenter( '', [ 10, 10, 10, 10 ] );
		$this->class->addMultiCenter( '', [ 10, 10, 10, 10 ] );
	}

	/**
	 * @since 1.0
	 */
	public function testAddMultiRight() {
		$mpdf = $this->getMockBuilder( mPDF::class )->getMock();
		$mpdf->expects( $this->exactly( 3 ) )
		     ->method( 'WriteFixedPosHTML' );

		$this->class->setMpdf( $mpdf );

		$this->class->addMultiRight( '', [ 10, 10, 10, 10 ] );
		$this->class->addMultiRight( '', [ 10, 10, 10, 10 ] );
		$this->class->addMultiRight( '', [ 10, 10, 10, 10 ] );
	}
}
