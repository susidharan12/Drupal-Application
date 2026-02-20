// IIHR Theme JS

// Live Clock
function updateClock() {
  const el = document.getElementById('live-time');
  if (el) {
    el.innerText = new Date().toLocaleString('en-US', {
      month: 'short', day: '2-digit', year: 'numeric',
      hour: '2-digit', minute: '2-digit', hour12: true
    });
  }
}
setInterval(updateClock, 1000);
updateClock();

// Hero Slider
let heroIdx = 0;
let heroTimer;
function heroSlide(dir) {
  const slides = document.querySelectorAll('.hero-slide');
  if (!slides.length) return;
  heroIdx = (heroIdx + dir + slides.length) % slides.length;
  heroGoTo(heroIdx);
}
function heroGoTo(i) {
  const slides = document.querySelectorAll('.hero-slide');
  if (!slides.length) return;
  heroIdx = i;
  document.getElementById('heroSlides').style.transform = `translateX(-${i * 100}%)`;
  document.querySelectorAll('.hero-dot').forEach((d, idx) => d.classList.toggle('active', idx === i));
  clearInterval(heroTimer);
  heroTimer = setInterval(() => heroSlide(1), 5000);
}
document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('heroSlides')) {
    heroTimer = setInterval(() => heroSlide(1), 5000);
  }
});

// Active Nav
function setActiveNav() {
  const path = window.location.pathname;
  document.querySelectorAll('.nav > a, .nd-trigger').forEach(el => {
    el.classList.remove('nav-active');
    const hx = el.getAttribute('hx-get') || el.getAttribute('href') || '';
    if (hx === path || (path === '/' && hx === '/home') || (path === '/home' && hx === '/home')) {
      el.classList.add('nav-active');
    }
  });
}
document.addEventListener('htmx:afterSwap', () => { setActiveNav(); updateClock(); });
document.addEventListener('DOMContentLoaded', setActiveNav);
