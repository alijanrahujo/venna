<?php
function is_weekend($your_date) {
    $week_day = date('w', strtotime($your_date));
    //returns true if Sunday or Saturday else returns false
    return ($week_day == 0 || $week_day == 6);
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateOneLetter($length = 1) {
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function validate_password($password) {
    $condition = True;
    $message = "Password must be at least ";

	if(strlen($password) < 8) {
        $condition = False;
        $message .= "8 character";
	}

	if(!preg_match("#[0-9]+#", $password)) {
        $condition = False;
        if(strlen($password) < 8){
            $message .= ", 1 digit";
        }else{
            $message .= "1 digit";
        }
	}

	if(!preg_match("#[a-z]+#", $password)) {
        if(strlen($password) < 8){
            $message .= ", 1 small letter";
        }else{
            $message .= "1 small letter";
        }
	}

	if(!preg_match("#[A-Z]+#", $password)) {
        $condition = False;
        if(strlen($password) > 7 && preg_match("#[0-9]+#", $password) && !preg_match("#[a-z]+#", $password)){
            $message .= ", 1 upper letter";
        }else if(strlen($password) < 8 && !preg_match("#[0-9]+#", $password) && preg_match("#[a-z]+#", $password)){
            $message .= ", 1 upper letter";
        }else if(strlen($password) < 8 && preg_match("#[0-9]+#", $password) && !preg_match("#[a-z]+#", $password)){
            $message .= ", 1 upper letter";
        }else{
            $message .= "1 upper letter";
        }
	}

	return $message;

}

function debugPrintArr($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}
?>