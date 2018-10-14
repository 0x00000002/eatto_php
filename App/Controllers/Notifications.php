<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth\Auth;
use App\Models\Notification;

class Notifications extends Controller
{
    public static function indexAction()
    {
        header("content-type:application/json");

        if ($user = Auth::getUser()) {
        	$me = $user->id;
	        if ( $res = Notification::getNotifications( $me ) ) {
		        $json['msg'] = $res['msg'];
		        $json['mtg'] = $res['mtg'];
		        $json['ref'] = $res['ref'];
		        echo json_encode( $json );
	        }
        }
    }
}