<?php
defined('BASEPATH') OR exit("No direct script access allowed");

$config['mail'] = array(
    'protocol' => 'smtp',
    'smtp_host' => 'mail.ainra.com',    // My host name
    'smtp_port' => 465,
    'smtp_user' => 'no-reply@ainra.com',   // My username
    'smtp_pass' => '6As$p28o',   // My password
    'charset' => 'iso-8859-1',
    'wordwrap' => TRUE,
    'smtp_timeout' => 4,
    'newline' => "\r\n",
    'crlf' => "\r\n",
    'mailtype' => "text"
);
?>