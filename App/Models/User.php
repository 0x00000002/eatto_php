<?php

namespace App\Models;

use App\Config;
use Core\Auth\Token;
use Core\Mail;
use Core\View;
use PDO;

class User extends \Core\Model
{
	public $errors = [];
	
	
	public function __construct( $data = [] )
	{
		if ( $data ) {
			foreach ( $data as $key => $value ) {
				$this->$key = $value;
			}
		}
	}
	
	
	public function save()
	{
		
		$this->validate();
		
		if ( empty( $this->errors ) ) {
			
			$passwordHash = password_hash( $this->password, PASSWORD_DEFAULT );
			
			$token = new Token();
			$hashed_token = $token->getHash();
			
			$sql = 'INSERT INTO users (name,email,password_hash, activation_hash)
					VALUES (:name,:email, :password_hash, :hashed_token)';
			
			$db   = self::getDB();
			$stmt = $db->prepare( $sql );
			$stmt->bindValue( ':name', $this->name, PDO::PARAM_STR );
			$stmt->bindValue( ':email', $this->email, PDO::PARAM_STR );
			$stmt->bindValue( ':password_hash', $passwordHash, PDO::PARAM_STR );
			$stmt->bindValue( ':hashed_token', $hashed_token, PDO::PARAM_STR );
			
			if ( $stmt->execute() )
				return $this->sendActivation($token->getToken());
			
			return false;
		}
		
		return false;
	}
	
	
	private function validate()
	{
		$this->errors = [];   // reset previous errors
		
		if ( $this->name == '' ) {
			$this->errors[] = 'No name is given';
		}
		
		if ( filter_var( $this->email, FILTER_VALIDATE_EMAIL ) === false ) {
			$this->errors[] = 'Invalid email';
		}
		
		if ( $this->emailExists( $this->email , $this->id ?? null) ) {
			$this->errors[] = 'Email already taken';
		}
		
		$this->validatePassword();
		
	}
	
	
	private function validatePassword(){
		if ( isset($this->password) ) {
			if ( strlen( $this->password ) < 8 ) {
				$this->errors[] = 'Password too short';
			}
		}
	}
	
	
	public static function emailExists( $email, $ignore_id = NULL)
	{
		$user = self::findUserByMail( $email );
		if ($user)
			if ($user->id != $ignore_id)
				return true;
		return false;
	}
	
	
	public static function findUserByMail( $email )
	{
		$db   = self::getDB();
		$stmt = $db->prepare( "SELECT * FROM users WHERE email = :email" );
		$stmt->bindValue( ':email', $email, PDO::PARAM_STR );
		$stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	
	public static function findUserById( $id )
	{
		$db   = self::getDB();
		$stmt = $db->prepare( "SELECT * FROM users WHERE id = :id" );
		$stmt->bindValue( ':id', $id, PDO::PARAM_STR );
		$stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	
	public static function getUsersByPartName( $partName )
	{
		$db   = self::getDB();
		$stmt = $db->prepare( "SELECT * FROM users WHERE name LIKE :name" );
		$stmt->bindValue( ':name', '%'.$partName.'%', PDO::PARAM_STR );
		$stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	
	public static function findUserByPasswordReset( $token )
	{
		$token = new Token($token);
		$hashed_token = $token->getHash();
		
		$db   = self::getDB();
		$stmt = $db->prepare( "SELECT * FROM users WHERE password_reset_hash = :hashed_token" );
		$stmt->bindValue( ':hashed_token', $hashed_token, PDO::PARAM_STR );
		$stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );
		$stmt->execute();
		$user = $stmt->fetch();
		if ($user) {
			if (strtotime($user->password_reset_expires_at) > time() )
				return $user;
		}
	}
	

	public static function findUserByActivation( $token )
	{
		$token = new Token($token);
		$hashed_token = $token->getHash();
		
		$db   = self::getDB();
		$stmt = $db->prepare( "SELECT * FROM users WHERE activation_hash = :hashed_token" );
		$stmt->bindValue( ':hashed_token', $hashed_token, PDO::PARAM_STR );
		$stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );
		$stmt->execute();
		return $stmt->fetch();
	}
	
	
	public static function activateUser( $token )
	{
		if ( $user = self::findUserByActivation( $token ) ) {
			
			$db  = self::getDB();
			$sql = "UPDATE users
				SET is_active = 1,
					activation_hash = NULL
				WHERE id = :id";
			
			$stmt = $db->prepare( $sql );
			$stmt->bindValue( ':id', $user->id, PDO::PARAM_INT );
			
			if ( $stmt->execute() ) {
				return $user;
			}
		}
		return false;
	}
	
	
	public static function authenticate( $email, $password )
	{
		
		$user = self::findUserByMail( $email );
		if ( $user ) {
			if ( password_verify( $password, $user->password_hash ) ) {
				return $user;
			}
		}
		
		return false;
		
	}
	
	
	public function rememberLogin()
	{
		$token                  = new Token();
		$hashed_token           = $token->getHash();
		$this->remember_token   = $token->getToken();
		$this->expire_timestamp = time() + 60 * 60 * 24 * \App\Config::RELOGIN_DAYS;
		
		$sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
				VALUES (:token_hash, :user_id, :expires_at)';
		
		$db   = static::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':token_hash', $hashed_token, PDO::PARAM_STR );
		$stmt->bindValue( ':user_id', $this->id, PDO::PARAM_INT );
		$stmt->bindValue( ':expires_at', date( 'Y-m-d H:i:s', $this->expire_timestamp ), PDO::PARAM_STR );
		
		return $stmt->execute();
		
	}
	
	
	public static function sendPasswordReset( $email )
	{
		$user = self::findUserByMail( $email );
		
		if ( $user ) {
			
			if ( $user->startPasswordReset() ) {
				
				$link     = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $user->password_reset_token;
				$subject  = "We've got password reset request";
				$text     = View::getTemplate('Password/reset_link.txt',  [ 'url' => $link ]);
				$html     = View::getTemplate('Password/reset_link.html', [ 'url' => $link ]);
				
				return Mail::send( $user->email, $subject, $text, $html );
			}
			
		}
		
		return false;
	}
	
	
	private function sendActivation( $token )
	{
		$link     = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $token;
		$subject  = Config::SITENAME . " account activation";
		$text     = View::getTemplate('Signup/activation_link.txt',  [ 'url' => $link ]);
		$html     = View::getTemplate('Signup/activation_link.html', [ 'url' => $link ]);
		return Mail::send( $this->email, $subject, $text, $html );
	}
	
	
	protected function startPasswordReset()
	{
		
		$token                      = new Token();
		$this->password_reset_token = $token->getToken();
		$hashed_token               = $token->getHash();
		$expire_timestamp           = time() + 60 * 60 * 2 ;   // 1 hour from now
		
		$sql = 'UPDATE users SET password_reset_hash = :hashed_token, password_reset_expires_at = :expires_at WHERE id = :id';
		
		$db   = static::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':hashed_token', $hashed_token, PDO::PARAM_STR );
		$stmt->bindValue( ':expires_at', date( 'Y-m-d H:i:s', $expire_timestamp ), PDO::PARAM_STR );
		$stmt->bindValue( ':id', $this->id, PDO::PARAM_INT );
		
