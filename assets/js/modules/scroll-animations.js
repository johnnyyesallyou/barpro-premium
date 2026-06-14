/**
 * BarPro Motion — GSAP SCROLLTRIGGER — REVEALS + PARALLAX + PIN
 */

'use strict';
function initScrollAnimations() {
  if (!HAS_GSAP || !HAS_ST || REDUCED) return;

  gsap.registerPlugin(ScrollTrigger);

  /* ── 4a. Universal reveal (заменяем CSS-класс) ─────────── */
  ScrollTrigger.batch('.reveal', {
    onEnter: batch => gsap.to(batch, {
      opacity: 1,
      y: 0,
      duration: 0.9,
      ease: 'power3.out',
      stagger: 0.08,
    }),
    start: 'top 88%',
    once: true,
  });

  // Начальное состояние для всех .reveal
  gsap.set('.reveal', { opacity: 0, y: 40 });

  /* ── 4b. Bento grid — stagger in ─────────────────────────── */
  gsap.from('.bento', {
    opacity: 0,
    y: 60,
    scale: 0.96,
    duration: 1,
    ease: 'power3.out',
    stagger: {
      each: 0.1,
      from: 'start',
    },
    scrollTrigger: {
      trigger: '.bento-section',
      start: 'top 80%',
      once: true,
    },
  });

  /* ── 4c. Pricing cards — cascade ─────────────────────────── */
  gsap.from('.studio-pricing-card', {
    opacity: 0,
    y: 70,
    duration: 1,
    ease: 'power3.out',
    stagger: 0.15,
    scrollTrigger: {
      trigger: '.studio-pricing__grid',
      start: 'top 82%',
      once: true,
    },
  });

  /* ── 4d. Cocktail cards — стаггер по диагонали ────────────── */
  gsap.from('.cocktail-card-studio', {
    opacity: 0,
    y: 50,
    rotateY: 8,
    transformOrigin: 'left center',
    duration: 0.85,
    ease: 'power3.out',
    stagger: {
      each: 0.07,
      grid: 'auto',
      from: 'start',
    },
    scrollTrigger: {
      trigger: '.cocktail-showcase__grid',
      start: 'top 82%',
      once: true,
    },
  });

  /* ── 4e. Testimonials — fade up с задержкой ──────────────── */
  gsap.from('.studio-testimonial', {
    opacity: 0,
    y: 50,
    duration: 0.9,
    ease: 'power3.out',
    stagger: 0.15,
    scrollTrigger: {
      trigger: '.studio-testimonials__grid',
      start: 'top 85%',
      once: true,
    },
  });

  /* ── 4f. Benefit cards — slide from left ─────────────────── */
  gsap.from('.benefit-card', {
    opacity: 0,
    x: -40,
    duration: 0.8,
    ease: 'power3.out',
    stagger: 0.12,
    scrollTrigger: {
      trigger: '.benefits-grid',
      start: 'top 85%',
      once: true,
    },
  });

  /* ── 4g. Team cards ─────────────────────────────────────── */
  gsap.from('.team-card-studio', {
    opacity: 0,
    y: 50,
    scale: 0.95,
    duration: 0.85,
    ease: 'power3.out',
    stagger: 0.1,
    scrollTrigger: {
      trigger: '.team-grid-studio',
      start: 'top 85%',
      once: true,
    },
  });

  /* ── 4h. CTA block — scale in ────────────────────────────── */
  gsap.from('.studio-cta__inner', {
    opacity: 0,
    scale: 0.94,
    y: 40,
    duration: 1.1,
    ease: 'power4.out',
    scrollTrigger: {
      trigger: '.studio-cta',
      start: 'top 80%',
      once: true,
    },
  });

  /* ── 4i. PARALLAX — hero bg ─────────────────────────────── */
  gsap.to('.studio-hero__bg video', {
    yPercent: 20,
    ease: 'none',
    scrollTrigger: {
      trigger: '.studio-hero',
      start: 'top top',
      end: 'bottom top',
      scrub: 1.5,
    },
  });

  /* ── 4j. PARALLAX — ambient glow ────────────────────────── */
  gsap.to('.studio-hero__glow', {
    yPercent: 35,
    xPercent: -8,
    ease: 'none',
    scrollTrigger: {
      trigger: '.studio-hero',
      start: 'top top',
      end: 'bottom top',
      scrub: 2,
    },
  });

  /* ── 4k. PARALLAX — hero content slower than scroll ─────── */
  gsap.to('.studio-hero__content', {
    yPercent: 15,
    opacity: 0,
    ease: 'none',
    scrollTrigger: {
      trigger: '.studio-hero',
      start: 'top top',
      end: '50% top',
      scrub: 1,
    },
  });

  /* ── 4l. Showcase cards — depth parallax ────────────────── */
  qsa('.showcase-card').forEach((card, i) => {
    const dir = i % 2 === 0 ? -20 : 20;
    gsap.fromTo(card,
      { y: dir },
      {
        y: -dir,
        ease: 'none',
        scrollTrigger: {
          trigger: card,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 1.2,
        },
      }
    );
  });

  /* ── 4m. Stats eyebrow line animate ─────────────────────── */
  gsap.from('.studio-section__eyebrow::before', {
    scaleX: 0,
    transformOrigin: 'left',
    duration: 0.8,
    ease: 'power2.out',
    scrollTrigger: {
      trigger: '.studio-section__eyebrow',
      start: 'top 88%',
      once: true,
    },
  });

  /* ── 4n. Footer columns — stagger ────────────────────────── */
  gsap.from('.studio-footer__col, .studio-footer__brand', {
    opacity: 0,
    y: 30,
    duration: 0.8,
    ease: 'power3.out',
    stagger: 0.1,
    scrollTrigger: {
      trigger: '.studio-footer__grid',
      start: 'top 90%',
      once: true,
    },
  });

  /* ── 4o. Progress bar ────────────────────────────────────── */
  const progressBar = qs('.studio-progress-bar');
  if (progressBar) {
    gsap.to(progressBar, {
      scaleX: 1,
      transformOrigin: 'left',
      ease: 'none',
      scrollTrigger: {
        trigger: document.body,
        start: 'top top',
        end: 'bottom bottom',
        scrub: 0.3,
      },
    });
  }
}

