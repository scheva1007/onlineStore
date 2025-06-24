<?php

include "price.php";

$cart = [];

if (isset($_COOKIE['cart']) && $_COOKIE['cart'] !== '') {
    $items = explode(',', $_COOKIE['cart']);
    foreach ($items as $item) {
        [$id, $quantity] = explode(':', $item);
        $cart[$id] = (int)$quantity;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = $_POST['id'];
    if (isset($cart[$id]) && isset($products[$id])) {
        $quantity = $products[$id]['quantity'];
        if ($_POST['action'] == 'plus') {
            if ($cart[$id] < $quantity) {
                $cart[$id]++;
            }
        } elseif ($_POST['action'] == 'minus') {
             $cart[$id] --;
             if ($cart[$id] <= 0) {
                 unset($cart[$id]);
             }
        }
        $newCart = [];
        foreach ($cart as $pid => $quantity) {
            $newCart[] = "$pid:$quantity";
        }
        setcookie('cart', implode(',', $newCart), time() + 2000, '/');
    }
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
          foreach ($cart as $id => $quantity):
              if (isset($products[$id])):
                  $product = $products[$id];
                  $summa = $product['price']*$quantity;
                  $total += $summa; ?>
          <p>
              <?= $product['name'] ?> - <?= $product['price'] ?> грн *
              <?= $quantity ?> = <?= $summa ?> грн
              макс: <?= $product['quantity'] ?>
              <form method="post">
                  <input type="hidden" name="id" value="<?= $id ?>">
                  <button type="submit" name="action" value="plus">+</button>
                  <button type="submit" name="action" value="minus">-</button>
              </form>
          </p>
          <?php endif; endforeach; ?>
      </div>
      <p>Загальна сума: <?= $total ?> грн</p>
      <?php endif; ?>
      <a href="index.php">Повернутися на головну сторінку</a>
</body>
</html>