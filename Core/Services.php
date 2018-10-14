<?php

namespace Core;


class Services
{
	public static function time_elapsed_string( $datetime, $full = false )
	{
		$now  = new \DateTime;
		$ago  = new \DateTime( $datetime );
		$diff = $now->diff( $ago );
		
		$diff->w = floor( $diff->d / 7 );
		$diff->d -= $diff->w * 7;
		
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ( $string as $k => &$v ) {
			if ( $diff->$k ) {
				$v = $diff->$k . ' ' . $v . ( $diff->$k > 1 ? 's' : '' );
			} else {
				unset( $string[ $k ] );
			}
		}
		
		if ( ! $full ) {
			$string = array_slice( $string, 0, 1 );
		}
		
		return $string ? implode( ', ', $string ) . ' ago' : 'just now';
	}
	
	
	public static function getCurrentLocation()
	{
		$ipInfo  = new \DavidePastore\Ipinfo\Ipinfo( [ "token" => 'Config::IPINFO_TOKEN' ] );
		$loc     = $ipInfo->getYourOwnIpSpecificField( \DavidePastore\Ipinfo\Ipinfo::LOC );
		$city    = $ipInfo->getYourOwnIpSpecificField( \DavidePastore\Ipinfo\Ipinfo::CITY );
		$country = $ipInfo->getYourOwnIpSpecificField( \DavidePastore\Ipinfo\Ipinfo::COUNTRY );
		return $loc;
	}
	
	public static function geocode( $address )
	{
		
		// url encode the address
		echo $address = urlencode( $address );
		
		// google map geocode api url
		$url = "http://maps.google.com/maps/api/geocode/json?address={$address}";
		
		$resp_json = file_get_contents( $url );
		$resp      = json_decode( $resp_json, true );
		
		if ( $resp['status'] == 'OK' ) {
			$id                = $resp['results'][0]['place_id'];
			$formatted_address = $resp['results'][0]['formatted_address'];
			$_SESSION['id']    = $id;
			$_SESSION['place'] = $formatted_address;
			
			return true;
			
		} else {
			return false;
		}
	}
	
}