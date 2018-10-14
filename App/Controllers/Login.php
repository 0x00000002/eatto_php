<?php

namespace App\Controllers;

use Core\Auth\Auth;
use Core\Flash;
use Core\Controller;
use Core\View;

use App\Models\User;


class Login extends Controller
{
	
	protected function indexAction()
	{
		View::renderTemplate( 'Login/index.html' );
	}
	
	
	protected function authenticateAction()
	{
		$user = User::authenticate( $_POST['email'], $_POST['password'] );
		$remember_me = $_POST['remember_me'] ?? false;
		
		if ( $user ) {
			
			if ( !$user->is_active ) {
				$this->redirect( '/signup/waiting' );
				exit;
			}

			Auth::login($user,$remember_me);
			Flash::addMessage( "Welcome back, " . $user->name, 'success' );
			$redirectTo = Auth::getRememberedPage();
			$_SESSION['return_to'] = '/';
			$this->redirect( $redirectTo );
			exit;
			
		} else {
			
			Flash::addMessage( 'Wrong user or password, please try again', 'warning' );
			View::renderTemplate( 'Login/index.html', [
				'email' => $_POST['email'],
				'remember_me' => $remember_me
			]);
		}
		
	}
	
	
	protected function logoutAction()
	{
		Auth::logout();
		$this->redirect('/login/logoutMessage');
		exit;
	}
	
	
	protected function logoutMessageAction()
	{
		Flash::addMessage( "Logout successful", 'success' );
		$this->redirect( '/' );
		exit;
	}
	
}