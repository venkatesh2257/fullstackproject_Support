<?php
require_once '../config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

$error = '';
$success = '';

// Handle subscription purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_subscription'])) {
    $plan_id = intval($_POST['plan_id'] ?? 0);
    
    // Subscription plans
    $plans = [
        1 => ['name' => 'Basic User Plan', 'price' => 29.99, 'duration' => 30, 'type' => 'user'],
        2 => ['name' => 'Premium User Plan', 'price' => 49.99, 'duration' => 60, 'type' => 'user'],
        3 => ['name' => 'Annual User Plan', 'price' => 199.99, 'duration' => 365, 'type' => 'user']
    ];
    
    if (!isset($plans[$plan_id])) {
        $error = 'Invalid plan selected';
    } else {
        $plan = $plans[$plan_id];
        $conn->begin_transaction();
        
        try {
            // Create subscription
            $started_at = date('Y-m-d H:i:s');
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$plan['duration']} days"));
            
            $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan_type, plan_name, price, duration_days, started_at, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdiss", $user_id, $plan['type'], $plan['name'], $plan['price'], $plan['duration'], $started_at, $expires_at);
            $stmt->execute();
            $subscription_id = $conn->insert_id;
            
            // Update user subscription status
            $stmt = $conn->prepare("UPDATE users SET subscription_status = 'active', subscription_expires_at = ? WHERE id = ?");
            $stmt->bind_param("si", $expires_at, $user_id);
            $stmt->execute();
            
            // Create payment record
            $stmt = $conn->prepare("INSERT INTO payments (user_id, subscription_id, amount, payment_type, status, transaction_id) VALUES (?, ?, ?, 'subscription', 'completed', ?)");
            $transaction_id = 'TXN' . time() . $user_id;
            $stmt->bind_param("iids", $user_id, $subscription_id, $plan['price'], $transaction_id);
            $stmt->execute();
            
            $conn->commit();
            $success = "Subscription activated successfully! Expires on " . date('M d, Y', strtotime($expires_at));
            
            // Update session
            $_SESSION['subscription_status'] = 'active';
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Failed to process subscription. Please try again.';
        }
    }
}

// Get current subscription
$subscription_query = "SELECT * FROM subscriptions WHERE user_id = ? AND status = 'active' AND expires_at > NOW() ORDER BY expires_at DESC LIMIT 1";
$stmt = $conn->prepare($subscription_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$subscription_result = $stmt->get_result();
$current_subscription = $subscription_result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Subscription Plans</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($current_subscription): ?>
            <div class="current-subscription">
                <h2>Current Subscription</h2>
                <div class="subscription-card active">
                    <h3><?php echo htmlspecialchars($current_subscription['plan_name']); ?></h3>
                    <p>Expires: <?php echo date('M d, Y', strtotime($current_subscription['expires_at'])); ?></p>
                    <p>Price: $<?php echo number_format($current_subscription['price'], 2); ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="subscription-plans">
            <h2>Available Plans</h2>
            <div class="plans-grid">
                <div class="plan-card">
                    <h3>Basic Plan</h3>
                    <div class="plan-price">$29.99</div>
                    <div class="plan-duration">30 Days</div>
                    <ul class="plan-features">
                        <li>Participate in auctions</li>
                        <li>Place unlimited bids</li>
                        <li>View all active auctions</li>
                    </ul>
                    <form method="POST">
                        <input type="hidden" name="plan_id" value="1">
                        <button type="submit" name="purchase_subscription" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
                
                <div class="plan-card featured">
                    <div class="plan-badge">Popular</div>
                    <h3>Premium Plan</h3>
                    <div class="plan-price">$49.99</div>
                    <div class="plan-duration">60 Days</div>
                    <ul class="plan-features">
                        <li>Everything in Basic</li>
                        <li>Priority support</li>
                        <li>Early access to new auctions</li>
                    </ul>
                    <form method="POST">
                        <input type="hidden" name="plan_id" value="2">
                        <button type="submit" name="purchase_subscription" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
                
                <div class="plan-card">
                    <h3>Annual Plan</h3>
                    <div class="plan-price">$199.99</div>
                    <div class="plan-duration">365 Days</div>
                    <ul class="plan-features">
                        <li>Everything in Premium</li>
                        <li>Best value</li>
                        <li>Save $400+ per year</li>
                    </ul>
                    <form method="POST">
                        <input type="hidden" name="plan_id" value="3">
                        <button type="submit" name="purchase_subscription" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

