<?php
require_once 'config.php';

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function canRedeemKey($ip) {
    global $db;
    
    $stmt = $db->prepare("SELECT last_redeemed FROM user_cooldowns WHERE ip_address = ?");
    $stmt->execute([$ip]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        return true;
    }
    
    $last_redeemed = strtotime($result['last_redeemed']);
    $hours_passed = (time() - $last_redeemed) / 3600;
    
    return $hours_passed >= COOLDOWN_HOURS;
}

function getRemainingTime($ip) {
    global $db;
    
    $stmt = $db->prepare("SELECT last_redeemed FROM user_cooldowns WHERE ip_address = ?");
    $stmt->execute([$ip]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        return 0;
    }
    
    $last_redeemed = strtotime($result['last_redeemed']);
    $next_available = $last_redeemed + (COOLDOWN_HOURS * 3600);
    $remaining = $next_available - time();
    
    return max(0, $remaining);
}

function getFreshKeyCount() {
    $keys = file(FRESH_KEYS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return count($keys);
}

function getRedeemedKeyCount() {
    $keys = file(REDEEMED_KEYS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return count($keys);
}

function getFreshKeys() {
    return file(FRESH_KEYS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function getRedeemedKeys() {
    return file(REDEEMED_KEYS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function redeemKey($ip) {
    global $db;
    
    // Check if user can redeem
    if (!canRedeemKey($ip)) {
        return ['success' => false, 'message' => 'Please wait for cooldown period'];
    }
    
    // Get fresh keys
    $freshKeys = getFreshKeys();
    if (empty($freshKeys)) {
        return ['success' => false, 'message' => 'No keys available'];
    }
    
    // Get first key
    $key = array_shift($freshKeys);
    
    // Update files
    file_put_contents(FRESH_KEYS_FILE, implode(PHP_EOL, $freshKeys));
    
    $redeemedKeys = getRedeemedKeys();
    $redeemedKeys[] = $key . ' - Redeemed on: ' . date('Y-m-d H:i:s') . ' by IP: ' . $ip;
    file_put_contents(REDEEMED_KEYS_FILE, implode(PHP_EOL, $redeemedKeys));
    
    // Update cooldown
    $stmt = $db->prepare("INSERT OR REPLACE INTO user_cooldowns (ip_address, last_redeemed) VALUES (?, ?)");
    $stmt->execute([$ip, date('Y-m-d H:i:s')]);
    
    return ['success' => true, 'key' => $key];
}

function addBulkKeys($keys) {
    $existingKeys = getFreshKeys();
    $newKeys = array_merge($existingKeys, $keys);
    $newKeys = array_unique($newKeys); // Remove duplicates
    file_put_contents(FRESH_KEYS_FILE, implode(PHP_EOL, $newKeys));
}

function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>
