<?php

namespace GFPDF\Plugins\DeveloperToolkit\Zip;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

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
 * @package GFPDF\Plugins\DeveloperToolkit\Zip
 */
class Zip {

	/**
	 * Create a zip file and save to disk
	 *
	 * @param string $dirPath Path to the directory to zip
	 * @param string $zipPath Path to save zip too
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function create( $dirPath, $zipPath ) {

		$dirPath = dirname( $dirPath ) . '/' . basename( $dirPath );

		$local = new FileSystem( new Local( $dirPath ) );
		$zip   = new Filesystem( new ZipArchiveAdapter( $zipPath ) );

		/* Get a recursive list of all contents in the local directory */
		$files = $local->listContents( '', true );
		foreach ( $files as $info ) {
			if ( $info['type'] === 'dir' ) {
				continue;
			}

			$zip->write( $info['path'], $local->read( $info['path'] ) );
		}

		/* Clean-up and generate zip */
		unset( $local );
		return $zip->getAdapter()->getArchive()->close();
	}
}
