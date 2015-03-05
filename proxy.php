<?php

function requestURL($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

	parse_str(file_get_contents('php://input'), $post_fields);
	if (empty($post_fields))
		$post_fields = $_POST;
	$post_fields_str = json_encode($post_fields); //http_build_query($post_fields);

	if (!empty($post_fields_str)){
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields_str);
	}

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); // do not use cache
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); // 0 for no timeout
	//curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	$parsed_url = parse_url($url);
	curl_setopt($ch, CURLOPT_REFERER, $parsed_url['scheme'].'://'.$parsed_url['host']);

	$headers = array(
		//'Host: '.$parsed_url['host'].':'.$parsed_url['port'],
		//'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		//'Accept-Language: en-US,en;q=0.5',
		//'Accept-Encoding: gzip, deflate',
		'Content-Type: application/json',
		'Content-Length: '.strlen($post_fields_str)
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	curl_setopt($ch, CURLOPT_VERBOSE, true);
	//curl_setopt($ch, CURLOPT_STDERR, fopen(dirname(__FILE__) . "/proxy_headers.txt", "w+"));

	$result = curl_exec($ch);
	//print_r(curl_getinfo($ch));
	curl_close($ch);
	//exit;
	return $result;
}

function echoJSON($object, $callback){
	if (isset($callback)){
		header('Content-type: text/javascript');
		echo $callback.'('.json_encode($object).');';
	}
	else{
		header('Content-type: application/json');
		echo json_encode($object);
	}
}

$url = $_GET['url'];
preg_match("/^http:\/\/www.busweb.hamilton.ca/", $url, $matches);
if (count($matches) === 0)
	exit;

echoJSON(requestURL($url), $_REQUEST['callback']);

?>