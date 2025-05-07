// Mobile menu toggle
document.getElementById('menu-toggle')?.addEventListener('click', function () {
    document.getElementById('mobile-menu')?.classList.toggle('hidden');
});

// Before/After slider functionality for multiple sliders
document.querySelectorAll('.before-after').forEach(beforeAfter => {
    const before = beforeAfter.querySelector('.before');
    const handle = beforeAfter.querySelector('.slider-handle');

    let isDragging = false;

    const moveSlider = (x, rect) => {
        x = Math.max(0, Math.min(x, rect.width));
        const percent = (x / rect.width) * 100;
        before.style.width = percent + '%';
        handle.style.left = percent + '%';
    };

    handle.addEventListener('mousedown', function (e) {
        isDragging = true;
        document.body.style.cursor = 'ew-resize';
    });

    document.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        const rect = beforeAfter.getBoundingClientRect();
        moveSlider(e.clientX - rect.left, rect);
    });

    document.addEventListener('mouseup', function () {
        isDragging = false;
        document.body.style.cursor = '';
    });

    // Touch support
    handle.addEventListener('touchstart', function (e) {
        isDragging = true;
        e.preventDefault();
    });

    document.addEventListener('touchmove', function (e) {
        if (!isDragging) return;
        const touch = e.touches[0];
        const rect = beforeAfter.getBoundingClientRect();
        moveSlider(touch.clientX - rect.left, rect);
        e.preventDefault();
    }, { passive: false });

    document.addEventListener('touchend', function () {
        isDragging = false;
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href'))?.scrollIntoView({
            behavior: 'smooth'
        });
        document.getElementById('mobile-menu')?.classList.add('hidden');
    });
});

// Hero Slider Functionality
const heroSlides = document.querySelectorAll('.hero-slide');
const prevBtn = document.getElementById('prev-slide');
const nextBtn = document.getElementById('next-slide');
const indicators = document.querySelectorAll('.slider-indicator');
let currentSlide = 0;
let slideInterval;

function initSlider() {
    if (heroSlides.length === 0) return; // Resim yoksa slider başlatma
    heroSlides.forEach((slide, index) => {
        slide.classList.toggle('active', index === 0);
    });
    updateIndicators();
    startSlider();
}

function showSlide(index) {
    if (heroSlides.length === 0) return;
     currentSlide = (index + heroSlides.length) % heroSlides.length; // Döngüsel index
    heroSlides.forEach((slide, i) => {
        slide.classList.toggle('active', i === currentSlide);
    });
    updateIndicators();
}

function nextSlide() {
    if (heroSlides.length === 0) return;
    showSlide(currentSlide + 1);
}

function prevSlide() {
    if (heroSlides.length === 0) return;
     showSlide(currentSlide - 1);
}

function updateIndicators() {
     if (indicators.length === 0) return;
    indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === currentSlide);
    });
}

function startSlider() {
    if (heroSlides.length < 2) return; // Tek resim varsa otomatik geçiş yapma
     clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, 4000);
}

function resetSlider() {
    startSlider();
}

prevBtn?.addEventListener('click', () => {
    prevSlide();
    resetSlider();
});

nextBtn?.addEventListener('click', () => {
    nextSlide();
    resetSlider();
});

indicators.forEach((indicator, index) => {
    indicator.addEventListener('click', () => {
        showSlide(index);
        resetSlider();
    });
});

document.addEventListener('DOMContentLoaded', initSlider);

const sliderContainer = document.querySelector('.hero-slider');
sliderContainer?.addEventListener('mouseenter', () => clearInterval(slideInterval));
sliderContainer?.addEventListener('mouseleave', () => resetSlider());

function acceptCookies() {
    const cookieBanner = document.getElementById("cookie-banner");
    if(cookieBanner) cookieBanner.style.display = "none";
    document.cookie = "cookies_accepted=true; max-age=" + 60 * 60 * 24 * 365 + "; path=/";
}

function cookiesAccepted() {
    return document.cookie.split(';').some((item) => item.trim().startsWith('cookies_accepted='));
}

window.onload = function () {
    if (!cookiesAccepted()) {
         const cookieBanner = document.getElementById("cookie-banner");
         if(cookieBanner) cookieBanner.style.display = "block";
    }
} 