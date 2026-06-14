/**
 * BarPro Motion — CUSTOM CURSOR
 */

'use strict';
function initCursor() {
  if (COARSE || REDUCED) return;

  const dot      = qs('.studio-cursor');
  const follower = qs('.studio-cursor-follower');
  if (!dot || !follower) return;

  let mx = -100, my = -100;
  let fx = -100, fy = -100;

  // Dot follows mouse instantly
  document.addEventListener('mousemove', e => {
    mx = e.clientX;
    my = e.clientY;
    dot.style.transform = `translate(calc(${mx}px - 50%), calc(${my}px - 50%))`;
  });

  // Follower lerps
  ;(function followLoop() {
    fx = lerp(fx, mx, 0.1);
    fy = lerp(fy, my, 0.1);
    follower.style.transform = `translate(calc(${fx}px - 50%), calc(${fy}px - 50%))`;
    requestAnimationFrame(followLoop);
  })();

  // States
  const hoverTargets = 'a, button, [role="button"], .bento, .showcase-card, .cocktail-card-studio, .studio-pricing-card, [data-cursor]';

  document.addEventListener('mouseover', e => {
    const target = e.target.closest(hoverTargets);
    if (!target) return;
    dot.classList.add('hovering');
    follower.classList.add('hovering');

    // Data-cursor label
    const label = target.dataset.cursor;
    if (label) follower.setAttribute('data-label', label);
  });

  document.addEventListener('mouseout', e => {
    const target = e.target.closest(hoverTargets);
    if (!target) return;
    dot.classList.remove('hovering');
    follower.classList.remove('hovering');
    follower.removeAttribute('data-label');
  });

  // Click squeeze
  document.addEventListener('mousedown', () => {
    dot.classList.add('clicking');
    follower.classList.add('clicking');
  });
  document.addEventListener('mouseup', () => {
    dot.classList.remove('clicking');
    follower.classList.remove('clicking');
  });
}
