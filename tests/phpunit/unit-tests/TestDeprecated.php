<?php

use GFPDF\Plugins\DeveloperToolkit\Factory\FactoryWriter;

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
