/**
 * BarPro Studio — Premium Interactions
 * Priority 1: custom cursor, magnetic buttons, scroll reveal, smooth scroll
 * Priority 2: 3D tilt cards, bento hover glow, ingredient reveal
 */

'use strict';

(function() {

  // ── Utils ────────────────────────────────────────────────
  const qs  = (sel, ctx = document) => ctx.querySelector(sel);
  const qsa = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const coarse  = window.matchMedia('(pointer: coarse)').matches;

  // ── 1. CUSTOM CURSOR ────────────────────────────────────
  if (!coarse && !reduced) {
    const cursor   = qs('.studio-cursor');
    const follower = qs('.studio-cursor-follower');

    if (cursor && follower) {
      let mx = 0, my = 0, fx = 0, fy = 0;

      document.addEventListener('mousemove', e => {
        mx = e.clientX;
        my = e.clientY;
        cursor.style.left   = mx + 'px';
        cursor.style.top    = my + 'px';
      });

      // Follower lags behind with lerp
      function lerpCursor() {
        fx += (mx - fx) * 0.12;
        fy += (my - fy) * 0.12;
        follower.style.left = fx + 'px';
        follower.style.top  = fy + 'px';
        requestAnimationFrame(lerpCursor);
      }
      lerpCursor();

      // Hover state on interactive elements
      const hoverEls = qsa('a, button, [role="button"], .bento, .showcase-card, .cocktail-card-studio, .studio-pricing-card');
      hoverEls.forEach(el => {
        el.addEventListener('mouseenter', () => {
          cursor.classList.add('hovering');
          follower.classList.add('hovering');
        });
        el.addEventListener('mouseleave', () => {
          cursor.classList.remove('hovering');
          follower.classList.remove('hovering');
        });
      });
    }
  }

  // ── 2. NAV: shrink on scroll + burger ───────────────────
  const nav      = qs('#studioNav');
  const burger   = qs('#burgerBtn');
  const drawer   = qs('#navDrawer');
  const overlay  = qs('#navOverlay');

  if (nav) {
    const onScroll = () => {
      nav.classList.toggle('scrolled', window.scrollY > 60);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  function openDrawer() {
    drawer && drawer.classList.add('open');
    overlay && overlay.classList.add('visible');
    burger  && (burger.classList.add('open'), burger.setAttribute('aria-expanded', 'true'));
    document.body.style.overflow = 'hidden';
  }
  function closeDrawer() {
    drawer && drawer.classList.remove('open');
    overlay && overlay.classList.remove('visible');
    burger  && (burger.classList.remove('open'), burger.setAttribute('aria-expanded', 'false'));
    document.body.style.overflow = '';
  }

  burger  && burger.addEventListener('click', () => drawer && drawer.classList.contains('open') ? closeDrawer() : openDrawer());
  overlay && overlay.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', e => e.key === 'Escape' && closeDrawer());

  // ── 3. SCROLL REVEAL ───────────────────────────────────
  if (!reduced) {
    const reveals = qsa('.reveal');
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

      reveals.forEach(el => observer.observe(el));
    } else {
      // Fallback: show all
      reveals.forEach(el => el.classList.add('visible'));
    }
  } else {
    // Reduced motion: show all immediately
    qsa('.reveal').forEach(el => el.classList.add('visible'));
  }

  // ── 4. BENTO HOVER GLOW (mouse-reactive) ───────────────
  if (!coarse && !reduced) {
    qsa('.bento').forEach(bento => {
      bento.addEventListener('mousemove', e => {
        const r = bento.getBoundingClientRect();
        const x = ((e.clientX - r.left) / r.width)  * 100;
        const y = ((e.clientY - r.top)  / r.height) * 100;
        bento.style.setProperty('--mx', x + '%');
        bento.style.setProperty('--my', y + '%');
      });
    });
  }

  // ── 5. 3D TILT — showcase cards ─────────────────────────
  if (!coarse && !reduced) {
    qsa('[data-tilt]').forEach(card => {
      card.addEventListener('mousemove', e => {
        const r  = card.getBoundingClientRect();
        const x  = e.clientX - r.left;
        const y  = e.clientY - r.top;
        const rx = -((y - r.height / 2) / 22);
        const ry =  ((x - r.width  / 2) / 22);
        card.style.transform = `perspective(1200px) rotateX(${rx}deg) rotateY(${ry}deg) translateZ(8px)`;
      });
      card.addEventListener('mouseleave', () => {
        card.style.transform = 'perspective(1200px) rotateX(0) rotateY(0) translateZ(0)';
      });
    });
  }

  // ── 6. MAGNETIC BUTTONS ─────────────────────────────────
  if (!coarse && !reduced) {
    qsa('.btn-magnetic').forEach(btn => {
      btn.addEventListener('mousemove', e => {
        const r  = btn.getBoundingClientRect();
        const x  = ((e.clientX - r.left) / r.width)  * 100;
        const y  = ((e.clientY - r.top)  / r.height) * 100;
        btn.style.setProperty('--mx', x + '%');
        btn.style.setProperty('--my', y + '%');

        // Light pull effect
        const dx = (e.clientX - (r.left + r.width  / 2)) * 0.25;
        const dy = (e.clientY - (r.top  + r.height / 2)) * 0.25;
        btn.style.transform = `translate(${dx}px, ${dy}px) scale(1.04)`;
      });
      btn.addEventListener('mouseleave', () => {
        btn.style.transform = '';
      });
    });
  }

  // ── 7. SMOOTH SCROLL for anchor links ───────────────────
  qsa('a[href^="#"]').forEach(link => {
    link.addEventListener('click', e => {
      const id = link.getAttribute('href').slice(1);
      const target = id ? document.getElementById(id) : null;
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ── 8. GSAP ANIMATIONS (только если загружен) ───────────
  if (typeof gsap !== 'undefined') {

    // ScrollTrigger batch reveals
    if (typeof ScrollTrigger !== 'undefined') {
      gsap.registerPlugin(ScrollTrigger);

      // Bento stagger
      gsap.fromTo('.bento', {
        opacity: 0, y: 40
      }, {
        opacity: 1, y: 0,
        stagger: .08,
        duration: .9,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: '.bento-section',
          start: 'top 80%',
        }
      });

      // Pricing cards
      gsap.fromTo('.studio-pricing-card', {
        opacity: 0, y: 50, scale: .97
      }, {
        opacity: 1, y: 0, scale: 1,
        stagger: .12,
        duration: .9,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: '.studio-pricing__grid',
          start: 'top 80%',
        }
      });

      // Cocktail cards
      gsap.fromTo('.cocktail-card-studio', {
        opacity: 0, y: 40
      }, {
        opacity: 1, y: 0,
        stagger: .07,
        duration: .8,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: '.cocktail-showcase__grid',
          start: 'top 80%',
        }
      });

      // CTA block
      gsap.fromTo('.studio-cta__inner', {
        opacity: 0, scale: .97
      }, {
        opacity: 1, scale: 1,
        duration: 1,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: '.studio-cta',
          start: 'top 80%',
        }
      });
    }
  }

  // ── 9. LENIS smooth scroll (если загружен) ─────────────
  if (typeof Lenis !== 'undefined' && !reduced) {
    const lenis = new Lenis({
      duration: 1.2,
      easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      orientation: 'vertical',
      smoothWheel: true,
    });

    function raf(time) {
      lenis.raf(time);
      requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    // Sync с ScrollTrigger если есть
    if (typeof ScrollTrigger !== 'undefined') {
      lenis.on('scroll', ScrollTrigger.update);
    }
  }

  // ── 10. HERO: число-счётчик ─────────────────────────────
  function animateCounter(el) {
    const target = parseInt(el.textContent.replace(/\D/g, ''), 10);
    if (!target) return;
    const suffix = el.textContent.replace(/[0-9]/g, '');
    let start = null;
    const dur = 1600;

    function step(ts) {
      if (!start) start = ts;
      const progress = Math.min((ts - start) / dur, 1);
      const ease = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(ease * target) + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  if ('IntersectionObserver' in window && !reduced) {
    const counterObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          counterObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    qsa('.studio-hero__stat-number, .bento__stat').forEach(el => {
      counterObserver.observe(el);
    });
  }

})();
