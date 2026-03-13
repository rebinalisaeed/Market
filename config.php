<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'market_system';

$conn = mysqli_connect($host, $username, $password, $database);

if(!$conn) {
    die("پەیوەندی بە داتابەیسەوە شکستی هێنا: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
