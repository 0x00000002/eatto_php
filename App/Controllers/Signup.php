<?php

namespace App\Controllers;

use Core\Auth\Auth;
use Core\Controller;
use Core\Flash;
use Core\View;
use App\Models\User;
use App\Models\UserProfile;


class Signup extends Controller
{
	
	protected function indexAction()
	{
		View::renderTemplate( 'Signup/index.html' );
	}
	
	
	protected function createAction()
	{
		$user      = new User($_POST);
		$isSaved = filter_var( $user->save(), FILTER_VALIDATE_BOOLEAN );
		
		$this->addProfile();
		
		if ( $isSaved ) {
			Flash::addMessage('Activation link is sent','success');
			$this->redirect( '/signup/waiting' );
			exit;
		} else {
			foreach ($user->errors as $error) {
				Flash::addMessage( $error, 'warning' );
			}
			View::renderTemplate( '/Signup/index.html', [ 'user' => $user ] );
		}
	}
	
	protected function addProfile()
	{
		$newUser = User::findUserByMail( $_POST['email'] );
		$data    = [ 'id' => $newUser->id, 'name' => $newUser->name ];
		$profile = new UserProfile( $data );
		$profile->add();
	}
	
	protected function waitingAction()
	{
		View::renderTemplate( '/Signup/waiting.html' );
	}

	
	protected function activateAction()
	{
		$token = $this->route_params['token'];
		$user = User::activateUser($token);

		if ( $user ) {
			Auth::login( $user );
			Flash::addMessage( 'Welcome, ' . $user->name . '. Your account is active now', 'success' );
			$this->redirect( '/' );
			exit;
		} else {
			Flash::addMessage('Account already active');
			$this->redirect( '/' );
		}
	}
	
}