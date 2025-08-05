<?php

/**
 * Git Webhook Handler for Laravel Auto Deployment
 * Place this file in your public directory or create a route for it
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file
$logFile = '/var/log/webhook.log';

// Function to log messages
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Get the raw POST data
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Verify the request is from Git (optional but recommended)
$signature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';
$expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, 'YOUR_WEBHOOK_SECRET');

if (!hash_equals($expectedSignature, $signature)) {
    logMessage('Invalid signature');
    http_response_code(403);
    exit('Invalid signature');
}

// Parse the JSON payload
$data = json_decode($payload, true);

if (!$data) {
    logMessage('Invalid JSON payload');
    http_response_code(400);
    exit('Invalid JSON payload');
}

// Check if this is a push to the main branch
$ref = $data['ref'] ?? '';
$branch = str_replace('refs/heads/', '', $ref);

if ($branch !== 'main' && $branch !== 'master') {
    logMessage("Ignoring push to branch: $branch");
    http_response_code(200);
    exit('Ignoring non-main branch');
}

// Log the deployment trigger
logMessage("Deployment triggered for branch: $branch");

// Execute the deployment script
$deployScript = '/var/www/restaurant-order-system/deploy.sh';
$output = [];
$returnCode = 0;

if (file_exists($deployScript)) {
    exec("bash $deployScript 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        logMessage('Deployment completed successfully');
        http_response_code(200);
        echo 'Deployment completed successfully';
    } else {
        logMessage('Deployment failed: ' . implode("\n", $output));
        http_response_code(500);
        echo 'Deployment failed';
    }
} else {
    logMessage('Deployment script not found: ' . $deployScript);
    http_response_code(500);
    echo 'Deployment script not found';
} 