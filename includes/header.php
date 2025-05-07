<?php
// includes/header.php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getContent('site_title', 'Professionelle Maler- und Bauarbeiten in Heilbronn') ?></title>
    <!--<script src="https://cdn.tailwindcss.com"></script>-->
	<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="font-sans">
<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-1 flex justify-between items-center">
        <div class="flex items-center">
            <img src="<?= getContent('site_logo_url', './images/logoMaestro1.png') ?>" alt="Maestro icon" class="w-22 h-24 inline-block">
            <a href="tel:<?= getContent('iletisim_telefon_numara_link', '+4915730222859') ?>"
                class="hidden md:flex ml-8 bg-blue-100 text-blue-600 px-3 py-1 rounded-lg text-xl font-medium hover:bg-blue-200 transition duration-300 items-center">
                <i class="fas fa-phone mr-2"></i> <?= getContent('iletisim_telefon_numara', '+49 160 7584450') ?>
            </a>
            <a href="tel:<?= getContent('iletisim_telefon_numara_link', '+4915730222859') ?>"
                class="logo-phone md:hidden ml-6 bg-blue-100 text-blue-600 p-2 rounded-full text-sm font-medium hover:bg-blue-200 transition duration-300">
                <i class="fas fa-phone mr-2 pt-1"></i> <?= getContent('iletisim_telefon_numara', '+49 160 7584450') ?>
            </a>
        </div>
        <nav class="hidden md:flex space-x-8 mr-32">
            <a href="#services" class="text-lg hover:text-blue-800 font-medium"><?= getContent('menu_hizmetler', 'Dienstleistungen') ?></a>
            <a href="#portfolio" class="text-lg text-gray-1000 hover:text-blue-800 font-medium"><?= getContent('menu_portfolio', 'Portfolio') ?></a>
            <a href="#about" class="text-lg text-gray-1000 hover:text-blue-800 font-medium"><?= getContent('menu_hakkimizda', 'Über uns') ?></a>
            <a href="#contact" class="text-lg text-gray-1000 hover:text-blue-800 font-medium"><?= getContent('menu_iletisim', 'Kontakt') ?></a>
        </nav>
        <div class="md:hidden">
            <button id="menu-toggle" class="text-gray-800 focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-white py-2 px-4 shadow-lg">
        <a href="#services" class="block py-2 text-gray-800 hover:text-blue-600"><?= getContent('menu_hizmetler', 'Dienstleistungen') ?></a>
        <a href="#portfolio" class="block py-2 text-gray-800 hover:text-blue-600"><?= getContent('menu_portfolio', 'Portfolio') ?></a>
        <a href="#about" class="block py-2 text-gray-800 hover:text-blue-600"><?= getContent('menu_hakkimizda', 'Über uns') ?></a>
        <a href="#contact" class="block py-2 text-gray-800 hover:text-blue-600"><?= getContent('menu_iletisim', 'Kontakt') ?></a>
        <a href="tel:<?= getContent('iletisim_telefon_numara_link', '+4915730222859') ?>" class="block py-2 text-blue-600 font-medium">
            <i class="fas fa-phone mr-2"></i> <?= getContent('iletisim_telefon_numara', '+49 157 30222859') ?>
        </a>
    </div>
</header> 