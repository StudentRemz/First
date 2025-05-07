<?php
// includes/functions.php

// Database connection
try {
    $host = 'localhost';
    $db   = 'maestrob_site';
    $user = 'maestrob_site';
    $pass = 'hakan727272';

    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('DB Connection failed: ' . $e->getMessage());
}

// Load content elements once and cache
function loadContentElements() {
    global $pdo;
    static $content = null;
    if ($content === null) {
        $stmt = $pdo->query("SELECT element_key, element_value FROM content_elements");
        $raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $content = array_map('htmlspecialchars', $raw);
    }
    return $content;
}

// Retrieve a single content element by key
function getContent(string $key, string $default = '') {
    $content = loadContentElements();
    return $content[$key] ?? $default;
}

// Get raw images, optionally filtered by usage_area
function getImages(string $area = null) {
    global $pdo;
    if ($area) {
        $stmt = $pdo->prepare("SELECT * FROM images WHERE usage_area = ? ORDER BY id DESC");
        $stmt->execute([$area]);
    } else {
        $stmt = $pdo->query("SELECT * FROM images ORDER BY id DESC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSliderImages() {
    $imgs = getImages('slider');
    return !empty($imgs) ? $imgs : getImages();
}

function getPortfolioImages() {
    $imgs = getImages('portfolio');
    if (count($imgs) >= 2) {
        return array_slice($imgs, 0, 2);
    }
    return array_slice(getImages(), 0, 2);
}
?> 