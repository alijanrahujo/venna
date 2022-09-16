<?php
    function replace($key, $replace_language = []){
        $CI = &get_instance();
        $CI->load->model('Lang_Model');
        $string = $CI->Lang_Model->replaceLang($key, $replace_language);
        echo $string;
    }
?>