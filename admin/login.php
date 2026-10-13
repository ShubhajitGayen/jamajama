<?php require_once __DIR__ . '/../inc/bootstrap.php';
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_SESSION['tries'] ?? 0) >= 8 && time() - ($_SESSION['last_try'] ?? 0) < 300) $err = 'Too many attempts. Wait 5 minutes.';
    else {
        $s = db()->prepare('SELECT id,password_hash FROM admins WHERE username=?'); $s->execute([trim($_POST['username'] ?? '')]);
        $u = $s->fetch();
        if ($u && password_verify((string)($_POST['password'] ?? ''), $u['password_hash'])) {
            session_regenerate_id(true); $_SESSION['admin_id'] = (int)$u['id']; $_SESSION['tries'] = 0;
            header('Location: index.php'); exit;
        }
        $_SESSION['tries'] = ($_SESSION['tries'] ?? 0) + 1; $_SESSION['last_try'] = time(); $err = 'Incorrect username or password.';
    }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin login</title><link rel="stylesheet" href="../assets/style.css"></head>
<body><div class="adm" style="max-width:380px"><h1>Admin login</h1><?php if ($err): ?><p class="msg"><?= e($err) ?></p><?php endif; ?>
<form class="box" method="post"><?= csrf_field() ?><label>Username</label><input type="text" name="username" required autofocus><label>Password</label><input type="password" name="password" required><p><button>Log in</button></p></form></div></body></html>
