<?php
session_start();
include 'users.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    foreach ($users as $id => $user) {
        if ($user['login'] == $login && $user['password'] == $password) {
            if (isset($_COOKIE['cart_guest']) && $_COOKIE['cart_guest'] !== '') {
                setcookie("cart_$id", $_COOKIE['cart_guest'], time() + 2000, '/');
                setcookie('cart_guest', '', time() - 2000, '/');
            }
            $_SESSION['user_id'] = $id;
            header('Location:index.php');
            exit;
        }
    }
    $error = 'Невірний пароль або логін';
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід</title>
</head>
<body>
    <form method="post" style="margin-bottom: 10px;">
        <h3>Вхід</h3>
        <?php if ($error): ?>
        <p style="color: red"><?= $error ?></p>
        <?php endif; ?>
        Логін: <input name="login" style="margin-bottom: 10px; margin-left: 12px;"><br>
        Пароль: <input name="password" type="password" style="margin-bottom: 10px;"><br>
        <button type="submit">Увійти</button>
    </form>

    <a href="index.php">Назад</a>
</body>
</html>

