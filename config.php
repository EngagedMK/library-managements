<?php

$server = "localhost";
$username = 'root';
$password = "";
$database = 'library-management';

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
	echo "Connection failed!";
}