<?php
declare(strict_types=1);

$host = '127.0.0.1';
$database = 'game_developer_applications';
$username = 'root';
$password = '';

$dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Could not connect to the database. Start MySQL in XAMPP and import database/schema.sql.');
}
