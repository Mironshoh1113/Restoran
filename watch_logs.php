<?php

// Simple log watcher for webhook debugging
$logFile = 'storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "❌ Log file not found: $logFile\n";
    echo "Creating empty log file...\n";
    file_put_contents($logFile, '');
}

echo "🔍 Watching Laravel logs for webhook activity...\n";
echo "📁 Log file: $logFile\n";
echo "🤖 Send /start to your bot now!\n";
echo str_repeat("=", 60) . "\n\n";

// Get current file size
$lastSize = filesize($logFile);

while (true) {
    clearstatcache();
    $currentSize = filesize($logFile);
    
    if ($currentSize > $lastSize) {
        // New content added
        $handle = fopen($logFile, 'r');
        fseek($handle, $lastSize);
        
        while (($line = fgets($handle)) !== false) {
            // Filter for webhook-related logs
            if (strpos($line, 'Webhook') !== false || 
                strpos($line, 'telegram') !== false ||
                strpos($line, 'TelegramController') !== false ||
                strpos($line, 'Processing message') !== false ||
                strpos($line, 'Restaurant found') !== false ||
                strpos($line, 'Attempting to send') !== false) {
                
                echo date('H:i:s') . " | " . trim($line) . "\n";
            }
        }
        
        fclose($handle);
        $lastSize = $currentSize;
    }
    
    usleep(500000); // Sleep for 0.5 seconds
} 