		return $stmt->execute();
		
	}
	
	
	public function changePassword()
	{
		
		$this->validatePassword();
		
		if ( empty($this->errors) ) {
			
			$passwordHash = password_hash( $this->password, PASSWORD_DEFAULT );
			$sql = 'UPDATE users
					SET password_hash = :password_hash,
						password_reset_hash = NULL,
						password_reset_expires_at = NULL
					WHERE id = :id';
			$db   = self::getDB();
			$stmt = $db->prepare( $sql );
			$stmt->bindValue( ':id', $this->id, PDO::PARAM_STR );
			$stmt->bindValue( ':password_hash', $passwordHash, PDO::PARAM_STR );
			return $stmt->execute();
		}
		
		return false;
	}
	
	
	public function updateProfile( $data )
	{
		$this->name = $data['name'];
		$this->email = $data['email'];
		
		// Only validate and update the password if a value provided
		if ($data['password'] != '') {
			$this->password = $data['password'];
		}
		
		$this->validate();
		
		if (empty($this->errors)) {
			
			$sql = 'UPDATE users
                    SET name = :name,
                        email = :email';
			
			// Add password if it's set
			if (isset($this->password)) {
				$sql .= ', password_hash = :password_hash';
			}
			
			$sql .= "\nWHERE id = :id";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			
			// Add password if it's set
			if (isset($this->password)) {
				$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
				$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			}
			
			return $stmt->execute();
		}
		
		return false;
	}
	
}