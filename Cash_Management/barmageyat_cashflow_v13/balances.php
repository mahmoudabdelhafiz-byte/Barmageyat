<?php require_once 'config.php'; require_login();
$users=$pdo->query("SELECT id,name,email,role FROM users WHERE role IN('admin','partner') ORDER BY name")->fetchAll();
$totalIncome=$pdo->query("SELECT COALESCE(SUM(amount),0) t FROM income WHERE status='approved'")->fetch()['t'];
$count=count($users); $share=$count?($totalIncome/$count):0;
include 'includes/header.php'; ?>
<h1>Partner Balances</h1><div class="card"><p class="note">Simple equal-share calculation: approved company income is divided equally across active partners/admins. Approved expenses paid by each partner are compared against their income share.</p><table><tr><th>Partner</th><th>Email</th><th>Approved Expenses Paid</th><th>Income Share</th><th>Balance</th><th>Meaning</th></tr><?php foreach($users as $u): $st=$pdo->prepare("SELECT COALESCE(SUM(amount),0) t FROM expenses WHERE status='approved' AND paid_by=?");$st->execute([$u['id']]);$paid=$st->fetch()['t'];$bal=$paid-$share;?><tr><td><?=h($u['name'])?></td><td><?=h($u['email'])?></td><td><?=number_format($paid,2)?></td><td><?=number_format($share,2)?></td><td><?=number_format($bal,2)?></td><td><?= $bal>=0 ? 'Company owes / partner overpaid' : 'Partner contribution gap' ?></td></tr><?php endforeach;?></table></div>
<?php include 'includes/footer.php'; ?>
