<?php

require_once '../includes/config.php';

if ($conn->connect_error) {
    die("Database error");
}

$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$id || !$status) {
    die("Missing ID or status");
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: index.php");
exit;
?>