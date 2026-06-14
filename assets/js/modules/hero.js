/**
 * BarPro Motion — HERO CINEMATIC ENTRANCE
 */

'use strict';
function initHeroEntrance() {
  if (!HAS_GSAP || REDUCED) return;

  const tl = gsap.timeline({ delay: 0.1 });

  // Eyebrow
  tl.from('.studio-hero__eyebrow', {
    opacity: 0, y: 20, duration: 0.7, ease: 'power3.out',
  });

  // Title lines — если нет SplitType, анимируем по span
  if (!HAS_SPLIT) {
    tl.from('.studio-hero__title .line-1', {
      opacity: 0, y: 80, duration: 0.9, ease: 'power4.out',
    }, '-=0.3')
    .from('.studio-hero__title .line-2', {
      opacity: 0, y: 80, duration: 0.9, ease: 'power4.out',
    }, '-=0.65')
    .from('.studio-hero__title .line-3', {
      opacity: 0, y: 80, duration: 0.9, ease: 'power4.out',
    }, '-=0.65');
  }

  // Sub + actions + stats
  tl.from('.studio-hero__sub', {
    opacity: 0, y: 30, duration: 0.8, ease: 'power3.out',
  }, '-=0.4')
  .from('.studio-hero__actions', {
    opacity: 0, y: 20, duration: 0.7, ease: 'power3.out',
  }, '-=0.5')
  .from('.studio-hero__stats', {
    opacity: 0, y: 20, duration: 0.7, ease: 'power3.out',
  }, '-=0.4')
  .from('.studio-hero__scroll', {
    opacity: 0, duration: 0.6, ease: 'power2.out',
  }, '-=0.3');

  // Video fade in
  tl.from('.studio-hero__bg video', {
    opacity: 0, scale: 1.05, duration: 2, ease: 'power2.out',
  }, 0);
}

/* ─── Video lazy load (только десктоп > 768px) ─────────── */
function initVideoLazyLoad() {
  const video = document.querySelector('.hero-video[data-autoplay]');
  if (!video) return;

  // На мобильных не грузим видео совсем
  if (window.innerWidth < 769 || REDUCED) {
    video.remove(); // убрать из DOM — poster остаётся как BG
    return;
  }

  // Загрузить src из data-src
  video.querySelectorAll('source[data-src]').forEach(source => {
    source.src = source.dataset.src;
    delete source.dataset.src;
  });

  video.load();
  video.play().catch(() => {
    // Autoplay blocked — poster уже показан
  });
}

// Вызвать при инициализации
document.addEventListener('DOMContentLoaded', initVideoLazyLoad);
