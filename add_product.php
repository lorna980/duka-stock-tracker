<?php
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name  = trim($_POST['product_name']);
    $category      = trim($_POST['category']);
    $buying_price  = (float) $_POST['buying_price'];
    $selling_price = (float) $_POST['selling_price'];
    $stock_qty     = (int) $_POST['stock_quantity'];
    $reorder_level = (int) $_POST['reorder_level'];

    if (!empty($product_name) && $selling_price > 0) {
        $sql = "INSERT INTO products (product_name, category, buying_price, selling_price, stock_quantity, reorder_level) 
                VALUES (:name, :cat, :buy, :sell, :qty, :reorder)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'    => $product_name,
            ':cat'     => $category,
            ':buy'     => $buying_price,
            ':sell'    => $selling_price,
            ':qty'     => $stock_qty,
            ':reorder' => $reorder_level
        ]);

        header("Location: index.php");
        exit;
    } else {
        $message = "Please enter a valid product name and selling price.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | DukaStock Kenya</title>
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
                <h4 style="margin:0;">Add New Shop Product</h4>
            </div>

            <?php if ($message): ?>
                <div style="background:#f8d7da; color:#842029; padding:10px; border-radius:5px; margin-bottom:15px;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label>Product Name</label>
                <input type="text" name="product_name" class="form-control" placeholder="e.g. Ketepa Tea Leaves 250g" required>

                <label>Category</label>
                <select name="category" class="form-select" required>
                    <option value="Flour & Grains">Flour & Grains</option>
                    <option value="Groceries">Groceries</option>
                    <option value="Cooking Oils">Cooking Oils</option>
                    <option value="Dairy">Dairy</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Household">Household</option>
                </select>

                <div class="row" style="margin-bottom: 0;">
                    <div style="flex: 1;">
                        <label>Buying Price (KES)</label>
                        <input type="number" step="0.01" name="buying_price" class="form-control" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Selling Price (KES)</label>
                        <input type="number" step="0.01" name="selling_price" class="form-control" required>
                    </div>
                </div>

                <div class="row" style="margin-bottom: 0;">
                    <div style="flex: 1;">
                        <label>Initial Stock Quantity</label>
                        <input type="number" name="stock_quantity" class="form-control" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Low Stock Alert Level</label>
                        <input type="number" name="reorder_level" class="form-control" value="5" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success" style="width:100%; padding:12px;">Save Product to Inventory</button>
            </form>
        </div>
    </div>
</body>
</html>