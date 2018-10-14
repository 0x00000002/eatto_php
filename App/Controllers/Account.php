<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;


class Account extends Controller
{
	
	public function validateEmailAction()
	{
		$url = $_SERVER['REQUEST_URI'];
		$parts = parse_url($url);
		parse_str($parts['query'], $query);
		$email = $query['email'];
		$ignore_id = $query['ignore_id'] ?? null;
		
		$isValid = ! User::emailExists( $email, $ignore_id);
		
		header('Content-Type: application/json');
		echo json_encode($isValid);
	}
	
}