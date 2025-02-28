<?php

$server = "localhost:8889";
$username = 'root';
$password = "root";
$database = 'library-management';

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
	echo "Connection failed!";
}