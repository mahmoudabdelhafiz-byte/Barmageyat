<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$db_host = "localhost";
$db_name = "barmagey_Cash_Managment";
$db_user = "barmagey_barmagey";
$db_pass = "Ham@2026";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("DB Error: " . mysqli_connect_error());
}

echo "Database connected successfully";
?>