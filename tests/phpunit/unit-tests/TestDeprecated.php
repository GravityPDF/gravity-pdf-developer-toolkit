<?php

use GFPDF\Plugins\DeveloperToolkit\Factory\FactoryWriter;

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
 * Class TestDeprecated
 *
 * @group   deprecated
 */
class TestDeprecated extends WP_UnitTestCase {

	/**
	 * @since 1.0
	 */
	public function setUp() {
		global $writer;
		$writer = FactoryWriter::build();
		$writer->setMpdf( new mPDF() );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function test_initialise() {
		$results = gfpdfe_business_plus::initilise( __DIR__ . '/pdfs/document1.pdf' );

		$this->assertArrayHasKey( 'load', $results );
		$this->assertArrayHasKey( 'size', $results );

		$this->assertEquals( '1', $results['load'][1] );
		$this->assertEquals( 215.9, $results['size'][1]['w'] );
		$this->assertEquals( 279.4, $results['size'][1]['h'] );
	}

	/**
	 * @since 1.0
	 */
	public function test_add_template() {
		global $writer;
		$mpdf = $writer->getMpdf();

		/* Add page 1 */
		gfpdfe_business_plus::initilise( __DIR__ . '/pdfs/document1.pdf' );
		$writer->addPage( 1 );

		/* Load second PDF from disk */
		$results = gfpdfe_business_plus::add_template( __DIR__ . '/pdfs/document2.pdf' );

		$this->assertEquals( '2', $results['load'][1] );
		$this->assertEquals( 420, round( $results['size'][1]['w'] ) );
		$this->assertEquals( 297, round( $results['size'][1]['h'] ) );

		$writer->addPage( 1 );

		$this->assertCount( 2, $mpdf->pages );
	}
}