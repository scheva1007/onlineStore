<?php

include "price.php";

$cart = [];

if (isset($_COOKIE['cart']) && $_COOKIE['cart'] !== '') {
    $cart = explode(',', $_COOKIE['cart']);
}

if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    $newCart = [];

    foreach ($cart as $item) {
        if ($item !== $removeId) {
            $newCart[] = $item;
        }
    }
    $cart = $newCart;
    setcookie('cart', implode(',', $cart), time() + 30);
    header('Location: cart.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кошик</title>
</head>
<body>
   <h3>Мій кошик</h3>
      <?php if (empty($cart)): ?>
      <p>Кошик порожній</p>
      <?php else: ?>
      <div>
          <?php
          $total = 0;
          foreach ($cart as $id):
              if (isset($products[$id])):
                  $product = $products[$id];
                  $total += $product['price']; ?>
          <p>
              <?= $product['name'] ?> - <?= $product['price'] ?> грн
              <a href="?remove=<?= $id ?>">Видалити</a>
          </p>
          <?php endif; endforeach; ?>
      </div>
      <p>Загальна сума: <?= $total ?> грн</p>
      <?php endif; ?>
      <a href="index.php">Повернутися на головну сторінку</a>
</body>
</html>