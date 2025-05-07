
    <!-- Footer Section -->
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
                        <p><i class="fas fa-phone mr-2"></i> <a href="tel:<?= getContent('iletisim_telefon_numara_link', '+491607584450') ?>" class="hover:text-white"><?= getContent('iletisim_telefon_numara', '...') ?></a></p>
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
    <a href="https://wa.me/<?= getContent('whatsapp_numara', '491607584450') ?>" target="_blank"
        class="fixed bottom-6 right-6 bg-green-500 text-white w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center text-3xl md:text-4xl shadow-lg hover:bg-green-600 transition duration-300 z-50">
        <i class="fab fa-whatsapp"></i>
    </a>
    <div id="cookie-banner">
        <?= getContent('cookie_banner_metin', 'Diese Website verwendet Cookies, um Ihnen das beste Erlebnis auf unserer Website zu bieten.') ?>
        <button onclick="acceptCookies()"><?= getContent('cookie_banner_buton', 'Akzeptieren') ?></button>
    </div>

    <script src="js/script.js"></script>
</body>
</html> 