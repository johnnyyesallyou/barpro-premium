/**
 * BarPro Motion — NAV SCROLL SHRINK + PROGRESS BAR
 */

'use strict';
function initNav() {
  const nav = qs('#studioNav');
  if (!nav) return;

  // Progress bar
  let bar = qs('.studio-progress-bar');
  if (!bar) {
    bar = document.createElement('div');
    bar.className = 'studio-progress-bar';
    nav.appendChild(bar);
  }

  let ticking = false;
  window.addEventListener('scroll', () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      const scrolled = window.scrollY;
      const total    = document.documentElement.scrollHeight - window.innerHeight;

      nav.classList.toggle('scrolled', scrolled > 60);

      // Progress bar — без GSAP, прямая установка
      if (!HAS_ST) {
        bar.style.transform = `scaleX(${scrolled / total})`;
      }

      ticking = false;
    });
  }, { passive: true });
}
