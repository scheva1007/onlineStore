<?php

session_start();
include 'users.php';

$userId = $_SESSION['user_id'] ?? '';
$cartKey = $userId ? "cart_{$userId}" : "cart_guest";
$products = products();
$users = users();
$balance = isset($users[$userId]) ? $users[$userId]['balance'] : '';
$cart = cartCookie($cartKey);

$cart = cartUpdate($cart, $cartKey, $products);
$array = purchase($cart, $cartKey, $products, $users, $userId);
$cart = $array[0];
$users = $array[1];
$products = $array[2];

function cartCookie($cartKey)
{
    $cart = [];
    if (isset($_COOKIE[$cartKey]) && $_COOKIE[$cartKey] !== '') {
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
        foreach ($cart as $pid => $quantity) {
            $newCart[] = "$pid:$quantity";
        }
        setcookie($cartKey, implode(',', $newCart), time() + 2000);
    }

    function products() {
        return json_decode(file_get_contents('products.json'), true);
    }

    function users() {
        return json_decode(file_get_contents('users.json'), true);
    }

    function saveUsers($users) {
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    function saveProducts($products)
    {
        file_put_contents('products.json', json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    function cartUpdate($cart, $cartKey, $products)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['id'])) {
            $id = $_POST['id'];
            if (isset($cart[$id]) && isset($products[$id])) {
                $maxQuantity = $products[$id]['quantity'];
                if ($_POST['action'] == 'plus' && $cart[$id] < $maxQuantity) {
                    $cart[$id]++;
                } elseif ($_POST['action'] == 'minus') {
                    $cart[$id]--;
                    if ($cart[$id] <= 0) {
                        unset($cart[$id]);
                    }
                }
            }
            saveCartCookie($cart, $cartKey);
            header('Location:cart.php');
            exit;
        }
        return $cart;
    }

    function purchase($cart, $cartKey, $products, $users, $userId) {
        if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['buy'])) {
            $total = calculateTotal($cart, $products);
            if (isset($users[$userId])) {
                if ($users[$userId]['balance'] < $total) {
                    echo "<p style='color:red;'>Недостатньо коштів для покупки.</p>";
                    exit;
                }
                $users[$userId]['balance'] -= $total;
                saveUsers($users);
            }
            $purchase = createPurchase($cart, $products, $userId, $total);
            $products = updateQuantity($products, $cart);
            savePurchase($purchase);
            saveProducts($products);
            clearCart($cartKey);
            $cart = [];

            header('Location:cart.php');
            exit;
        }
        return [$cart, $users, $products];
    }

    function calculateTotal($cart, $products) {
        $total = 0;
        foreach ($cart as $id => $quantity) {
            if (isset($products[$id])) {
                $total += $products[$id]['price']*$quantity;
            }
        }
        return $total;
    }

    function createPurchase($cart, $products, $userId, $total) {
        $purchase = [
                'user_id' => $userId,
                'date' => date('Y-m-d H:i:s' ),
                'items' => [],
                'total' => $total
        ];

        foreach ($cart as $id => $quantity) {
            if (isset($products[$id])) {
                $purchase['items'][] = [
                        'id' => $id,
                        'name' => $products[$id]['name'],
                        'price' => $products[$id]['price'],
                        'quantity' => $quantity
                ];
            }
        }

        return $purchase;
    }

    function updateQuantity($products, $cart) {
        foreach ($cart as $id => $quantity) {
            if (isset($products[$id])) {
                $products[$id]['quantity'] -= $quantity;
                if ($products[$id] < 0) {
                    $products[$id]['quantity'] = 0;
                }
            }
        }
        return $products;
    }

    function savePurchase($purchase) {
        $historyFile = 'purchaseHistory.json';
        $history = [];

        if (file_exists($historyFile)) {
            $history = json_decode(file_get_contents($historyFile), true);
        }
        $history[] = $purchase;
        file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
     }

     function clearCart($cartKey) {
        setcookie($cartKey, '', time() - 2000);
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