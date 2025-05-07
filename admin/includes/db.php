<?php
$host = 'localhost';
$db = 'maestrob_site';
$user = 'maestrob_site';
$pass = 'hakan727272';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Bağlantı hatası: ' . $e->getMessage();
}
?> 