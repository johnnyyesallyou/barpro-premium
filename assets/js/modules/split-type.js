/**
 * BarPro Motion — SPLITTYPE — KINETIC TYPOGRAPHY
 */

'use strict';
function initSplitType() {
  if (!HAS_SPLIT || !HAS_GSAP || REDUCED) return;

  // Hero title — per-char входной animate
  const heroLines = qsa('.studio-hero__title span');
  if (heroLines.length) {
    heroLines.forEach((line, i) => {
      const split = new SplitType(line, { types: 'chars' });
      gsap.from(split.chars, {
        opacity: 0,
        y: 60,
        rotateX: -90,
        transformOrigin: '50% 50% -20px',
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.03,
        delay: 0.3 + i * 0.18,
      });
    });
  }

  // Section titles — reveal при скролле
  if (!HAS_ST) return;

  qsa('.studio-section__title, .studio-cta__title, .bento__title').forEach(el => {
    const split = new SplitType(el, { types: 'lines,words' });
    gsap.set(split.words, { overflow: 'hidden' });

    gsap.from(split.words, {
      yPercent: 110,
      duration: 0.85,
      ease: 'power3.out',
      stagger: 0.06,
      scrollTrigger: {
        trigger: el,
        start: 'top 88%',
        once: true,
      },
    });
  });
}
