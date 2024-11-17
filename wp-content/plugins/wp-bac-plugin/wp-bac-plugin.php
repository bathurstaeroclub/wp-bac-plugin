<?php

/**
 * Plugin Name
 *
 * @package           BACPluginPackage
 * @author            Edward O'Callaghan
 * @copyright         2024 Edward O'Callaghan
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       BAC WordPress Plugin
 * Plugin URI:        https://github.com/bathurstaeroclub/wp-bac-plugin
 * Description:       Misc tasks required by the club website.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Edward O'Callaghan
 * Author URI:        https://github.com/bathurstaeroclub
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://github.com/bathurstaeroclub/wp-bac-plugin
 * Requires Plugins:  cf7
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//DEBUG: Create an instance; passing `true` enables exceptions
//$mail = new PHPMailer(true);

function bac_phpmailer_smtp( $mail ) {
	try {
		//Server settings
		// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$mail->isSMTP();     
		$mail->Host       = 'smtp.bathurstaeroclub.com.au';
		$mail->SMTPAuth   = true;

		$mail->Username   = 'noreply@bathurstaeroclub.com.au';
		$mail->Password   = 'secret';

		// Choose 'ssl' for SMTPS on port 465, or 'tls' for SMTP+STARTTLS on port 25 or 587
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		//TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
		$mail->Port       = 587;

		//Recipients
		$mail->setFrom('noreply@bathurstaeroclub.com.au', 'System Mailer');
		$mail->addReplyTo('info@bathurstaeroclub.com.au', 'Information');
		$mail->addBCC('admin@bathurstaeroclub.com.au');
	} catch (Exception $e) {
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
}

add_action( 'phpmailer_init', 'bac_phpmailer_smtp' );

// --------

function bac_dashboard_help() {
	echo '<p>BAC Misc Management Plugin.</p>';
}
function bac_dashboard_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('custom_help_widget', 'BAC Plugin Support', 'bac_dashboard_help');
}

add_action('wp_dashboard_setup', 'bac_dashboard_widgets');
