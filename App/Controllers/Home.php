<?php

namespace App\Controllers;

use Core\Auth\Auth;
use Core\Controller;
use Core\View;
use Core\Services;


class Home extends Controller
{
	
	protected function indexAction()
	{
		$me  = Auth::getUser()->id ?? '';
		$loc = explode(',', Services::getCurrentLocation());
		$location = " { lat: $loc[0], lng: $loc[1] }";
		View::renderTemplate( 'Home/index.html', ['me' => $me, 'location' => $location ] );
	}
	
	
	protected function before()
	{
	}
	
	
	protected function after()
	{
	}
	
	
}