<?php

namespace Core\Auth;

use Core\Flash;

abstract class Authenticated extends \Core\Controller
{
	
	
	protected function requireLogin(){
		if (!Auth::getUser()) {
			Auth::rememberRequestedPage();
			Flash::addMessage("Login required to access that page.",'info' );
			$this->redirect( '/login' );
			exit;
		}
	}
	
	
	protected function before()
	{
		self::requireLogin();
	}
	
	
	protected function after()
	{
	}
	
	
}