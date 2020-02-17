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
 * Class TestStyles
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @group   writer
 */
class TestStyles extends WP_UnitTestCase {

	/**
	 * @var Styles
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Styles();
		$this->class->setMpdf( new Mpdf( [ 'mode' => 'c' ] ) );

		parent::setUp();
	}

	public function testStyles() {
		$mpdf = $this->getMockBuilder( mPDF::class )->getMock();
		$mpdf->expects( $this->once() )
		     ->method( 'WriteHTML' );

		$this->class->setMpdf( $mpdf );

		/* Ensure buffer opens */
		$this->class->beginStyles();
		echo 'testing';

		$this->assertSame( 'testing', ob_get_clean() );

		/* Ensure buffer writes */
		$this->class->beginStyles();
		echo '<style>div {}</style>';
		$this->class->endStyles();
	}
}
