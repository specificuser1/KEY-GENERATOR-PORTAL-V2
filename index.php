<?php
require_once 'functions.php';

$ip = getUserIP();
$remainingTime = getRemainingTime($ip);
$canRedeem = $remainingTime == 0;
$freshKeyCount = getFreshKeyCount();
$redeemedKeyCount = getRedeemedKeyCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Get Your Key</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Key Generator Portal</h1>
            <p>Generate your unique key with just one click</p>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value"><?php echo $freshKeyCount; ?></div>
                <div class="stat-label">Fresh Keys Available</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $redeemedKeyCount; ?></div>
                <div class="stat-label">Keys Redeemed</div>
            </div>
        </div>
        
        <div class="main-card">
            <div class="key-stock-info">
                <div class="stock-bar">
                    <div class="stock-fill" style="width: <?php echo $freshKeyCount > 0 ? min(100, ($freshKeyCount / ($freshKeyCount + $redeemedKeyCount)) * 100) : 0; ?>%"></div>
                </div>
                <div class="stock-labels">
                    <span>Fresh: <?php echo $freshKeyCount; ?></span>
                    <span>Redeemed: <?php echo $redeemedKeyCount; ?></span>
                </div>
            </div>
            
            <button id="getKeyBtn" class="get-key-btn" <?php echo !$canRedeem ? 'disabled' : ''; ?>>
                <?php echo $canRedeem ? 'Get Key' : 'Wait for cooldown'; ?>
            </button>
            
            <?php if (!$canRedeem): ?>
                <div class="cooldown-timer" id="cooldownTimer" data-remaining="<?php echo $remainingTime; ?>">
                    Next key available in: <span id="timer"></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Key Popup Modal -->
    <div id="keyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Your Generated Key</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="key-display" id="generatedKey"></div>
                <div class="cooldown-info">
                    <p>Next key available in: <span id="nextKeyTimer">6 hours</span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="copy-btn" onclick="copyKey()">Copy Key</button>
                <button class="close-modal-btn" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>
