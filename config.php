<?php
session_start();

// Database configuration
define('DB_PATH', __DIR__ . '/database.sqlite');
define('FRESH_KEYS_FILE', __DIR__ . '/keys/fresh_keys.txt');
define('REDEEMED_KEYS_FILE', __DIR__ . '/keys/redeemed_keys.txt');
define('COOLDOWN_HOURS', 6); // Cooldown period in hours
define('SITE_NAME', 'Key Generator Portal');

// Create keys directory if not exists
if (!file_exists(__DIR__ . '/keys')) {
    mkdir(__DIR__ . '/keys', 0777, true);
}

// Create fresh keys file if not exists
if (!file_exists(FRESH_KEYS_FILE)) {
    file_put_contents(FRESH_KEYS_FILE, '');
}

// Create redeemed keys file if not exists
if (!file_exists(REDEEMED_KEYS_FILE)) {
    file_put_contents(REDEEMED_KEYS_FILE, '');
}

// Initialize SQLite database
try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create admins table
    $db->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create cooldowns table
    $db->exec("CREATE TABLE IF NOT EXISTS user_cooldowns (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip_address TEXT NOT NULL,
        last_redeemed DATETIME NOT NULL,
        UNIQUE(ip_address)
    )");
    
    // Insert default admin if not exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashed_password]);
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
