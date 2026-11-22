<?php
// delete_group.php
header('Content-Type: text/html; charset=utf-8');
require 'db.php';

$group_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($group_id == 0) {
    header('Location: index.php');
    exit;
}

// Проверяем, есть ли группа
$stmt = $pdo->prepare("SELECT name FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    header('Location: index.php');
    exit;
}

// Удаляем группу (студенты удалятся автоматически благодаря ON DELETE CASCADE)
$stmt = $pdo->prepare("DELETE FROM groups WHERE id = ?");
if ($stmt->execute([$group_id])) {
    header('Location: index.php?message=group_deleted');
    exit;
} else {
    echo 'Ошибка при удалении группы';
    echo '<br><a href="index.php">Вернуться на главную</a>';
}
?>