<?php
require 'includes/functions.php';
require 'includes/header.php';

// Dinamik Resim Listeleri (Slider, Portfolyo, Hizmetler)
$image_stmt = $pdo->query("SELECT * FROM images ORDER BY id DESC"); // Veya gerekli sıralama
$images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

// Slider için kullanılacak resimleri filtrele (opsiyonel)
$slider_images = array_filter($images, fn($img) => isset($img['usage_area']) && $img['usage_area'] === 'slider');
if (empty($slider_images)) $slider_images = $images; // Slider resmi yoksa tüm resimleri kullan (geçici çözüm)

// Portfolyo için kullanılacak resimleri filtrele (opsiyonel)
$portfolio_images = array_filter($images, fn($img) => isset($img['usage_area']) && $img['usage_area'] === 'portfolio');
if (empty($portfolio_images)) $portfolio_images = array_slice($images, 0, 2); // Portfolyo resmi yoksa ilk 2 resmi kullan (geçici çözüm)
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getContent('site_title', 'Professionelle Maler- und Bauarbeiten in Heilbronn') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Inline stiller kaldırıldı, gerekirse buraya özel eklemeler yapılabilir */
    </style>
</head>

<body class="font-sans">
    <!-- Hero Slider Section -->
    <section class="hero-slider relative h-screen max-h-[700px] overflow-hidden">
        <?php if (!empty($slider_images)):
            // Slider resimlerini indexlemek için array_values kullanalım
            $indexed_slider_images = array_values($slider_images);
        ?>
            <?php foreach ($indexed_slider_images as $index => $image): ?>
                <?php
                    $slide_image_url = htmlspecialchars($image['image_url']);
                    // Başlığı content_elements'dan al (images.description yerine)
                    $slide_title_key = 'hero_slide_'.($index+1).'_baslik';
                    $slide_title = getContent($slide_title_key, 'Slider Başlık ' . ($index+1));

                    // Butonları content_elements'dan al
                    $button_text_key = 'hero_slide_'.($index+1).'_buton_metin';
                    $button_link_key = 'hero_slide_'.($index+1).'_buton_link';
                    $button_icon_key = 'hero_slide_'.($index+1).'_buton_icon';

                    $button_text = getContent($button_text_key);
                    $button_link = getContent($button_link_key);
                    $button_icon = getContent($button_icon_key);
                ?>
                <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>"
                    style="background-position: center;height: 100vh;width: 100%;background-size: cover; background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('<?= $slide_image_url ?>');">
                    <div class="container mx-auto px-4 h-full flex items-center justify-center text-center">
                        <div class="text-white">
                            <h1 class="text-4xl md:text-6xl font-bold mb-56"><?= $slide_title ?></h1>
                            <div class="flex flex-col md:flex-row justify-center gap-4">
                                <?php if (!empty($button_link) && !empty($button_text)): ?>
                                    <a href="<?= $button_link ?>"
                                        class="bg-white hover:bg-gray-100 text-blue-600 font-bold py-3 px-8 rounded-lg transition duration-300">
                                        <?php if(!empty($button_icon)): ?><i class="<?= $button_icon ?> mr-2"></i><?php endif; ?> <?= $button_text ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Slider Navigation -->
            <button id="prev-slide"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-30 text-white p-3 rounded-full hover:bg-opacity-50 transition duration-300 z-10">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button id="next-slide"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-30 text-white p-3 rounded-full hover:bg-opacity-50 transition duration-300 z-10">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Slider Indicators -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                <?php foreach ($indexed_slider_images as $index => $image): ?>
                    <button class="slider-indicator <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="h-screen max-h-[700px] flex items-center justify-center bg-gray-200 text-gray-500">
                Slider için resim bulunamadı.
            </div>
        <?php endif; ?>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="py-4">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-800"><?= getContent('portfolio_baslik', 'Unsere Arbeiten') ?></h2>

            <?php
            // Portfolio before/after images from content elements
            $before1 = getContent('portfolio_1_before_url', '');
            $after1  = getContent('portfolio_1_after_url', '');
            $before2 = getContent('portfolio_2_before_url', '');
            $after2  = getContent('portfolio_2_after_url', '');
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <?php if ($before1 || $after1): ?>
                <div class="before-after rounded-lg overflow-hidden shadow-lg">
                    <img src="<?= htmlspecialchars($after1) ?>" alt="After" class="after">
                    <div class="before">
                        <img src="<?= htmlspecialchars($before1) ?>" alt="Before">
                    </div>
                    <div class="slider-handle"></div>
                </div>
                <?php endif; ?>
                <?php if ($before2 || $after2): ?>
                <div class="before-after rounded-lg overflow-hidden shadow-lg">
                    <img src="<?= htmlspecialchars($after2) ?>" alt="After" class="after">
                    <div class="before">
                        <img src="<?= htmlspecialchars($before2) ?>" alt="Before">
                    </div>
                    <div class="slider-handle"></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-4 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-800"><?= getContent('hizmetler_baslik', 'Unsere Dienstleistungen') ?></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $services_list = [
                    ['key_prefix' => 'hizmet_renovasyon', 'default_img' => 'https://via.placeholder.com/400x300/EBF4FF/76A9FA?text=Renovierung'],
                    ['key_prefix' => 'hizmet_kurubau', 'default_img' => 'https://via.placeholder.com/400x300/EBF4FF/76A9FA?text=Trockenbau', 'has_before_after' => true], // Özel durum
                    ['key_prefix' => 'hizmet_vollwärmeschutz', 'default_img' => 'https://via.placeholder.com/400x300/EBF4FF/76A9FA?text=WDVS'],
                    ['key_prefix' => 'hizmet_maler', 'default_img' => 'https://via.placeholder.com/400x300/EBF4FF/76A9FA?text=Maler'],
                    ['key_prefix' => 'hizmet_stuckateur', 'default_img' => 'https://via.placeholder.com/400x300/EBF4FF/76A9FA?text=Stuckateur'],
                    ['key_prefix' => 'hizmet_bodenbelaege', 'default_img' => 'https://via.placeholder.com/400x300/EBF4FF/76A9FA?text=Boden'],
                ];
                ?>
                <?php foreach ($services_list as $service): ?>
                    <?php
                        $key_baslik = $service['key_prefix'] . '_baslik';
                        $key_aciklama = $service['key_prefix'] . '_aciklama';
                        $key_madde_1 = $service['key_prefix'] . '_madde_1';
                        $key_madde_2 = $service['key_prefix'] . '_madde_2';
                        $key_madde_3 = $service['key_prefix'] . '_madde_3';
                        $key_resim = $service['key_prefix'] . '_resim_url';
                        $resim_url = getContent($key_resim, $service['default_img']);
                        $has_before_after = $service['has_before_after'] ?? false;

                        // Trockenbau için before/after resim anahtarları (opsiyonel)
                        $key_resim_before = $service['key_prefix'] . '_resim_before_url';
                        $key_resim_after = $service['key_prefix'] . '_resim_after_url';
                        $resim_before_url = getContent($key_resim_before, $resim_url); // Varsayılan olarak ana resmi kullan
                        $resim_after_url = getContent($key_resim_after, $resim_url);  // Varsayılan olarak ana resmi kullan

                    ?>
                    <div class="service-card bg-white rounded-lg overflow-hidden shadow-lg transition duration-300 flex flex-col">
                        <div class="h-48 bg-gray-200 flex items-center justify-center overflow-hidden <?= $has_before_after ? 'relative' : '' ?>">
                            <?php if ($has_before_after): ?>
                                <div class="before-after">
                                    <img src="https://maestrobau.de/images/orj/salonyenilemesonrasi.jpg" alt="After" class="after">
                                    <div class="before absolute top-0 left-0 w-1/2 h-full overflow-hidden">
                                        <img src="<?= $resim_before_url ?>" alt="Before" class="w-full h-full object-cover absolute top-0 left-0 max-w-none" style="">
                                    </div>
                                    <div class="slider-handle absolute left-1/2 top-0 bottom-0 w-1 bg-white transform -translate-x-1/2 cursor-ew-resize">
                                        <div class="slider-handle-icon absolute w-8 h-8 rounded-full bg-white top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex items-center justify-center text-gray-500">
                                            <i class="fas fa-arrows-alt-h"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <img src="<?= $resim_url ?>" alt="<?= getContent($key_baslik, 'Hizmet Resmi') ?>" class="w-full h-full object-cover text-blue-600">
                            <?php endif; ?>
                        </div>
                        <div class="p-6 flex-grow">
                            <h3 class="text-xl font-bold mb-3 text-gray-800"><?= getContent($key_baslik, 'Hizmet Başlığı') ?></h3>
                            <p class="text-gray-600 mb-4"><?= getContent($key_aciklama, 'Hizmet açıklaması buraya gelecek.') ?></p>
                            <ul class="text-gray-600 space-y-2">
                                <li><i class="fas fa-check text-green-500 mr-2"></i> <?= getContent($key_madde_1, 'Madde 1') ?></li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> <?= getContent($key_madde_2, 'Madde 2') ?></li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i> <?= getContent($key_madde_3, 'Madde 3') ?></li>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2">
                    <img src="<?= getContent('hakkimizda_resim_url', 'https://via.placeholder.com/600x400/cccccc/969696?text=Unser+Team') ?>"
                        alt="Unser Team" class="rounded-lg shadow-lg w-full">
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-800"><?= getContent('hakkimizda_baslik', 'Über unser Unternehmen') ?></h2>
                    <p class="text-gray-600 mb-4"><?= getContent('hakkimizda_paragraf_1', 'Hakkımızda paragraf 1...') ?></p>
                    <p class="text-gray-600 mb-6"><?= getContent('hakkimizda_paragraf_2', 'Hakkımızda paragraf 2...') ?></p>
                    <p class="text-gray-600 mb-6"><?= getContent('hakkimizda_paragraf_3', 'Hakkımızda paragraf 3...') ?></p>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-2xl mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-bold text-gray-800"><?= getContent('hakkimizda_liste_1_baslik', 'Liste 1 Başlık') ?></h4>
                                <p class="text-gray-600"><?= getContent('hakkimizda_liste_1_aciklama', 'Liste 1 Açıklama') ?></p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-2xl mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-bold text-gray-800"><?= getContent('hakkimizda_liste_2_baslik', 'Liste 2 Başlık') ?></h4>
                                <p class="text-gray-600"><?= getContent('hakkimizda_liste_2_aciklama', 'Liste 2 Açıklama') ?></p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-2xl mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-bold text-gray-800"><?= getContent('hakkimizda_liste_3_baslik', 'Liste 3 Başlık') ?></h4>
                                <p class="text-gray-600"><?= getContent('hakkimizda_liste_3_aciklama', 'Liste 3 Açıklama') ?></p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mt-4"><?= getContent('hakkimizda_paragraf_son', 'Hakkımızda son paragraf...') ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-blue-600 text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12"><?= getContent('musteri_yorum_baslik', 'Kundenstimmen') ?></h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php for ($i = 1; $i <= 3; $i++): ?>
                    <?php
                        $rating = (float)getContent("musteri_yorum_{$i}_rating", 0);
                        $quote = getContent("musteri_yorum_{$i}_metin");
                        $name = getContent("musteri_yorum_{$i}_isim");
                        $location = getContent("musteri_yorum_{$i}_lokasyon");
                        // Sadece isim varsa göster
                        if (empty($name)) continue;
                    ?>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="text-yellow-400 mr-2 flex">
                                <?php for ($j = 1; $j <= 5; $j++): ?>
                                    <?php if ($j <= floor($rating)): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($rating - floor($rating) >= 0.5 && $j === ceil($rating)): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i> <?php // Boş yıldız için far kullanılır ?>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="mb-4 italic">"<?= $quote ?>"</p>
                        <div class="flex items-center">
                            <div class="bg-white text-blue-600 rounded-full w-10 h-10 flex items-center justify-center font-bold mr-3 text-lg">
                                <?= !empty($name) ? strtoupper(mb_substr($name, 0, 1)) : ' ' ?>
                            </div>
                            <div>
                                <h4 class="font-bold"><?= $name ?></h4>
                                <p class="text-blue-200"><?= $location ?></p>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-800"><?= getContent('iletisim_baslik', 'Kontaktieren Sie uns') ?></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="h-[450px] md:h-full">
                    <iframe
                        src="<?= getContent('iletisim_harita_url', 'https://www.google.com/maps/embed?pb=...') ?>"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="md:w-full">
                    <div class="bg-gray-50 p-8 rounded-lg shadow-md h-full">
                        <h3 class="text-2xl font-bold mb-6 text-gray-800"><?= getContent('iletisim_info_baslik', 'Kontaktinformationen') ?></h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xl mt-1 mr-4 w-5 text-center"></i>
                                <div>
                                    <h4 class="font-bold text-gray-800"><?= getContent('iletisim_adres_hn_baslik', 'Adresse Heilbronn') ?></h4>
                                    <p class="text-gray-600"><?= getContent('iletisim_adres_hn_satir_1', 'Schellingstraße, 18') ?> <br><?= getContent('iletisim_adres_hn_satir_2', '74072, Heilbronn, Deutschland') ?></p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xl mt-1 mr-4 w-5 text-center"></i>
                                <div>
                                    <h4 class="font-bold text-gray-800"><?= getContent('iletisim_adres_ma_baslik', 'Adresse Mannheim') ?></h4>
                                    <p class="text-gray-600"><?= getContent('iletisim_adres_ma_satir_1', 'Theodor-Heuss-Anlage, 12') ?> <br><?= getContent('iletisim_adres_ma_satir_2', '68165, Mannheim, Deutschland') ?></p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-phone-alt text-blue-600 text-xl mt-1 mr-4 w-5 text-center"></i>
                                <div>
                                    <h4 class="font-bold text-gray-800"><?= getContent('iletisim_telefon_baslik', 'Telefon') ?></h4>
                                    <p class="text-gray-600">
                                        <a href="tel:<?= getContent('iletisim_telefon_numara_link', '+4915730222859') ?>" class="hover:text-blue-800">
                                            <?= getContent('iletisim_telefon_numara', '+49 157 30222859') ?>
                                        </a>
                                    </p>
                                    <p class="text-gray-600 text-sm"><?= getContent('iletisim_telefon_saatler', 'Mo-Fr: 8:00 - 18:00 Uhr') ?></p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-clock text-blue-600 text-xl mt-1 mr-4 w-5 text-center"></i>
                                <div>
                                    <h4 class="font-bold text-gray-800"><?= getContent('iletisim_calisma_saat_baslik', 'Öffnungszeiten') ?></h4>
                                    <p class="text-gray-600"><?= getContent('iletisim_calisma_saat_1', 'Montag - Freitag: 8:00 - 18:00 Uhr') ?><br><?= getContent('iletisim_calisma_saat_2', 'Samstag: 9:00 - 14:00 Uhr') ?><br><?= getContent('iletisim_calisma_saat_3', 'Sonntag: Geschlossen') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8">
                            <h4 class="font-bold text-gray-800 mb-4"><?= getContent('iletisim_sosyal_medya_baslik', 'Folgen Sie uns') ?></h4>
                            <div class="flex space-x-4">
                                <a href="<?= getContent('sosyal_facebook_url', '#') ?>" target="_blank"
                                    class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-700 transition duration-300">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="<?= getContent('sosyal_twitter_url', '#') ?>" target="_blank"
                                    class="bg-blue-400 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-500 transition duration-300">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="<?= getContent('sosyal_instagram_url', '#') ?>" target="_blank"
                                    class="bg-pink-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-pink-700 transition duration-300">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="<?= getContent('sosyal_linkedin_url', '#') ?>" target="_blank"
                                    class="bg-gray-800 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-gray-900 transition duration-300">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-paint-roller text-blue-400 text-3xl mr-2"></i>
                        <span class="text-xl font-bold"><?= getContent('footer_marka_adi', 'Maestro Bau') ?></span>
                    </div>
                    <p class="text-gray-400"><?= getContent('footer_slogan', 'Slogan buraya...') ?></p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4"><?= getContent('footer_menu_hizmetler_baslik', 'Dienstleistungen') ?></h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#services" class="hover:text-white transition duration-300"><?= getContent('footer_menu_malerarbeiten', 'Malerarbeiten') ?></a></li>
                        <li><a href="#services" class="hover:text-white transition duration-300"><?= getContent('footer_menu_renovierungen', 'Renovierungen') ?></a></li>
                        <li><a href="#services" class="hover:text-white transition duration-300"><?= getContent('footer_menu_trockenbau', 'Trockenbau') ?></a></li>
                        <li><a href="#services" class="hover:text-white transition duration-300"><?= getContent('footer_menu_vollwaermeschutz', 'Vollwärmeschutz') ?></a></li>
                        <li><a href="#services" class="hover:text-white transition duration-300"><?= getContent('footer_menu_stuckateur', 'Stuckateur') ?></a></li>
                        <li><a href="#services" class="hover:text-white transition duration-300"><?= getContent('footer_menu_bodenbelaege', 'Bodenbeläge') ?></a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4"><?= getContent('footer_menu_kontakt_baslik', 'Kontakt') ?></h4>
                    <div class="space-y-2 text-gray-400">
                        <p><i class="fas fa-map-marker-alt mr-2"></i> <?= getContent('iletisim_adres_hn_satir_1', '...') ?>, <?= getContent('iletisim_adres_hn_satir_2', '...') ?></p>
                        <p><i class="fas fa-phone mr-2"></i> <a href="tel:<?= getContent('iletisim_telefon_numara_link', '+4915730222859') ?>" class="hover:text-white"><?= getContent('iletisim_telefon_numara', '...') ?></a></p>
                        <p><i class="fas fa-clock mr-2"></i> <?= getContent('iletisim_telefon_saatler', '...') ?></p>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4"><?= getContent('footer_menu_ekstra_baslik', 'Linkler') ?></h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="<?= getContent('footer_link_datenschutz_url', '#') ?>" class="hover:text-white transition duration-300"><?= getContent('footer_link_datenschutz', 'Datenschutz') ?></a></li>
                        <li><a href="<?= getContent('footer_link_agb_url', '#') ?>" class="hover:text-white transition duration-300"><?= getContent('footer_link_agb', 'AGB') ?></a></li>
                        <li><a href="<?= getContent('footer_link_impressum_url', '#') ?>" class="hover:text-white transition duration-300"><?= getContent('footer_link_impressum', 'Impressum') ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400"><?= getContent('footer_haklar', '© 2025 Maestro Bau. Alle Rechte vorbehalten.') ?></p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/<?= getContent('whatsapp_numara', '4915730222859') ?>" target="_blank"
        class="fixed bottom-6 right-6 bg-green-500 text-white w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center text-3xl md:text-4xl shadow-lg hover:bg-green-600 transition duration-300 z-50">
        <i class="fab fa-whatsapp"></i>
    </a>
    <div id="cookie-banner">
        <?= getContent('cookie_banner_metin', 'Diese Website verwendet Cookies, um Ihnen das beste Erlebnis auf unserer Website zu bieten.') ?>
        <button onclick="acceptCookies()"><?= getContent('cookie_banner_buton', 'Akzeptieren') ?></button>
    </div>

