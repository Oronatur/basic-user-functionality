<?php
session_name("id");
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('display_startup_errors', 0);
ini_set('error_log', dirname(__DIR__).'\error_log\log.txt');
error_reporting(E_ALL);
ini_set( 'session.use_only_cookies', 1);
ini_set( 'session.use_trans_sid', 0);
?>