<?php

session_start();

$userId = $_SESSION['user_id'] ?? '';
$cartKey = $userId ? "cart_{$userId}" : 'cart_guest';
$products = json_decode(file_get_contents('products.json'), true);
$cart = [];

if (isset($_COOKIE[$cartKey]) && $_COOKIE[$cartKey] !== '') {
    $items = explode(',', $_COOKIE[$cartKey]);
    foreach ($items as $id => $item) {
        [$id, $quantity] = explode(':', $item);
        $cart[$id] = (int)$quantity;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = $_POST['id'];
    if (isset($cart[$id]) && isset($products[$id])) {
        $maxQuantity = $products[$id]['quantity'];
        if ($_POST['action'] == 'plus') {
            if ($cart[$id] < $maxQuantity) {
                $cart[$id] ++;
            }
        } elseif ($_POST['action'] == 'minus') {
            $cart[$id] --;
            if ($cart[$id] <= 0) {
                unset($cart[$id]);
            }
        }
    }
    $newCart = [];
    foreach ($cart as $pid => $quantity) {
        $newCart[] = "$pid:$quantity";
    }
    setcookie($cartKey, implode(',', $newCart), time() + 2000);
    header('Location:cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    $purchase = [
        'user_id' => $userId,
        'date' => date('Y-m-d H:s:s'),
        'items' => [],
        'total' => 0
    ];
    foreach ($cart as $id => $quantity) {
        if (isset($products[$id])) {
            $products[$id]['quantity'] -= $quantity;
            if ($products[$id]['quantity'] < 0) {
                $products[$id]['quantity'] = 0;
            }

            $purchase['items'][] = [
                'id' => $id,
                'name' => $products[$id]['name'],
                'price' => $products[$id]['price'],
                'quantity' => $quantity
            ];
            $purchase['total'] += $products[$id]['price']*$quantity;
        }
    }

    $historyFile = 'purchaseHistory.json';
    $history = [];
    if (file_exists($historyFile)) {
    $history = json_decode(file_get_contents($historyFile), true);
    if (!is_array($history)) {
        $history = [];
       }
    }
    $history[] = $purchase;
    file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    setcookie($cartKey, '', time() - 2000);
    $cart = [];
    file_put_contents('products.json', json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location:cart.php');
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