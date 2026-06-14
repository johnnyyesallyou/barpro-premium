/**
 * BarPro Motion — NUMBER COUNTERS
 */

'use strict';
function initCounters() {
  if (REDUCED) return;

  const counterEls = qsa('.studio-hero__stat-number, .bento__stat');
  if (!counterEls.length) return;

  const observe = el => {
    const raw    = el.textContent.trim();
    const target = parseInt(raw.replace(/\D/g, ''), 10);
    const suffix = raw.replace(/[\d\s]/g, '');
    if (!target) return;

    const io = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        io.unobserve(el);

        if (HAS_GSAP) {
          gsap.fromTo({ val: 0 }, { val: target }, {
            duration: 1.8,
            ease: 'power2.out',
            onUpdate() { el.textContent = Math.round(this.targets()[0].val) + suffix; },
          });
        } else {
          // Fallback RAF counter
          let start = performance.now();
          const dur = 1800;
          ;(function step(now) {
            const p = clamp((now - start) / dur, 0, 1);
            const e = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(e * target) + suffix;
            if (p < 1) requestAnimationFrame(step);
          })(start);
        }
      });
    }, { threshold: 0.5 });

    io.observe(el);
  };

  counterEls.forEach(observe);
}
