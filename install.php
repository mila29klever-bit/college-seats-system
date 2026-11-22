<?php
// install.php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS college_seats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE college_seats");

    $pdo->exec("CREATE TABLE IF NOT EXISTS groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS computers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        number VARCHAR(50) NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT,
        fio VARCHAR(255) NOT NULL,
        computer_id INT,
        FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
        FOREIGN KEY (computer_id) REFERENCES computers(id) ON DELETE SET NULL
    )");

    $stmt = $pdo->query("SELECT COUNT(*) FROM groups");
    if ($stmt->fetchColumn() == 0) {
        $groups = ['ИСиП-231', 'ИСиП-241', 'ВР-261', 'ПД-251', 'ПД-261'];
        $stmtGroup = $pdo->prepare("INSERT INTO groups (name) VALUES (?)");
        foreach ($groups as $g) $stmtGroup->execute([$g]);

        $stmtComp = $pdo->prepare("INSERT INTO computers (number) VALUES (?)");
        for ($i = 1; $i <= 20; $i++) {
            $stmtComp->execute(["Компьютер $i"]);
        }

        $surnames = ['Иванов', 'Петров', 'Сидоров', 'Смирнов', 'Кузнецов', 'Попов', 'Лебедев', 'Козлов', 'Новиков', 'Морозов'];
        $names = ['Иван', 'Петр', 'Алексей', 'Дмитрий', 'Сергей', 'Анна', 'Мария', 'Елена', 'Ольга', 'Дарья'];
        
        $stmtStud = $pdo->prepare("INSERT INTO students (group_id, fio, computer_id) VALUES (?, ?, ?)");
        
        $groupId = 1;
        $compId = 1;
        for ($g = 0; $g < 5; $g++) {
            for ($s = 1; $s <= 18; $s++) {
                $fio = $surnames[array_rand($surnames)] . ' ' . mb_substr($names[array_rand($names)], 0, 1) . '. ' . mb_substr($names[array_rand($names)], 0, 1) . '.';
                $stmtStud->execute([$groupId, $fio, $compId]);
                $compId++;
                if ($compId > 20) $compId = 1;
            }
            $groupId++;
        }
    }
    
    // Автоматический переход на главную страницу
    header('Location: index.php');
    exit;
    
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>