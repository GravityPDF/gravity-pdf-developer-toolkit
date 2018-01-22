<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer;

use BadMethodCallException;

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
 * This class acts as a router for all method calls. We register classes that impliment InterfaceWriter
 * and then search through each class for a matching method name. If found, we pass the arguments directly to the method.
 * This allows us to provide a simple API to our users without creating a God class.
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer
 *
 * @since   1.0
 */
class Writer extends AbstractWriter {

	/**
	 * @var InterfaceWriter[]
	 * @since 1.0
	 */
	protected $classes = [];

	/**
	 * Register all our classes
	 *
	 * @param InterfaceWriter[] $classes
	 *
	 * @since 1.0
	 */
	public function __construct( $classes = [] ) {
		foreach ( $classes as $class ) {
			$this->register_class( $class );
		}
	}

	/**
	 * Register the class with Writer
	 *
	 * @param InterfaceWriter $class
	 *
	 * @since 1.0
	 */
	public function register_class( InterfaceWriter $class ) {
		$this->classes[] = $class;
	}

	/**
	 * Search through all registered classes for a public method name matching the one called in Writer
	 *
	 * @param string $name      The method being called
	 * @param array  $arguments The method arguments
	 *
	 * @return mixed
	 *
	 * @throws BadMethodCallException
	 */
	public function __call( $name, $arguments ) {
		foreach ( $this->classes as $class ) {
			if ( is_callable( [ $class, $name ] ) ) {
				$this->maybe_inject_mpdf( $class );

				return call_user_func_array( [ $class, $name ], $arguments );
			}
		}

		throw new BadMethodCallException( sprintf( 'The method "%s" could not be found.', $name ) );
	}

	/**
	 * Inject Mpdf to our registered classes right before calling the chosen method
	 *
	 * @param InterfaceWriter $class
	 *
	 * @since 1.0
	 */
	public function maybe_inject_mpdf( $class ) {
		if ( ! $class->is_mpdf_set() ) {
			$class->set_mpdf( $this->mpdf );
		}
	}
}