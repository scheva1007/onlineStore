<?php
    include 'price.php';

    if (isset($_GET['id'])) {
        $productId = $_GET['id'];
        $cart = [];

        if (isset($_COOKIE['cart']) && $_COOKIE['cart'] !== '') {
            $cart = explode(',', $_COOKIE['cart']);
        }

        $alreadyAdded = false;
        foreach ($cart as $item) {
            if ($item == $productId) {
                $alreadyAdded = true;
                break;
            }
        }

        if (!$alreadyAdded) {
            $cart[] = $productId;
            setcookie('cart', implode(',', $cart), time() + 30);
        }
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
       <a href="?id=<?= $id ?>">Додати в кошик</a>
    <?php endforeach; ?></p>
   </div>
   <a href="cart.php">Перейти до кошика</a>
</body>


</html>
