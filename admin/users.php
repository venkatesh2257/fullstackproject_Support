<?php
require_once '../config.php';
requireAdmin();

$conn = getDBConnection();

// Get all users
$users = $conn->query("SELECT u.*, 
                       (SELECT COUNT(*) FROM subscriptions s WHERE s.user_id = u.id AND s.status = 'active' AND s.expires_at > NOW()) as active_subscriptions
                       FROM users u 
                       WHERE u.role = 'user' 
                       ORDER BY u.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Manage Users</h1>
            <nav class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="auctions.php">Auctions</a>
                <a href="users.php" class="active">Users</a>
                <a href="merchants.php">Merchants</a>
            </nav>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Subscription Status</th>
                        <th>Active Subscriptions</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && $users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="badge badge-<?php echo $user['subscription_status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($user['subscription_status']); ?></span></td>
                                <td><?php echo $user['active_subscriptions']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

