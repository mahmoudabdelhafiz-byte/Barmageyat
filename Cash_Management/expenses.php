<?php 
require_once 'config.php'; 
require_login();

if(isset($_GET['approve']) && is_admin()){ 
    $pdo->prepare("UPDATE expenses SET status='approved',approved_by=?,approved_at=NOW() WHERE id=?")
        ->execute([$_SESSION['user_id'], $_GET['approve']]); 
    header('Location: expenses.php'); 
    exit; 
}

if(isset($_GET['reject']) && is_admin()){ 
    $pdo->prepare("UPDATE expenses SET status='rejected',approved_by=?,approved_at=NOW() WHERE id=?")
        ->execute([$_SESSION['user_id'], $_GET['reject']]); 
    header('Location: expenses.php'); 
    exit; 
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $amount = (float)$_POST['amount'];
    $currency = strtoupper(trim($_POST['currency']));
    $exchange_rate = isset($_POST['exchange_rate']) ? (float)$_POST['exchange_rate'] : 1;

    if($currency === 'EGP'){
        $exchange_rate = 1;
    }

    $egp_amount = $amount * $exchange_rate;

    $file = null; 

    if(!empty($_FILES['receipt']['name'])){
        $ext = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION); 
        $file = 'expense_' . time() . '_' . rand(1000,9999) . '.' . $ext; 
        move_uploaded_file($_FILES['receipt']['tmp_name'], 'uploads/' . $file); 
    }

    $pdo->prepare("
        INSERT INTO expenses
        (paid_by, category_id, amount, currency, exchange_rate, egp_amount, expense_date, description, receipt_file) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ")->execute([
        $_SESSION['user_id'],
        $_POST['category_id'],
        $amount,
        $currency,
        $exchange_rate,
        $egp_amount,
        $_POST['expense_date'],
        $_POST['description'],
        $file
    ]);

    header('Location: expenses.php'); 
    exit;
}

$cats = $pdo->query("SELECT * FROM categories WHERE type='expense' ORDER BY name")->fetchAll();

$rows = $pdo->query("
    SELECT e.*, u.name user, c.name cat 
    FROM expenses e 
    JOIN users u ON u.id = e.paid_by 
    LEFT JOIN categories c ON c.id = e.category_id 
    ORDER BY e.id DESC
")->fetchAll();

include 'includes/header.php'; 
?>

<h1>Expenses</h1>

<div class="card">
    <h3>Add Expense</h3>

    <form method="post" enctype="multipart/form-data">

        <div class="grid">

            <div>
                <label>Category</label>
                <select name="category_id" required>
                    <?php foreach($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= h($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Amount</label>
                <input name="amount" id="amount" type="number" step="0.01" required>
            </div>

            <div>
                <label>Currency</label>
                <select name="currency" id="currency" required>
                    <option value="EGP">EGP</option>
                    <option value="USD">USD</option>
                    <option value="SAR">SAR</option>
                    <option value="AED">AED</option>
                    <option value="EUR">EUR</option>
                </select>
            </div>

            <div>
                <label>Exchange Rate to EGP</label>
                <input name="exchange_rate" id="exchange_rate" type="number" step="0.0001" value="1" required>
            </div>

            <div>
                <label>EGP Equivalent</label>
                <input name="egp_amount_preview" id="egp_amount" type="number" step="0.01" value="0.00" readonly>
            </div>

            <div>
                <label>Date</label>
                <input name="expense_date" type="date" value="<?= date('Y-m-d') ?>" required>
            </div>

        </div>

        <label>Description</label>
        <textarea name="description"></textarea>

        <label>Receipt</label>
        <input type="file" name="receipt">

        <button>Submit Expense</button>

    </form>
</div>

<div class="card">
    <h3>Expense List</h3>

    <table>
        <tr>
            <th>Date</th>
            <th>Partner</th>
            <th>Category</th>
            <th>Original Amount</th>
            <th>Exchange Rate</th>
            <th>EGP Amount</th>
            <th>Status</th>
            <th>Receipt</th>
            <th>Actions</th>
        </tr>

        <?php foreach($rows as $r): ?>
            <tr>
                <td><?= h($r['expense_date']) ?></td>
                <td><?= h($r['user']) ?></td>
                <td><?= h($r['cat']) ?></td>

                <td>
                    <?= number_format($r['amount'], 2) . ' ' . h($r['currency']) ?>
                </td>

                <td>
                    <?= number_format($r['exchange_rate'] ?? 1, 4) ?>
                </td>

                <td>
                    <?= number_format($r['egp_amount'] ?? $r['amount'], 2) ?> EGP
                </td>

                <td>
                    <span class="status <?= h($r['status']) ?>">
                        <?= h($r['status']) ?>
                    </span>
                </td>

                <td>
                    <?php if($r['receipt_file']): ?>
                        <a class="btn secondary" href="uploads/<?= h($r['receipt_file']) ?>" target="_blank">Open</a>
                    <?php endif; ?>
                </td>

                <td class="actions">
                    <?php if(is_admin() && $r['status'] == 'pending'): ?>
                        <a class="btn ok" href="?approve=<?= $r['id'] ?>">Approve</a>
                        <a class="btn danger" href="?reject=<?= $r['id'] ?>">Reject</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>
</div>

<script>
const amountInput = document.getElementById('amount');
const currencyInput = document.getElementById('currency');
const rateInput = document.getElementById('exchange_rate');
const egpInput = document.getElementById('egp_amount');

function calculateEGP() {
    const amount = parseFloat(amountInput.value) || 0;
    const rate = parseFloat(rateInput.value) || 1;
    egpInput.value = (amount * rate).toFixed(2);
}

async function fetchRate() {
    const currency = currencyInput.value;

    if(currency === 'EGP') {
        rateInput.value = 1;
        calculateEGP();
        return;
    }

    try {
        const response = await fetch('get_rate.php?from=' + currency + '&to=EGP');
        const data = await response.json();

        if(data.rate) {
            rateInput.value = parseFloat(data.rate).toFixed(4);
            calculateEGP();
        } else {
            alert('Exchange rate could not be fetched. Please enter it manually.');
        }

    } catch(e) {
        alert('Exchange rate service unavailable. Please enter it manually.');
    }
}

amountInput.addEventListener('input', calculateEGP);
rateInput.addEventListener('input', calculateEGP);
currencyInput.addEventListener('change', fetchRate);

calculateEGP();
</script>

<?php include 'includes/footer.php'; ?>