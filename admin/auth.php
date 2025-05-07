<?php
include 'includes/db.php'; // Veritabanı bağlantısını dahil et
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Kullanıcı adı ve şifre boş bırakılamaz.';
        header('Location: login.php');
        exit;
    }

    // Kullanıcıyı veritabanında bul
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kullanıcı bulunduysa ve şifre doğruysa
    if ($user && password_verify($password, $user['password_hash'])) {
        // Giriş başarılı, oturum değişkenlerini ayarla
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Oturumu yeniden oluştur (session fixation saldırılarını önlemek için)
        session_regenerate_id(true);

        // Admin paneline yönlendir
        header('Location: index.php');
        exit;
    } else {
        // Giriş başarısız
        $_SESSION['login_error'] = 'Geçersiz kullanıcı adı veya şifre.';
        header('Location: login.php');
        exit;
    }
} else {
    // POST isteği değilse login sayfasına yönlendir
    header('Location: login.php');
    exit;
}
?> 