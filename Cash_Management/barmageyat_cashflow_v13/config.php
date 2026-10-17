<?php
// Update these values after creating MySQL database on Mocha/cPanel
$db_host = 'localhost';
$db_name = 'barmageyat_cashflow';
$db_user = 'YOUR_DB_USER';
$db_pass = 'YOUR_DB_PASSWORD';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('Database connection failed. Please check config.php');
}

if (session_status() === PHP_SESSION_NONE) session_start();

function is_logged_in() { return isset($_SESSION['user_id']); }
function is_admin() { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function require_login() { if (!is_logged_in()) { header('Location: login.php'); exit; } }
function require_admin() { require_login(); if (!is_admin()) { header('Location: dashboard.php'); exit; } }
function h($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
?>
