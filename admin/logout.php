<?php require_once __DIR__ . '/../inc/bootstrap.php'; if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check(); $_SESSION = []; session_destroy(); } header('Location: login.php');
