<?php

namespace App\Models;

use Core\Model;
use PDO;

class Notification extends Model
{
    public static function update($id, $type, $status)
    {
        switch ($type) {
            case "msg":
                $column = "msg";
                break;
            case "mtg":
                $column = "mtg";
                break;
            case "ref":
                $column = "ref";
                break;
            default:
                return false;
        }

        $sql  = "INSERT INTO notifications (user,".$column.")
                 VALUES (:user,:status)
                 ON DUPLICATE KEY UPDATE ".$column." = :status
			  	 ";

        $db = self::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user', $id, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_BOOL);
        $stmt->bindValue(':column', $column, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public static function clear($id)
    {
        $sql = "INSERT INTO notifications (user,action)
                VALUES (:user,NULL)
                ON DUPLICATE KEY UPDATE action = NULL 
		  		";
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user', $id, PDO::PARAM_INT);
        $stmt->bindValue(':action', $action, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public static function getNotifications($userId)
    {
        $sql = "SELECT msg,mtg,ref FROM notifications WHERE user = :user";
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
	
}