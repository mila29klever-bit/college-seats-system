<?php
// export.php
require 'db.php';

$export_all = isset($_GET['all']);
$group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
$filename = $export_all ? "Все_группы.xls" : "Группа_" . $group_id . ".xls";
header("Content-Disposition: attachment; filename=\"$filename\"");

echo "\xEF\xBB\xBF"; 

echo "
<style>
    body { font-family: 'Cascadia Mono', 'Consolas', 'Courier New', monospace; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th { background-color: #0033A0; color: #FFD700; border: 1px solid #002147; padding: 8px; font-weight: 500; }
    td { border: 1px solid #ccc; padding: 6px 8px; }
    .group-title { color: #0033A0; font-size: 14px; font-weight: bold; margin-top: 15px; margin-bottom: 5px; }
</style>
";

function exportGroup($pdo, $group_id, $group_name) {
    echo "<p class='group-title'>Группа: $group_name</p>";
    echo "<table>
            <tr><th>№</th><th>ФИО Студента</th><th>Рабочее место</th></tr>";
    
    $stmt = $pdo->prepare("
        SELECT s.fio, c.number 
        FROM students s
        LEFT JOIN computers c ON s.computer_id = c.id
        WHERE s.group_id = ?
        ORDER BY s.fio
    ");
    $stmt->execute([$group_id]);
    $students = $stmt->fetchAll();
    
    $i = 1;
    foreach ($students as $s) {
        echo "<tr>
                <td>{$i}</td>
                <td>{$s['fio']}</td>
                <td>{$s['number']}</td>
              </tr>";
        $i++;
    }
    echo "</table><br>";
}

if ($export_all) {
    $groups = $pdo->query("SELECT * FROM groups ORDER BY name")->fetchAll();
    foreach ($groups as $g) {
        exportGroup($pdo, $g['id'], $g['name']);
    }
} elseif ($group_id > 0) {
    $stmt = $pdo->prepare("SELECT name FROM groups WHERE id = ?");
    $stmt->execute([$group_id]);
    $group = $stmt->fetch();
    if ($group) {
        exportGroup($pdo, $group_id, $group['name']);
    }
}
?>