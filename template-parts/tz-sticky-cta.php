<?php
/**
 * Sticky CTA-бар внизу на мобильных
 *
 * @package BarPro_Premium
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$phone   = get_theme_mod( 'barpro_phone', '+7 (999) 999-99-99' );
$tg_raw  = get_theme_mod( 'barpro_telegram', 'https://t.me/barpro' );
$tg_link = $tg_raw;
if ( $tg_link && strpos( $tg_link, 'http' ) !== 0 ) {
    $tg_link = 'https://t.me/' . ltrim( $tg_link, '@' );
}
?>
<div class="tz-sticky-cta" role="region" aria-label="Быстрая связь">
    <a href="<?php echo esc_url( home_url( '/calculator' ) ); ?>" class="tz-btn tz-btn--primary">Расчёт</a>
    <?php if ( $phone ) : ?>
        <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>" class="tz-btn tz-btn--ghost" aria-label="Позвонить">📞</a>
    <?php endif; ?>
    <?php if ( $tg_link ) : ?>
        <a href="<?php echo esc_url( $tg_link ); ?>" class="tz-btn tz-btn--ghost" target="_blank" rel="noopener" aria-label="Telegram">✈</a>
    <?php endif; ?>
</div>
