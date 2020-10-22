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
        $mpdf = \Spies\mock_object( new Mpdf( [ 'mode' => 'c' ] ) );
        $spy = $mpdf->spy_on_method('WriteHTML' );

		$this->class->setMpdf( $mpdf );

		/* Ensure buffer opens */
		$this->class->beginStyles();
		echo 'testing';

		$this->assertSame( 'testing', ob_get_clean() );

		/* Ensure buffer writes */
		$this->class->beginStyles();
		echo '<style>div {}</style>';
		$this->class->endStyles();

        $expectation = \Spies\expect_spy( $spy )->to_be_called->once();
        $this->assertTrue( $expectation->met_expectations() );
	}
}
