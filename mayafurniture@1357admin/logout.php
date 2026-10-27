<?php

session_start();
error_reporting(E_ALL);

require '../config/config.php';
require 'functions/authentication.php';

$auth = new AdminManager();
$auth->SignOut();

?>