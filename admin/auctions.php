<?php
require_once '../config.php';
requireAdmin();

$conn = getDBConnection();
$error = '';
$success = '';

// Handle auction creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_auction'])) {
        $product_id = intval($_POST['product_id']);
        $starting_price = floatval($_POST['starting_price']);
        $bid_increment = floatval($_POST['bid_increment']);
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        
        if ($product_id > 0 && $starting_price > 0 && $bid_increment > 0) {
            $stmt = $conn->prepare("INSERT INTO auctions (product_id, starting_price, current_price, bid_increment, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, 'upcoming')");
            $stmt->bind_param("idddss", $product_id, $starting_price, $starting_price, $bid_increment, $start_time, $end_time);
            if ($stmt->execute()) {
                $success = 'Auction created successfully';
            } else {
                $error = 'Failed to create auction';
            }
            $stmt->close();
        } else {
            $error = 'Please fill in all required fields';
        }
    } elseif (isset($_POST['update_auction'])) {
        $auction_id = intval($_POST['auction_id']);
        $status = sanitize($_POST['status']);
        $stmt = $conn->prepare("UPDATE auctions SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $auction_id);
        if ($stmt->execute()) {
            $success = 'Auction updated successfully';
        } else {
            $error = 'Failed to update auction';
        }
        $stmt->close();
    }
}

// Get approved products for auction creation
$approved_products = $conn->query("SELECT id, name FROM products WHERE status = 'approved' ORDER BY name");

// Get all auctions
$auctions = $conn->query("SELECT a.*, p.name as product_name 
                          FROM auctions a 
                          JOIN products p ON a.product_id = p.id 
                          ORDER BY a.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Auctions - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Manage Auctions</h1>
            <nav class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="auctions.php" class="active">Auctions</a>
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

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2>Create New Auction</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Product</label>
                        <select name="product_id" required>
                            <option value="">Select Product</option>
                            <?php while ($product = $approved_products->fetch_assoc()): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Starting Price</label>
                        <input type="number" name="starting_price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Bid Increment</label>
                        <input type="number" name="bid_increment" step="0.01" min="0" value="10.00" required>
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="datetime-local" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="datetime-local" name="end_time" required>
                    </div>
                    <button type="submit" name="create_auction" class="btn btn-primary">Create Auction</button>
                </form>
            </div>

            <div class="dashboard-card">
                <h2>All Auctions</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Starting Price</th>
                                <th>Current Price</th>
                                <th>Status</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($auctions && $auctions->num_rows > 0): ?>
                                <?php while ($auction = $auctions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($auction['product_name']); ?></td>
                                        <td>$<?php echo number_format($auction['starting_price'], 2); ?></td>
                                        <td>$<?php echo number_format($auction['current_price'], 2); ?></td>
                                        <td><span class="badge badge-<?php echo $auction['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($auction['status']); ?></span></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($auction['start_time'])); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($auction['end_time'])); ?></td>
                                        <td>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="auction_id" value="<?php echo $auction['id']; ?>">
                                                <select name="status" onchange="this.form.submit()">
                                                    <option value="upcoming" <?php echo $auction['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                                    <option value="active" <?php echo $auction['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="ended" <?php echo $auction['status'] === 'ended' ? 'selected' : ''; ?>>Ended</option>
                                                    <option value="cancelled" <?php echo $auction['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                                <input type="hidden" name="update_auction" value="1">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No auctions found</td>
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

