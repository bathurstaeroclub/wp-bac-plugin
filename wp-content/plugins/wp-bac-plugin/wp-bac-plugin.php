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
 * Requires Plugins:  contact-form-7
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
		$mail->Host       = 'mail.bathurstaeroclub.com.au';
		$mail->SMTPAuth   = true;

		$mail->Username   = 'noreply@bathurstaeroclub.com.au';
		$mail->Password   = get_option('bac_misc_options')['password'];

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
	echo '<p>Howdy Club Admin, please report feature requests and bugs on the plugin github page. See plugin website under the Plugin Menu. Cheers, Edward.</p>';
}
function bac_dashboard_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('custom_help_widget', 'BAC Plugin Support', 'bac_dashboard_help');
}

add_action('wp_dashboard_setup', 'bac_dashboard_widgets');

// ---------

function bac_misc_smtp_text_cb( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Here you can set all the options for using SMTP in the BAC plugin.', 'bac_misc' ); ?></p>
	<?php
}

/**
 * SMTP Set Password section callback function.
 *
 * @param array $args
 */
function bac_misc_set_smtp_password_cb( $args ) {
	$options = get_option( 'bac_misc_options' );
	?>
		<input id='bac_misc_set_smtp_password' name='bac_misc_options[password]' type='text' value="<?php echo esc_attr( $options['password'] ); ?>" />
	<?php
}

function bac_misc_options_validate( $input ) {
	// any validation of form input here..
	return $newinput;
}
function bac_misc_settings_init() {
	register_setting( 'bac_misc', 'bac_misc_options', 'bac_misc_options_validate' );

	// Register a new section in the "bac_misc" page.
	add_settings_section(
		'bac_misc_smtp_settings',
		'SMTP Settings', 'bac_misc_smtp_text_cb',
		'bac_misc'
	);
	add_settings_field(
		'bac_misc_set_smtp_password',
		'SMTP Password', 'bac_misc_set_smtp_password_cb',
		'bac_misc',
		'bac_misc_smtp_settings'
	);
}
add_action( 'admin_init', 'bac_misc_settings_init' );

function bac_misc_render_options_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( 'bac_misc_messages', 'bac_misc_message', __( 'Settings Saved', 'bac_misc' ), 'updated' );
	}
	settings_errors( 'bac_misc_messages' );

	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'bac_misc' );
			do_settings_sections( 'bac_misc' );
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}

function bac_misc_options_page() {
	add_options_page(
		'BAC plugin page',
		'BAC Plugin Options',
		'manage_options',
		'bac_misc',
		'bac_misc_render_options_page_html'
	);
}
add_action( 'admin_menu', 'bac_misc_options_page' );
