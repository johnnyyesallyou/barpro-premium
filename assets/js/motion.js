/**
 * BarPro Studio — Motion System Entry Point
 * Координирует все motion-модули.
 * В dev подключается напрямую, в prod собирается через Vite.
 */

'use strict';

/* ─── Guards ────────────────────────────────────────────── */
/**
 * BarPro Studio — Motion Design System
 * ─────────────────────────────────────
 * 1.  Lenis smooth scroll
 * 2.  Page transitions (overlay wipe)
 * 3.  SplitType — kinetic typography
 * 4.  GSAP ScrollTrigger — reveal, parallax, pinned sections
 * 5.  Magnetic buttons (real physics)
 * 6.  Custom cursor (dot + follower + text label)
 * 7.  Micro-interactions (ripple, button press, card lift)
 * 8.  Hero cinematic entrance sequence
 * 9.  Horizontal scroll section
 * 10. Number counters
 * 11. Nav shrink + progress bar
 * 12. Mobile burger drawer
 */


/* ─── Guards ───────────────────────────────────────────────── */
const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const COARSE  = window.matchMedia('(pointer: coarse)').matches;
const HAS_GSAP = typeof gsap !== 'undefined';
const HAS_ST   = HAS_GSAP && typeof ScrollTrigger !== 'undefined';
const HAS_SPLIT = typeof SplitType !== 'undefined';
const HAS_LENIS = typeof Lenis !== 'undefined';

/* ─── Utils ────────────────────────────────────────────────── */
const qs  = (s, c = document) => c.querySelector(s);
const qsa = (s, c = document) => [...c.querySelectorAll(s)];
const lerp = (a, b, t) => a + (b - a) * t;
const clamp = (v, min, max) => Math.min(Math.max(v, min), max);

/* ─── Modules ───────────────────────────────────────────── */
// В dev-режиме модули подключаются отдельно через wp_enqueue_script.
// В prod они объединяются Vite в единый bundle.

/* ─── INIT ──────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  // Lenis
  if (typeof initLenis        === 'function') initLenis();
  // Transitions
  if (typeof initPageTransitions === 'function') initPageTransitions();
  // Nav
  if (typeof initNav          === 'function') initNav();
  if (typeof initDrawer       === 'function') initDrawer();
  // Cursor
  if (typeof initCursor       === 'function') initCursor();
  // Hero
  if (typeof initHeroEntrance === 'function') initHeroEntrance();
  // Typography
  if (typeof initSplitType    === 'function') initSplitType();
  // Scroll
  if (typeof initScrollAnimations === 'function') initScrollAnimations();
  // Interactive
  if (typeof initMagneticButtons  === 'function') initMagneticButtons();
  if (typeof initTilt3D           === 'function') initTilt3D();
  if (typeof initBentoSpotlight   === 'function') initBentoSpotlight();
  if (typeof initCardMicroInteractions  === 'function') initCardMicroInteractions();
  if (typeof initNavMicroInteractions   === 'function') initNavMicroInteractions();
  if (typeof initInputMicroInteractions === 'function') initInputMicroInteractions();
  // Counters + scroll-to-top
  if (typeof initCounters    === 'function') initCounters();
  if (typeof initScrollToTop === 'function') initScrollToTop();
});
