<?php
// db.example.php — скопируйте в db.php и заполните своими данными
$host = 'localhost';
$db   = 'college_seats';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>