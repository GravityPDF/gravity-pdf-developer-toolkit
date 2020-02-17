<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use WP_UnitTestCase;
use GPDFAPI;

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
 * Class TestLegacyLoader
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Legacy
 *
 * @group   legacy
 */
class TestLegacyLoader extends WP_UnitTestCase {

	/**
	 * @var LegacyLoader
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new LegacyLoader();

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testMaybeSkipPdfHtmlRender() {
		$this->assertFalse( $this->class->maybeSkipPdfHtmlRender( false, [] ) );
		$this->assertTrue( $this->class->maybeSkipPdfHtmlRender( false, [ 'settings' => [ 'advanced_template' => 'Yes' ] ] ) );
	}

	/**
	 * @since 1.0
	 */
	public function testMaybeAddLegacyTemplateArgs() {
		global $pdf, $writer;

		$this->assertNull( $pdf );
		$this->assertNull( $writer );

		$results = $this->class->maybeAddLegacyTemplateArgs( [], [] );
		$this->assertSame( [], $results );

		/*
		 * Ensure $args is correctly updated
		 */
		$results = $this->class->maybeAddLegacyTemplateArgs(
			[
				'mpdf'     => 'mpdf',
				'w'        => 'w',
				'settings' => [ 'advanced_template' => 'Yes' ],
			], [
				'form_id'  => '',
				'lead_ids' => '',
				'lead_id'  => '',
			]
		);

		$this->assertArrayHasKey( 'form_id', $results );
		$this->assertArrayHasKey( 'lead_ids', $results );
		$this->assertArrayHasKey( 'lead_id', $results );

		$this->assertNotNull( $pdf );
		$this->assertNotNull( $writer );

	}
}
