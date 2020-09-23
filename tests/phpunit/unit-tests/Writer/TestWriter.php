<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer;

use GFPDF\Plugins\DeveloperToolkit\Factory\FactoryWriter;

use GFPDF\Helper\Helper_Mpdf as Mpdf;
use WP_UnitTestCase;

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
		$this->class->setMpdf( new mPDF( [ 'mode' => 'c' ] ) );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function test_call() {
		$e = null;

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
		$this->class->registerClass( new Test() );
		$this->assertSame( 'yes', $this->class->example() );
	}

	/**
	 * @since 1.0
	 */
	public function test_abstract_classes() {
		$class = FactoryWriter::build();
		$this->assertFalse( $class->isMpdfSet() );

		$class->setMpdf( new mPDF( [ 'mode' => 'c' ] ) );
		$this->assertTrue( $class->isMpdfSet() );
		$this->assertInstanceOf( Mpdf::class, $class->getMpdf() );
	}
}

class Test extends AbstractWriter {
	public function example() {
		return 'yes';
	}
}
