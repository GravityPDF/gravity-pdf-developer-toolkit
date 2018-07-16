<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Helper\Helper_Templates;
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
 * Class TestHeader
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @group   loader
 */
class TestHeader extends WP_UnitTestCase {

	/**
	 * @var Header
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$template = $this->getMockBuilder( Helper_Templates::class )
						->setConstructorArgs(
							[
								\GPDFAPI::get_log_class(),
								\GPDFAPI::get_data_class(),
								\GPDFAPI::get_form_class(),
							]
						)
						 ->setMethods( [ 'get_template_path_by_id' ] )
						 ->getMock();

		$template->method( 'get_template_path_by_id' )
				->will(
					$this->onConsecutiveCalls(
						__DIR__ . '/../pdfs/sample1.php',
						__DIR__ . '/../pdfs/sample2.php'
					)
				);

		$this->class = new Header( $template );
		$this->class->init();

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testToolkitHeader() {
		$results = apply_filters( 'gfpdf_template_header_details', [] );

		$this->assertArrayHasKey( 'toolkit', $results );
	}

	/**
	 * @since 1.0
	 */
	public function testAddToolkitSettings() {
		$results = $this->class->addToolkitSetting( [ 'toolkit' => true ] );
		$this->assertArrayNotHasKey( 'toolkit', $results );

		$results = $this->class->addToolkitSetting( [] );
		$this->assertArrayHasKey( 'toolkit', $results );
	}
}
