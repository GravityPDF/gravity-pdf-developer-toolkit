<?php

namespace GFPDF\Plugins\DeveloperToolkit;

use GFPDF\Helper\Helper_Singleton;
use GFPDF\Helper\Helper_Logger;
use GFPDF\Helper\Helper_Notices;

use GFPDF\Plugins\DeveloperToolkit\Cli\Register;
use GFPDF\Plugins\DeveloperToolkit\Legacy\AdvancedTemplate;
use GFPDF\Plugins\DeveloperToolkit\Legacy\Deactivate;
use GFPDF\Plugins\DeveloperToolkit\Loader\Header;
use GFPDF\Plugins\DeveloperToolkit\Loader\Loader;
use GFPDF\Plugins\DeveloperToolkit\Legacy\LegacyLoader;

use GPDFAPI;

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
 * Class TestBootstrap
 *
 * @package GFPDF\Plugins\DeveloperToolkit
 *
 * @group   bootstrap
 */
class TestBootstrap extends WP_UnitTestCase {

	/**
	 * @var Bootstrap
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {

		$this->class = new Bootstrap(
			'slug',
			'name',
			'author',
			'1.0',
			__DIR__,
			GPDFAPI::get_data_class(),
			GPDFAPI::get_options_class(),
			new Helper_Singleton(),
			new Helper_Logger( 'slug', 'name' ),
			new Helper_Notices()
		);

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function test_init() {
		$this->class->init();

		$this->assertInstanceOf(
			Loader::class,
			$this->class->singleton->get_class( 'Loader' )
		);

		$this->assertInstanceOf(
			LegacyLoader::class,
			$this->class->singleton->get_class( 'LegacyLoader' )
		);

		$this->assertInstanceOf(
			Header::class,
			$this->class->singleton->get_class( 'Header' )
		);

		$this->assertInstanceOf(
			Deactivate::class,
			$this->class->singleton->get_class( 'Deactivate' )
		);

		$this->assertInstanceOf(
			AdvancedTemplate::class,
			$this->class->singleton->get_class( 'AdvancedTemplate' )
		);

		$this->assertInstanceOf(
			Register::class,
			$this->class->singleton->get_class( 'Register' )
		);
	}
}