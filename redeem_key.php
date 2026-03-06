<?php
require_once 'functions.php';

header('Content-Type: application/json');

$ip = getUserIP();
$result = redeemKey($ip);

echo json_encode($result);
?>
