<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use WP_CLI;

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
 * Class Cli
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 *
 * @since   1.0
 */
class Cli implements InterfaceCli {

	/**
	 * Logs a message
	 *
	 * @param string $text
	 *
	 * @since 1.0
	 */
	public function log( $text ) {
		WP_CLI::log( $text );
	}

	/**
	 * Logs a warning message
	 *
	 * @param string $text
	 *
	 * @since 1.0
	 */
	public function warning( $text ) {
		WP_CLI::warning( $text );
	}

	/**
	 * Logs a success message
	 *
	 * @param string $text
	 *
	 * @since 1.0
	 */
	public function success( $text ) {
		WP_CLI::success( $text );
	}

	/**
	 * Logs an error
	 *
	 * @param string $text
	 * @param bool   $exit
	 *
	 * @since 1.0
	 * @throws WP_CLI\ExitException
	 */
	public function error( $text, $exit = true ) {
		WP_CLI::error( $text, $exit );
	}

	/**
	 * Ask the CLI user a question and return their response
	 *
	 * @param string $question
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public function getResponse( $question ) {
		fwrite( STDOUT, $question );
		return trim( fgets( STDIN ) );
	}

	/**
	 * Output the data in a specific format
	 *
	 * @param string $format Either 'table', 'json', 'csv' or 'yaml'
	 * @param array  $data   In the format [ [ 'Key' => 'Value' ], [ 'Key' => 'Value' ] ]
	 * @param array  $keys   The Keys used in $data
	 *
	 * @since 1.0
	 */
	public function outputInFormat( $format, $data, $keys ) {
		\WP_CLI\Utils\format_items( $format, $data, $keys );
	}
}
