<?php

namespace Core;

abstract class Controller
{
	
	protected $route_params = [];   // route variables, like {id:...}, {token:...} and so on
	
	
	public function __construct( $route_params )
	{
		$this->route_params = $route_params;
	}
	
	
	public function __call( $name, $args )
	{
		$method = $name . 'Action';
		
		if ( method_exists( $this, $method ) ) {
			if($this->before() !== false ) {
				call_user_func( [ $this, $method ], $args );
				$this->after();
			}
		} else
			throw new \Exception("No '".$method."' method in controller '". get_class($this)."'");
	}
	
	
	public function redirect($url) {
		header('Location: http://'.$_SERVER['HTTP_HOST'].$url, true, 303);
		exit;
	}
	
	
	protected function before()
	{
		//	$_SESSION['return_to'] = '/';
	}
	
	
	protected function after()
	{
	}
	
	
}