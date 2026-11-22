<?php
// index.php
header('Content-Type: text/html; charset=utf-8');
require 'db.php';

$selected_group = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
$search_computer = isset($_GET['search_computer']) ? (int)$_GET['search_computer'] : 0;

$groups = $pdo->query("SELECT * FROM groups ORDER BY name")->fetchAll();
$computers = $pdo->query("SELECT * FROM computers ORDER BY CAST(SUBSTRING(number, 11) AS UNSIGNED)")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Распределение рабочих мест</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <h1>Система учета рабочих мест студентов</h1>
    <div class="nav">
        <a href="index.php" class="active">Главная</a>
        <a href="add_group.php">Добавить группу</a>
        <a href="add_student.php">Добавить студента</a>
    </div>
</div>

<div class="container">

    <!-- Сообщение об успешном удалении группы -->
    <?php if (isset($_GET['message']) && $_GET['message'] == 'group_deleted'): ?>
        <div class="message message-success">Группа успешно удалена!</div>
    <?php endif; ?>

    <!-- Сообщение об успешном удалении студента -->
    <?php if (isset($_GET['message']) && $_GET['message'] == 'student_deleted'): ?>
        <div class="message message-success">Студент успешно удалён!</div>
    <?php endif; ?>

    <!-- Блок поиска по компьютеру -->
    <div class="search-box">
        <h3>Поиск по рабочему месту</h3>
        <form method="GET" action="index.php" class="form-group">
            <label for="search_computer">Номер компьютера:</label>
            <select name="search_computer" id="search_computer">
                <option value="">-- Выберите компьютер --</option>
                <?php foreach ($computers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $search_computer == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary">Найти</button>
        </form>

        <?php if ($search_computer > 0): 
            $stmt = $pdo->prepare("
                SELECT s.fio, g.name as group_name, c.number
                FROM students s
                JOIN groups g ON s.group_id = g.id
                JOIN computers c ON s.computer_id = c.id
                WHERE s.computer_id = ?
                ORDER BY g.name, s.fio
            ");
            $stmt->execute([$search_computer]);
            $results = $stmt->fetchAll();
            
            $computer_name = '';
            foreach($computers as $c) { if($c['id'] == $search_computer) $computer_name = $c['number']; }
        ?>
            <h3 class="group-title">Студенты за <?= htmlspecialchars($computer_name) ?></h3>
            <?php if (count($results) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ФИО Студента</th>
                            <th>Группа</th>
                            <th>Рабочее место</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['fio']) ?></td>
                            <td><?= htmlspecialchars($r['group_name']) ?></td>
                            <td><?= htmlspecialchars($r['number']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="message message-error">За этим компьютером никто не закреплён.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <hr>

    <!-- Блок выбора группы -->
    <form method="GET" action="index.php" class="form-group">
        <label for="group_id">Выберите группу:</label>
        <select name="group_id" id="group_id">
            <option value="">-- Выберите группу --</option>
            <?php foreach ($groups as $g): ?>
                <option value="<?= $g['id'] ?>" <?= $selected_group == $g['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-secondary">Показать</button>
        <?php if ($selected_group > 0): ?>
            <a href="delete_group.php?id=<?= $selected_group ?>" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить эту группу? Все студенты этой группы также будут удалены!')">Удалить группу</a>
        <?php endif; ?>
    </form>

    <!-- Кнопки экспорта -->
    <div class="form-group">
        <strong>Экспорт в Excel:</strong>
        <?php if ($selected_group > 0): ?>
            <a href="export.php?group_id=<?= $selected_group ?>" class="btn btn-primary">Экспорт выбранной группы</a>
        <?php endif; ?>
        <a href="export.php?all=1" class="btn btn-primary">Экспорт всех групп</a>
    </div>

    <!-- Таблица студентов выбранной группы -->
    <?php if ($selected_group > 0): 
        $stmt = $pdo->prepare("
            SELECT s.id, s.fio, c.number 
            FROM students s
            LEFT JOIN computers c ON s.computer_id = c.id
            WHERE s.group_id = ?
            ORDER BY s.fio
        ");
        $stmt->execute([$selected_group]);
        $students = $stmt->fetchAll();
        
        $group_name = '';
        foreach($groups as $g) { if($g['id'] == $selected_group) $group_name = $g['name']; }
    ?>
        <h2 class="group-title">Список студентов группы <?= htmlspecialchars($group_name) ?></h2>
        <table>
            <thead>
                <tr>
                    <th>№</th>
                    <th>ФИО Студента</th>
                    <th>Рабочее место</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($students as $s): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($s['fio']) ?></td>
                    <td><?= htmlspecialchars($s['number']) ?></td>
                    <td class="action-btns">
                        <a href="edit_student.php?id=<?= $s['id'] ?>" class="btn btn-warning">Изменить</a>
                        <a href="delete_student.php?id=<?= $s['id'] ?>" class="btn btn-danger" onclick="return confirm('Удалить студента?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            <a href="add_student.php?group_id=<?= $selected_group ?>" class="btn btn-primary">Добавить студента в группу</a>
        </div>
    <?php endif; ?>

</div>

</body>
</html>