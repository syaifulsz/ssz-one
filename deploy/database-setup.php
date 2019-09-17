<?php
$servername = "ssz_one_db";
$username = "root";
$password = "root";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query( 'DROP DATABASE ssz_one' );
$conn->query( 'CREATE DATABASE ssz_one' );

echo PHP_EOL . "FRESH DATABASE CREATED!" . PHP_EOL;

$conn->close();
?>
