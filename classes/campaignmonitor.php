<?php

namespace CampaignMonitor;

class CampaignMonitor
{
	/*
	* Your API Key
	*/
	protected static $_api_key = '';

	/*
	* The API Endpoint (ie URL)
	*/	
	protected static $_api_endpoint = '';

	/*
	* A query string to be appended to the API Endpoint
	*/		
	protected static $_query_string = '';

	/*
	* Params to send as POST fields
	*/		
	protected static $_params = '';

	/*
	* Method Type: GET, PUT, or POST
	*/		
	protected static $_method_type = '';

	/*
	* Method name (ie subscribers, clients, etc.)
	*/		
	protected static $_method = '';

	/*
	* How to format the request (leave this as JSON.  The other option is XML but it is not supported in this package)
	*/	
	protected static $_request_format = 'json';

	/*
	* Called automatically when class is instantiated
	*/	
	public static function _init()
	{
		\Config::load('campaignmonitor', true);
		self::$_api_endpoint = \Config::get('campaignmonitor.api_endpoint');
		self::$_api_key	= \Config::get('campaignmonitor.api_key');
	}

	/*
	* Magic method to build query
	*
	* @param	string
	* @param	array
	*/	
	public static function __callStatic($method, $params)
	{
		self::$_method_type = $params[0];
		unset($params[0]);
		
		self::$_method = $method;
		
		foreach($params as $k=>$v)
		{
			if(is_array($v))
			{	
				if(isset($v['qs_params']))
				{
					self::$_query_string = http_build_query($v['qs_params']);					
				}
				else
				{
					self::$_params = json_encode($v);
				}
			}
			
			if(!is_array($v))
			{
				self::$_method .= '/'.$v;
			}
		}
		
		$response = self::_make_request();
		return $response;
	}

	/*
	* Make request to library
	*
	* @return	object
	*/	
	protected static function _make_request()
	{
		// create a new cURL resource
		$curl = curl_init();
		$request = self::$_api_endpoint.self::$_method.'.'.self::$_request_format.self::$_query_string;
		curl_setopt($curl, CURLOPT_URL, $request);
		
		if(self::$_method_type == 'GET')
		{			
			curl_setopt($curl, CURLOPT_HTTPGET, true);
		}
		
		if(self::$_method_type == 'POST')
		{
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			curl_setopt($curl, CURLOPT_POSTFIELDS, self::$_params);				
		}

		if(self::$_method_type == 'PUT')
		{
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_POSTFIELDS, self::$_params);
		}

		$username = self::$_api_key;
		
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:");	
		curl_setopt($curl, CURLOPT_HEADER, 1);			
		// set to return the data as a string
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        		
		// Run the query and get a response
		$response = curl_exec($curl);
		
		// close cURL resource, and free up system resources
		curl_close($curl);

		// Return the response
		return self::_parse_response($response);			
	}

	/*
	* Parse response from the gateway
	*
	* @param	string
	* @return	object
	*/	
	protected static function _parse_response($response)
	{	
		$response = html_entity_decode($response);
		$exploded = explode('Content-Length', $response);
		$http = explode(' ', $exploded[0]);
		$response_code = $http[1];		
		$first_brack = strpos($exploded[1], '{');
		$response = substr($exploded[1], $first_brack);
		if (strpos('[', $response) !== false)
		{
			$response = str_replace(']', '', $response);
			$response = str_replace('[', '', $response);			
		}
			
		//Create an array to return
		$final = array();
		
		//Success or failure?
		$final['http_response_code'] = $response_code;
		
		//Check if the status code is a 200 class (success)
		if(strpos($response_code, '2') !== false)
		{
			$status = 'success';
		}
		else
		{
			$status = 'failure';
		}
			
		$final['status'] = $status;
		
		
		if(self::$_method_type == 'GET')
		{
			if($status == 'success')
			{
	
				$final['data'] = self::_parse_success($response);
			}
			else
			{	
				$final['data'] = self::_parse_failure($response);
			}
		}
		
		if(self::$_method_type == 'POST')
		{
			if($status == 'success')
			{
				$final['data'] = 'The Action was Successful!';
			}
			else
			{
				$final['data'] = '';
			}
		}
		
		if(self::$_method_type == 'PUT')
		{
			if($status == 'success')
			{
	
				$final['data'] = 'The Action was Successful!';
			}	
		}
		
		return (object) $final;
	}

	/*
	* Parse a success response
	* @param	mixed
	* @response	array
	*/	
	protected static function _parse_success($response)
	{
		$data = array();
		if(is_array($response))
		{
			$response_data = explode('{', str_replace('}', '', str_replace('"', '', $response[1])));
		}
		else
		{
			$response_data = explode('{', str_replace('}', '', str_replace('"', '', $response)));
		}
		
		unset($response_data[0]);
		
		foreach($response_data as $k=>$v)
		{
			$pieces = explode(',', $v);
			
			$divided = array();
			foreach($pieces as $i=>$p)
			{
				if(empty($p))
				{
					unset($i);
					break 1;
				}		
				$piece_ex = explode(':', $p);
				$key = strtolower($piece_ex[0]);
				$value = $piece_ex[1];
				$divided[$key] = $value;
			}
			$data[$k] = $divided;
		}
		
		return $data;
	}

	/*
	* Parse a failure response
	* @param	string
	* @response	array
	*/	
	protected static function _parse_failure($response)
	{
		$data = array();
		$str_replaced = str_replace('"', '', str_replace('}', '', str_replace('{', '', $response)));
		$exploded = explode(',', $str_replaced);
		
		foreach($exploded as $k=>$v)
		{
			$pieces = explode(':', $v);
			$key = strtolower($pieces[0]);
			$value = $pieces[1];
			$data[$key] = $value;
		}
		return $data;
	}
}