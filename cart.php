<?php

session_start();
include 'users.php';


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
   <p style="color: green">Ваш баланс: <?= $balance ?> грн</p>
<?php if (empty($cart)): ?>
   <p>Кошик порожній</p>
<?php else: ?>
    <div>
        <?php $total = 0 ?>
       <?php foreach($cart as $id => $quantity):
           if (isset($products[$id])):
               $product = $products[$id];
               $summa = $product['price']*$quantity;
               $total += $summa; ?>
        <p>
            <?= $product['name'] ?> - <?= $product['price'] ?> грн *
            <?= $quantity ?> = <?= $summa ?> грн
            макс: <?= $product['quantity'] ?> шт.

        <form method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" name="action" value="plus">+</button>
            <button type="submit" name="action" value="minus">-</button>
        </form>
        </p>
        <?php endif; endforeach; ?>
    </div>
        <p>Загальна сума: <?= $total ?> грн </p>
<?php endif; ?>
    <form method="post" style="margin-bottom: 10px;">
        <button type="submit" name="buy" value="1">Купити</button>
    </form>
<a href="index.php">Повернутися на головну сторінку</a>
</body>
</html>