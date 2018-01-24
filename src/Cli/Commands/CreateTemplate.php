<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use QueryPath\Exception;
use WP_CLI;

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
 * Processes the `wp gpdf create-template` WP CLI Command
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 */
class CreateTemplate {

	/**
	 * @var string The absolute path to the PDF Working Directory
	 *
	 * @since 1.0
	 */
	protected $working_directory;

	/**
	 * @param string $working_directory The absolute path to the PDF Working Directory
	 *
	 * @since 1.0
	 */
	public function __construct( $working_directory ) {
		$this->working_directory = $working_directory;
	}

	/**
	 * Generates a PDF template in the PDF Working Directory
	 *
	 * ## OPTIONS
	 *
	 * <template-name>
	 * : The name of the PDF template you'd like to create.
	 *
	 * [--enable-config]
	 * : When included, a configuration file will be created alongside your template
	 *
	 * [--enable-toolkit]
	 * : When included, the Dev Toolkit classes will be injected into your PDF template and the sandbox disabled.
	 *
	 * [--skip-headers]
	 * : When included, we will not ask you any additional questions about the PDF template.
	 *
	 * ## EXAMPLES
	 *
	 *     # Create a PDF template called 'my-custom-template.php'
	 *     $ wp gpdf create-template "My Custom Template"
	 *
	 *     # Create a PDF template called 'my-custom-template.php', as well as an associated config file '/config/my-custom-template.php'
	 *     $ wp gpdf create-template "My Custom Template" --enable-config
	 *
	 *     # Create a PDF template called 'my-custom-template.php' using the Toolkit
	 *     $ wp gpdf create-template "My Custom Template" --enable-toolkit
	 *
	 *     # Create a PDF template called 'my-custom-template.php' using the Toolkit, as well as an associated config file '/config/my-custom-template.php'
	 *     $ wp gpdf create-template "My Custom Template" --enable-config --enable-toolkit
	 *
	 *     # Create a PDF template called 'my-custom-template.php' without asking any additional questions about the template
	 *     $ wp gpdf create-template "My Custom Template" --skip-headers
	 *
	 * @since 1.0
	 *
	 * @param array $template_array The PDF Template Name the use has entered. If they used quotes it'll be an array with one element, otherwise each space will signify a new array element.
	 * @param array $args           The additional arguments passed to the cli. May include `enable-config`, `enable-toolkit` and `skip-headers`
	 *
	 * @throws WP_CLI\ExitException
	 */
	public function __invoke( $template_array, $args = [] ) {
		$template_name     = implode( ' ', array_filter( $template_array ) );
		$shortname         = mb_strtolower( str_replace( ' ', '-', $template_name ) );
		$filename          = $shortname . '.php';
		$full_path_to_file = $this->working_directory . $filename;

		/* Check if template already exists */
		if ( is_file( $full_path_to_file ) ) {
			WP_CLI::error( sprintf( 'A PDF template with the name "%s" already exists. Try a different <template-name>.', $filename ) );
		}

		$this->generate_base_template( $template_name, $full_path_to_file, $args );

		if ( ! empty( $args['enable-config'] ) ) {
			$this->generate_config_template( $this->get_class_name( $shortname ), $filename );
		}

		WP_CLI::log( 'Happy PDFing!' );
	}

	/**
	 * Generate and save a PDF template based on the user's responses
	 *
	 * @param string $template_name     The PDF Template Name provided by the user
	 * @param string $full_path_to_file The full path to the PDF template we want to create
	 * @param array  $args              The additional CLI arguments being passed. May include `enable-config`, `enable-toolkit` and `skip-headers`
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	protected function generate_base_template( $template_name, $full_path_to_file, $args ) {
		/* Get the template variables */
		$data         = $this->get_template_data( empty( $args['skip-headers'] ) );
		$data['name'] = $template_name;

		/* Create our core template */
		$base_template = ( ! empty( $args['enable-toolkit'] ) ) ? 'toolkit-base' : 'base';

		file_put_contents(
			$full_path_to_file,
			$this->load_template( $data, $base_template )
		);

		WP_CLI::success( sprintf(
				'Your template has been generated and saved to "%s".',
				$full_path_to_file
			)
		);
	}

	/**
	 * Generate and save a PDF template configuration file
	 *
	 * @param string $class_name The generated class name the config file names
	 * @param string $filename   The filename (not full path) of the config file
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	protected function generate_config_template( $class_name, $filename ) {
		$full_path_to_file = $this->working_directory . 'config/' . $filename;

		$data = [
			'name' => $class_name,
		];

		file_put_contents(
			$full_path_to_file,
			$this->load_template( $data, 'config-base' )
		);

		WP_CLI::success( sprintf(
				'Your template configuration file has been generated and saved to "%s".',
				$full_path_to_file
			)
		);

	}

	/**
	 * Converts the template shortname to the class name
	 *
	 * @param string $shortname The PDF template filename (minus the extension)
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	protected function get_class_name( $shortname ) {
		$class_name = str_replace( '-', ' ', $shortname );
		$class_name = mb_convert_case( $class_name, MB_CASE_TITLE, 'UTF-8' );
		$class_name = str_replace( ' ', '_', $class_name );

		return $class_name;
	}

	/**
	 * Get additional information about the PDF template
	 *
	 * @param bool $ask_for_headers When true, we'll ask the user additional questions
	 *
	 * @return array The user responses to each question
	 *
	 * @since 1.0
	 */
	protected function get_template_data( $ask_for_headers ) {
		$data = [
			'desc'             => '',
			'author'           => '',
			'author_uri'       => '',
			'group'            => 'Custom',
			'license'          => '',
			'required_version' => '4.0.0',
			'tags'             => '',
		];

		if ( $ask_for_headers ) {
			WP_CLI::log( "We are going to ask a few questions to help setup the PDF. Leave blank to skip a question.\n" );

			$questions = [
				'desc'             => 'Describe what the PDF template will be used for: ',
				'author'           => 'Author name: ',
				'author_uri'       => 'Author website: ',
				'group'            => 'Group name: ',
				'license'          => 'License (if any): ',
				'required_version' => 'Minimum Gravity PDF version (defaults to 4.0): ',
				'tags'             => 'Tags (separate by comma): ',
			];

			foreach ( $questions as $key => $q ) {
				$response = $this->get_response( $q );

				if ( strlen( $response ) > 0 ) {
					$data[ $key ] = $response;
				}
			}
		}

		return $data;
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
	protected function get_response( $question ) {
		fwrite( STDOUT, $question );
		return trim( fgets( STDIN ) );
	}

	/**
	 * Our basic template loader
	 *
	 * The loader will include the chosen template file and decode any PHP opening and closing braces
	 *
	 * @param array  $data Data to be used in the PDF template
	 * @param string $name The filename (minus the extension) for the template to load
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	protected function load_template( $data, $name = 'base' ) {
		ob_start();
		include __DIR__ . '/templates/' . $name . '.php';
		return str_replace( [ '&#x3C;', '&#x3E;' ], [ '<', '>' ], ob_get_clean() );
	}
}