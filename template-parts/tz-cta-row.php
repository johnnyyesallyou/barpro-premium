<?php
/**
 * CTA-набор кнопок по ТЗ: Получить расчёт / WhatsApp / Telegram / Позвонить
 *
 * @package BarPro_Premium
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$phone    = get_theme_mod( 'barpro_phone',    '+7 (999) 999-99-99' );
$wa_raw   = get_theme_mod( 'barpro_whatsapp', '' );
$tg_raw   = get_theme_mod( 'barpro_telegram', 'https://t.me/barpro' );
$calc_url = home_url( '/calculator' );
$variant  = $args['variant'] ?? 'default'; // default | hero | inline | result

// WhatsApp ссылка
$wa_link = '';
if ( $wa_raw ) {
    $wa_digits = preg_replace( '/\D/', '', $wa_raw );
    $wa_text   = urlencode( 'Здравствуйте! Хочу получить КП на бар для мероприятия.' );
    $wa_link   = "https://wa.me/{$wa_digits}?text={$wa_text}";
}

// Telegram ссылка
$tg_link = $tg_raw;
if ( $tg_link && strpos( $tg_link, 'http' ) !== 0 ) {
    $tg_link = 'https://t.me/' . ltrim( $tg_link, '@' );
}
?>
<div class="tz-cta-row tz-cta-row--<?php echo esc_attr( $variant ); ?>" role="group" aria-label="Связаться с BarPro">

    <?php if ( $variant !== 'result' ) : ?>
    <a href="<?php echo esc_url( $calc_url ); ?>" class="tz-btn tz-btn--primary" data-tz-cta="calc">
        Получить расчёт
    </a>
    <?php endif; ?>

    <?php if ( $wa_link ) : ?>
        <a href="<?php echo esc_url( $wa_link ); ?>"
           class="tz-btn tz-btn--whatsapp"
           target="_blank" rel="noopener noreferrer"
           data-tz-cta="wa"
           aria-label="Получить КП в WhatsApp">
            <svg width="18" height="18" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><path d="M16 0C7.163 0 0 7.163 0 16c0 2.825.737 5.607 2.137 8.048L0 32l7.933-2.127A15.93 15.93 0 0016 32c8.837 0 16-7.163 16-16S24.837 0 16 0zm7.46 21.973c-.197-.099-1.163-.573-1.342-.639-.179-.066-.309-.099-.439.099s-.505.639-.619.77c-.114.131-.228.147-.425.049s-.829-.305-1.579-.974c-.584-.521-.978-1.163-1.093-1.36s-.012-.304.087-.403c.088-.088.197-.228.295-.342s.131-.197.197-.328c.066-.131.033-.246-.016-.344s-.439-1.057-.602-1.447c-.158-.379-.319-.327-.439-.333-.114-.006-.244-.007-.374-.007s-.342.049-.521.246-.684.668-.684 1.629.7 1.891.798 2.021c.099.131 1.393 2.128 3.375 2.984.471.203.839.325 1.126.416.473.151.904.129 1.246.078.38-.057 1.163-.476 1.327-.935s.164-.853.115-.935c-.05-.082-.181-.131-.378-.23z"/></svg>
            КП в WhatsApp
        </a>
    <?php endif; ?>

    <?php if ( $tg_link ) : ?>
        <a href="<?php echo esc_url( $tg_link ); ?>"
           class="tz-btn tz-btn--ghost"
           target="_blank" rel="noopener noreferrer"
           data-tz-cta="tg"
           aria-label="Написать в Telegram">
            <span aria-hidden="true">✈</span> КП в Telegram
        </a>
    <?php endif; ?>

    <?php if ( $phone ) : ?>
        <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"
           class="tz-btn tz-btn--ghost"
           data-tz-cta="call"
           aria-label="Позвонить <?php echo esc_attr( $phone ); ?>">
            <span aria-hidden="true">📞</span> Позвонить
        </a>
    <?php endif; ?>

</div>
