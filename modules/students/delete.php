<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$id = $_GET['id'] ?? 0;
$conn->query("UPDATE students SET status=0 WHERE id=$id");
header('Location: ./');
?>