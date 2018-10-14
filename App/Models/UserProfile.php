<?php

namespace App\Models;

use App\Config;
use PDO;
use Eventviva\ImageResize;

class UserProfile extends \Core\Model
{
	
	public function __construct( $data = [] )
	{
		if ( $data ) {
			foreach ( $data as $key => $value ) {
				$this->$key = $value;
			}
		}
	}
	
	public function view()
	{
		$sql = '
				SELECT
				  user_id as id,
				  user_name as name,
				  ref as photo,
				  info,
				  status,
				  email,
				  l.name AS place_name,
				  l.id AS place_id,
				  address
				  FROM
				    (
				      SELECT *
				      FROM profile
				      WHERE user_id = :id
				    ) as p
				  LEFT JOIN photos f ON p.photo_id = f.id
				  LEFT JOIN hosts h ON p.user_id = h.user
				  LEFT JOIN users u ON p.user_id = u.id
				  LEFT JOIN locations l on h.location_id = l.id
               ';
		
		$db   = static::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':id', $this->user_id, PDO::PARAM_INT );
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	public function add()
	{
		$sql  = 'INSERT INTO profile SET user_name = :name, user_id = :id';
		$db   = static::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':name', $this->name, PDO::PARAM_STR );
		$stmt->bindValue( ':id', $this->id, PDO::PARAM_INT );
		
		return $stmt->execute();
	}
	
	public function update()
	{
		$results = [];
		$db      = static::getDB();
		
		$sql1 = 'UPDATE profile
				 SET user_name = :name, info = :info, status = :status, address = :address
                 WHERE user_id = :user_id';
		
		$stmt1 = $db->prepare( $sql1 );
		$stmt1->bindValue( ':name', $this->name, PDO::PARAM_STR );
		$stmt1->bindValue( ':info', $this->info ?? '', PDO::PARAM_STR );
		$stmt1->bindValue( ':status', $this->status ?? '', PDO::PARAM_INT );
		$stmt1->bindValue( ':user_id', $this->user_id, PDO::PARAM_INT );
		$stmt1->bindValue( ':address', $this->address ?? '', PDO::PARAM_STR );
		$results[] = $stmt1->execute();
		
		
		$sql2  = 'INSERT IGNORE INTO locations SET id = :place_id, name = :place_name';
		$stmt2 = $db->prepare( $sql2 );
		$stmt2->bindValue( ':place_id', $this->place_id ?? '', PDO::PARAM_STR );
		$stmt2->bindValue( ':place_name', $this->place_name ?? '', PDO::PARAM_STR );
		$results[] = $stmt2->execute();
		
		$sql3  = 'INSERT INTO hosts ( hosts.user, hosts.location_id ) VALUES (:user_id, :place_id)
					ON DUPLICATE KEY UPDATE hosts.location_id = :place_id';
		
		var_dump($sql3);
		var_dump($this->place_id);

		$stmt3 = $db->prepare( $sql3 );
		$stmt3->bindValue( ':user_id', $this->user_id, PDO::PARAM_INT );
		$stmt3->bindValue( ':place_id', $this->place_id ?? '', PDO::PARAM_STR );
		$results[] = $stmt3->execute();
		
		return $results;
	}
	
	public function getPhotos()
	{
		
		$sql = '
				SELECT id, photo_id as profile_photo_id, ref
				FROM photos
				  INNER JOIN profile ON user = profile.user_id
				WHERE user = :user_id
				';
		
		$db   = static::getDB();
		$stmt = $db->prepare( $sql );
		
		$stmt->bindValue( ':user_id', $this->user_id, PDO::PARAM_INT );
		$stmt->execute();
		
		return $stmt->fetchAll();
		
	}
	
	public function addPhoto( $fileName, $tmpName, $fileType, $fileSize = [] )
	{
		$userId    = $this->user_id;
		$uploadDir = Config::UPLOADS . $userId . "/";
		$thumbDir  = Config::UPLOADS . $userId . "/thumbs/";
		$fullName  = $uploadDir . $fileName;
		$thumbName = $thumbDir . $fileName;
		
		$this->validateFile( $fileName, $fileType );
		
		if ( ! file_exists( $uploadDir ) ) {
			mkdir( Config::UPLOADS . $userId, 0777 );
		}
		
		if ( ! file_exists( $thumbDir ) ) {
			mkdir( Config::UPLOADS . $userId . "/thumbs/", 0777 );
		}
		
		move_uploaded_file( $tmpName, $fullName );
		
		$image = new ImageResize( $fullName );
		$image->crop( 200, 200 );
		$image->save( $thumbName );
		
		$sql = 'INSERT INTO photos SET user = :user_id, ref = :ref';
		
		$db   = static::getDB();
		$stmt = $db->prepare( $sql );
		
		$stmt->bindValue( ':user_id', $userId, PDO::PARAM_INT );
		$stmt->bindValue( ':ref', $fileName, PDO::PARAM_INT );
		$stmt->execute();
		
		return $db->lastInsertId();
	}
	
	public function setProfilePhoto( $photo_id )
	{
		$sql = '
				UPDATE profile
				  SET profile.photo_id =
				  if(
				      (
				        SELECT id
				        FROM
				          (
				            SELECT *
				            FROM profile
				              INNER JOIN photos ON photos.user = profile.user_id
				          ) AS f
				        WHERE f.user = :user_id AND f.id = :photo_id
				      ),
				      (
				        SELECT id
				        FROM
				          (
				            SELECT *
				            FROM profile
				              INNER JOIN photos ON photos.user = profile.user_id
				          ) AS f
				        WHERE f.user = :user_id AND f.id = :photo_id
				      ), profile.photo_id
				  )
				  WHERE profile.user_id = :user_id
                ';
		
		$db   = static::getDB();
		$stmt = $db->prepare( $sql );
		
		$stmt->bindValue( ':user_id', $this->user_id, PDO::PARAM_INT );
		$stmt->bindValue( ':photo_id', $photo_id, PDO::PARAM_INT );
		
		return $stmt->execute();
	}
	
	public function deletePhoto( $photo_id )
	{
		$sql = 'DELETE FROM photos WHERE user = :user_id AND id = :photo_id';
		
		$db   = static::getDB();
		$stmt = $db->prepare( $sql );
		
		$stmt->bindValue( ':user_id', $this->user_id, PDO::PARAM_INT );
		$stmt->bindValue( ':photo_id', $photo_id, PDO::PARAM_INT );
		
		return $stmt->execute();
		
		//TODO: unlink file
		
	}
	
	public function validateFile( $fileName, $fileType )
	{
		$error = false;
		
		if ( $fileType != "image/gif" && $fileType != "image/jpeg" && $fileType != "image/png" ) {
			$error = 'Allowed types: JPG, JPEG, PNG, GIF';
		}
		
		if ( file_exists( $fileName ) ) {
			$error = 'File exist';
		}
		
		//		if ( $_FILES["file"]["size"] > 2500000 ) {
		//			Flash::addMessage('Maximum 2 Mb filesize supported', "warning");
		//          $error = true;
		//		}
		
		return $error;
	}
	
}