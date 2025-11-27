<?php

namespace DoroteoDigital\AutoEmail\sender;

class Sender {


	/*
	 * Initializes a Sender object
	 */
	function __construct() {
	}

	/*
	 * Sends an email with the given $message
	 *
	 * This email is sent to the address in $to, with
	 * the given subject and headers.
	 */
	public function send(
		string $to,
		string $subject,
		string $message,
		string $headers = '',
		$attachments = array()
	) {

		//TODO

		// Example Data
//		$to      = 'sendto@example.com';
//		$subject = 'The subject';
//		$message    = 'The email body content';
//		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail( $to, $subject, $message, $headers ); // Stub
	}
}