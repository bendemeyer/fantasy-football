<?php

Class Utilities {
	static getRequestParts($request_uri, $get = false, $post = false) {
		$parts = explode('/', trim(explode('?', $request_uri)[0], "/"));
		$request = new stdClass();
		$request->route = 'landing';
		if (!empty($parts)) {
			$request->route = array_shift($parts);
		}
		$request->method = 'index';
		if (!empty($parts) && count($parts) % 2 == 1) {
			$request->method = array_shift($parts);
		}
		$request->params = array();
		if (!empty($parts)) {
			$request->params['url'] = array();
		}
		while (!empty($parts)) {
			$request->params['url'][array_shift($parts)] = array_shift($parts);
		}
		if (!empty($get)) {
			$request->params['get'] = $get;
		}
		if (!empty($post)) {
			$request->params['post'] = $post;
		}
		
		return $request;
	}
}