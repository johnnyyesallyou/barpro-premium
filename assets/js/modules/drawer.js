/**
 * BarPro Motion — MOBILE BURGER + DRAWER
 */

'use strict';
function initDrawer() {
  const burger  = qs('#burgerBtn');
  const drawer  = qs('#navDrawer');
  const overlay = qs('#navOverlay');
  if (!burger || !drawer) return;

  const open = () => {
    drawer.classList.add('open');
    overlay && overlay.classList.add('visible');
    burger.classList.add('open');
    burger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';

    if (HAS_GSAP && !REDUCED) {
      gsap.from(qsa('a', drawer), {
        opacity: 0, x: 30, duration: 0.5,
        ease: 'power3.out', stagger: 0.06, delay: 0.1,
      });
    }
  };

  const close = () => {
    drawer.classList.remove('open');
    overlay && overlay.classList.remove('visible');
    burger.classList.remove('open');
    burger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  };

  burger.addEventListener('click', () =>
    drawer.classList.contains('open') ? close() : open()
  );
  overlay && overlay.addEventListener('click', close);
  document.addEventListener('keydown', e => e.key === 'Escape' && close());
}
