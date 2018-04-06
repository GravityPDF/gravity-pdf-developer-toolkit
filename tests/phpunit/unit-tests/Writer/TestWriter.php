<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer;

use GFPDF\Plugins\DeveloperToolkit\Factory\FactoryWriter;

use mPDF;
use WP_UnitTestCase;

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
 * Class TestWriter
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer
 *
 * @group   writer
 */
class TestWriter extends WP_UnitTestCase {

	/**
	 * @var Writer
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = FactoryWriter::build();
		$this->class->setMpdf( new mPDF() );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function test_call() {
		try {
			$this->class->ellipse( [ 1, 1, 1, 1 ] );
			$this->class->addHtml( 'Markup' );
			$this->class->addPdf( __DIR__ . '/../pdfs/document1.pdf' );
			$this->class->addMulti( 'Item', [ 1, 1, 10, 5 ] );
			$this->class->add( 'Item', [ 1, 1, 10, 5 ] );
			$this->class->beginStyles();
			$this->class->endStyles();
			$this->class->tick( [ 1, 1 ] );

		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertNull( $e );
	}

	/**
	 * @since 1.0
	 */
	public function test_empty_class() {
		try {
			$this->class->doesNotExist();
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( 'The method "doesNotExist" could not be found.', $e->getMessage() );
	}

	/**
	 * @since 1.0
	 */
	public function test_registered_classes() {
		$this->class->registerClass( new test() );
		$this->assertSame( 'yes', $this->class->example() );
	}

	/**
	 * @since 1.0
	 */
	public function test_abstract_classes() {
		$class = FactoryWriter::build();
		$this->assertFalse( $class->isMpdfSet() );

		$class->setMpdf( new mPDF() );
		$this->assertTrue( $class->isMpdfSet() );
		$this->assertInstanceOf( mPDF::class, $class->getMpdf() );
	}
}

class test extends AbstractWriter {
	public function example() {
		return 'yes';
	}
}