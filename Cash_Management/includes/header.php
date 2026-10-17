<?php require_once __DIR__ . '/../config.php'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Barmageyat Cash Flow</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="topbar">
  <div class="brand"><img src="assets/logo.jpg" alt="Barmageyat Logo"><span>Cash Flow Control</span></div>
  <?php if(is_logged_in()): ?>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="expenses.php">Expenses</a>
    <a href="income.php">Income</a>
    <a href="balances.php">Partner Balances</a>
    <a href="reports.php">Reports</a>
    <?php if(is_admin()): ?><a href="admin.php">Admin</a><?php endif; ?>
    <a href="change_password.php">Change Password</a>
    <a href="logout.php">Logout</a>
  </nav>
  <?php endif; ?>
</div>
<main class="container">
