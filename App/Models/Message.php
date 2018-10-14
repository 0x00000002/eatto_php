<?php


namespace App\Models;
use PDO;
use Core\Services;

class Message  extends \Core\Model
{
	
	public static function getRecent($userId) {
		$chats = self::findChats($userId);
		return self::timeAgo($chats);
	}
	
	public static function findChats($userId)
	{
		$sql = "
			SELECT
			  full.message_id,
			  full.chat_id,
			  full.user_id,
			  full.sender_id,
			  p.ref AS photo,
			  full.user_name,
			  full.text,
			  full.sent_on,
			  full.is_read
			  FROM
			    (
			      SELECT
			        recent.message_id,
			        recent.chat_id,
			        recent.user_id,
			        recent.sender as sender_id,
			        p.user_name,
			        p.photo_id,
			        recent.text,
			        recent.sent_on,
			        recent.is_read
			        FROM
			          (
			            SELECT id AS message_id, chat as chat_id, user_id, msg.sender, text, sent_on, is_read
			              FROM
			                (
			                  SELECT max(sent) sent_on, m1.sender, if(m1.sender = :user, m1.receiver, m1.sender) AS
			                  user_id
			                    FROM messages m1
			                      WHERE m1.sender = :user
			                            OR m1.receiver = :user
			                    GROUP BY m1.chat
			                ) pairs
			                LEFT JOIN messages msg
			                  ON msg.sent = pairs.sent_on
			          ) as recent
			        LEFT JOIN profile p
			            ON p.user_id = recent.user_id
			    ) as full
			  LEFT JOIN hosts h
			      ON h.user = full.user_id
			  LEFT JOIN photos p
			      ON p.id = full.photo_id
			  ORDER BY sent_on DESC
		";
		
		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':user', $userId, PDO::PARAM_INT );
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public static function getChat($opponent,$logged_user)
	{
		self::markRead($opponent,$logged_user);
	
		$sql = "
    		SELECT message_id, full.sender_id AS sender_id, p.user_name AS sender_name, f.ref as photo, text, sent_on
            FROM (
              SELECT
                id AS message_id,
                if(sender = :opponent, :opponent, sender ) as sender_id,
                text,
                sent as sent_on
              FROM messages
              WHERE
                chat = concat(:opponent,'-',:logged_user)
                OR chat = concat(:logged_user,'-',:opponent)
            ) as full
            LEFT JOIN hosts h ON h.user = full.sender_id
            LEFT JOIN profile p ON p.user_id = sender_id
            LEFT JOIN photos f ON f.id = p.photo_id
            ORDER BY sent_on DESC
    		";

		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':opponent', $opponent, PDO::PARAM_INT );
		$stmt->bindValue( ':logged_user', $logged_user, PDO::PARAM_INT );
		$stmt->execute();
		$messages = $stmt->fetchAll();
		return self::timeAgo($messages);
	}
	
	public static function newChat( $sender, $receiver )
	{
		$chat = $sender.'-'.$receiver;
		$chat_reversed = $receiver.'-'.$sender;

		$sqlInsert = "SELECT * FROM messages WHERE chat = :chat" ;
		$db   = self::getDB();
		$stmt = $db->prepare( $sqlInsert);
		$stmt->bindValue( ':chat', $chat, PDO::PARAM_STR );
		$stmt->execute();
		if($stmt->fetch()) {
			return $chat;
		}
		else {
			return $chat_reversed;
		}
	}
	
	public static function send($sender,$receiver,$text)
	{
		$chat = self::newChat($sender, $receiver);
		$sql = "
			INSERT INTO messages (chat, receiver, sender, text, sent)
			VALUES (:chat, :from, :to, :text, :sent)
    		";
		
		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':chat', $chat, PDO::PARAM_INT );
		$stmt->bindValue( ':from', $sender, PDO::PARAM_INT );
		$stmt->bindValue( ':to', $receiver, PDO::PARAM_INT );
		$stmt->bindValue( ':sent',  $sent = date("Y-m-d H:i:s"), PDO::PARAM_STR );
		$stmt->bindValue( ':text', $text, PDO::PARAM_STR );
		return $stmt->execute();
	}
	
	public static function markRead( $sender, $receiver )
	{
		$chat = $sender.'-'.$receiver;
		$chat_reversed = $receiver.'-'.$sender;
		
		$sql = "
			UPDATE messages SET is_read = 1
			WHERE chat = :chat
				OR chat = :chat_reversed
				AND receiver = :receiver
    		";
		
		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':chat', $chat, PDO::PARAM_STR );
		$stmt->bindValue( ':chat_reversed', $chat_reversed, PDO::PARAM_STR );
		$stmt->bindValue( ':receiver', $receiver, PDO::PARAM_INT );
		return $stmt->execute();
		
	}
	
	public static function timeAgo( $messages )
	{
		$i = 0;
		foreach ($messages as $msg)
			$messages[$i++]['sent_on'] = Services::time_elapsed_string($msg['sent_on']);
		return $messages;
		
	}
	
	
	
	public function edit($text) // TODO: edit text of message - not in MVP
	{
		return;
	}
	
	public function erase() // TODO: erase message - not in MVP
	{
		return;
	}
	

}