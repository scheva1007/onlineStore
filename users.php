<?php

$users = json_decode(file_get_contents('users.json'), true);

if (!is_array($users)) {
    echo "Помилка JSON: " . json_last_error_msg();
    $users = [];
}