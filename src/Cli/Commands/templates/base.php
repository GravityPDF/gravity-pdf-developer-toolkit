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
 */

/* Prevent direct access to the template */
if ( ! class_exists( 'GFForms' ) ) {
	return;
}

/*
 * All Gravity PDF 4.x templates have access to the following variables:
 *
 * $form (The current Gravity Form array)
 * $entry (The raw entry data)
 * $form_data (The processed entry data stored in an array)
 * $settings (the current PDF configuration)
 * $fields (an array of Gravity Form fields which can be accessed with their ID number)
 * $config (The initialised template config class – eg. /config/zadani.php)
 * $gfpdf (the main Gravity PDF object containing all our helper classes)
 * $args (contains an array of all variables - the ones being described right now - passed to the template)
 */

?>

<!-- Include styles needed for the PDF -->
<style>

</style>

<!-- Output our HTML markup -->