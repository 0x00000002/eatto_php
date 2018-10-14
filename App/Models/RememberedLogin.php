<?php

namespace App\Models;

use PDO;
use Core\Auth\Token;

class RememberedLogin extends \Core\Model
{
	
	public static function findByToken( $token )
	{
		$token = new Token($token);
		$token_hash = $token->getHash();
		
		$db   = self::getDB();
		$stmt = $db->prepare( "SELECT * FROM remembered_logins WHERE token_hash = :token_hash" );
		$stmt->bindValue( ':token_hash', $token_hash, PDO::PARAM_STR );
		$stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	
	public static function getRememberedLogin()
	{
		$cookie = $_COOKIE['remember_me'] ?? false;
		if ($cookie)
			return RememberedLogin::findByToken($cookie);
	}
	
	
	public function hasExpired(  )
	{
		return strtotime($this->expires_at) < time();
	}
	
	
	public function delete()
	{
		$sql = 'delete from remembered_logins where token_hash=:token_hash';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);
		$stmt->execute();
	}
	
	
	// Необходимо чистить базу от просроченных токенов
	// Как и базу пользователей?
	// Что там с производительностью?

}