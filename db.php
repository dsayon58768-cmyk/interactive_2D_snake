<?php
// Change these values if your local MySQL setup uses different settings.
$host = 'localhost';
$dbname = 'snake_bite';
$dbuser = 'sayon';
$dbpass = '2005das@#say';

$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser,
    $dbpass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);