/* ──────────────────────────────────────────────────────────
   PREMIUM UX — Cinematic scroll scenes
   ────────────────────────────────────────────────────────── */

/**
 * Pinned horizontal scroll для showcase секции.
 * Карточки уезжают горизонтально при вертикальном скролле.
 */
function initHorizontalScroll() {
  if (!HAS_GSAP || !HAS_ST || REDUCED) return;

  const section = document.querySelector('.showcase-section');
  const cards   = document.querySelectorAll('.showcase-card');
  if (!section || cards.length < 2) return;

  // Общая ширина для scroll
  const totalWidth = (cards.length - 1) * (cards[0].offsetWidth + 24);

  gsap.to(cards, {
    x: -totalWidth,
    ease: 'none',
    scrollTrigger: {
      trigger: section,
      start: 'top top',
      end: () => '+=' + totalWidth,
      pin: true,
      scrub: 1,
      anticipatePin: 1,
    },
  });
}

/**
 * Text scramble на hover — заголовки карточек.
 */
function initTextScramble() {
  if (REDUCED || COARSE) return;

  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%';

  qsa('.showcase-card__title, .bento__title').forEach(el => {
    const original = el.textContent;
    let interval = null;
    let iteration = 0;

    el.addEventListener('mouseenter', () => {
      clearInterval(interval);
      iteration = 0;

      interval = setInterval(() => {
        el.innerText = original
          .split('')
          .map((char, i) => {
            if (i < iteration) return original[i];
            if (char === ' ') return ' ';
            return chars[Math.floor(Math.random() * chars.length)];
          })
          .join('');

        if (iteration >= original.length) clearInterval(interval);
        iteration += 0.5;
      }, 30);
    });

    el.addEventListener('mouseleave', () => {
      clearInterval(interval);
      el.textContent = original;
    });
  });
}

/**
 * Parallax на изображениях коктейлей.
 */
function initImageParallax() {
  if (!HAS_ST || REDUCED) return;

  qsa('.cocktail-card-studio__img img, .team-card-studio__photo img').forEach(img => {
    gsap.fromTo(img,
      { yPercent: -8 },
      {
        yPercent: 8,
        ease: 'none',
        scrollTrigger: {
          trigger: img,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 1.5,
        },
      }
    );
  });
}

/**
 * Gold line — декоративная линия растёт при скролле.
 */
function initGoldLines() {
  if (!HAS_ST || REDUCED) return;

  qsa('.studio-section__eyebrow').forEach(eyebrow => {
    // Псевдоэлемент нельзя анимировать через GSAP, ставим span
    const line = document.createElement('span');
    line.className = 'eyebrow-line';
    line.setAttribute('aria-hidden', 'true');
    eyebrow.prepend(line);

    gsap.fromTo(line,
      { scaleX: 0, transformOrigin: 'left' },
      {
        scaleX: 1,
        duration: 0.8,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: eyebrow,
          start: 'top 88%',
          once: true,
        },
      }
    );
  });
}

/**
 * Stagger letters в hero stats.
 */
function initStatsEntrance() {
  if (!HAS_GSAP || REDUCED) return;

  const stats = document.querySelectorAll('.studio-hero__stat-number');
  if (!stats.length) return;

  gsap.from(stats, {
    opacity: 0,
    y: 30,
    scale: 0.8,
    duration: 0.7,
    ease: 'back.out(1.5)',
    stagger: 0.12,
    delay: 1.2,
  });
}

// Добавляем в init
document.addEventListener('DOMContentLoaded', () => {
  initHorizontalScroll();
  initTextScramble();
  initImageParallax();
  initGoldLines();
  initStatsEntrance();
});
