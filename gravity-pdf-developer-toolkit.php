<?php

/**
 * Plugin Name:     Gravity PDF Developer Toolkit
 * Plugin URI:      https://gravitypdf.com/shop/pdf-developer-toolkit/
 * Description:     Gravity PDF Developer Toolkit allows developers to easily generate boilerplate PDF templates for Gravity PDF using the WP CLI, as well as provide tools (and documentation) for importing and auto-filling existing PDF documents. It is a drop-in replacement for the legacy Gravity PDF Tier 2 plugin.
 * Author:          Gravity PDF
 * Author URI:      https://gravitypdf.com
 * Text Domain:     gravity-pdf-developer-toolkit
 * Domain Path:     /languages
 * Version:         1.0.0-beta6
 */

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

define( 'GFPDF_DEVELOPER_TOOLKIT_FILE', __FILE__ );
define( 'GFPDF_DEVELOPER_TOOLKIT_VERSION', '1.0.0-beta6' );

/**
 * Class GPDF_Core_Booster_Checks
 *
 * @since 1.0
 */
class GpdfDeveloperToolkitChecks {

	/**
	 * Holds any blocker error messages stopping plugin running
	 *
	 * @var array
	 *
	 * @since 1.0
	 */
	private $notices = [];

	/**
	 * @var string
	 *
	 * @since 1.0
	 */
	private $requiredGravitypdfVersion = '4.4.0';

	/**
	 * Run our pre-checks and if it passes bootstrap the plugin
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function init() {

		/* Test the minimum version requirements are met */
		$this->checkGravitypdfVersion();

		/* Check if any errors were thrown, enqueue them and exit early */
		if ( count( $this->notices ) > 0 ) {
			add_action( 'admin_notices', [ $this, 'displayNotices' ] );

			return null;
		}

		add_action(
			'gfpdf_fully_loaded',
			function() {
				require_once __DIR__ . '/src/bootstrap.php';
			}
		);
	}

	/**
	 * Check if the current version of Gravity PDF is compatible with this add-on
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function checkGravitypdfVersion() {

		/* Check if the Gravity PDF Minimum version requirements are met */
		if (
			defined( 'PDF_EXTENDED_VERSION' ) &&
			version_compare( PDF_EXTENDED_VERSION, $this->requiredGravitypdfVersion, '>=' )
		) {
			return true;
		}

		/* Throw error */
		$this->notices[] = sprintf( esc_html__( 'Gravity PDF Version %s or higher is required to use this add-on. Please upgrade Gravity PDF to the latest version.', 'gravity-pdf-developer-toolkit' ), $this->requiredGravitypdfVersion );
	}

	/**
	 * Helper function to easily display error messages
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function displayNotices() {
		?>
		<div class="error">
			<p>
				<strong><?php esc_html_e( 'Gravity PDF Developer Toolkit Installation Problem', 'gravity-pdf-developer-toolkit' ); ?></strong>
			</p>

			<p><?php esc_html_e( 'The minimum requirements for the Gravity PDF Developer Toolkit plugin have not been met. Please fix the issue(s) below to continue:', 'gravity-pdf-developer-toolkit' ); ?></p>
			<ul style="padding-bottom: 0.5em">
				<?php foreach ( $this->notices as $notice ): ?>
					<li style="padding-left: 20px;list-style: inside"><?php echo $notice; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

/* Initialise the software */
add_action(
	'plugins_loaded',
	function() {
		$gravitypdfDeveloperToolkit = new GpdfDeveloperToolkitChecks();
		$gravitypdfDeveloperToolkit->init();
	}
);
