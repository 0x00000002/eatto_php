<?php

namespace App\Models;

use Core\Model;
use PDO;

class Host extends Model
{
	
	public static function getAll( $placeId )
	{
		$sql = "
		  	SELECT
			  h.user AS user_id,
			  p.user_name,
			  h.location_id,
			  f.ref AS profile_photo,
			  refs,
			  p.status,
			  p.info AS user_info,
			  p.address
			FROM hosts h
			LEFT JOIN profile p ON p.user_id = h.user
			LEFT JOIN photos f  ON f.id = p.photo_id
			LEFT JOIN
			(
			  SELECT user, COUNT(DISTINCT ref) AS refs
			  FROM refs
			  GROUP BY user
			) as r ON r.user = p.user_id
			WHERE h.location_id = :place_id
				";
		$db = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue(':place_id', $placeId, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	
	public static function getHostById($id)
	{
		$sql = "
			SELECT
			  p.user_id,
			  p.user_name,
			  f.ref AS profile_photo,
			  refs,
			  p.info AS user_info,
			  p.status,
			  l.name AS place,
			  p.address
			  FROM profile p
			  LEFT JOIN hosts h ON h.user = p.user_id
			  LEFT JOIN photos f  ON f.user = p.user_id
			  LEFT JOIN locations l ON l.id = h.location_id
			  LEFT JOIN
			  (
			    SELECT user, COUNT(DISTINCT ref) AS refs
			    FROM refs
			    GROUP BY user
			  ) as r ON r.user = p.user_id
			WHERE user_id = :user_id AND p.photo_id = f.id
				";
		$db = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}
	public static function getRefsById($id)
	{
		$sql = "
			SELECT ref, profile.user_name as name, author as id
			FROM refs
			LEFT JOIN profile ON profile.user_id = refs.author
			WHERE user = :user_id
		";
		$db = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

}
