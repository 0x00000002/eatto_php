<?php

namespace Core;


class Error
{
	public static function errorHandler($level,$message,$file,$line)
	{
		if(error_reporting() !== 0) {
			throw new \ErrorException( $message,0, $level, $file, $line);
		}
	}
	
	public static function exceptionHandler($exception)
	{
		$code = $exception->getCode();
		if ($code != 404)
			$code = 500;
		http_response_code($code);
		
		$message = '<h1>' . get_class( $exception ) . '</h1>';
		$message .= '<p><b>Message</b>: ' . $exception->getMessage() . '.<p>';
		$message .= '<p><b>Location</b>: ' . $exception->getFile() . ' on line ' . $exception->getLine() . '.</p>';
		$message .= '<p><b>Stack trace</b>: <pre>' . $exception->getTraceAsString() . '</pre></p>';
		
		if ( SHOW_ERRORS === true ) {
			echo $message;
		} else {
			date_default_timezone_set('GMT');
			$log = dirname(__DIR__).'/logs/'.date('Y-m-d').'.log';
			ini_set('error_log',$log);
			error_log($message);
			
			View::renderTemplate("$code.html");
		}
	}
}