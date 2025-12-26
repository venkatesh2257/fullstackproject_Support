<?php
require_once '../config.php';
requireMerchant();

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

$error = '';
$success = '';

// Handle product submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $merchant_price = floatval($_POST['merchant_price'] ?? 0);
    $image_url = sanitize($_POST['image_url'] ?? '');
    
    if (empty($name) || $merchant_price <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        // Check if merchant has active subscription
        if (!hasActiveSubscription($user_id)) {
            $error = 'You need an active subscription to add products';
        } else {
            $stmt = $conn->prepare("INSERT INTO products (merchant_id, name, description, merchant_price, image_url, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("issds", $user_id, $name, $description, $merchant_price, $image_url);
            if ($stmt->execute()) {
                $success = 'Product submitted successfully! Waiting for admin approval.';
            } else {
                $error = 'Failed to submit product';
            }
            $stmt->close();
        }
    }
}

// Get merchant's subscription status
$has_subscription = hasActiveSubscription($user_id);

// Get merchant's products
$products_query = "SELECT * FROM products WHERE merchant_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($products_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$products_result = $stmt->get_result();
$stmt->close();

// Get statistics
$stats = [];
$stats['total_products'] = $conn->query("SELECT COUNT(*) as count FROM products WHERE merchant_id = $user_id")->fetch_assoc()['count'];
$stats['approved_products'] = $conn->query("SELECT COUNT(*) as count FROM products WHERE merchant_id = $user_id AND status = 'approved'")->fetch_assoc()['count'];
$stats['pending_products'] = $conn->query("SELECT COUNT(*) as count FROM products WHERE merchant_id = $user_id AND status = 'pending'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Merchant Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p class="stat-number"><?php echo $stats['total_products']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Approved Products</h3>
                <p class="stat-number"><?php echo $stats['approved_products']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Pending Products</h3>
                <p class="stat-number"><?php echo $stats['pending_products']; ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2>Add New Product</h2>
                <?php if (!$has_subscription): ?>
                    <div class="alert alert-warning">
                        You need an active subscription to add products. 
                        <a href="subscription.php">Subscribe now</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label>Product Name *</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Your Price (Merchant Price) *</label>
                            <input type="number" name="merchant_price" step="0.01" min="0" required>
                            <small>This is the price you want to sell the product for</small>
                        </div>
                        <div class="form-group">
                            <label>Image URL</label>
                            <input type="url" name="image_url" placeholder="https://example.com/image.jpg">
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary">Submit Product</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="dashboard-card">
                <h2>My Products</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Merchant Price</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products_result && $products_result->num_rows > 0): ?>
                                <?php while ($product = $products_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td>$<?php echo number_format($product['merchant_price'], 2); ?></td>
                                        <td><span class="badge badge-<?php echo $product['status'] === 'approved' ? 'success' : ($product['status'] === 'pending' ? 'warning' : 'danger'); ?>"><?php echo ucfirst($product['status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No products yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

