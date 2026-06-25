<?php
$host = 'localhost';
$user = 'root';
$password = '34000618'; // 
$database = 'mtaa_db';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
