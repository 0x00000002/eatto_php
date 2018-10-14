<?php

namespace Core;

use Core\Auth\Auth;


class View
{
	
	public static function renderTemplate( $template, $args = [] )
	{
		if ( isset( $_SESSION['notification'] ) ) {
			$args['notification'] = $_SESSION['notification'];
			echo $args['notification'];
		}
		echo self::getTemplate( $template, $args );
		
	}
	
	
	public static function getTemplate( $template, $args = [] )
	{
		static $twig = null;
		if ( $twig === null ) {
			$loader = new \Twig_Loader_Filesystem( '../App/Views' );
			$twig   = new \Twig_Environment( $loader );
			$twig->addGlobal( 'current_user', Auth::getUser() );
			$twig->addGlobal( 'flash_messages', Flash::getMessages() );
		}
		
		return $twig->render( $template, $args );
		
	}
	
}