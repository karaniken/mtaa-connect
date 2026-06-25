<?php
$conn = mysqli_connect("localhost", "root", "34000618", "mysql");

if($conn) {
    echo "<h1 style='color: purple;'>Connected Successfully to MySQL!</h1>";
} else {
    echo "<h1 style='color: red;'>Connection Failed: " . mysqli_connect_error() . "</h1>";
}
?>
