<?php
session_start();
include 'users.php';
include 'price.php';

$userId = $_SESSION['user_id'] ?? null;
$cartKey = $userId ? "cart_$userId" : 'cart_guest';

$cart = [];

if (isset($_COOKIE[$cartKey]) && $_COOKIE[$cartKey] !== '') {
    $items = explode(',', $_COOKIE[$cartKey]);
    foreach ($items as $item) {
        [$id, $quantity] = explode(':', $item);
        $cart[$id] = (int)$quantity;
    }
}

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    if (isset($products[$productId]) && $products[$productId]['quantity'] > 0) {
        if (!isset($cart[$productId])) {
            $cart[$productId] = 1;
        }
    }
    $newCart = [];
    foreach ($cart as $id => $quantity) {
        $newCart[] = "$id:$quantity";
    }
    setcookie($cartKey, implode(',', $newCart), time() + 2000, '/');
    header('Location:index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Інтернет-магазин</title>
</head>
<body>
   <?php if ($userId): ?>
   <p>Вітаємо, <?= $users[$userId]['login'] ?> <a href="logout.php">Вийти</a> </p>
   <?php else: ?> <a href="login.php">Увійти</a>
   <?php endif; ?>
   <h4>Список товарів</h4>
   <div>
<?php foreach ($products as $id => $product): ?>
    <p><?= $product['name'] ?> - <?= $product['price'] ?> грн
        <?php if ($product['quantity'] > 0): ?>
            залишок: <?= $product['quantity'] ?>
        <?php endif; ?>
        <?php if ($product['quantity'] == 0): ?>
        <span style="color: red">Немає в наявності</span>
        <?php elseif (isset($cart[$id])): ?>
        <span style="color: green;">В корзині</span>
        <?php else: ?>
       <a href="?id=<?= $id ?>">Додати в кошик</a>
    <?php endif; ?></p>
       <?php endforeach ?>
   </div>
   <a href="cart.php">Перейти до кошика</a>
</body>


</html>
