<?php

namespace App\Controllers;

use Core\Auth\Auth;
use Core\Controller;
use Core\Flash;
use Core\Services;
use Core\View;
use App\Models\Host;


class Hosts extends Controller
{
	public $location = [];
	
	public function indexAction()
	{
//		$_SESSION['place_id'] = null;
//		$_SESSION['place_name'] = null;
		$this->getLocation();
		if ( isset( $this->location['id'] ) ) {
			$this->redirect('/hosts/show');
		} else {
			View::renderTemplate( '/Hosts/index.html', [ 'place' => $this->location['name'] ?? '' ] );
		}
	}
	
	public function showAction()
	{
		$this->getLocation();
		if ( isset( $this->location['id'] ) ) {
			$hosts = Host::getAll( $this->location['id'] );
			View::renderTemplate( '/Hosts/index.html', [
				'hosts' => $hosts ?? '',
				'name'  => $this->location['name'],
			] );
		}
	}
	
	public function viewAction()
	{
		$userId         = $this->route_params['id'];
		$host           = Host::getHostByID( $userId );
		$refs           = Host::getRefsByID( $userId );
		$me             = isset( Auth::getUser()->id ) ? Auth::getUser()->id : false;
		$params         = [ 'host' => $host ];
		$params['refs'] = $refs;
		$params['me']   = $me;
		
		if ( $params ) {
			View::renderTemplate( '/Hosts/details.html', $params );
		} else {
			View::renderTemplate( '/Hosts/notfound.html' );
		}
	}
	
	public function getLocation()
	{
		if ( isset( $_POST['name'] ) && isset( $_POST['id'] ) ) {
			$this->location         = [
				'name' => ucfirst( $_POST['name'] ),
				'id'   => ucfirst( $_POST['id'] )
			];
			$_SESSION['place_id']   = $this->location['id'];
			$_SESSION['place_name'] = $this->location['name'];
			return;
		}
		
		if ( isset( $_SESSION['place_id'] ) && isset( $_SESSION['place_name'] ) ) {
			$this->location = [
				'id' => $_SESSION['place_id'],
				'name' => $_SESSION['place_name']
			];
			return;
		}
	}
	
	
}