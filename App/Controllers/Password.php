<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\Flash;
use Core\View;


class Password extends Controller
{
	
	public function forgotAction()
	{
		View::renderTemplate( 'Password/forgot.html', [ 'email' => $_GET['email'] ?? '' ] );
	}
	
	
	public function requestResetAction()
	{
		User::sendPasswordReset( $_POST['email'] );
		$this->redirect( '/password/resetSent' );
		
		/*
				//  If email existing confirmation is needed.
		
				if (User::sendPasswordReset($_POST['email'])) {
					View::renderTemplate( 'Password/reset_sent.html' );
					
				}
				else {
					Flash::addMessage('No such user', 'danger');
					View::renderTemplate( 'Password/forgot.html', [ 'email' => $_POST['email'] ] );
				}
		
		*/
		
	}
	
	
	public function resetSentAction()
	{
		View::renderTemplate( 'Password/reset_sent.html' );
	}
	
	
	public function resetAction()
	{
		$token = $this->route_params['token'];
		$this->getUser($token);
		View::renderTemplate( 'Password/reset.html', [ 'token' => $token ] );
	}
	
	
	public function resetPasswordAction()
	{
		$user = $this->getUser($_POST['token']);
		$user->password = $_POST['password'];
		
		if ( $user->changePassword() ) {
			self::redirect( '/password/password-changed' );
			exit;
		}
		
		Flash::addMessage( $user->errors[0], 'danger');           // fire last error
		$this->redirect('/password/reset/'.$_POST['token']);
	}
	
	
	public function getUser( $token )
	{
		$user  = User::findUserByPasswordReset( $token );
		if ( $user )
			return $user;
		else {
			Flash::addMessage('Password request expired', 'danger');
			$this->redirect( '/password/forgot' );
			exit;
		}
	}
	
	
	public function PasswordChangedAction()
	{
		View::renderTemplate( 'Password/changed.html' );
	}
}