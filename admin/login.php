<?php
session_start();
$error = null;

// Eğer zaten giriş yapmışsa admin paneline yönlendir
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Form gönderilmişse ve hata varsa göster
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Hata mesajını gösterdikten sonra temizle
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - Giriş</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin Paneli Girişi</h2>

        <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-200 rounded-md text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="auth.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Kullanıcı Adı</label>
                <input type="text" id="username" name="username" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div>
                <button type="submit"
                        class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-sign-in-alt mr-2"></i> Giriş Yap
                </button>
            </div>
        </form>
    </div>
</body>
</html> 