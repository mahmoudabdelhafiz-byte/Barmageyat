<?php 
require_once 'config.php'; 
require_login();

$partners = $pdo->query("
    SELECT 
        u.id,
        u.name,
        u.email,

        COALESCE(exp.total_expenses, 0) AS total_expenses,
        COALESCE(inc.total_income_received, 0) AS total_income_received

    FROM users u

    LEFT JOIN (
        SELECT 
            paid_by,
            SUM(CASE 
                WHEN egp_amount IS NULL OR egp_amount = 0 THEN amount 
                ELSE egp_amount 
            END) AS total_expenses
        FROM expenses
        WHERE status = 'approved'
        GROUP BY paid_by
    ) exp ON exp.paid_by = u.id

    LEFT JOIN (
        SELECT 
            received_by,
            SUM(CASE 
                WHEN egp_amount IS NULL OR egp_amount = 0 THEN amount 
                ELSE egp_amount 
            END) AS total_income_received
        FROM income
        WHERE status = 'approved'
        GROUP BY received_by
    ) inc ON inc.received_by = u.id

    WHERE u.role IN ('admin', 'partner')
    ORDER BY u.name
")->fetchAll();

$totalExpenses = 0;
$totalIncomeReceived = 0;

foreach($partners as $p){
    $totalExpenses += $p['total_expenses'];
    $totalIncomeReceived += $p['total_income_received'];
}

$netCash = $totalIncomeReceived - $totalExpenses;

include 'includes/header.php'; 
?>

<h1>Partner Balances</h1>

<div class="grid">

    <div class="kpi">
        <div class="label">Total Partner Paid Expenses</div>
        <div class="value"><?= number_format($totalExpenses, 2) ?> EGP</div>
    </div>

    <div class="kpi">
        <div class="label">Total Income Received</div>
        <div class="value"><?= number_format($totalIncomeReceived, 2) ?> EGP</div>
    </div>

    <div class="kpi">
        <div class="label">Net Company Cash</div>
        <div class="value"><?= number_format($netCash, 2) ?> EGP</div>
    </div>

</div>

<div class="card">
    <h3>Partner Contribution Summary</h3>

    <table>
        <tr>
            <th>Partner</th>
            <th>Email</th>
            <th>Approved Expenses Paid</th>
            <th>Income Received</th>
            <th>Net Contribution</th>
        </tr>

        <?php foreach($partners as $p): 
            $netContribution = $p['total_expenses'] - $p['total_income_received'];
        ?>
            <tr>
                <td><?= h($p['name']) ?></td>
                <td><?= h($p['email']) ?></td>

                <td><?= number_format($p['total_expenses'], 2) ?> EGP</td>

                <td><?= number_format($p['total_income_received'], 2) ?> EGP</td>

                <td>
                    <?= number_format($netContribution, 2) ?> EGP
                    <br>
                    <small>
                        <?php if($netContribution > 0): ?>
                            Partner paid more than received
                        <?php elseif($netContribution < 0): ?>
                            Partner received more than paid
                        <?php else: ?>
                            Balanced
                        <?php endif; ?>
                    </small>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>
</div>

<div class="card">
    <h3>How to Read This</h3>
    <p>
        This page converts all approved income and expenses to EGP using the saved exchange rate.
        Original transaction currencies are preserved in the Income and Expenses pages.
    </p>
    <p>
        Net Contribution = Approved Expenses Paid - Income Received.
    </p>
</div>

<?php include 'includes/footer.php'; ?>