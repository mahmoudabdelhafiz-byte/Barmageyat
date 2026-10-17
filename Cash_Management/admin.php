<?php require_once 'config.php'; require_admin();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add_user'){
 $hash=password_hash($_POST['password'], PASSWORD_DEFAULT);
 $pdo->prepare('INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)')->execute([$_POST['name'],$_POST['email'],$hash,$_POST['role']]); $msg='User added successfully.';
}
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add_category'){
 $pdo->prepare('INSERT INTO categories(name,type) VALUES(?,?)')->execute([$_POST['name'],$_POST['type']]); $msg='Category added successfully.';
}
$users=$pdo->query('SELECT id,name,email,role,is_active,created_at FROM users ORDER BY id DESC')->fetchAll();
$cats=$pdo->query('SELECT * FROM categories ORDER BY type,name')->fetchAll();
include 'includes/header.php'; ?>
<h1>Admin</h1><?php if($msg):?><p class="status approved"><?=h($msg)?></p><?php endif;?>
<div class="grid"><div class="card"><h3>Add User / Partner</h3><form method="post"><input type="hidden" name="action" value="add_user"><label>Name</label><input name="name" required><label>Email</label><input name="email" type="email" required><label>Password</label><input name="password" required><label>Role</label><select name="role"><option value="partner">Partner</option><option value="admin">Admin</option><option value="viewer">Viewer</option></select><button>Add User</button></form></div>
<div class="card"><h3>Add Category</h3><form method="post"><input type="hidden" name="action" value="add_category"><label>Name</label><input name="name" required><label>Type</label><select name="type"><option value="expense">Expense</option><option value="income">Income</option></select><button>Add Category</button></form></div></div>
<div class="card"><h3>Users</h3><table><tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th></tr><?php foreach($users as $u):?><tr><td><?=h($u['name'])?></td><td><?=h($u['email'])?></td><td><?=h($u['role'])?></td><td><?=h($u['is_active'])?></td></tr><?php endforeach;?></table></div>
<div class="card"><h3>Categories</h3><table><tr><th>Name</th><th>Type</th></tr><?php foreach($cats as $c):?><tr><td><?=h($c['name'])?></td><td><?=h($c['type'])?></td></tr><?php endforeach;?></table></div>
<?php include 'includes/footer.php'; ?>
