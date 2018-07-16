<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use RuntimeException;

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
	protected $workingDirectory;

	/**
	 * @var \WP_CLI
	 */
	protected $cli;

	/**
	 * @param string $workingDirectory The absolute path to the PDF Working Directory
	 * @param object $cli              The WP_CLI class, or a suitable drop-in replacement (for testing)
	 *
	 * @since 1.0
	 */
	public function __construct( $workingDirectory, $cli ) {
		$this->workingDirectory = $workingDirectory;
		$this->cli              = $cli;
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
	 * @param array $templateArray The PDF Template Name the use has entered. If they used quotes it'll be an array with one element, otherwise each space will signify a new array element.
	 * @param array $args          The additional arguments passed to the cli. May include `enable-config`, `enable-toolkit` and `skip-headers`
	 *
	 * @throws \Exception
	 */
	public function __invoke( $templateArray, $args = [] ) {
		$templateName   = implode( ' ', array_filter( $templateArray ) );
		$shortname      = $this->getTemplateFilename( $templateName );
		$filename       = $shortname . '.php';
		$fullPathToFile = $this->workingDirectory . $filename;

		/* Check if template already exists */
		$this->checkFileExistsAndDelete( $fullPathToFile );

		if ( ! is_file( $fullPathToFile ) ) {
			$this->generateBaseTemplate( $templateName, $fullPathToFile, $args );
		} else {
			$this->cli->warning( sprintf( __( 'Skipping creation of PDF template file at %s', 'gravity-pdf-developer-toolkit' ), $fullPathToFile ) );
		}

		if ( ! empty( $args['enable-config'] ) ) {
			$this->generateConfigTemplate( $this->getClassName( $shortname ), $filename );
		}

		$this->cli->log( 'Happy PDFing!' );
	}

	/**
	 * Generate and save a PDF template based on the user's responses
	 *
	 * @param string $templateName   The PDF Template Name provided by the user
	 * @param string $fullPathToFile The full path to the PDF template we want to create
	 * @param array  $args           The additional CLI arguments being passed. May include `enable-config`, `enable-toolkit` and `skip-headers`
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	protected function generateBaseTemplate( $templateName, $fullPathToFile, $args ) {
		/* Get the template variables */
		$data         = $this->getTemplateData( empty( $args['skip-headers'] ) );
		$data['name'] = $templateName;

		/* Create our core template */
		$baseTemplate = ( ! empty( $args['enable-toolkit'] ) ) ? 'toolkit-base' : 'base';

		$this->saveToFileAndMessage(
			$fullPathToFile,
			$this->loadTemplate( $data, $baseTemplate ),
			sprintf(
				__( 'Your template has been generated and saved to "%s".', 'gravity-pdf-developer-toolkit' ),
				$fullPathToFile
			),
			sprintf(
				__( 'Could not save template file to %s', 'gravity-pdf-developer-toolkit' ),
				$fullPathToFile
			)
		);
	}

	/**
	 * Generate and save a PDF template configuration file
	 *
	 * @param string $className The generated class name the config file names
	 * @param string $fileName  The filename (not full path) of the config file
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 1.0
	 */
	protected function generateConfigTemplate( $className, $fileName ) {
		$pathToConfig = $this->workingDirectory . 'config/';

		/* Create config directory is not present */
		if ( ! is_dir( $pathToConfig ) ) {
			mkdir( $pathToConfig );
		}

		/* Check if config file exists and ask to override if it does */
		$fullPathToFile = $pathToConfig . $fileName;
		if ( is_file( $fullPathToFile ) ) {
			$this->checkFileExistsAndDelete( $fullPathToFile );
		}

		if ( ! is_file( $fullPathToFile ) ) {
			$this->saveToFileAndMessage(
				$fullPathToFile,
				$this->loadTemplate( [ 'name' => $className ], 'config-base' ),
				sprintf(
					__( 'Your template configuration file has been generated and saved to %s.', 'gravity-pdf-developer-toolkit' ),
					$fullPathToFile
				),
				sprintf(
					__( 'Could not save template configuration file to %s', 'gravity-pdf-developer-toolkit' ),
					$fullPathToFile
				)
			);
		} else {
			$this->cli->warning( sprintf( __( 'Skipping creation of PDF template file at %s', 'gravity-pdf-developer-toolkit' ), $fullPathToFile ) );
		}
	}

	protected function saveToFileAndMessage( $path, $content, $successMsg, $failureMsg ) {
		$result = file_put_contents( $path, $content );

		if ( $result === false ) {
			$this->cli->warning( $failureMsg );
		} else {
			$this->cli->success( $successMsg );
		}
	}

	/**
	 * Strip non-ASCII and special characters from filename
	 *
	 * @param string $filename
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	protected function getTemplateFilename( $filename ) {
		$characters = [ '/', '\\', '"', '*', '?', '|', ':', '<', '>', "'" ];

		$filename = str_replace( $characters, '', $filename );
		$filename = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $filename );
		$filename = str_replace( ' ', '-', $filename );

		return mb_strtolower( $filename );
	}

	/**
	 * Converts the template shortname to the class name
	 *
	 * @param string $shortName The PDF template filename (minus the extension)
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	protected function getClassName( $shortName ) {
		$className = str_replace( '-', ' ', $shortName );
		$className = mb_convert_case( $className, MB_CASE_TITLE, 'UTF-8' );
		$className = str_replace( ' ', '_', $className );

		return $className;
	}

	/**
	 * Get additional information about the PDF template
	 *
	 * @param bool $askForHeaders When true, we'll ask the user additional questions
	 *
	 * @return array The user responses to each question
	 *
	 * @since 1.0
	 */
	protected function getTemplateData( $askForHeaders ) {
		$data = [
			'desc'             => '',
			'author'           => '',
			'author_uri'       => '',
			'group'            => 'Custom',
			'license'          => '',
			'required_version' => '4.4.0',
			'tags'             => '',
		];

		if ( $askForHeaders ) {
			$this->cli->log( __( "We are going to ask a few questions to help setup the PDF. Leave blank to skip a question.\n", 'gravity-pdf-developer-toolkit' ) );

			$questions = [
				'desc'             => __( 'Describe what the PDF template will be used for: ', 'gravity-pdf-developer-toolkit' ),
				'author'           => __( 'Author name: ', 'gravity-pdf-developer-toolkit' ),
				'author_uri'       => __( 'Author website: ', 'gravity-pdf-developer-toolkit' ),
				'group'            => __( 'Group name: ', 'gravity-pdf-developer-toolkit' ),
				'license'          => __( 'License (if any): ', 'gravity-pdf-developer-toolkit' ),
				'required_version' => __( 'Minimum Gravity PDF version (defaults to 4.4.0): ', 'gravity-pdf-developer-toolkit' ),
				'tags'             => __( 'Tags (separate by comma): ', 'gravity-pdf-developer-toolkit' ),
			];

			foreach ( $questions as $key => $q ) {
				$response = $this->cli->getResponse( $q );

				if ( strlen( $response ) > 0 ) {
					$data[ $key ] = $response;
				}
			}
		}

		return $data;
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
	protected function loadTemplate( $data, $name = 'base' ) {
		ob_start();
		include __DIR__ . '/templates/' . $name . '.php';
		return str_replace( [ '&#x3C;', '&#x3E;' ], [ '<', '>' ], ob_get_clean() );
	}

	/**
	 * Asks if a file should be deleted and removes if needed
	 *
	 * @param string $fullPathToFile
	 *
	 * @throws \Exception
	 *
	 * @since 1.0
	 */
	protected function checkFileExistsAndDelete( $fullPathToFile ) {
		if ( is_file( $fullPathToFile ) ) {
			$response = $this->cli->getResponse(
				sprintf(
					__( 'A file with the name "%s" already exists. Would you like to override (y/N)?', 'gravity-pdf-developer-toolkit' ),
					str_replace( $this->workingDirectory, '', $fullPathToFile )
				)
			);

			if ( stripos( $response, 'y' ) !== false ) {
				if ( ! unlink( $fullPathToFile ) ) {
					$this->cli->error(
						sprintf(
							__( 'Could not delete the file located at %s', 'gravity-pdf-developer-toolkit' ),
							$fullPathToFile
						)
					);
				}
			}
		}
	}
}
