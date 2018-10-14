<?php

namespace App\Controllers;

use App\Models\Meeting;
use Core\Auth\Auth;
use Core\Auth\Authenticated;
use Core\Flash;
use Core\View;
use App\Models\Notification;
use App\Models\Host;

class Meetings extends Authenticated
{
	
	public function indexAction()
	{
		$me       = Auth::getUser()->id;
		$meetings = Meeting::getUserMeetings( $me );
		Notification::update( $me, "mtg", false );
		View::renderTemplate( 'Meetings/index.html', [ 'meetings' => $meetings, 'me' => $me ] );
	}
	
	public function viewAction()
	{
		$me             = Auth::getUser()->id;
		$meetingId      = $this->route_params['id'];
		$meeting        = Meeting::view( $me, $meetingId );
		$params['mtg']       = $meeting;
		$refs           = Host::getRefsByID( $meeting['user_id'] );
		$params['refs'] = $refs;
		$params['me']   = $me;
		if ( $meeting ) {
			View::renderTemplate( 'Meetings/view.html', $params );
		}
	}
	
	public function confirmAction()
	{
		$this->updateMeeting(1);
	}
	
	public function declineAction()
	{
		$this->updateMeeting(-1);
	}
	
	public function updateMeeting( $status )
	{
		$me        = Auth::getUser()->id;
		$meetingId = $this->route_params['id'];
		if ( Meeting::update( $me, $meetingId, $status ) ) {
			$userId = Meeting::getOpponent($meetingId, $me)['user_id'];
			$message = $status == 1 ? 'Meeting confirmed' : 'Meeting declined';
			Flash::addMessage( $message );
			Notification::update( $userId, "mtg", $status);
			$this->redirect( '/meetings' );
			exit;
		}
	}
	
	public function newAction()
	{
		$me     = Auth::getUser()->id;
		$userId = $this->route_params['id'];
		if ( isset( $_POST['text'] ) && isset( $_POST['meet_on'] ) ) {
			$res = Meeting::new( $me, $userId, $_POST['meet_on'], $_POST['text'] );
			if ( $res ) {
				Flash::addMessage( 'Meeting proposed', 'success' );
				Notification::update( $userId, "mtg", true );
			} else {
				Flash::addMessage( 'Error', 'warning' );
			}
		} else {
			Flash::addMessage( 'No text to send' );
		}
		$this->redirect( '/Meetings' );
		exit;
	}
	
	public function referenceAction()
	{
		$me     = Auth::getUser()->id;
		$userId = $this->route_params['id'];
		if ( isset( $_POST['text'] ) ) {
			$res = Meeting::addReference( $userId, $me, $_POST['text'], $_POST['meeting_id'] );
			if ( $res ) {
				Notification::update( $userId, "ref", true );
				Flash::addMessage( 'Reference added', 'success' );
			} else {
				Flash::addMessage( 'Error', 'warning' );
			}
		} else {
			Flash::addMessage( 'No reference to write' );
		}
		$this->redirect( "/meetings" );
		exit;
	}
	
}
