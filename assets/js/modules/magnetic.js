/**
 * BarPro Motion — MAGNETIC BUTTONS — real physics
 */

'use strict';
function initMagneticButtons() {
  if (COARSE || REDUCED) return;

  qsa('[data-magnetic], .btn-magnetic, .btn-pill').forEach(btn => {
    const strength = parseFloat(btn.dataset.magneticStrength || 0.35);
    let targetX = 0, targetY = 0;
    let currentX = 0, currentY = 0;
    let raf = null;
    let isHovered = false;

    function animate() {
      currentX = lerp(currentX, targetX, 0.1);
      currentY = lerp(currentY, targetY, 0.1);

      const dist = Math.sqrt(currentX ** 2 + currentY ** 2);
      if (dist < 0.05 && !isHovered) {
        cancelAnimationFrame(raf);
        raf = null;
        return;
      }

      btn.style.transform = `translate(${currentX}px, ${currentY}px)`;
      raf = requestAnimationFrame(animate);
    }

    btn.addEventListener('mousemove', e => {
      const r  = btn.getBoundingClientRect();
      const cx = r.left + r.width  / 2;
      const cy = r.top  + r.height / 2;
      targetX = (e.clientX - cx) * strength;
      targetY = (e.clientY - cy) * strength;
      isHovered = true;
      if (!raf) raf = requestAnimationFrame(animate);
    });

    btn.addEventListener('mouseleave', () => {
      targetX = 0;
      targetY = 0;
      isHovered = false;
      if (!raf) raf = requestAnimationFrame(animate);
    });

    // Ripple on click
    btn.addEventListener('click', e => createRipple(btn, e));
  });
}
