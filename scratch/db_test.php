<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'digiturno2';

echo "Testing connection to $host...\n";

try {
    $start = microtime(true);
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass, [
        PDO::ATTR_TIMEOUT => 5, // 5 seconds timeout
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $end = microtime(true);
    echo "Connected successfully in " . ($end - $start) . " seconds.\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
