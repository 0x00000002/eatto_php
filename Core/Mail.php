<?php

namespace Core;

use \Mailgun\Mailgun;

class Mail
{
	public static function send( $to, $subject, $text, $html )
	{
		# Instantiate the client.

		$mailgun = new Mailgun(\App\Config::MAILGUN_KEY);
		$domain = \App\Config::MAILGUN_DOMAIN;
		
		$mailData = array(
			'from'    => \App\Config::MAILGUN_FROM,
		    'to'      => $to,
		    'subject' => $subject,
		    'text'    => $text,
		    'html'    => $html
		);
		
		$result = $mailgun->sendMessage("$domain", $mailData);

		return $result->http_response_code == 200 ? true : false;

	}
}