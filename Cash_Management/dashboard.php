<?php 
require_once 'config.php'; 
require_login();

$income = $pdo->query("
    SELECT COALESCE(SUM(egp_amount), 0) t 
    FROM income 
    WHERE status = 'approved'
")->fetch()['t'];

$expenses = $pdo->query("
    SELECT COALESCE(SUM(egp_amount), 0) t 
    FROM expenses 
    WHERE status = 'approved'
")->fetch()['t'];

$pendingExp = $pdo->query("
    SELECT COUNT(*) c 
    FROM expenses 
    WHERE status = 'pending'
")->fetch()['c'];

$pendingInc = $pdo->query("
    SELECT COUNT(*) c 
    FROM income 
    WHERE status = 'pending'
")->fetch()['c'];

$recentExp = $pdo->query("
    SELECT e.*, u.name user, c.name cat 
    FROM expenses e 
    JOIN users u ON u.id = e.paid_by 
    LEFT JOIN categories c ON c.id = e.category_id 
    ORDER BY e.id DESC 
    LIMIT 5
")->fetchAll();

$recentInc = $pdo->query("
    SELECT i.*, u.name user, c.name cat 
    FROM income i 
    JOIN users u ON u.id = i.received_by 
    LEFT JOIN categories c ON c.id = i.category_id 
    ORDER BY i.id DESC 
    LIMIT 5
")->fetchAll();

include 'includes/header.php'; 
?>

<h1>Dashboard</h1>

<div class="grid">

    <div class="kpi">
        <div class="label">Approved Income</div>
        <div class="value"><?= number_format($income, 2) ?> EGP</div>
    </div>

    <div class="kpi">
        <div class="label">Approved Expenses</div>
        <div class="value"><?= number_format($expenses, 2) ?> EGP</div>
    </div>

    <div class="kpi">
        <div class="label">Net Cash Position</div>
        <div class="value"><?= number_format($income - $expenses, 2) ?> EGP</div>
    </div>

    <div class="kpi">
        <div class="label">Pending Approvals</div>
        <div class="value"><?= ($pendingExp + $pendingInc) ?></div>
    </div>

</div>

<div class="grid">

    <div class="card">
        <h3>Recent Expenses</h3>

        <table>
            <tr>
                <th>Date</th>
                <th>Partner</th>
                <th>Category</th>
                <th>Original Amount</th>
                <th>EGP Amount</th>
                <th>Status</th>
            </tr>

            <?php foreach($recentExp as $r): ?>
                <tr>
                    <td><?= h($r['expense_date']) ?></td>
                    <td><?= h($r['user']) ?></td>
                    <td><?= h($r['cat']) ?></td>

                    <td>
                        <?= number_format($r['amount'], 2) . ' ' . h($r['currency']) ?>
                        <br>
                        <small>Rate: <?= number_format($r['exchange_rate'] ?? 1, 4) ?></small>
                    </td>

                    <td>
                        <?= number_format($r['egp_amount'] ?? $r['amount'], 2) ?> EGP
                    </td>

                    <td>
                        <span class="status <?= h($r['status']) ?>">
                            <?= h($r['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>

    <div class="card">
        <h3>Recent Income</h3>

        <table>
            <tr>
                <th>Date</th>
                <th>Received By</th>
                <th>Customer</th>
                <th>Original Amount</th>
                <th>EGP Amount</th>
                <th>Status</th>
            </tr>

            <?php foreach($recentInc as $r): ?>
                <tr>
                    <td><?= h($r['income_date']) ?></td>
                    <td><?= h($r['user']) ?></td>
                    <td><?= h($r['customer_name']) ?></td>

                    <td>
                        <?= number_format($r['amount'], 2) . ' ' . h($r['currency']) ?>
                        <br>
                        <small>Rate: <?= number_format($r['exchange_rate'] ?? 1, 4) ?></small>
                    </td>

                    <td>
                        <?= number_format($r['egp_amount'] ?? $r['amount'], 2) ?> EGP
                    </td>

                    <td>
                        <span class="status <?= h($r['status']) ?>">
                            <?= h($r['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>

</div>

<?php include 'includes/footer.php'; ?>