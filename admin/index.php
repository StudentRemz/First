<?php
include 'includes/check_auth.php'; // Oturum kontrolünü en başa ekle
include 'includes/db.php';

// --- Mesaj Değişkenleri ---
$content_success_message = null;
$portfolio_image_message = null;
$slider_image_message = null;

// --- CSRF Token Oluşturma ve Doğrulama (Basit Yöntem) ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

function verify_csrf_token() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Token eşleşmiyorsa veya yoksa işlemi durdur
        die('CSRF token validation failed.');
    }
}

// --- Form İşlemleri ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_token(); // Tüm POST işlemlerinde CSRF kontrolü yap

    // --- İçerik Öğeleri Güncelleme ---
    if (isset($_POST['update_content'])) {
        foreach ($_POST['content'] as $key => $value) {
            $stmt = $pdo->prepare("UPDATE content_elements SET element_value = ? WHERE element_key = ?");
            $stmt->execute([$value, $key]);
        }
        $content_success_message = "Metin içerikleri başarıyla güncellendi!";
    }

    // --- Portfolio Toplu Güncelleme ---
    elseif (isset($_POST['update_portfolio_images'])) {
        foreach ($portfolio_images as $img) {
            $field = 'portfolio_url_' . $img['id'];
            $newUrl = filter_input(INPUT_POST, $field, FILTER_SANITIZE_URL);
            if ($newUrl && filter_var($newUrl, FILTER_VALIDATE_URL)) {
                $stmt = $pdo->prepare("UPDATE images SET image_url = ? WHERE id = ? AND usage_area = 'portfolio'");
                $stmt->execute([$newUrl, $img['id']]);
            }
        }
        $portfolio_image_message = ['type' => 'success', 'text' => 'Portfolio resimleri başarıyla güncellendi!'];
    }

    // --- Slider Resim İşlemleri ---
    elseif (isset($_POST['add_slider_image'])) {
        $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL);
        $description = filter_input(INPUT_POST, 'description', FILTER_DEFAULT);
        if (!empty($image_url) && filter_var($image_url, FILTER_VALIDATE_URL)) {
            $stmt = $pdo->prepare("INSERT INTO images (image_url, description, usage_area) VALUES (?, ?, 'slider')");
            $stmt->execute([$image_url, htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8')]);
            $slider_image_message = ['type' => 'success', 'text' => 'Yeni slider resmi başarıyla eklendi!'];
        } else {
            $slider_image_message = ['type' => 'error', 'text' => 'Hata: Geçerli bir resim URL\'si girin.'];
        }
    }
    elseif (isset($_POST['update_slider_image'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL);
        $description = filter_input(INPUT_POST, 'description', FILTER_DEFAULT);
        if ($id && !empty($image_url) && filter_var($image_url, FILTER_VALIDATE_URL)) {
            // Güvenlik: Sadece slider resimlerini güncelle
            $stmt = $pdo->prepare("UPDATE images SET image_url = ?, description = ? WHERE id = ? AND usage_area = 'slider'");
            $stmt->execute([$image_url, htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'), $id]);
             if ($stmt->rowCount() > 0) {
                 $slider_image_message = ['type' => 'success', 'text' => 'Slider resmi başarıyla güncellendi!'];
             } else {
                 $slider_image_message = ['type' => 'error', 'text' => 'Hata: Resim güncellenemedi veya bulunamadı.'];
             }
        } else {
            $slider_image_message = ['type' => 'error', 'text' => 'Hata: Geçerli ID ve URL girin.'];
        }
    }
    elseif (isset($_POST['delete_slider_image'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            // Güvenlik: Sadece slider resimlerini sil
            $stmt = $pdo->prepare("DELETE FROM images WHERE id = ? AND usage_area = 'slider'");
            $stmt->execute([$id]);
            if ($stmt->rowCount() > 0) {
                 $slider_image_message = ['type' => 'success', 'text' => 'Slider resmi başarıyla silindi!'];
            } else {
                 $slider_image_message = ['type' => 'error', 'text' => 'Hata: Resim bulunamadı veya silme yetkiniz yok.'];
            }
        }
    }
}

// --- Veri Çekme ---
// İçerik Öğeleri (Gruplama aynı kalıyor)
$content_stmt = $pdo->query("SELECT element_key, element_value, element_type FROM content_elements ORDER BY element_key");
$content_elements_raw = $content_stmt->fetchAll(PDO::FETCH_ASSOC);
$content = [];
foreach ($content_elements_raw as $item) {
    $content[$item['element_key']] = $item;
}

function groupContentByKeyPrefix(array $content, string $prefix): array {
    $grouped = [];
    foreach ($content as $key => $item) {
        if (strpos($key, $prefix) === 0) {
            $grouped[$key] = $item;
        }
    }
    // ksort($grouped); // Sıralama artık ORDER BY ile yapıldığı için gereksiz olabilir
    return $grouped;
}

$genel_ayar_keys = [
    'site_title', 'site_logo_url', 'menu_hizmetler', 'menu_portfolio', 'menu_hakkimizda', 'menu_iletisim',
    'whatsapp_numara', 'cookie_banner_metin', 'cookie_banner_buton'
];
$genel_ayarlar_content = array_intersect_key($content, array_flip($genel_ayar_keys));
// ksort($genel_ayarlar_content);

$hero_content = groupContentByKeyPrefix($content, 'hero_');
$portfolio_content = groupContentByKeyPrefix($content, 'portfolio_'); // Bu grup artık metin değil, resim yönetecek
$hizmetler_content = groupContentByKeyPrefix($content, 'hizmet_');
$hakkimizda_content = groupContentByKeyPrefix($content, 'hakkimizda_');
$musteri_yorum_content = groupContentByKeyPrefix($content, 'musteri_yorum_');
$iletisim_content = groupContentByKeyPrefix($content, 'iletisim_');
$footer_content = groupContentByKeyPrefix($content, 'footer_');
$diger_content = array_diff_key($content, $genel_ayarlar_content, $hero_content, $portfolio_content, $hizmetler_content, $hakkimizda_content, $musteri_yorum_content, $iletisim_content, $footer_content);

// Resimleri Çek ve Grupla
$image_stmt = $pdo->query("SELECT * FROM images ORDER BY id DESC");
$all_images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

$portfolio_images = array_filter($all_images, fn($img) => isset($img['usage_area']) && $img['usage_area'] === 'portfolio');
$slider_images = array_filter($all_images, fn($img) => isset($img['usage_area']) && $img['usage_area'] === 'slider');
// İleride başka usage_area'lar olursa buraya eklenebilir veya $other_images = array_diff_key(...) kullanılabilir.

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Stil tanımlamaları aynı kalıyor */
        .sidebar-link.active {
            background-color: #1d4ed8; color: white; font-weight: 600;
        }
        .main-content-section { display: none; }
        .main-content-section.active { display: block; }
        .sidebar { height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; }
        .main-content { margin-left: 256px; }
        @media (max-width: 768px) {
             .sidebar { position: fixed; transform: translateX(-100%); transition: transform 0.3s ease-in-out; z-index: 40; }
             .sidebar.open { transform: translateX(0); }
             .main-content { margin-left: 0; }
             #sidebar-toggle { display: block; }
         }
         /* Mesaj kutuları için stiller */
        .message-box {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
        }
        .message-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .message-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="sidebar w-64 bg-gray-800 text-gray-200 p-4 space-y-6 flex flex-col">
            <div class="text-center mb-8">
                <a href="#" class="text-2xl font-semibold text-white hover:text-gray-300">
                    <i class="fas fa-tachometer-alt mr-2"></i> Admin Paneli
                </a>
            </div>

            <nav class="space-y-1 flex-grow">
                 <span class="px-4 text-xs uppercase text-gray-500 font-semibold">İçerik Yönetimi</span>
                 <a href="#" class="sidebar-link active flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#genel-ayarlar-section">
                    <i class="fas fa-cog w-5 mr-3 text-center"></i> Genel Ayarlar
                 </a>
                 <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#hero-content-section">
                    <i class="fas fa-desktop w-5 mr-3 text-center"></i> Hero Alanı
                 </a>
                 <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#hizmetler-content-section">
                    <i class="fas fa-concierge-bell w-5 mr-3 text-center"></i> Hizmetler Alanı
                 </a>
                 <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#hakkimizda-content-section">
                    <i class="fas fa-info-circle w-5 mr-3 text-center"></i> Hakkımızda Alanı
                 </a>
                  <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#musteri-yorum-content-section">
                     <i class="fas fa-comments w-5 mr-3 text-center"></i> Müşteri Yorumları
                 </a>
                  <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#iletisim-content-section">
                     <i class="fas fa-map-marker-alt w-5 mr-3 text-center"></i> İletişim Alanı
                 </a>
                  <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#footer-content-section">
                     <i class="fas fa-shoe-prints w-5 mr-3 text-center"></i> Footer Alanı
                 </a>
                  <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#diger-content-section">
                     <i class="fas fa-puzzle-piece w-5 mr-3 text-center"></i> Diğer Metinler
                 </a>

                 <span class="px-4 pt-4 pb-1 block text-xs uppercase text-gray-500 font-semibold">Resim Yönetimi</span>
                 <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#portfolio-content-section">
                    <i class="fas fa-briefcase w-5 mr-3 text-center"></i> Portfolio Resimleri
                 </a>
                  <a href="#" class="sidebar-link flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" data-target="#slider-images-section">
                     <i class="fas fa-images w-5 mr-3 text-center"></i> Slider Resimleri
                 </a>
            </nav>

             <div class="mt-auto pt-2 border-t border-gray-700">
                <a href="/" target="_blank" class="text-sm text-gray-400 hover:text-gray-200 flex items-center mb-2">
                     <i class="fas fa-external-link-alt mr-2"></i> Siteyi Görüntüle
                 </a>
                <a href="logout.php" class="text-sm text-red-400 hover:text-red-300 flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap (<?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>)
                </a>
             </div>

        </aside>

        <!-- Main Content -->
         <main class="main-content flex-1 p-6 md:p-8">
             <button id="sidebar-toggle" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded focus:outline-none">
                 <i class="fas fa-bars"></i>
             </button>

            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6" id="main-content-title">Genel Ayarlar</h1>

            <!-- ==================== Metin İçerikleri Yönetimi ==================== -->
             <form method="POST">
                 <input type="hidden" name="update_content" value="1">
                 <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                 <?php if ($content_success_message): ?>
                    <div class="message-box message-success">
                        <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($content_success_message) ?>
                    </div>
                 <?php endif; ?>

                 <?php
                  function render_content_field($key, $item) {
                     $label = ucwords(str_replace('_', ' ', $key));
                     $value = $item['element_value'] ?? '';
                     $type = $item['element_type'] ?? 'text';
                     $input_id = 'content_' . htmlspecialchars($key);

                     echo '<div class="mb-4">'; // Removed extra padding/border from individual items
                     echo '<label for="' . $input_id . '" class="block text-sm font-medium text-gray-700 mb-1">' . htmlspecialchars($label) . ' <span class="text-xs text-gray-400">(' . htmlspecialchars($key) . ')</span></label>';

                     if ($type === 'textarea') {
                         echo '<textarea id="' . $input_id . '" name="content[' . htmlspecialchars($key) . ']" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">' . htmlspecialchars($value) . '</textarea>';
                     } elseif ($type === 'image_url') {
                         echo '<div class="flex items-center space-x-4">';
                         echo '<input type="text" id="' . $input_id . '" name="content[' . htmlspecialchars($key) . ']" value="' . htmlspecialchars($value) . '" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">';
                         if (!empty($value)) {
                            echo '<img src="' . htmlspecialchars($value) . '" alt="Önizleme" class="h-10 w-auto border rounded flex-shrink-0">';
                         }
                         echo '</div>';
                     } else {
                         echo '<input type="text" id="' . $input_id . '" name="content[' . htmlspecialchars($key) . ']" value="' . htmlspecialchars($value) . '" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">';
                     }
                     echo '</div>';
                  }
                 ?>

                 <!-- Genel Ayarlar -->
                 <section id="genel-ayarlar-section" class="main-content-section active bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Genel Site Ayarları</h2>
                    <div class="space-y-4">
                        <?php foreach ($genel_ayarlar_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>

                 <!-- Hero Alanı -->
                 <section id="hero-content-section" class="main-content-section bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Hero Alanı Metinleri</h2>
                    <div class="space-y-4">
                        <?php foreach ($hero_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>

                 <!-- Hizmetler Alanı -->
                 <section id="hizmetler-content-section" class="main-content-section bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Hizmetler Alanı Metinleri</h2>
                    <div class="space-y-4">
                        <?php foreach ($hizmetler_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>

                 <!-- Hakkımızda Alanı -->
                 <section id="hakkimizda-content-section" class="main-content-section bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Hakkımızda Alanı Metinleri</h2>
                    <div class="space-y-4">
                        <?php foreach ($hakkimizda_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>

                 <!-- Müşteri Yorumları -->
                 <section id="musteri-yorum-content-section" class="main-content-section bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Müşteri Yorumları Alanı Metinleri</h2>
                    <div class="space-y-4">
                        <?php foreach ($musteri_yorum_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>

                 <!-- İletişim Alanı -->
                 <section id="iletisim-content-section" class="main-content-section bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">İletişim Alanı Metinleri</h2>
                    <div class="space-y-4">
                        <?php foreach ($iletisim_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>

                 <!-- Footer Alanı -->
                 <section id="footer-content-section" class="main-content-section bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Footer Alanı Metinleri</h2>
                    <div class="space-y-4">
                        <?php foreach ($footer_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>

                 <!-- Diğer Metinler -->
                 <section id="diger-content-section" class="main-content-section bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Diğer Metinler</h2>
                    <div class="space-y-4">
                        <?php foreach ($diger_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>

                 <!-- ==================== Portfolio Resimleri Yönetimi ==================== -->
                 <section id="portfolio-content-section" class="main-content-section bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Portfolio Resimleri</h2>
                    <div class="space-y-4">
                        <?php foreach ($portfolio_content as $key => $item) render_content_field($key, $item); ?>
                    </div>
                 </section>
                 <!-- ==================== Portfolio Resimleri Yönetimi Sonu ==================== -->

                 <!-- Ortak Kaydet Butonu -->
                 <div class="mt-6 pt-4 border-t sticky bottom-0 bg-gray-100 py-4 z-10">
                     <button type="submit"
                             class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Tüm Metin İçeriklerini Kaydet
                     </button>
                 </div>
             </form>
             <!-- ==================== Metin İçerikleri Yönetimi Sonu ==================== -->

             <!-- ==================== Slider Resimleri Yönetimi ==================== -->
             <section id="slider-images-section" class="main-content-section">
                 <!-- Yeni Slider Resmi Ekleme -->
                 <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                     <h2 class="text-xl font-semibold text-gray-700 mb-4">Yeni Slider Resmi Ekle</h2>
                    <?php if ($slider_image_message && isset($_POST['add_slider_image'])): ?>
                        <div class="message-box <?= $slider_image_message['type'] === 'success' ? 'message-success' : 'message-error' ?>">
                            <i class="fas <?= $slider_image_message['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
                            <?= htmlspecialchars($slider_image_message['text']) ?>
                        </div>
                    <?php endif; ?>
                     <form method="POST" class="space-y-4">
                         <input type="hidden" name="add_slider_image" value="1">
                         <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                         <div>
                             <label for="new_slider_image_url" class="block text-sm font-medium text-gray-700">Resim URL'si</label>
                             <input type="url" id="new_slider_image_url" name="image_url" placeholder="https://..." required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                         </div>
                         <div>
                             <label for="new_slider_description" class="block text-sm font-medium text-gray-700">Açıklama (Slider Başlığı)</label>
                             <textarea id="new_slider_description" name="description" placeholder="Sliderda görünecek başlık" rows="2"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                         </div>
                         <div>
                             <button type="submit"
                                     class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                 <i class="fas fa-plus mr-2"></i> Slider'a Ekle
                             </button>
                         </div>
                     </form>
                 </div>

                 <!-- Mevcut Slider Resimleri -->
                 <div class="bg-white p-6 rounded-lg shadow-md">
                     <h2 class="text-xl font-semibold text-gray-700 mb-4">Mevcut Slider Resimleri</h2>
                    <?php if ($slider_image_message && (isset($_POST['update_slider_image']) || isset($_POST['delete_slider_image'])) ): ?>
                        <div class="message-box <?= $slider_image_message['type'] === 'success' ? 'message-success' : 'message-error' ?>">
                            <i class="fas <?= $slider_image_message['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
                             <?= htmlspecialchars($slider_image_message['text']) ?>
                        </div>
                    <?php endif; ?>
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                         <?php if (empty($slider_images)): ?>
                             <p class="text-gray-500 text-center col-span-full py-4">Henüz slider resmi eklenmemiş.</p>
                         <?php else: ?>
                            <?php foreach ($slider_images as $image): ?>
                                 <div class="border border-gray-200 rounded-lg overflow-hidden flex flex-col">
                                     <img src="<?= htmlspecialchars($image['image_url']) ?>" alt="Slider Resmi" class="w-full h-48 object-cover">
                                     <div class="p-4 flex-grow flex flex-col justify-between">
                                         <form method="POST" class="space-y-3">
                                             <input type="hidden" name="update_slider_image" value="1">
                                             <input type="hidden" name="id" value="<?= $image['id'] ?>">
                                             <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                             <div>
                                                 <label for="slider_image_url_<?= $image['id'] ?>" class="block text-xs font-medium text-gray-500">URL</label>
                                                 <input type="url" id="slider_image_url_<?= $image['id'] ?>" name="image_url" value="<?= htmlspecialchars($image['image_url']) ?>" required
                                                        class="mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                             </div>
                                             <div>
                                                 <label for="slider_description_<?= $image['id'] ?>" class="block text-xs font-medium text-gray-500">Açıklama (Başlık)</label>
                                                 <textarea id="slider_description_<?= $image['id'] ?>" name="description" rows="2"
                                                           class="mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($image['description'] ?? '') ?></textarea>
                                             </div>
                                             <div class="flex justify-between items-center mt-auto pt-3">
                                                 <button type="submit"
                                                         class="inline-flex items-center py-1 px-3 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                     <i class="fas fa-save mr-1"></i> Güncelle
                                                 </button>
                                                 <form method="POST" onsubmit="return confirm('Bu slider resmini silmek istediğinizden emin misiniz?');" class="inline-block">
                                                     <input type="hidden" name="delete_slider_image" value="1">
                                                     <input type="hidden" name="id" value="<?= $image['id'] ?>">
                                                     <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                     <button type="submit"
                                                             class="inline-flex items-center py-1 px-3 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                         <i class="fas fa-trash mr-1"></i> Sil
                                                     </button>
                                                 </form>
                                             </div>
                                         </form>
                                     </div>
                                 </div>
                             <?php endforeach; ?>
                         <?php endif; ?>
                     </div>
                 </div>
             </section>
             <!-- ==================== Slider Resimleri Yönetimi Sonu ==================== -->

         </main>
    </div>

    <script>
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        const mainContentSections = document.querySelectorAll('.main-content-section');
        const mainContentTitle = document.getElementById('main-content-title');
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');

        // Initial setup: Set default active link and section
        const setDefaultActive = () => {
            const defaultLink = document.querySelector('.sidebar-link[data-target="#genel-ayarlar-section"]');
            const defaultSection = document.getElementById('genel-ayarlar-section');
            const defaultTitle = defaultLink ? defaultLink.textContent.trim() : 'Admin Paneli';

            sidebarLinks.forEach(l => l.classList.remove('active'));
            mainContentSections.forEach(s => s.classList.remove('active'));

            if(defaultLink) defaultLink.classList.add('active');
            if(defaultSection) defaultSection.classList.add('active');
            mainContentTitle.textContent = defaultTitle;
        };

        sidebarLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('data-target');
                const targetSection = document.querySelector(targetId);
                if (!targetSection) return; // Hedef bölüm yoksa devam etme

                mainContentTitle.textContent = link.textContent.trim();
                sidebarLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                mainContentSections.forEach(section => section.classList.remove('active'));
                targetSection.classList.add('active');

                if (window.innerWidth < 768) {
                     sidebar.classList.remove('open');
                 }
            });
        });

        // Mobile sidebar toggle
        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Close sidebar if clicking outside of it on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== sidebarToggle && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Set the default view when the page loads
        setDefaultActive();

    </script>

</body>
</html> 