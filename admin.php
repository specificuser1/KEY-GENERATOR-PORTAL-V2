<?php
require_once 'functions.php';

// Check if admin is logged in
if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Handle bulk key addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_keys'])) {
    $keys = array_filter(array_map('trim', explode("\n", $_POST['bulk_keys'])));
    if (!empty($keys)) {
        addBulkKeys($keys);
        $message = 'Keys added successfully!';
    } else {
        $error = 'Please enter at least one key';
    }
}

// Get keys for display
$freshKeys = getFreshKeys();
$redeemedKeys = getRedeemedKeys();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container admin-container">
        <div class="header">
            <h1>🔐 Admin Panel</h1>
            <p>Manage your keys and monitor usage</p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <?php if ($message): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value"><?php echo count($freshKeys); ?></div>
                <div class="stat-label">Fresh Keys</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($redeemedKeys); ?></div>
                <div class="stat-label">Redeemed Keys</div>
            </div>
        </div>
        
        <div class="admin-grid">
            <div class="admin-card">
                <h2>Add Bulk Keys</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="bulk_keys">Enter keys (one per line):</label>
                        <textarea id="bulk_keys" name="bulk_keys" rows="10" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Add Keys</button>
                </form>
            </div>
            
            <div class="admin-card">
                <h2>Fresh Keys (<?php echo count($freshKeys); ?>)</h2>
                <div class="keys-list fresh-keys">
                    <?php foreach ($freshKeys as $key): ?>
                        <div class="key-item"><?php echo htmlspecialchars($key); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="admin-card full-width">
                <h2>Redeemed Keys (<?php echo count($redeemedKeys); ?>)</h2>
                <div class="keys-list redeemed-keys">
                    <?php foreach ($redeemedKeys as $key): ?>
                        <div class="key-item"><?php echo htmlspecialchars($key); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
