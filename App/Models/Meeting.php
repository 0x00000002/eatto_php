<?php

namespace App\Models;
use PDO;

class Meeting  extends \Core\Model
{
	
	public static function getUserMeetings($id)
	{
		$sql = "

          SELECT
            s.meeting_id,
            s.opponent_id,
            refs.is_reviewed,
            u.name AS opponent_name,
            r.refs AS refs,
            s.guest_id,
            s.host_id,
            s.info AS opponent_details,
            f.ref AS photo_name,
            s.proposal,
            s.meet_on,
            s.is_confirmed
          FROM (
                 SELECT
                   o.meeting_id,
                   o.guest_id,
                   o.host_id,
                   o.oppo AS opponent_id,
                   p.user_name AS opponent_name,
                   p.info,
                   p.photo_id,
                   o.proposal,
                   o.meet_on,
                   o.is_confirmed
                 FROM
                   (
                     SELECT
                       m.id AS meeting_id,
                       if (m.guest_id = :user_id, m.host_id, m.guest_id ) AS oppo,
                       m.guest_id,
                       m.host_id,
                       m.proposal,
                       m.meet_on,
                       m.is_confirmed
                     FROM meetings m
                     WHERE guest_id = :user_id OR host_id = :user_id
                   ) as o
                   LEFT JOIN profile p ON o.oppo = p.user_id
               ) as s
            LEFT JOIN users u     ON u.id   = s.opponent_id
            LEFT JOIN hosts h     ON h.user = s.opponent_id
            LEFT JOIN photos f    ON f.id   = s.photo_id
            LEFT JOIN
            (
              SELECT
                COUNT(DISTINCT ref) AS refs,
                user,
                author
              FROM refs
              GROUP BY user
            ) AS r
              ON r.user = s.opponent_id
            LEFT JOIN
            (
              SELECT
                user,
                author,
                meeting,
                1 AS is_reviewed
              FROM refs
              WHERE author = :user_id
            ) refs
              ON refs.meeting = s.meeting_id
          ORDER BY meet_on DESC
		";
		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':user_id', $id, PDO::PARAM_INT );
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public static function view($userId,$meetingId)
	{
		$sql = "
SELECT
            s.meeting_id,
            s.host_id,
            s.user_id,
            s.user_name,
            l.name AS location,
            s.info AS user_details,
            f.ref AS photo_name,
            s.proposal,
            s.meet_on,
            s.is_confirmed,
            if (r.author = :user_id, 1,0) AS is_reviewed,
            r.ref AS review
          FROM
            (
              SELECT
                o.meeting_id,
                o.oppo AS user_id,
                p.user_name,
                p.info,
                o.host_id,
                p.address,
                p.photo_id,
                o.proposal,
                o.meet_on,
                o.is_confirmed
              FROM
                (
                  SELECT
                    m.id AS meeting_id,
                    if (m.guest_id = :user_id, m.host_id, m.guest_id ) AS oppo,
                    m.host_id,
                    m.proposal,
                    m.meet_on,
                    m.is_confirmed
                  FROM meetings m
                  WHERE m.id = :meeting_id
                        AND ( guest_id = :user_id OR host_id = :user_id )
                ) as o
                LEFT JOIN profile p   ON p.user_id = o.oppo
            ) as s
            LEFT JOIN hosts h     ON h.user = s.user_id
            LEFT JOIN photos f    ON f.id = s.photo_id
            LEFT JOIN refs r ON r.user = s.user_id AND r.meeting = s.meeting_id
            LEFT JOIN locations l ON h.location_id = l.id
                ";

		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':meeting_id', $meetingId, PDO::PARAM_INT );
		$stmt->bindValue( ':user_id', $userId, PDO::PARAM_INT );
		$stmt->execute();
		return $stmt->fetch();
	}

    public static function new( $guest, $host, $datetime, $text )
    {
        $sql = "INSERT INTO meetings
					( guest_id, host_id, meet_on, proposal )
				VALUES
					( :guest, :host, :datetime, :text )";

        $db   = self::getDB();
        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':guest', $guest, PDO::PARAM_INT );
        $stmt->bindValue( ':host', $host, PDO::PARAM_INT );
        $stmt->bindValue( ':datetime', $datetime, PDO::PARAM_STR );
        $stmt->bindValue( ':text', $text, PDO::PARAM_STR );
        return $stmt->execute();
    }

    public static function addReference( $user, $author, $text, $meeting )
    {
    	
    	// TODO: check if meeting_id and users are real
    	
        $sql = "INSERT INTO refs
					( user, author, ref, meeting )
				VALUES
					( :user, :author, :text, :meeting )";

        $db   = self::getDB();
        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':user', $user, PDO::PARAM_INT );
        $stmt->bindValue( ':author', $author, PDO::PARAM_INT );
        $stmt->bindValue( ':text', $text, PDO::PARAM_STR );
        $stmt->bindValue( ':meeting', $meeting, PDO::PARAM_STR );
        return $stmt->execute();
    }
	
	public static function update($myId, $meetingId, $status)
	{
		$status == 'confirm' ? 1 : -1;
		$sql = "UPDATE meetings SET is_confirmed = :status WHERE id = :id AND host_id = :host_id";
		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue(':id', $meetingId, PDO::PARAM_INT);
		$stmt->bindValue(':host_id', $myId, PDO::PARAM_INT);
		$stmt->bindValue(':status', $status, PDO::PARAM_INT);
		return $stmt->execute();
	}
	
	public static function getOpponent($meetingId, $myId)
	{
		$sql = "
			SELECT if( :user_id = host_id, guest_id, host_id ) as user_id
			FROM meetings
			WHERE id = :id
";
		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue(':id', $meetingId, PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $myId, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}
	
	public static function cleanOld($id)
	{
		$sql = "DELETE FROM meetings
				WHERE (host_id = :id OR guest_id = :id)
				  AND meet_on < DATE_SUB(NOW(),INTERVAL 1 WEEK)
				  AND is_confirmed = 0
				";
		$db   = self::getDB();
		$stmt = $db->prepare( $sql );
		$stmt->bindValue( ':id', $id, PDO::PARAM_INT );
		return $stmt->execute();
	}
	
}