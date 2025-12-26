<?php
require_once '../config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// Get user's active subscriptions
$subscription_query = "SELECT * FROM subscriptions WHERE user_id = ? AND status = 'active' AND expires_at > NOW() ORDER BY expires_at DESC LIMIT 1";
$stmt = $conn->prepare($subscription_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$subscription_result = $stmt->get_result();
$active_subscription = $subscription_result->fetch_assoc();
$stmt->close();

$has_subscription = hasActiveSubscription($user_id);

// Get user's bids
$bids_query = "SELECT b.*, a.id as auction_id, p.name as product_name, a.status as auction_status
               FROM bids b
               JOIN auctions a ON b.auction_id = a.id
               JOIN products p ON a.product_id = p.id
               WHERE b.user_id = ?
               ORDER BY b.bid_time DESC
               LIMIT 20";
$stmt = $conn->prepare($bids_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bids_result = $stmt->get_result();
$stmt->close();

// Get active auctions with merchant info
$auctions_query = "SELECT a.*, p.name as product_name, p.image_url, p.merchant_id, p.merchant_price, p.admin_price
                    FROM auctions a
                    JOIN products p ON a.product_id = p.id
                    WHERE a.status = 'active'
                    ORDER BY a.end_time ASC
                    LIMIT 10";
$auctions_result = $conn->query($auctions_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2>Subscription Status</h2>
                <?php if ($has_subscription && $active_subscription): ?>
                    <div class="subscription-info active">
                        <p><strong>Status:</strong> Active</p>
                        <p><strong>Plan:</strong> <?php echo htmlspecialchars($active_subscription['plan_name']); ?></p>
                        <p><strong>Expires:</strong> <?php echo date('M d, Y', strtotime($active_subscription['expires_at'])); ?></p>
                        <a href="subscription.php" class="btn btn-secondary">Manage Subscription</a>
                    </div>
                <?php else: ?>
                    <div class="subscription-info inactive">
                        <p><strong>Status:</strong> No Active Subscription</p>
                        <p>You need a subscription to participate in auctions.</p>
                        <a href="subscription.php" class="btn btn-primary">Subscribe Now</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="dashboard-card">
                <h2>My Bids</h2>
                <div class="bids-list">
                    <?php if ($bids_result && $bids_result->num_rows > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Bid Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($bid = $bids_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><a href="../auction.php?id=<?php echo $bid['auction_id']; ?>"><?php echo htmlspecialchars($bid['product_name']); ?></a></td>
                                        <td>$<?php echo number_format($bid['bid_amount'], 2); ?></td>
                                        <td>
                                            <?php if ($bid['is_winning_bid']): ?>
                                                <span class="badge badge-success">Winning</span>
                                            <?php elseif ($bid['auction_status'] === 'ended'): ?>
                                                <span class="badge badge-secondary">Ended</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Outbid</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($bid['bid_time'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>You haven't placed any bids yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dashboard-card">
                <h2>Active Auctions</h2>
                <div class="auctions-list">
                    <?php if ($auctions_result && $auctions_result->num_rows > 0): ?>
                        <?php while ($auction = $auctions_result->fetch_assoc()): ?>
                            <div class="auction-item">
                                <?php if ($auction['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($auction['image_url']); ?>" alt="<?php echo htmlspecialchars($auction['product_name']); ?>" class="auction-thumb">
                                <?php endif; ?>
                                <div class="auction-item-info">
                                    <h3><?php echo htmlspecialchars($auction['product_name']); ?></h3>
                                    <?php
                                    // Show merchant_price only to the product owner merchant, otherwise show current_price
                                    $display_price = $auction['current_price'];
                                    if (getUserRole() === 'merchant' && $user_id == $auction['merchant_id']) {
                                        $display_price = $auction['merchant_price'];
                                    }
                                    ?>
                                    <p>Current Bid: $<?php echo number_format($display_price, 2); ?></p>
                                    <a href="../auction.php?id=<?php echo $auction['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No active auctions at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

