<?php
// add_student.php
header('Content-Type: text/html; charset=utf-8');
require 'db.php';

$message = '';
$message_type = '';

$groups = $pdo->query("SELECT * FROM groups ORDER BY name")->fetchAll();
$computers = $pdo->query("SELECT * FROM computers ORDER BY CAST(SUBSTRING(number, 11) AS UNSIGNED)")->fetchAll();

$selected_group = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fio = trim($_POST['fio'] ?? '');
    $group_id = (int)($_POST['group_id'] ?? 0);
    $computer_id = (int)($_POST['computer_id'] ?? 0);
    
    if (empty($fio)) {
        $message = 'ФИО не может быть пустым';
        $message_type = 'error';
    } elseif ($group_id == 0) {
        $message = 'Выберите группу';
        $message_type = 'error';
    } else {
        $stmt = $pdo->prepare("INSERT INTO students (fio, group_id, computer_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$fio, $group_id, $computer_id])) {
            $message = "Студент '$fio' успешно добавлен!";
            $message_type = 'success';
        } else {
            $message = 'Ошибка при добавлении студента';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить студента</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <h1>Система учета рабочих мест студентов</h1>
    <div class="nav">
        <a href="index.php">Главная</a>
        <a href="add_group.php">Добавить группу</a>
        <a href="add_student.php" class="active">Добавить студента</a>
    </div>
</div>

<div class="container">
    <h2 class="group-title">Добавление нового студента</h2>
    
    <?php if ($message): ?>
        <div class="message message-<?= $message_type ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="add_student.php">
        <div class="form-group">
            <label for="fio">ФИО студента:</label>
            <input type="text" name="fio" id="fio" required placeholder="Иванов Иван Иванович">
        </div>
        
        <div class="form-group">
            <label for="group_id">Группа:</label>
            <select name="group_id" id="group_id" required>
                <option value="">-- Выберите группу --</option>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $selected_group == $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="computer_id">Рабочее место:</label>
            <select name="computer_id" id="computer_id" required>
                <option value="">-- Выберите компьютер --</option>
                <?php foreach ($computers as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Добавить студента</button>
            <a href="index.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>