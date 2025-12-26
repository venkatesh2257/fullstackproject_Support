<?php
require_once '../config.php';
requireAdmin();

$conn = getDBConnection();

// Get statistics
$stats = [];
$stats['total_users'] = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$stats['total_merchants'] = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'merchant'")->fetch_assoc()['count'];
$stats['total_products'] = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$stats['active_auctions'] = $conn->query("SELECT COUNT(*) as count FROM auctions WHERE status = 'active'")->fetch_assoc()['count'];
$stats['pending_products'] = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 'pending'")->fetch_assoc()['count'];
$stats['total_revenue'] = $conn->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;

// Get pending products
$pending_products = $conn->query("SELECT p.*, u.username as merchant_name 
                                    FROM products p 
                                    JOIN users u ON p.merchant_id = u.id 
                                    WHERE p.status = 'pending' 
                                    ORDER BY p.created_at DESC");

// Get recent auctions
$recent_auctions = $conn->query("SELECT a.*, p.name as product_name 
                                  FROM auctions a 
                                  JOIN products p ON a.product_id = p.id 
                                  ORDER BY a.created_at DESC 
                                  LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <nav class="admin-nav">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="auctions.php">Auctions</a>
                <a href="users.php">Users</a>
                <a href="merchants.php">Merchants</a>
            </nav>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p class="stat-number"><?php echo $stats['total_users']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Merchants</h3>
                <p class="stat-number"><?php echo $stats['total_merchants']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Products</h3>
                <p class="stat-number"><?php echo $stats['total_products']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Auctions</h3>
                <p class="stat-number"><?php echo $stats['active_auctions']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Pending Products</h3>
                <p class="stat-number"><?php echo $stats['pending_products']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p class="stat-number">$<?php echo number_format($stats['total_revenue'], 2); ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2>Pending Products</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Merchant</th>
                                <th>Merchant Price</th>
                                <th>Admin Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($pending_products && $pending_products->num_rows > 0): ?>
                                <?php while ($product = $pending_products->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['merchant_name']); ?></td>
                                        <td>$<?php echo number_format($product['merchant_price'], 2); ?></td>
                                        <td>
                                            <?php if ($product['admin_price']): ?>
                                                $<?php echo number_format($product['admin_price'], 2); ?>
                                            <?php else: ?>
                                                <a href="products.php?edit=<?php echo $product['id']; ?>">Set Price</a>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge badge-warning"><?php echo ucfirst($product['status']); ?></span></td>
                                        <td>
                                            <a href="products.php?approve=<?php echo $product['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                                            <a href="products.php?reject=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No pending products</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dashboard-card">
                <h2>Recent Auctions</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Price</th>
                                <th>Status</th>
                                <th>End Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_auctions && $recent_auctions->num_rows > 0): ?>
                                <?php while ($auction = $recent_auctions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($auction['product_name']); ?></td>
                                        <td>$<?php echo number_format($auction['current_price'], 2); ?></td>
                                        <td><span class="badge badge-<?php echo $auction['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($auction['status']); ?></span></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($auction['end_time'])); ?></td>
                                        <td><a href="auctions.php?edit=<?php echo $auction['id']; ?>" class="btn btn-primary btn-sm">Edit</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No auctions</td>
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

