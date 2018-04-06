<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use WP_UnitTestCase;
use GPDFAPI;

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
			], [ 'form_id' => '', 'lead_ids' => '', 'lead_id' => '' ] );

		$this->assertArrayHasKey( 'form_id', $results );
		$this->assertArrayHasKey( 'lead_ids', $results );
		$this->assertArrayHasKey( 'lead_id', $results );

		$this->assertNotNull( $pdf );
		$this->assertNotNull( $writer );

	}
}