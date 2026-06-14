/**
 * BarPro Motion — PAGE TRANSITIONS
 */

'use strict';
function initPageTransitions() {
  if (!HAS_GSAP || REDUCED) return;

  // Создаём overlay если его нет
  let overlay = qs('.page-transition-overlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.className = 'page-transition-overlay';
    overlay.innerHTML = '<div class="page-transition-overlay__bar"></div>';
    document.body.appendChild(overlay);
  }

  const bar = qs('.page-transition-overlay__bar', overlay);

  // Enter: overlay уходит вверх при загрузке
  const tlEnter = gsap.timeline();
  tlEnter
    .set(overlay, { yPercent: 0, display: 'flex' })
    .to(overlay, {
      yPercent: -100,
      duration: 1.0,
      ease: 'power4.inOut',
      delay: 0.05,
    })
    .set(overlay, { display: 'none' });

  // Exit: overlay приходит снизу при клике на ссылку
  function exitTransition(href) {
    lenis && lenis.stop();
    gsap.set(overlay, { yPercent: 100, display: 'flex' });
    gsap.to(overlay, {
      yPercent: 0,
      duration: 0.7,
      ease: 'power4.inOut',
      onComplete: () => { window.location.href = href; },
    });
  }

  // Перехватываем клики по внутренним ссылкам
  document.addEventListener('click', e => {
    const link = e.target.closest('a');
    if (!link) return;
    const href = link.getAttribute('href');
    if (!href) return;

    const isSameDomain = link.hostname === window.location.hostname;
    const isAnchor = href.startsWith('#');
    const isSpecial = link.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey;

    if (isSameDomain && !isAnchor && !isSpecial) {
      e.preventDefault();
      exitTransition(href);
    }
  });
}
