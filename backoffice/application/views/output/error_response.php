<?php
	$error = array('status' => "Failed");
	if (isset($message)) {
		if (is_array($message)) {
			$error['message'] = $message;
		} else {
			$error['message'][] = $message;
		}
	}
	if (isset($response_data)) {
		$error['data'] = $response_data;
	}
	if(isset($additional_params) && $additional_params != ''){
		foreach($additional_params as $key=>$val){
			$error[$key] = $val;
		}
	}
	$json_text = json_encode($error);

	echo $json_text;
	exit;
?>