&#x3C;?php

/*
 * Template Name: <?php echo $data['name'] . "\n"; ?>
 * Version: 1.0
 * Description: <?php echo $data['desc'] . "\n"; ?>
 * Author: <?php echo $data['author'] . "\n"; ?>
 * Author URI: <?php echo $data['author_uri'] . "\n"; ?>
 * Group: <?php echo $data['group'] . "\n"; ?>
<?php echo ( !empty( $data['license'] ) ) ? ' * License: ' . $data['license'] . "\n" : ''; ?>
 * Required PDF Version: <?php echo $data['required_version'] . "\n"; ?>
<?php echo ( !empty( $data['tags'] ) ) ? ' * Tags: ' . $data['tags'] . "\n" : ''; ?>
 * Toolkit: true
 */

/* Prevent direct access to the template */
if ( ! class_exists( 'GFForms' ) ) {
	return;
}

/**
 * Gravity PDF Toolkit templates have access to the following variables
 *
 * $w (A helper class that does the heavy lifting and PDF manipulation)
 * $mpdf (The raw Mpdf object)
 * $form (The current Gravity Form array)
 * $entry (The raw entry data)
 * $form_data (The processed entry data stored in an array)
 * $settings (the current PDF configuration)
 * $fields (an array of Gravity Form fields which can be accessed with their ID number)
 * $config (The initialised template config class – eg. /config/zadani.php)
 * $gfpdf (the main Gravity PDF object containing all our helper classes)
 * $args (contains an array of all variables - the ones being described right now - passed to the template)
 *
 * @var GFPDF\Plugins\DeveloperToolkit\Writer\Writer $w
 * @var mPDF $mpdf
 */

/* Load PDF Styles */
$w->beginStyles();
?&#x3E;
    <style>
        body {
            color: red;
        }
    </style>
&#x3C;?php
$w->endStyles();

/* Begin PDF Generation */
$w->addPdf( __DIR__ . '/pdfs/my-pdf-document.pdf' ); /* CHANGE THIS TO POINT TO YOUR PDF */
$w->addPage( 1 );
$w->add( 'My content', [ 50, 50, 10, 10 ] ); /* x, y, w, h */