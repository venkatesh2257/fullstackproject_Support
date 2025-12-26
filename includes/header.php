<?php
// Determine base path based on current directory
$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/merchant/') !== false) {
    $base_path = '../';
}
if (!isset($conn)) require_once $base_path . 'config.php';
?>
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="<?php echo $base_path; ?>index.php"><?php echo SITE_NAME; ?></a>
            </div>
            <nav class="main-nav">
                <a href="<?php echo $base_path; ?>index.php">Home</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (getUserRole() === 'admin'): ?>
                        <a href="<?php echo $base_path; ?>admin/dashboard.php">Admin Dashboard</a>
                    <?php elseif (getUserRole() === 'merchant'): ?>
                        <a href="<?php echo $base_path; ?>merchant/dashboard.php">Merchant Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo $base_path; ?>user/dashboard.php">My Dashboard</a>
                    <?php endif; ?>
                    <a href="<?php echo $base_path; ?>logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>login.php">Login</a>
                    <a href="<?php echo $base_path; ?>register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

