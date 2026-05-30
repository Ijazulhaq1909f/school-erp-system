<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');
$conn->query("UPDATE teachers SET status=0 WHERE id={$_GET['id']}");
header('Location: ./');
?>