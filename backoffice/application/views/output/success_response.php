<?php
	$response = array('status' => "Success");
	if (isset($message)) {
		if (is_array($message)) {
			$error['message'] = $message;
		} else {
			$error['message'][] = $message;
		}
	}
	if (isset($response_data)) {
		$response['data'] = $response_data;
	}
	if(isset($additional_params) && $additional_params != ''){
		foreach($additional_params as $key=>$val){
			$response[$key] = $val;
		}
	}
	$json_text = json_encode($response);
	
	echo $json_text;
	
	exit;
?>