<?php
    include 'price.php';

    $cart = [];
    if (isset($_COOKIE['cart']) && $_COOKIE['cart'] !== '') {
        $items = explode(',', $_COOKIE['cart']);
        foreach ($items as $item) {
            [$id, $quantity] = explode(':', $item);
            $cart[$id] = (int)$quantity;
        }
    }

    if (isset($_GET['id'])) {
        $productId = $_GET['id'];

        if (isset($products[$productId])) {
            if (!isset($cart[$productId])) {
                $cart[$productId] = 1;
            }
        }
            $newCart = [];
        foreach ($cart as $id => $quantity) {
            $newCart[] = "$id:$quantity";
        }
        setcookie('cart', implode(',', $newCart), time() + 2000, '/');

        header('Location: index.php');
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
   <h3>Список товарів</h3>
   <div>
<?php foreach ($products as $id => $product): ?>
    <p><?= $product['name'] ?> - <?= $product['price'] ?> грн
        <?php if (isset($cart[$id])): ?>
        <span style="color: green;">В корзині</span>
        <?php else: ?>
       <a href="?id=<?= $id ?>">Додати в кошик</a>
    <?php endif; ?></p>
       <?php endforeach ?>
   </div>
   <a href="cart.php">Перейти до кошика</a>
</body>


</html>
