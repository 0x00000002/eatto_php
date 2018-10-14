<?php

namespace App\Controllers;

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Core\Auth\Authenticated;
use Core\Auth\Auth;
use Core\Flash;
use Core\View;


class Messages extends Authenticated
{
	public function indexAction()
	{
		$me    = Auth::getUser()->id;
		$params['me'] = $me;
		$chats = Message::getRecent( $me );
		$params['messages'] = $chats;
		
        Notification::update($me,"msg", false);
		View::renderTemplate( '/Messages/index.html', $params);
	}
	
	public function viewAction()
	{
		$params             = $this->authorizeMe();
		$user_id            = $params['user']['id'];
		$me                 = $params['user']['current'];
		$messages           = Message::getChat( $user_id, $me );
		$params['messages'] = $messages;
		Notification::update($me,"msg", false);
		View::renderTemplate( '/Messages/view.html', $params );
    }

	public function sendAction()
	{
		$params  = $this->authorizeMe();
		$userId = $params['user']['id'];
		$me      = $params['user']['current'];
		if ( isset( $_POST['text'] ) ) {
			$res = Message::send( $userId, $me, $_POST['text'] );
			if ( $res ) {
				Flash::addMessage( 'Message sent', 'success' );
                Notification::update($userId,"msg", true );
			} else {
				Flash::addMessage( 'Error', 'warning' );
			}
			$this->redirect( '/Messages/' . $params['user']['id'] . '/view' );
			exit;
		}
	}

	public function authorizeMe()
	{
		$me             = Auth::getUser()->id;
		$userId         = $this->route_params['id'];
		$user           = User::findUserById( $userId );
		$params['user'] = [ 'current' => $me, 'name' => $user->name, 'id' => $user->id ];
		
		return $params;
	}
	
}