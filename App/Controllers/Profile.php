<?php

namespace App\Controllers;

use Core\Auth\Auth;
use Core\Auth\Authenticated;
use Core\Flash;
use Core\View;
use App\Models\Notification;
use App\Models\UserProfile;
use Eventviva\ImageResize;

class Profile extends Authenticated
{
	
	public function before()
	{
		$this->user = Auth::getUser();
		$this->requireLogin();
		$this->profile = new UserProfile( [ 'user_id' => $this->user->id ] );
	}
	
	public function indexAction()
	{
		$me               = $this->user->id;
		$params['user']   = $this->profile->view();
		$params['photos'] = $this->profile->getPhotos();
		$params['me']     = $me;
		Notification::update( $me, "ref", false );
		View::renderTemplate( '/Profile/index.html', $params );
	}
	
	public function editAction()
	{
		$me             = $this->user;
		$params         = $this->profile->view();
		$params['user'] = $me;
		View::renderTemplate( '/Profile/edit.html', $params );
	}
	
	public function updateAction()
	{
		$me = $this->user;
		
		if ( $me->updateProfile( $_POST ) ) {
			$profile          = new UserProfile( $_POST );
			$profile->user_id = $me->id;
			if ( $profile->update() ) {
				Flash::addMessage( 'Profile updated', 'success' );
				$this->redirect( '/profile' );
				exit;
			}
		} else {
			Flash::addMessage( 'Something goes wrong, try again', 'warning' );
			View::renderTemplate( '/Profile/edit.html', [ 'user' => $user ] );
		}
		
	}
	
	public function addPhotoAction()
	{
		if ( isset( $_REQUEST['submit'] ) ) {
			$fileName  = $_FILES["file"]["name"];
			$tmpName  = $_FILES["file"]["tmp_name"];
			$fileType = $_FILES["file"]["type"];
			$fileSize = $_FILES["file"]["size"];
			$idInserted = $this->profile->addPhoto( $fileName, $tmpName, $fileType, $fileSize );
			if ( $idInserted ) {
				Flash::addMessage( 'File successfully uploaded', 'success' );
			} else {
				Flash::addMessage( 'Could not save your photo. Try again', 'warning' );
			}
			$this->redirect("/profile/$idInserted/setProfilePhoto");
		}
	}
	
	public function setProfilePhotoAction()
	{
		$photoId = $this->route_params['id'];
		$profile = $this->profile;
		if(!$profile->setProfilePhoto($photoId)) {
			Flash::addMessage('Can not change profile photo', "warning");
		}
		$this->redirect('/profile');
		exit;
	}
	
	public function deletePhotoAction()
	{
		$photoId = $this->route_params['id'];
		$profile = $this->profile;
		if(!$profile->deletePhoto($photoId)) {
			Flash::addMessage('Can not delete photo', "warning");
		}
		$this->redirect('/profile');
		exit;
	}
	
	public function managePhotosAction()
	{
		$me               = $this->user->id;
		$params['photos'] = $this->profile->getPhotos();
		$params['me']     = $me;
		View::renderTemplate( '/Profile/photos.html', $params );
	}
}
