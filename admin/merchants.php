<?php
require_once '../config.php';
requireAdmin();

$conn = getDBConnection();

// Get all merchants
$merchants = $conn->query("SELECT u.*, 
                           COUNT(p.id) as total_products,
                           COUNT(CASE WHEN p.status = 'approved' THEN 1 END) as approved_products,
                           COUNT(CASE WHEN p.status = 'pending' THEN 1 END) as pending_products
                           FROM users u 
                           LEFT JOIN products p ON u.id = p.merchant_id
                           WHERE u.role = 'merchant' 
                           GROUP BY u.id
                           ORDER BY u.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Merchants - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Manage Merchants</h1>
            <nav class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="auctions.php">Auctions</a>
                <a href="users.php">Users</a>
                <a href="merchants.php" class="active">Merchants</a>
            </nav>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Total Products</th>
                        <th>Approved</th>
                        <th>Pending</th>
                        <th>Subscription Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($merchants && $merchants->num_rows > 0): ?>
                        <?php while ($merchant = $merchants->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $merchant['id']; ?></td>
                                <td><?php echo htmlspecialchars($merchant['username']); ?></td>
                                <td><?php echo htmlspecialchars($merchant['email']); ?></td>
                                <td><?php echo $merchant['total_products']; ?></td>
                                <td><?php echo $merchant['approved_products']; ?></td>
                                <td><?php echo $merchant['pending_products']; ?></td>
                                <td><span class="badge badge-<?php echo $merchant['subscription_status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($merchant['subscription_status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($merchant['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No merchants found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

