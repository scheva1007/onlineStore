<?php

session_start();

$userId = $_SESSION['user_id'] ?? 'guest';
$history = [];
$historyFile = 'purchaseHistory.json';
if (file_exists($historyFile)) {
    $allHistory = json_decode(file_get_contents($historyFile), true);
    if (is_array($allHistory)) {
        foreach ($allHistory as $purchase) {
            if ($purchase['user_id'] == $userId) {
                $history[] = $purchase;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Історія покупок</title>
</head>
<body>
<a href="index.php">На головну</a>
    <h3>Історія моїх покупок</h3>
    <?php if (empty($history)): ?>
    <p>Ви ще нічого не купили</p>
    <?php else: ?>
    <?php foreach ($history as $historyBuy): ?>
    <div>
        <p style="color: green"><strong style="color: blue">Дата:</strong> <?= $historyBuy['date'] ?></p>
            <div>
                <?php foreach ($historyBuy['items'] as $item): ?>
                <p><?= $item['name'] ?> - <?= $item['quantity'] ?> шт. по <?= $item['price'] ?> грн</p>
                <?php endforeach; ?>
                <p><strong>Загальна сума: <?= $historyBuy['total'] ?></strong></p>
            </div> <br>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>


