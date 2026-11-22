<?php
// add_group.php
header('Content-Type: text/html; charset=utf-8');
require 'db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = trim($_POST['group_name'] ?? '');
    
    if (empty($group_name)) {
        $message = 'Название группы не может быть пустым';
        $message_type = 'error';
    } else {
        $stmt = $pdo->prepare("INSERT INTO groups (name) VALUES (?)");
        if ($stmt->execute([$group_name])) {
            $message = "Группа '$group_name' успешно добавлена!";
            $message_type = 'success';
        } else {
            $message = 'Ошибка при добавлении группы';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить группу</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <h1>Система учета рабочих мест студентов</h1>
    <div class="nav">
        <a href="index.php">Главная</a>
        <a href="add_group.php" class="active">Добавить группу</a>
        <a href="add_student.php">Добавить студента</a>
    </div>
</div>

<div class="container">
    <h2 class="group-title">Добавление новой группы</h2>
    
    <?php if ($message): ?>
        <div class="message message-<?= $message_type ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="add_group.php">
        <div class="form-group">
            <label for="group_name">Название группы:</label>
            <input type="text" name="group_name" id="group_name" required placeholder="Например: ИСиП-271">
            <button type="submit" class="btn btn-primary">Добавить</button>
        </div>
    </form>
</div>

</body>
</html>