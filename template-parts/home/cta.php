<?php
/**
 * Template Part: CTA BLOCK — studio rounded
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="studio-cta reveal" aria-labelledby="cta-heading">
    <div class="studio-cta__inner">
        <p class="studio-cta__proof">🔥 Более 200 мероприятий в 2025 году</p>
        <h2 id="cta-heading" class="studio-cta__title">
            Готовы сделать<br>событие незабываемым?
        </h2>
        <p class="studio-cta__sub">
            Рассчитайте стоимость за 2 минуты · Без обязательств · Ответим за 5 минут
        </p>
        <div class="studio-cta__buttons">
            <a href="<?php echo esc_url(home_url('/calculator')); ?>"
               class="btn-magnetic">
                Рассчитать стоимость →
            </a>
            <?php if ($phone = get_theme_mod('barpro_phone')) : ?>
                <a href="tel:<?php echo esc_attr(preg_replace('/\D/', '', $phone)); ?>"
                   class="btn-pill btn-pill--ghost">
                    📞 Позвонить
                </a>
            <?php endif; ?>
        </div>
        <p class="studio-cta__guarantee">🛡 Фиксируем цену на 30 дней</p>
    </div>
</section>
