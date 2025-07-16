<?php
session_start();
include 'users.php';
include 'price.php';

$userId = $_SESSION['user_id'] ?? null;
$cartKey = $userId ? "cart_$userId" : 'cart_guest';
$products = products();
$cart = cartCookie($cartKey);

$cart = addCart($cart, $cartKey, $products);

function cartCookie($cartKey)
{
    $cart = [];
    if (isset($_COOKIE[$cartKey])) {
        $items = explode(',', $_COOKIE[$cartKey]);
        foreach ($items as $item) {
            $array = explode(':', $item);
            if (count($array) == 2) {
                $id = $array[0];
                $quantity = $array[1];
                $cart[$id] = $quantity;
            }
        }
    }

    return $cart;
}

function saveCartCookie($cart, $cartKey) {
    $newCart = [];
    foreach ($cart as $id => $quantity) {
        $newCart[] = "$id:$quantity";
    }
    setcookie($cartKey, implode(',', $newCart), time() + 2000, '/');
}

function addCart($cart, $cartKey, $products) {
    if (isset($_GET['id'])) {
        $productId = $_GET['id'];
        if (isset($products[$productId]) && $products[$productId]['quantity'] > 0) {
            if (!isset($cart[$productId])) {
                $cart[$productId] = 1;
            }
            saveCartCookie($cart, $cartKey);
        }
        header('Location:index.php');
        exit;
    }

    return $cart;
}

function products() {
    return json_decode(file_get_contents('products.json'), true);
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
   <p>
       Вітаємо, <?= $users[$userId]['login'] ?> <a href="logout.php">Вийти</a>
   </p>
       <a href="purchaseHistory.php">Переглянути історію покупок</a>
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
