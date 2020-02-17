<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

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
 * Interface InterfaceWriter
 *
 * For use as our WP_CLI Interface
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 *
 * @since   1.0
 */
interface InterfaceCli {

	/**
	 * Logs a message
	 *
	 * @param string $text
	 *
	 * @since 1.0
	 */
	public function log( $text );

	/**
	 * Logs a warning message
	 *
	 * @param string $text
	 *
	 * @since 1.0
	 */
	public function warning( $text );

	/**
	 * Logs a success message
	 *
	 * @param string $text
	 *
	 * @since 1.0
	 */
	public function success( $text );

	/**
	 * Logs an error
	 *
	 * @param string $text
	 * @param bool   $exit
	 *
	 * @since 1.0
	 */
	public function error( $text, $exit = true );

	/**
	 * Get a user response
	 *
	 * @value string $text
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public function getResponse( $text );

	/**
	 * Output the data in a specific format
	 *
	 * @param string $format Either 'table', 'json', 'csv' or 'yaml'
	 * @param array  $data   In the format [ [ 'Key' => 'Value' ], [ 'Key' => 'Value' ] ]
	 * @param array  $keys   The Keys used in $data
	 *
	 * @since 1.0
	 */
	public function outputInFormat( $format, $data, $keys );
}
