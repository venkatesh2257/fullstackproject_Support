<?php
/**
 * Fix Admin Password Script
 * Run this once to set the correct admin password
 * Access: http://localhost/fullstackproject_Support/fix_admin_password.php
 */

require_once 'config.php';

$conn = getDBConnection();

// Generate correct password hash for "admin123"
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update admin password
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'admin@bidding.com'");
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "<h2 style='color: green;'>✅ Admin password updated successfully!</h2>";
    echo "<p><strong>Email:</strong> admin@bidding.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
} else {
    echo "<h2 style='color: red;'>❌ Error updating password: " . $conn->error . "</h2>";
}

$stmt->close();

// Also check if admin user exists, if not create it
$check = $conn->query("SELECT id FROM users WHERE email = 'admin@bidding.com'");
if ($check->num_rows === 0) {
    // Create admin user if it doesn't exist
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, subscription_status) VALUES (?, ?, ?, 'admin', 'active')");
    $username = 'admin';
    $email = 'admin@bidding.com';
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "<h2 style='color: green;'>✅ Admin user created successfully!</h2>";
    } else {
        echo "<h2 style='color: red;'>❌ Error creating admin: " . $conn->error . "</h2>";
    }
    $stmt->close();
}

$conn->close();
?>

