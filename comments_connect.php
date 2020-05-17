<?php
$databaseHost = 'localhost';
$databaseName = 'laravel58';
$databaseUsername = 'root';
$databasePassword = '';

try {
    $dbConn = new PDO("mysql:host={$databaseHost};dbname={$databaseName}", $databaseUsername, $databasePassword);

    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Setting Error Mode as Exception
    // More on setAttribute: http://php.net/manual/en/pdo.setattribute.php
} catch (PDOException $e) {
    $feedback_message = $e->getMessage();
}
