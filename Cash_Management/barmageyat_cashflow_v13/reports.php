<?php require_once 'config.php'; require_login();
if(isset($_GET['export']) && $_GET['export']=='csv'){
 header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="barmageyat_cashflow_report.csv"'); $out=fopen('php://output','w');
 fputcsv($out,['Type','Date','Person','Category/Customer','Description/Project','Amount','Currency','Status']);
 foreach($pdo->query("SELECT 'Expense' type,e.expense_date d,u.name person,c.name cat,e.description descr,e.amount,e.currency,e.status FROM expenses e JOIN users u ON u.id=e.paid_by LEFT JOIN categories c ON c.id=e.category_id") as $r) fputcsv($out,$r);
 foreach($pdo->query("SELECT 'Income' type,i.income_date d,u.name person,i.customer_name cat,i.project_service descr,i.amount,i.currency,i.status FROM income i JOIN users u ON u.id=i.received_by") as $r) fputcsv($out,$r);
 exit;
}
$byCat=$pdo->query("SELECT c.name, SUM(e.amount) total FROM expenses e LEFT JOIN categories c ON c.id=e.category_id WHERE e.status='approved' GROUP BY c.name ORDER BY total DESC")->fetchAll();
$revByCustomer=$pdo->query("SELECT COALESCE(customer_name,'Unknown') name, SUM(amount) total FROM income WHERE status='approved' GROUP BY customer_name ORDER BY total DESC")->fetchAll();
include 'includes/header.php'; ?>
<h1>Reports</h1><p><a class="btn" href="?export=csv">Export CSV</a></p><div class="grid"><div class="card"><h3>Expenses by Category</h3><table><tr><th>Category</th><th>Total</th></tr><?php foreach($byCat as $r):?><tr><td><?=h($r['name'])?></td><td><?=number_format($r['total'],2)?></td></tr><?php endforeach;?></table></div><div class="card"><h3>Income by Customer</h3><table><tr><th>Customer</th><th>Total</th></tr><?php foreach($revByCustomer as $r):?><tr><td><?=h($r['name'])?></td><td><?=number_format($r['total'],2)?></td></tr><?php endforeach;?></table></div></div>
<?php include 'includes/footer.php'; ?>
