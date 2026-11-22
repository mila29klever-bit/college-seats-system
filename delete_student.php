<?php
// delete_student.php
header('Content-Type: text/html; charset=utf-8');
require 'db.php';

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id == 0) {
    header('Location: index.php');
    exit;
}

// Получаем данные студента для возврата в нужную группу
$stmt = $pdo->prepare("SELECT group_id FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: index.php');
    exit;
}

// Удаляем студента
$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
if ($stmt->execute([$student_id])) {
    header('Location: index.php?group_id=' . $student['group_id']);
    exit;
} else {
    echo 'Ошибка при удалении студента';
    echo '<br><a href="index.php">Вернуться на главную</a>';
}
?>