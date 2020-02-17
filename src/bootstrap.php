<?php

namespace GFPDF\Plugins\DeveloperToolkit;

use GFPDF\Plugins\DeveloperToolkit\Cli\Register;
use GFPDF\Plugins\DeveloperToolkit\Legacy\AdvancedTemplate;
use GFPDF\Plugins\DeveloperToolkit\Legacy\Deactivate;
use GFPDF\Plugins\DeveloperToolkit\Loader\Header;
use GFPDF\Plugins\DeveloperToolkit\Loader\Javascript;
use GFPDF\Plugins\DeveloperToolkit\Loader\Loader;
use GFPDF\Plugins\DeveloperToolkit\Legacy\LegacyLoader;

use GFPDF\Helper\Licensing\EDD_SL_Plugin_Updater;
use GFPDF\Helper\Helper_Abstract_Addon;
use GFPDF\Helper\Helper_Singleton;
use GFPDF\Helper\Helper_Logger;
use GFPDF\Helper\Helper_Notices;

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

/* Load Composer */
require_once( __DIR__ . '/../vendor/autoload.php' );

/* Load our legacy class */
require_once( __DIR__ . '/deprecated.php' );

/**
 * Class to bootstrap the Gravity PDF Developer Toolkit plugin
 *
 * @package  GFPDF\Plugins\DeveloperToolkit
 *
 * @since    1.0
 */
class Bootstrap extends Helper_Abstract_Addon {

	/**
	 * Initialise the plugin classes and pass them to our parent class which is included in the Gravity PDF plugin to
	 * handle the rest of the bootstrapping (licensing ect)
	 *
	 * @param array $classes An array of additional classes to store in our object
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function init( $classes = [] ) {

		/* Register our classes and pass back up to the parent initialiser */
		$classes = array_merge(
			$classes,
			[
				new Loader(),
				new LegacyLoader(),
				new Header( GPDFAPI::get_templates_class() ),
				new Javascript( GPDFAPI::get_misc_class(), GPDFAPI::get_templates_class() ),
				new Deactivate( GPDFAPI::get_options_class() ),
				new AdvancedTemplate( GPDFAPI::get_options_class() ),
				new Register(),
			]
		);

		/* Run the setup */
		parent::init( $classes );
	}

	/**
	 * Check the plugin's license is active and initialise the EDD Updater
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function plugin_updater() {
		$licenseInfo = $this->get_license_info();

		new EDD_SL_Plugin_Updater(
			$this->data->store_url,
			$this->get_main_plugin_file(),
			[
				'version'   => $this->get_version(),
				'license'   => $licenseInfo['license'],
				'item_name' => $this->get_short_name(),
				'author'    => $this->get_author(),
				'beta'      => false,
			]
		);
	}
}

/* Use the filter below to replace and extend our Bootstrap class if needed */
$name = 'Gravity PDF Developer Toolkit';
$slug = 'gravity-pdf-developer-toolkit';

$plugin = apply_filters(
	'gfpdf_developer_toolkit_initialise',
	new Bootstrap(
		$slug,
		$name,
		'Gravity PDF',
		GFPDF_DEVELOPER_TOOLKIT_VERSION,
		GFPDF_DEVELOPER_TOOLKIT_FILE,
		GPDFAPI::get_data_class(),
		GPDFAPI::get_options_class(),
		new Helper_Singleton(),
		new Helper_Logger( $slug, $name ),
		new Helper_Notices()
	)
);

$plugin->set_edd_download_id( 20716 );
$plugin->set_addon_documentation_slug( 'shop-plugin-developer-toolkit' );
$plugin->init();

/* Use the action below to access our Bootstrap class, and any singletons saved in $plugin->singleton */
do_action( 'gfpdf_developer_toolkit_bootrapped', $plugin );
