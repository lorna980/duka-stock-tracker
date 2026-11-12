<?php
require_once 'config/db.php';

// 1. Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY stock_quantity ASC");
$products = $stmt->fetchAll();

$total_products = count($products);
$low_stock_count = 0;
$total_inventory_value = 0;

foreach ($products as $p) {
    if ($p['stock_quantity'] <= $p['reorder_level']) {
        $low_stock_count++;
    }
    $total_inventory_value += ($p['selling_price'] * $p['stock_quantity']);
}

// 2. Calculate M-Pesa vs Cash Revenue
$mpesa_total = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE payment_method = 'M-Pesa'")->fetchColumn();
$cash_total  = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE payment_method = 'Cash'")->fetchColumn();

// 3. Fetch last 10 sales using SQL JOIN
$salesStmt = $pdo->query("
    SELECT s.id, p.product_name, s.quantity_sold, s.total_amount, s.payment_method, s.sale_date 
    FROM sales s 
    JOIN products p ON s.product_id = p.id 
    ORDER BY s.sale_date DESC 
    LIMIT 10
");
$recent_sales = $salesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DukaStock | SME Inventory & M-Pesa Tracker</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">DukaStock Kenya</a>
        <span>Smart SME Inventory & Sales Tracker</span>
    </nav>

    <div class="container">
        <!-- Summary Metrics -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h6>Low Stock Alerts</h6>
                    <h3 class="text-danger"><?= $low_stock_count ?> <small style="font-size:0.9rem; color:#666;">of <?= $total_products ?> items</small></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h6>Stock Value</h6>
                    <h3>KES <?= number_format($total_inventory_value, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h6>M-Pesa Sales Total</h6>
                    <h3 class="text-success">KES <?= number_format($mpesa_total, 2) ?></h3>
                    <small style="color:#666;">Cash Sales: KES <?= number_format($cash_total, 2) ?></small>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="card">
            <div class="card-header">
                <h4 style="margin:0;">Current Shop Inventory</h4>
                <div>
                    <a href="add_product.php" class="btn btn-outline-success">+ Add Product</a>
                    <a href="record_sale.php" class="btn btn-success">Record Sale (Cash / M-Pesa)</a>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Buying (KES)</th>
                        <th>Selling (KES)</th>
                        <th>In Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): 
                        $is_low = $product['stock_quantity'] <= $product['reorder_level'];
                    ?>
                    <tr class="<?= $is_low ? 'table-danger' : '' ?>">
                        <td><strong><?= htmlspecialchars($product['product_name']) ?></strong></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><?= number_format($product['buying_price'], 2) ?></td>
                        <td><?= number_format($product['selling_price'], 2) ?></td>
                        <td><strong><?= $product['stock_quantity'] ?> units</strong></td>
                        <td>
                            <?php if ($is_low): ?>
                                <span class="badge bg-danger">Low Stock</span>
                            <?php else: ?>
                                <span class="badge bg-success">In Stock</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Recent Sales Log -->
        <div class="card">
            <div class="card-header">
                <h4 style="margin:0;">Recent Transactions (M-Pesa & Cash)</h4>
            </div>
            <?php if (empty($recent_sales)): ?>
                <p style="color:#666;">No sales recorded yet. Click "Record Sale" above to log your first transaction.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Total (KES)</th>
                            <th>Payment</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_sales as $sale): ?>
                        <tr>
                            <td><?= htmlspecialchars($sale['product_name']) ?></td>
                            <td><?= $sale['quantity_sold'] ?></td>
                            <td><strong><?= number_format($sale['total_amount'], 2) ?></strong></td>
                            <td>
                                <span class="badge" style="background-color: <?= $sale['payment_method'] === 'M-Pesa' ? '#198754' : '#6c757d' ?>;">
                                    <?= htmlspecialchars($sale['payment_method']) ?>
                                </span>
                            </td>
                            <td style="color:#666; font-size:0.9rem;"><?= date('d M Y, h:i A', strtotime($sale['sale_date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>