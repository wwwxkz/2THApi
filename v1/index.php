<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'calls/platform.php';
require_once 'calls/report.php';
require_once 'calls/user.php';

class API
{
	public static function open($request)
	{
		$url = explode('/', $request['url']);
		
		$class = ucfirst($url[0]);
		array_shift($url);

		$method = $url[0];
		array_shift($url);

		$parameters = array();
		$parameters = $url;

		try {
			if (class_exists($class)) {
				if (method_exists($class, $method)) {
					$retorno = call_user_func_array(array(new $class, $method), $parameters);

					return json_encode(array('status' => 'success', 'data' => $retorno));
				} else {
					return json_encode(array('status' => 'error', 'data' => 'Method does not exist'));
				}
			} else {
				return json_encode(array('status' => 'error', 'data' => 'Class does not exist'));
			}	
		} catch (Exception $e) {
			return json_encode(array('status' => 'error', 'data' => $e->getMessage()));
		}
		
	}
}

if (isset($_REQUEST)) {
	echo API::open($_REQUEST);
}