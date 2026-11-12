<?php
require_once 'config/db.php';

$message = '';

$stmt = $pdo->query("SELECT id, product_name, selling_price, stock_quantity FROM products WHERE stock_quantity > 0 ORDER BY product_name ASC");
$products = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id     = (int) $_POST['product_id'];
    $quantity_sold  = (int) $_POST['quantity_sold'];
    $payment_method = $_POST['payment_method'];

    $check = $pdo->prepare("SELECT product_name, selling_price, stock_quantity FROM products WHERE id = :id");
    $check->execute([':id' => $product_id]);
    $item = $check->fetch();

    if (!$item) {
        $message = "Selected product not found.";
    } elseif ($quantity_sold <= 0) {
        $message = "Quantity must be at least 1.";
    } elseif ($quantity_sold > $item['stock_quantity']) {
        $message = "Not enough stock! Only " . $item['stock_quantity'] . " units available.";
    } else {
        $total_amount = $item['selling_price'] * $quantity_sold;

        $pdo->beginTransaction();
        try {
            $insertSale = $pdo->prepare("INSERT INTO sales (product_id, quantity_sold, total_amount, payment_method) VALUES (:pid, :qty, :total, :pay)");
            $insertSale->execute([
                ':pid'   => $product_id,
                ':qty'   => $quantity_sold,
                ':total' => $total_amount,
                ':pay'   => $payment_method
            ]);

            $updateStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :pid");
            $updateStock->execute([
                ':qty' => $quantity_sold,
                ':pid' => $product_id
            ]);

            $pdo->commit();
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Transaction failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Sale | DukaStock Kenya</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">DukaStock Kenya</a>
        <a href="index.php" class="btn btn-outline-light">Back to Dashboard</a>
    </nav>

    <div class="container" style="max-width: 600px;">
        <div class="card">
            <div class="card-header">
                <h4 style="margin:0;">Record Customer Sale</h4>
            </div>

            <?php if ($message): ?>
                <div style="background:#f8d7da; color:#842029; padding:10px; border-radius:5px; margin-bottom:15px;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label>Select Product</label>
                <select name="product_id" class="form-select" required>
                    <option value="">-- Choose an item --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['product_name']) ?> — KES <?= number_format($p['selling_price'], 2) ?> (<?= $p['stock_quantity'] ?> in stock)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Quantity Sold</label>
                <input type="number" name="quantity_sold" class="form-control" min="1" value="1" required>

                <label>Payment Method</label>
                <select name="payment_method" class="form-select" required>
                    <option value="M-Pesa">M-Pesa (Mobile Money)</option>
                    <option value="Cash">Cash</option>
                </select>

                <button type="submit" class="btn btn-success" style="width:100%; padding:12px;">Complete Sale & Update Stock</button>
            </form>
        </div>
    </div>
</body>
</html>