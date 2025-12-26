<?php
require_once 'config.php';
requireLogin();

$auction_id = intval($_GET['id'] ?? 0);
$conn = getDBConnection();

// Get auction details
$stmt = $conn->prepare("SELECT a.*, p.name as product_name, p.description, p.image_url, p.merchant_price 
                        FROM auctions a 
                        JOIN products p ON a.product_id = p.id 
                        WHERE a.id = ?");
$stmt->bind_param("i", $auction_id);
$stmt->execute();
$result = $stmt->get_result();
$auction = $result->fetch_assoc();
$stmt->close();

if (!$auction) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$has_subscription = hasActiveSubscription($user_id);

// Handle bid submission
$bid_error = '';
$bid_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_bid'])) {
    if (!$has_subscription) {
        $bid_error = 'You need an active subscription to place bids';
    } elseif ($auction['status'] !== 'active') {
        $bid_error = 'This auction is not active';
    } else {
        $bid_amount = floatval($_POST['bid_amount'] ?? 0);
        
        if ($bid_amount <= $auction['current_price']) {
            $bid_error = 'Bid amount must be higher than current price';
        } else {
            $conn->begin_transaction();
            try {
                // Insert bid
                $stmt = $conn->prepare("INSERT INTO bids (auction_id, user_id, bid_amount) VALUES (?, ?, ?)");
                $stmt->bind_param("iid", $auction_id, $user_id, $bid_amount);
                $stmt->execute();
                
                // Update auction current price
                $stmt = $conn->prepare("UPDATE auctions SET current_price = ? WHERE id = ?");
                $stmt->bind_param("di", $bid_amount, $auction_id);
                $stmt->execute();
                
                // Update previous winning bid
                $stmt = $conn->prepare("UPDATE bids SET is_winning_bid = FALSE WHERE auction_id = ?");
                $stmt->bind_param("i", $auction_id);
                $stmt->execute();
                
                // Set new winning bid
                $stmt = $conn->prepare("UPDATE bids SET is_winning_bid = TRUE WHERE auction_id = ? AND user_id = ? AND bid_amount = ? ORDER BY id DESC LIMIT 1");
                $stmt->bind_param("iid", $auction_id, $user_id, $bid_amount);
                $stmt->execute();
                
                $conn->commit();
                $bid_success = 'Bid placed successfully!';
                
                // Refresh auction data
                $stmt = $conn->prepare("SELECT * FROM auctions WHERE id = ?");
                $stmt->bind_param("i", $auction_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $auction = $result->fetch_assoc();
                $stmt->close();
            } catch (Exception $e) {
                $conn->rollback();
                $bid_error = 'Failed to place bid. Please try again.';
            }
        }
    }
}

// Get recent bids
$bids_query = "SELECT b.*, u.username 
               FROM bids b 
               JOIN users u ON b.user_id = u.id 
               WHERE b.auction_id = ? 
               ORDER BY b.bid_time DESC 
               LIMIT 10";
$stmt = $conn->prepare($bids_query);
$stmt->bind_param("i", $auction_id);
$stmt->execute();
$bids_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($auction['product_name']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="auction-detail">
            <div class="auction-image">
                <?php if ($auction['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($auction['image_url']); ?>" alt="<?php echo htmlspecialchars($auction['product_name']); ?>">
                <?php else: ?>
                    <div class="no-image-large">No Image</div>
                <?php endif; ?>
            </div>
            
            <div class="auction-info">
                <h1><?php echo htmlspecialchars($auction['product_name']); ?></h1>
                <p class="description"><?php echo nl2br(htmlspecialchars($auction['description'])); ?></p>
                
                <div class="auction-stats">
                    <div class="stat-item">
                        <span class="stat-label">Current Bid:</span>
                        <span class="stat-value">$<?php echo number_format($auction['current_price'], 2); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Starting Price:</span>
                        <span class="stat-value">$<?php echo number_format($auction['starting_price'], 2); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Bid Increment:</span>
                        <span class="stat-value">$<?php echo number_format($auction['bid_increment'], 2); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Ends:</span>
                        <span class="stat-value"><?php echo date('M d, Y H:i', strtotime($auction['end_time'])); ?></span>
                    </div>
                </div>
                
                <?php if ($auction['status'] === 'active'): ?>
                    <div class="bid-section">
                        <?php if ($bid_error): ?>
                            <div class="alert alert-error"><?php echo $bid_error; ?></div>
                        <?php endif; ?>
                        <?php if ($bid_success): ?>
                            <div class="alert alert-success"><?php echo $bid_success; ?></div>
                        <?php endif; ?>
                        
                        <?php if (!$has_subscription): ?>
                            <div class="alert alert-warning">
                                You need an active subscription to place bids. 
                                <a href="user/subscription.php">Subscribe now</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" class="bid-form">
                                <div class="form-group">
                                    <label>Your Bid Amount</label>
                                    <input type="number" name="bid_amount" 
                                           step="0.01" 
                                           min="<?php echo $auction['current_price'] + $auction['bid_increment']; ?>"
                                           value="<?php echo $auction['current_price'] + $auction['bid_increment']; ?>"
                                           required>
                                    <small>Minimum bid: $<?php echo number_format($auction['current_price'] + $auction['bid_increment'], 2); ?></small>
                                </div>
                                <button type="submit" name="place_bid" class="btn btn-primary">Place Bid</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">This auction has ended.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="recent-bids">
            <h2>Recent Bids</h2>
            <table class="bids-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Bid Amount</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bids_result && $bids_result->num_rows > 0): ?>
                        <?php while ($bid = $bids_result->fetch_assoc()): ?>
                            <tr class="<?php echo $bid['is_winning_bid'] ? 'winning-bid' : ''; ?>">
                                <td><?php echo htmlspecialchars($bid['username']); ?></td>
                                <td>$<?php echo number_format($bid['bid_amount'], 2); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($bid['bid_time'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No bids yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

