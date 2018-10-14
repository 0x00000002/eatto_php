<?php

namespace Core\Auth;

use App\Models\RememberedLogin;
use App\Models\User;

class Auth
{
	
	public static function login( $user, $remember = [] )
	{
		session_regenerate_id( true );
		$_SESSION['id'] = $user->id;
		if ( $remember ) {
			$user->rememberLogin();
			setcookie( 'remember_me', $user->remember_token, $user->expire_timestamp, '/' );
		}
	}
	
	
	public static function logout()
	{
		
		$_SESSION = array();
		
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		
		if ( ini_get( "session.use_cookies" ) ) {
			$params = session_get_cookie_params();
			setcookie( session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		
		// Finally, destroy the session.
		session_destroy();
		
		self::forgetLogin();
		
	}
	
	
	public static function getUser()
	{
		if ( isset( $_SESSION['id'] ) )
			return User::findUserById( $_SESSION['id'] );
		
		return self::loginFromRememberCookie();
		
	}
	
	
	protected static function loginFromRememberCookie()
	{
		$remembered_login = RememberedLogin::getRememberedLogin();
		if ( $remembered_login && ! $remembered_login->hasExpired() ) {
			$user = User::findUserById( $remembered_login->user_id );
			self::login( $user, false );
			return $user;
		}
	}
	
	
	protected static function forgetLogin()
	{
		$remembered_login = RememberedLogin::getRememberedLogin();
		if ( $remembered_login ) {
			$remembered_login->delete();
		}
		setcookie( 'remember_me', '', time() - 42000, '/' );
	}
	
	
	public static function rememberRequestedPage()
	{
		$_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
	}
	
	
	public static function getRememberedPage()
	{
		return $_SESSION['return_to'] ?? '/';
	}
	
}