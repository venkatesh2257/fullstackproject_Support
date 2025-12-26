<?php
require_once 'config.php';
$conn = getDBConnection();

// Get active auctions
$auctions_query = "SELECT a.*, p.name as product_name, p.image_url, p.description 
                    FROM auctions a 
                    JOIN products p ON a.product_id = p.id 
                    WHERE a.status = 'active' 
                    ORDER BY a.end_time ASC 
                    LIMIT 12";
$auctions_result = $conn->query($auctions_query);
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
                                <p class="current-price">Current Bid: $<?php echo number_format($auction['current_price'], 2); ?></p>
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

