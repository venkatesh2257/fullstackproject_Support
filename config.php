<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bidding_system');

// Site Configuration
define('SITE_NAME', 'Bidding System');
define('SITE_URL', 'http://localhost/fullstackproject_Support');

// Session Configuration
session_start();

// Database Connection
function getDBConnection() {
    static $conn = null;
    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
    return $conn;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Check if user has active subscription
function hasActiveSubscription($userId = null) {
    if ($userId === null) {
        $userId = $_SESSION['user_id'] ?? null;
    }
    if (!$userId) return false;
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT subscription_status, subscription_expires_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) return false;
    
    if ($user['subscription_status'] === 'active') {
        if ($user['subscription_expires_at'] && strtotime($user['subscription_expires_at']) > time()) {
            return true;
        }
    }
    return false;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (getUserRole() !== 'admin') {
        header('Location: index.php');
        exit();
    }
}

// Redirect if not merchant
function requireMerchant() {
    requireLogin();
    if (getUserRole() !== 'merchant') {
        header('Location: index.php');
        exit();
    }
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

?>

