<?php
require_once 'config.php';
$conn = getDBConnection();

// Get active auctions with merchant info
$auctions_query = "SELECT a.*, p.name as product_name, p.image_url, p.description, p.merchant_id, p.merchant_price, p.admin_price
                    FROM auctions a 
                    JOIN products p ON a.product_id = p.id 
                    WHERE a.status = 'active' 
                    ORDER BY a.end_time ASC 
                    LIMIT 12";
$auctions_result = $conn->query($auctions_query);

// Get current user info for price display logic
$current_user_id = $_SESSION['user_id'] ?? null;
$current_user_role = getUserRole();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="hero-section">
            <h1>Welcome to <?php echo SITE_NAME; ?></h1>
            <p>Discover amazing products and place your bids!</p>
            <?php if (!isLoggedIn()): ?>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                    <a href="login.php" class="btn btn-secondary">Login</a>
                </div>
            <?php endif; ?>
        </div>

        <section class="auctions-section">
            <h2>Active Auctions</h2>
            <div class="auctions-grid">
                <?php if ($auctions_result && $auctions_result->num_rows > 0): ?>
                    <?php while ($auction = $auctions_result->fetch_assoc()): ?>
                        <div class="auction-card">
                            <?php if ($auction['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($auction['image_url']); ?>" alt="<?php echo htmlspecialchars($auction['product_name']); ?>">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                            <div class="auction-card-body">
                                <h3><?php echo htmlspecialchars($auction['product_name']); ?></h3>
                                <?php
                                // Show merchant_price only to the product owner merchant, otherwise show current_price (admin price)
                                $display_price = $auction['current_price'];
                                if ($current_user_role === 'merchant' && $current_user_id == $auction['merchant_id']) {
                                    // This merchant owns the product - show their merchant_price
                                    $display_price = $auction['merchant_price'];
                                }
                                ?>
                                <p class="current-price">Current Bid: $<?php echo number_format($display_price, 2); ?></p>
                                <p class="end-time">Ends: <?php echo date('M d, Y H:i', strtotime($auction['end_time'])); ?></p>
                                <a href="auction.php?id=<?php echo $auction['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-auctions">No active auctions at the moment.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

