<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


$hostname = "localhost:3306";
$username = "grp11";
$password = "thah9Oow";
$database = "db_grp11";

$conn = mysqli_connect($hostname, $username, $password, $database);

mysqli_set_charset($conn, "utf8");



?>