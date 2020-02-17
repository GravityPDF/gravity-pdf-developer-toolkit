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
 * Class TestImport
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @group   writer
 */
class TestImport extends WP_UnitTestCase {

	/**
	 * @var Import
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Import();
		$this->class->setMpdf( new Mpdf( [ 'mode' => 'c' ] ) );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testAddPdfException() {
		try {
			$this->class->addPdf( 'item.pdf' );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( 'Could not find item.pdf', $e->getMessage() );
	}

	/**
	 * @since 1.0
	 */
	public function testAddPdf() {
		$this->class->addPdf( __DIR__ . '/../../pdfs/document1.pdf' );

		$sizes = $this->class->getPdfPageSize();
		$ids   = $this->class->getPdfPageIds();

		$this->assertEquals( 215.9, $sizes[1]['w'] );
		$this->assertEquals( 279.4, $sizes[1]['h'] );
		$this->assertEquals( 1, $ids[1] );

		$this->class->addPdf( __DIR__ . '/../../pdfs/document2.pdf' );

		$sizes = $this->class->getPdfPageSize();
		$ids   = $this->class->getPdfPageIds();

		$this->assertEquals( 420, round( $sizes[1]['w'] ) );
		$this->assertEquals( 297, round( $sizes[1]['h'] ) );
		$this->assertEquals( 2, $ids[1] );
	}

	/**
	 * @since 1.0
	 */
	public function testAddBlankPdf() {
		$mpdf = $this->class->getMpdf();

		$this->class->addBlankPage();
		$this->class->addBlankPage();
		$this->class->addBlankPage();

		$this->assertCount( 3, $mpdf->pages );
	}

	/**
	 * @since 1.0
	 */
	public function testAddPageException() {
		try {
			$this->class->addPage( 1 );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( 'The loaded PDF "" does not have page #1', $e->getMessage() );

		try {
			$this->class->addPage( [ 1 ] );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( 'When $id is an array it should only contain two array items that signify the start and end of the PDF pages to load', $e->getMessage() );

		try {
			$this->class->addPage( [ 2, 3 ] );
		} catch ( \BadMethodCallException $e ) {

		}

		$this->assertEquals( 'The loaded PDF "" does not have page #2', $e->getMessage() );
	}

	/**
	 * @since 1.0
	 */
	public function testAddPage() {
		$mpdf = $this->class->getMpdf();
		$this->class->addPdf( __DIR__ . '/../../pdfs/document1.pdf' );
		$this->class->addPage( 1 );
		$this->class->addPage( 1 );
		$this->class->addPage( 1 );

		$this->assertCount( 3, $mpdf->pages );
	}
}
