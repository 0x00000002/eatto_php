<?php

namespace Core;
use PDO;

abstract class Model
{
	protected static function getDB()
	{
		static $db = null;
		
		if ( $db === null ) {

			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8";
			$db = new PDO($dsn,DB_USER,DB_PASSWORD);
			
			// Setting "throw an exception" mode
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		}
		return $db;
	}
}