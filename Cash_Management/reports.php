<?php 
require_once 'config.php'; 
require_login();

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

$expenseByCategory = $pdo->prepare("
    SELECT 
        COALESCE(c.name, 'Uncategorized') AS category,
        SUM(CASE 
            WHEN e.egp_amount IS NULL OR e.egp_amount = 0 THEN e.amount 
            ELSE e.egp_amount 
        END) AS total
    FROM expenses e
    LEFT JOIN categories c ON c.id = e.category_id
    WHERE e.status = 'approved'
    AND e.expense_date BETWEEN ? AND ?
    GROUP BY c.name
    ORDER BY total DESC
");
$expenseByCategory->execute([$from, $to]);
$expenseByCategory = $expenseByCategory->fetchAll();

$incomeByCategory = $pdo->prepare("
    SELECT 
        COALESCE(c.name, 'Uncategorized') AS category,
        SUM(CASE 
            WHEN i.egp_amount IS NULL OR i.egp_amount = 0 THEN i.amount 
            ELSE i.egp_amount 
        END) AS total
    FROM income i
    LEFT JOIN categories c ON c.id = i.category_id
    WHERE i.status = 'approved'
    AND i.income_date BETWEEN ? AND ?
    GROUP BY c.name
    ORDER BY total DESC
");
$incomeByCategory->execute([$from, $to]);
$incomeByCategory = $incomeByCategory->fetchAll();

$expenseByPartner = $pdo->prepare("
    SELECT 
        u.name AS partner,
        SUM(CASE 
            WHEN e.egp_amount IS NULL OR e.egp_amount = 0 THEN e.amount 
            ELSE e.egp_amount 
        END) AS total
    FROM expenses e
    JOIN users u ON u.id = e.paid_by
    WHERE e.status = 'approved'
    AND e.expense_date BETWEEN ? AND ?
    GROUP BY u.name
    ORDER BY total DESC
");
$expenseByPartner->execute([$from, $to]);
$expenseByPartner = $expenseByPartner->fetchAll();

$incomeByReceiver = $pdo->prepare("
    SELECT 
        u.name AS partner,
        SUM(CASE 
            WHEN i.egp_amount IS NULL OR i.egp_amount = 0 THEN i.amount 
            ELSE i.egp_amount 
        END) AS total
    FROM income i
    JOIN users u ON u.id = i.received_by
    WHERE i.status = 'approved'
    AND i.income_date BETWEEN ? AND ?
    GROUP BY u.name
    ORDER BY total DESC
");
$incomeByReceiver->execute([$from, $to]);
$incomeByReceiver = $incomeByReceiver->fetchAll();

$totalIncomeStmt = $pdo->prepare("
    SELECT COALESCE(SUM(CASE 
        WHEN egp_amount IS NULL OR egp_amount = 0 THEN amount 
        ELSE egp_amount 
    END), 0) total
    FROM income
    WHERE status = 'approved'
    AND income_date BETWEEN ? AND ?
");
$totalIncomeStmt->execute([$from, $to]);
$totalIncome = $totalIncomeStmt->fetch()['total'];

$totalExpenseStmt = $pdo->prepare("
    SELECT COALESCE(SUM(CASE 
        WHEN egp_amount IS NULL OR egp_amount = 0 THEN amount 
        ELSE egp_amount 
    END), 0) total
    FROM expenses
    WHERE status = 'approved'
    AND expense_date BETWEEN ? AND ?
");
$totalExpenseStmt->execute([$from, $to]);
$totalExpenses = $totalExpenseStmt->fetch()['total'];

include 'includes/header.php'; 
?>

<h1>Reports</h1>

<div class="card">
    <h3>Report Filter</h3>

    <form method="get">
        <div class="grid">
            <div>
                <label>From</label>
                <input type="date" name="from" value="<?= h($from) ?>">
            </div>

            <div>
                <label>To</label>
                <input type="date" name="to" value="<?= h($to) ?>">
            </div>
        </div>

        <button>Apply Filter</button>
    </form>
</div>

<div class="grid">

    <div class="kpi">
        <div class="label">Approved Income</div>
        <div class="value"><?= number_format($totalIncome, 2) ?> EGP</div>
    </div>

    <div class="kpi">
        <div class="label">Approved Expenses</div>
        <div class="value"><?= number_format($totalExpenses, 2) ?> EGP</div>
    </div>

    <div class="kpi">
        <div class="label">Net Cash</div>
        <div class="value"><?= number_format($totalIncome - $totalExpenses, 2) ?> EGP</div>
    </div>

</div>

<div class="grid">

    <div class="card">
        <h3>Expenses by Category</h3>
        <table>
            <tr>
                <th>Category</th>
                <th>Total</th>
            </tr>

            <?php foreach($expenseByCategory as $r): ?>
                <tr>
                    <td><?= h($r['category']) ?></td>
                    <td><?= number_format($r['total'], 2) ?> EGP</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h3>Income by Category</h3>
        <table>
            <tr>
                <th>Category</th>
                <th>Total</th>
            </tr>

            <?php foreach($incomeByCategory as $r): ?>
                <tr>
                    <td><?= h($r['category']) ?></td>
                    <td><?= number_format($r['total'], 2) ?> EGP</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>

<div class="grid">

    <div class="card">
        <h3>Expenses by Partner</h3>
        <table>
            <tr>
                <th>Partner</th>
                <th>Total Paid</th>
            </tr>

            <?php foreach($expenseByPartner as $r): ?>
                <tr>
                    <td><?= h($r['partner']) ?></td>
                    <td><?= number_format($r['total'], 2) ?> EGP</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h3>Income Received by Partner</h3>
        <table>
            <tr>
                <th>Partner</th>
                <th>Total Received</th>
            </tr>

            <?php foreach($incomeByReceiver as $r): ?>
                <tr>
                    <td><?= h($r['partner']) ?></td>
                    <td><?= number_format($r['total'], 2) ?> EGP</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>

<div class="card">
    <h3>Notes</h3>
    <p>
        All report totals are shown in EGP using the saved exchange rate at the time of transaction entry.
        Original transaction currency remains available in the Income and Expenses pages.
    </p>
</div>

<?php include 'includes/footer.php'; ?>