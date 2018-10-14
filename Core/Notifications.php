<?php

namespace Core;

use App\Models\Notification;

class Notifications
{
	public static function new( $user, $type )
	{
		$note = new Notification( [ 'recipient' => $user, 'type' => $type] );
		$note->save();
	}
	
	public static function check( $user )
	{
		$note = Notification::getNotifications($user);
		if ($note)
			$_SESSION['notification'] = $note->type;
	}

}