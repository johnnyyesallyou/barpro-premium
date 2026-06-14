<?php
/**
 * Универсальный Hero в стиле Event Premium
 *
 * @package BarPro_Premium
 *
 * args:
 *   title       — заголовок (string)
 *   subtitle    — подзаголовок (string)
 *   eyebrow     — надбровник (string)
 *   video_url   — URL фонового видео (опционально)
 *   image_url   — URL фонового изображения (если нет видео)
 *   parallax    — bool, включить parallax
 *   cta         — bool, показать CTA-набор
 *   breadcrumbs — array of items для хлебных крошек
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$title       = $args['title']       ?? '';
$subtitle    = $args['subtitle']    ?? '';
$eyebrow     = $args['eyebrow']     ?? '';
$video_url   = $args['video_url']   ?? '';
$image_url   = $args['image_url']   ?? '';
$parallax    = ! empty( $args['parallax'] );
$cta         = $args['cta']         ?? true;
$breadcrumbs = $args['breadcrumbs'] ?? null;
?>
<section class="tz-hero<?php echo $parallax ? '' : ''; ?>" <?php echo $parallax ? 'data-parallax="1"' : ''; ?>>
    <div class="tz-hero__media" aria-hidden="true">
        <?php if ( $video_url ) : ?>
            <video autoplay muted loop playsinline preload="metadata"
                   poster="<?php echo esc_url( $image_url ); ?>">
                <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
            </video>
        <?php elseif ( $image_url ) : ?>
            <img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="eager" decoding="async">
        <?php endif; ?>
    </div>

    <div class="tz-container tz-hero__inner">
        <?php if ( $eyebrow ) : ?>
            <span class="tz-eyebrow tz-reveal"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>

        <?php if ( $title ) : ?>
            <h1 class="tz-h1 tz-hero__title tz-reveal" data-delay="1"><?php echo wp_kses_post( $title ); ?></h1>
        <?php endif; ?>

        <?php if ( $subtitle ) : ?>
            <p class="tz-hero__subtitle tz-reveal" data-delay="2"><?php echo wp_kses_post( $subtitle ); ?></p>
        <?php endif; ?>

        <?php if ( $cta ) : ?>
            <div class="tz-reveal" data-delay="3">
                <?php get_template_part( 'template-parts/tz-cta-row', null, [ 'variant' => 'hero' ] ); ?>
            </div>
        <?php endif; ?>

        <?php if ( is_array( $breadcrumbs ) ) :
            get_template_part( 'template-parts/tz-breadcrumbs', null, [ 'items' => $breadcrumbs ] );
        endif; ?>
    </div>
</section>
