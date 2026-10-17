<?php
require_once 'config.php';
require_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password'])) {
        $message = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $message = "New passwords do not match.";
    } else {

        $hash = password_hash($new, PASSWORD_DEFAULT);

        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hash, $_SESSION['user_id']]);

        $message = "Password updated successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <style>
        body{
            font-family: Arial;
            background:#081b29;
            color:white;
            padding:40px;
        }

        .box{
            width:400px;
            margin:auto;
            background:#0f2d44;
            padding:30px;
            border-radius:10px;
        }

        input{
            width:100%;
            padding:10px;
            margin-bottom:15px;
        }

        button{
            padding:10px 20px;
            background:#20c997;
            border:none;
            color:white;
            cursor:pointer;
        }

        .msg{
            margin-bottom:15px;
            color:#ffc107;
        }
    </style>
</head>
<body>

<div class="box">

<h2>Change Password</h2>

<?php if($message): ?>
<div class="msg"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST">

<input type="password" name="current_password" placeholder="Current Password" required>

<input type="password" name="new_password" placeholder="New Password" required>

<input type="password" name="confirm_password" placeholder="Confirm New Password" required>

<button type="submit">Update Password</button>

</form>

</div>

</body>
</html>