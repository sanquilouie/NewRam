<?php
// connection.php

// Database configuration
$dbhost = "localhost";
$dbuser = "u916947975_ramstardb";
$dbpass = "?i7PuPc[0@w:";
$dbname = "u916947975_ramstardb";

// Mysqli connection
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
	die("Failed to connect using MySQLi: " . mysqli_connect_error());
}

// PDO connection
try {
	$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	// Set the PDO error mode to exception
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Failed to connect using PDO: " . $e->getMessage());
}
?>