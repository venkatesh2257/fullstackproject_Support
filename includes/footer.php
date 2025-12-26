<?php
// Determine base path
$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/merchant/') !== false) {
    $base_path = '../';
}
if (!defined('SITE_NAME')) {
    require_once $base_path . 'config.php';
}
?>
<footer class="main-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </div>
</footer>

