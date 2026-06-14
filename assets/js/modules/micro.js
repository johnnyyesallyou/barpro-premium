/**
 * BarPro Motion — MICRO-INTERACTIONS
 */

'use strict';
/* ── Ripple effect ─────────────────────────────────────────── */
function createRipple(btn, e) {
  const existing = btn.querySelector('.ripple');
  if (existing) existing.remove();

  const r    = btn.getBoundingClientRect();
  const size = Math.max(r.width, r.height) * 2;
  const x    = e.clientX - r.left - size / 2;
  const y    = e.clientY - r.top  - size / 2;

  const ripple = document.createElement('span');
  ripple.className = 'ripple';
  ripple.style.cssText = `
    position:absolute; border-radius:50%; pointer-events:none;
    width:${size}px; height:${size}px;
    left:${x}px; top:${y}px;
    background:rgba(255,255,255,0.25);
    transform:scale(0); opacity:1;
    animation:rippleAnim 0.6s cubic-bezier(.4,0,.2,1) forwards;
  `;
  btn.style.position = btn.style.position || 'relative';
  btn.style.overflow = 'hidden';
  btn.appendChild(ripple);
  ripple.addEventListener('animationend', () => ripple.remove());
}

/* ── Card lift — добавляем тени ────────────────────────────── */
function initCardMicroInteractions() {
  if (!HAS_GSAP) return;

  qsa('.studio-pricing-card, .bento, .team-card-studio, .studio-testimonial').forEach(card => {
    card.addEventListener('mouseenter', () => {
      if (REDUCED) return;
      gsap.to(card, { y: -6, duration: 0.4, ease: 'power2.out' });
    });
    card.addEventListener('mouseleave', () => {
      if (REDUCED) return;
      gsap.to(card, { y: 0, duration: 0.5, ease: 'power2.out' });
    });
  });
}

/* ── Nav link underline animate ────────────────────────────── */
function initNavMicroInteractions() {
  if (!HAS_GSAP || REDUCED) return;

  qsa('.studio-nav__menu a').forEach(link => {
    const line = document.createElement('span');
    line.className = 'nav-underline';
    line.style.cssText = `
      position:absolute; bottom:-4px; left:0;
      width:100%; height:1px;
      background:var(--accent);
      transform:scaleX(0); transform-origin:right;
      transition:transform 0.35s cubic-bezier(.76,0,.24,1);
    `;
    link.style.position = 'relative';
    link.appendChild(line);

    link.addEventListener('mouseenter', () => {
      line.style.transformOrigin = 'left';
      line.style.transform = 'scaleX(1)';
    });
    link.addEventListener('mouseleave', () => {
      line.style.transformOrigin = 'right';
      line.style.transform = 'scaleX(0)';
    });
  });
}

/* ── Input focus animations ─────────────────────────────────── */
function initInputMicroInteractions() {
  if (!HAS_GSAP) return;

  qsa('input, textarea, select').forEach(input => {
    const label = input.previousElementSibling;

    input.addEventListener('focus', () => {
      gsap.to(input, {
        boxShadow: '0 0 0 2px rgba(212,175,55,0.4)',
        duration: 0.3,
        ease: 'power2.out',
      });
      if (label && label.tagName === 'LABEL') {
        gsap.to(label, { color: '#d4af37', duration: 0.3 });
      }
    });

    input.addEventListener('blur', () => {
      gsap.to(input, { boxShadow: '0 0 0 0px transparent', duration: 0.3 });
      if (label && label.tagName === 'LABEL') {
        gsap.to(label, { color: '', duration: 0.3 });
      }
    });
  });
}

/* ── Scroll-to-top кнопка ────────────────────────────────────── */
function initScrollToTop() {
  let btn = qs('.scroll-to-top');
  if (!btn) {
    btn = document.createElement('button');
    btn.className = 'scroll-to-top';
    btn.setAttribute('aria-label', 'Наверх');
    btn.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg>';
    document.body.appendChild(btn);
  }

  window.addEventListener('scroll', () => {
    btn.classList.toggle('visible', window.scrollY > window.innerHeight);
  }, { passive: true });

  btn.addEventListener('click', () => {
    if (lenis) { lenis.scrollTo(0); } else { window.scrollTo({ top: 0, behavior: 'smooth' }); }
  });
}

/* ── 3D Tilt на showcase cards ──────────────────────────────── */
function initTilt3D() {
  if (COARSE || REDUCED) return;

  qsa('[data-tilt]').forEach(card => {
    let raf = null;
    let targetRX = 0, targetRY = 0;
    let currentRX = 0, currentRY = 0;

    function tiltLoop() {
      currentRX = lerp(currentRX, targetRX, 0.08);
      currentRY = lerp(currentRY, targetRY, 0.08);
      card.style.transform =
        `perspective(1200px) rotateX(${currentRX}deg) rotateY(${currentRY}deg) translateZ(10px)`;

      if (Math.abs(currentRX - targetRX) > 0.01 || Math.abs(currentRY - targetRY) > 0.01) {
        raf = requestAnimationFrame(tiltLoop);
      } else {
        raf = null;
      }
    }

    card.addEventListener('mousemove', e => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left - r.width  / 2) / (r.width  / 2);
      const y = (e.clientY - r.top  - r.height / 2) / (r.height / 2);
      targetRY =  x * 10;
      targetRX = -y * 10;
      if (!raf) raf = requestAnimationFrame(tiltLoop);
    });

    card.addEventListener('mouseleave', () => {
      targetRX = 0;
      targetRY = 0;
      if (!raf) raf = requestAnimationFrame(tiltLoop);
    });
  });
}

/* ── Bento spotlight ────────────────────────────────────────── */
function initBentoSpotlight() {
  if (COARSE || REDUCED) return;

  qsa('.bento').forEach(el => {
    el.addEventListener('mousemove', e => {
      const r = el.getBoundingClientRect();
      const x = ((e.clientX - r.left) / r.width)  * 100;
      const y = ((e.clientY - r.top)  / r.height) * 100;
      el.style.setProperty('--mx', x + '%');
      el.style.setProperty('--my', y + '%');
    });
  });
}
