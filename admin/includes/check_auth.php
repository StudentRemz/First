<?php
session_start();

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    // Oturum yoksa doğru login sayfasına yönlendir
    $base = dirname($_SERVER['SCRIPT_NAME']); // örn. /Meister/admin
    header("Location: {$base}/login.php");
    exit;
}
?> 