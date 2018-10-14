<?php

namespace Core;


class Router
{
	protected $routes = [];
	protected $params = [];
	
	public function setRoutes( $route, $explicitControllerAction = [] )
	{
		// Convert the route to a regular expression: escape forward slashes
		$route = preg_replace('/\//', '\\/', $route);
		
		// Convert variables e.g. {controller}
		$route = preg_replace('/\{([a-z]+)\}/', '(?<\1>[a-z-]+)', $route);
		//		$route = preg_replace( '/\{\\\\([^\}]+)\}/', '(?<id>\\\\\1)', $route );

		// Convert variables with custom regular expressions e.g. {id:\d+}
		$route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?<\1>\2)', $route);
		
		// Add start and end delimiters, and case insensitive flag
		$route = '/^' . $route . '$/i';
		
		$this->routes[ $route ] = $explicitControllerAction;

//		 echo "<pre>";
//		 var_dump($this->routes);
//		 echo "</pre>";
		
	}
	
	
	public function match( $url )
	{
		
		foreach ( $this->routes as $route => $valuesControllerAction ) {

			if ( preg_match( $route, $url, $controllerAction ) ) {

				foreach ( $controllerAction as $name => $value ) {

					if ( is_string( $name ) ) {
						$valuesControllerAction[ $name ] = $value;
					}
				}
				
				$this->params = $valuesControllerAction; // if explicitly defined in /public/index.php
				
				return true;
			}
		}
		
		return false;
	}
	
	
	public function dispatch( $url )
	{
		$url = $this->removeQueryString( $url );

		if ( $this->match( $url ) ) {
			$controller = $this->convertToPascalCase( $this->params['controller'] );
			$controller = $this->getNamespace() . $controller;
			
			if ( class_exists( $controller ) ) {
				$targetController = new $controller( $this->params );
				$action           = $this->convertToCamelCase( $this->params['action'] );
				if ( preg_match( '/action$/i', $action ) == 0 ) {
					$targetController->$action();
				} else {
					throw new \Exception( "Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method" );
				}
			} else {
				throw new \Exception( "Controller '" . $this->params['controller'] . "' not found" );
			}
		
		} else {
			throw new \Exception("No such route: '".$url."'",404);
		}
	}
	
	
	protected function removeQueryString( $url )
	{
		if ( $url != '' ) {
			$urlParts = explode( '&', $url );
			if ( strpos( $urlParts[0], '=' ) === false ) {
				$url = $urlParts[0];
			} else {
				$url = '';
			}
		}
		
		return $url;
	}
	
	
	protected function convertToPascalCase( $str )
	{
		return str_replace( ' ', '', ucwords( str_replace( '-', ' ', $str ) ) );
	}
	
	
	protected function convertToCamelCase( $str )
	{
		return lcfirst( $this->convertToPascalCase( $str ) );
	}
	
	
	protected function getNamespace()
	{
		$namespace = "App\Controllers\\";
		if ( array_key_exists( 'namespace', $this->params ) ) {
			$namespace .= $this->params['namespace'] . '\\';
		}
		
		return $namespace;
	}
	
}