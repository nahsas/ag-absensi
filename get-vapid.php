<?php
require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\VAPID;

$vapidKeys = VAPID::createVapidKeys();

echo "Public Key: " . $vapidKeys['publicKey'] . "\n";
echo "Private Key: " . $vapidKeys['privateKey'] . "\n";
?>