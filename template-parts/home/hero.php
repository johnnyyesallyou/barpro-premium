<?php
/**
 * Template Part: CINEMATIC HERO
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="studio-hero" aria-label="Hero">

    <!-- BG: video + gradients + grain -->
    <div class="studio-hero__bg">
        <?php
        $video_mp4  = get_theme_mod( 'barpro_hero_video_mp4',  '' );
        $video_webm = get_theme_mod( 'barpro_hero_video_webm', '' );
        $poster     = get_theme_mod( 'barpro_hero_poster',     '' );
        ?>
        <?php if ( $video_mp4 || $video_webm ) : ?>
        <!-- data-src: JS загружает src только на десктопе (> 768px) -->
        <video class="hero-video"
               muted loop playsinline preload="none"
               <?php if ( $poster ) : ?>poster="<?php echo esc_url( $poster ); ?>"<?php endif; ?>
               aria-hidden="true"
               data-autoplay>
            <?php if ( $video_webm ) : ?>
                <source data-src="<?php echo esc_url( $video_webm ); ?>" type="video/webm">
            <?php endif; ?>
            <?php if ( $video_mp4 ) : ?>
                <source data-src="<?php echo esc_url( $video_mp4 ); ?>" type="video/mp4">
            <?php endif; ?>
        </video>
        <?php endif; ?>
    </div>

    <!-- Ambient glow -->
    <div class="studio-hero__glow" aria-hidden="true"></div>

    <!-- Content -->
    <div class="studio-hero__content">

        <span class="studio-hero__eyebrow">Luxury Cocktail Catering · Москва</span>

        <h1 class="studio-hero__title">
            <span class="line-1">Премиум</span>
            <span class="line-2">Бар</span>
            <span class="line-3">Для вашего события</span>
        </h1>

        <p class="studio-hero__sub">
            Создаём cinematic cocktail experiences с 2010 года.
            Studio-grade сервис, иммерсивная подача, luxury атмосфера.
        </p>

        <div class="studio-hero__actions">
            <a href="<?php echo esc_url(home_url('/calculator')); ?>"
               class="btn-magnetic">
                Рассчитать стоимость
            </a>
            <a href="<?php echo esc_url(home_url('/cocktails')); ?>"
               class="btn-pill btn-pill--ghost">
                Меню коктейлей
            </a>
        </div>

        <!-- Stats -->
        <div class="studio-hero__stats">
            <div>
                <span class="studio-hero__stat-number">50+</span>
                <span class="studio-hero__stat-label">Коктейлей</span>
            </div>
            <div>
                <span class="studio-hero__stat-number">8</span>
                <span class="studio-hero__stat-label">Барменов</span>
            </div>
            <div>
                <span class="studio-hero__stat-number">200+</span>
                <span class="studio-hero__stat-label">Мероприятий</span>
            </div>
            <div>
                <span class="studio-hero__stat-number">14</span>
                <span class="studio-hero__stat-label">Шоу-программ</span>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="studio-hero__scroll" aria-hidden="true">
        <span class="studio-hero__scroll-text">Scroll</span>
        <div class="studio-hero__scroll-line"></div>
    </div>

</section>
