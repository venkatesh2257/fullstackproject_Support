<?php
require_once '../config.php';
requireAdmin();

$conn = getDBConnection();
$error = '';
$success = '';

// Handle product approval/rejection
if (isset($_GET['approve'])) {
    $product_id = intval($_GET['approve']);
    $stmt = $conn->prepare("UPDATE products SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        $success = 'Product approved successfully';
    } else {
        $error = 'Failed to approve product';
    }
    $stmt->close();
}

if (isset($_GET['reject'])) {
    $product_id = intval($_GET['reject']);
    $stmt = $conn->prepare("UPDATE products SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        $success = 'Product rejected';
    } else {
        $error = 'Failed to reject product';
    }
    $stmt->close();
}

// Handle price update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_price'])) {
    $product_id = intval($_POST['product_id']);
    $admin_price = floatval($_POST['admin_price']);
    
    if ($admin_price > 0) {
        $stmt = $conn->prepare("UPDATE products SET admin_price = ? WHERE id = ?");
        $stmt->bind_param("di", $admin_price, $product_id);
        if ($stmt->execute()) {
            $success = 'Price updated successfully';
        } else {
            $error = 'Failed to update price';
        }
        $stmt->close();
    } else {
        $error = 'Invalid price';
    }
}

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $product_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_product = $result->fetch_assoc();
    $stmt->close();
}

// Get all products
$status_filter = $_GET['status'] ?? 'all';
$where_clause = $status_filter !== 'all' ? "WHERE p.status = '$status_filter'" : "";
$products = $conn->query("SELECT p.*, u.username as merchant_name 
                          FROM products p 
                          JOIN users u ON p.merchant_id = u.id 
                          $where_clause
                          ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Manage Products</h1>
            <nav class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php" class="active">Products</a>
                <a href="auctions.php">Auctions</a>
                <a href="users.php">Users</a>
                <a href="merchants.php">Merchants</a>
            </nav>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($edit_product): ?>
            <div class="edit-form-card">
                <h2>Set Admin Price for: <?php echo htmlspecialchars($edit_product['name']); ?></h2>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <div class="form-group">
                        <label>Merchant Price</label>
                        <input type="text" value="$<?php echo number_format($edit_product['merchant_price'], 2); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Admin Price</label>
                        <input type="number" name="admin_price" step="0.01" min="0" 
                               value="<?php echo $edit_product['admin_price'] ?? ''; ?>" required>
                    </div>
                    <button type="submit" name="update_price" class="btn btn-primary">Update Price</button>
                    <a href="products.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        <?php endif; ?>

        <div class="filter-section">
            <a href="?status=all" class="btn <?php echo $status_filter === 'all' ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
            <a href="?status=pending" class="btn <?php echo $status_filter === 'pending' ? 'btn-primary' : 'btn-secondary'; ?>">Pending</a>
            <a href="?status=approved" class="btn <?php echo $status_filter === 'approved' ? 'btn-primary' : 'btn-secondary'; ?>">Approved</a>
            <a href="?status=rejected" class="btn <?php echo $status_filter === 'rejected' ? 'btn-primary' : 'btn-secondary'; ?>">Rejected</a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Merchant</th>
                        <th>Merchant Price</th>
                        <th>Admin Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while ($product = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['merchant_name']); ?></td>
                                <td>$<?php echo number_format($product['merchant_price'], 2); ?></td>
                                <td>
                                    <?php if ($product['admin_price']): ?>
                                        $<?php echo number_format($product['admin_price'], 2); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-<?php echo $product['status'] === 'approved' ? 'success' : ($product['status'] === 'pending' ? 'warning' : 'danger'); ?>"><?php echo ucfirst($product['status']); ?></span></td>
                                <td>
                                    <a href="?edit=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">Set Price</a>
                                    <?php if ($product['status'] === 'pending'): ?>
                                        <a href="?approve=<?php echo $product['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                                        <a href="?reject=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No products found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

