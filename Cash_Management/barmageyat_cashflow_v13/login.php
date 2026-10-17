<?php require_once 'config.php'; if(is_logged_in()){header('Location: dashboard.php');exit;} $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $email=trim($_POST['email']??''); $password=$_POST['password']??'';
 $stmt=$pdo->prepare('SELECT * FROM users WHERE email=? AND is_active=1'); $stmt->execute([$email]); $u=$stmt->fetch();
 if($u && password_verify($password,$u['password'])){$_SESSION['user_id']=$u['id'];$_SESSION['name']=$u['name'];$_SESSION['role']=$u['role'];header('Location: dashboard.php');exit;} else $error='Invalid login details';
}
include 'includes/header.php'; ?>
<div class="login card center"><img class="logo-big" src="assets/logo.jpg"><h2>Barmageyat Cash Flow</h2><?php if($error):?><p class="status rejected"><?=h($error)?></p><?php endif;?>
<form method="post"><label>Email</label><input name="email" type="email" required><label>Password</label><input name="password" type="password" required><button>Login</button></form><p class="note">Default: admin@barmageyat.com / admin123</p></div>
<?php include 'includes/footer.php'; ?>
