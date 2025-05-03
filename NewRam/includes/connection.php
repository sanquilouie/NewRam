<?php
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // Local
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "ramstardb";
} else {
    // Live
    $dbhost = "localhost";
	$dbuser = "u916947975_ramstardb";
	$dbpass = "?i7PuPc[0@w:";
	$dbname = "u916947975_ramstardb";
}

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
	die("Failed to connect using MySQLi: " . mysqli_connect_error());
}

mysqli_query($conn, "SET time_zone = 'Asia/Manila'");

try {
    // Connect using PDO
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set the time zone for the PDO connection
    $pdo->exec("SET time_zone = 'Asia/Manila'");
} catch (PDOException $e) {
    die("Failed to connect using PDO: " . $e->getMessage());
}
?>