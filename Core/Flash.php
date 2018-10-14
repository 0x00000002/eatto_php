<?php

namespace Core;


class Flash
{
	const SUCCESS = 'success';
	const WARNING = 'warning';
	const DANGER = 'danger';
	const INFO = 'info';
	
	public static function addMessage( $message, $type = 'info' )
	{

		if ( isset( $_SESSION['flash_notifications'] ) ) {
			$_SESSION['flash_notifications'] = [];
		}
		$_SESSION['flash_notifications'][] = [ 'text' => $message, 'type' => $type ]; //add to array of messages
	}
	
	
	public static function getMessages()
	{
		$messages = $_SESSION['flash_notifications'] ?? false;
		unset( $_SESSION['flash_notifications'] );
		return $messages;
	}
	
}