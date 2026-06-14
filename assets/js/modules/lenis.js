/**
 * BarPro Motion — LENIS SMOOTH SCROLL
 */

'use strict';
let lenis = null;

function initLenis() {
  if (!HAS_LENIS || REDUCED) return;

  lenis = new Lenis({
    duration: 1.4,
    easing: t => 1 - Math.pow(1 - t, 4),   // quartic ease-out
    orientation: 'vertical',
    smoothWheel: true,
    wheelMultiplier: 0.9,
    touchMultiplier: 1.8,
  });

  function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
  }
  requestAnimationFrame(raf);

  if (HAS_ST) {
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add(time => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
  }

  // Anchor links через Lenis
  qsa('a[href^="#"]').forEach(link => {
    link.addEventListener('click', e => {
      const id = link.getAttribute('href').slice(1);
      const target = id ? document.getElementById(id) : null;
      if (target) { e.preventDefault(); lenis.scrollTo(target, { offset: -80 }); }
    });
  });
}
