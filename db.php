<?php
// db.php - edit credentials if needed
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'library_db';

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_errno) {
    die("Connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
?>
