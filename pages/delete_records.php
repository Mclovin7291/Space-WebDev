<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include('../includes/db.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM space_discoveries WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'id' => $id,
        'user_id' => $_SESSION['user_id']
    ]);
}

header('Location: /webdev/pages/view_records.php');
exit;
?>
