<?php
/**
 * Single Case Study
 *
 * @package BarPro_Premium
 */
get_header();
$has_acf = function_exists( 'get_field' );
?>

<?php while ( have_posts() ) : the_post();
    $hero_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );

    get_template_part( 'template-parts/tz-hero', null, [
        'title'      => get_the_title(),
        'subtitle'   => '',
        'eyebrow'    => 'Кейс',
        'image_url'  => $hero_img,
        'parallax'   => true,
        'cta'        => false,
        'breadcrumbs'=> [
            [ 'label' => 'Главная',   'url' => home_url( '/' ) ],
            [ 'label' => 'Кейсы',     'url' => get_post_type_archive_link( 'case_study' ) ],
            [ 'label' => get_the_title(), 'url' => get_permalink() ],
        ],
    ] );

    $task     = $has_acf ? get_field( 'case_task' )     : '';
    $solution = $has_acf ? get_field( 'case_solution' ) : '';
    $what_done= $has_acf ? get_field( 'case_done' )     : '';
    $result   = $has_acf ? get_field( 'case_result' )   : '';
    $gallery  = $has_acf ? get_field( 'case_gallery' )  : [];
    $guests   = $has_acf ? get_field( 'case_guests' )   : '';
    $date     = $has_acf ? get_field( 'case_date' )     : '';
    ?>

    <!-- Метаданные -->
    <section class="tz-section tz-section--tight">
        <div class="tz-container">
            <div class="tz-grid tz-grid--4 tz-reveal">
                <?php if ( $date ) : ?>
                    <div class="tz-glass tz-card"><div class="tz-card__title">Дата</div><p class="tz-card__text"><?php echo esc_html( $date ); ?></p></div>
                <?php endif; ?>
                <?php if ( $guests ) : ?>
                    <div class="tz-glass tz-card"><div class="tz-card__title">Гостей</div><p class="tz-card__text"><?php echo esc_html( $guests ); ?></p></div>
                <?php endif; ?>
                <?php $terms = get_the_terms( get_the_ID(), 'event_type' ); if ( $terms && ! is_wp_error( $terms ) ) : ?>
                    <div class="tz-glass tz-card"><div class="tz-card__title">Формат</div><p class="tz-card__text"><?php echo esc_html( $terms[0]->name ); ?></p></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Задача / Решение -->
    <section class="tz-section tz-section--tight">
        <div class="tz-container">
            <div class="tz-grid tz-grid--2">
                <?php if ( $task ) : ?>
                    <div class="tz-glass tz-card tz-reveal">
                        <span class="tz-eyebrow">Задача клиента</span>
                        <div class="tz-card__text"><?php echo wp_kses_post( wpautop( $task ) ); ?></div>
                    </div>
                <?php endif; ?>
                <?php if ( $solution ) : ?>
                    <div class="tz-glass tz-card tz-reveal" data-delay="1">
                        <span class="tz-eyebrow">Решение</span>
                        <div class="tz-card__text"><?php echo wp_kses_post( wpautop( $solution ) ); ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( $what_done ) : ?>
                <div class="tz-glass tz-card tz-reveal" style="margin-top:1.4rem;">
                    <span class="tz-eyebrow">Что было реализовано</span>
                    <div class="tz-card__text"><?php echo wp_kses_post( wpautop( $what_done ) ); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Галерея -->
    <?php if ( $gallery && is_array( $gallery ) ) : ?>
        <section class="tz-section tz-section--tight">
            <div class="tz-container">
                <header class="tz-reveal" style="margin-bottom:1.5rem;">
                    <span class="tz-eyebrow">Галерея</span>
                    <h2 class="tz-h2">Как это выглядело</h2>
                </header>
                <div class="tz-masonry" data-tz-lightbox>
                    <?php foreach ( $gallery as $img ) :
                        $thumb = is_array( $img ) ? ( $img['sizes']['medium_large'] ?? $img['url'] ?? '' ) : $img;
                        $full  = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                        $alt   = is_array( $img ) ? ( $img['alt'] ?? '' ) : '';
                        ?>
                        <div>
                            <img src="<?php echo esc_url( $thumb ); ?>" data-full="<?php echo esc_url( $full ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Итоги -->
    <?php if ( $result ) : ?>
        <section class="tz-section tz-section--tight">
            <div class="tz-container">
                <div class="tz-glass tz-card tz-reveal" style="padding:2rem;">
                    <span class="tz-eyebrow">Итоги</span>
                    <div class="tz-card__text" style="font-size:1.05rem;"><?php echo wp_kses_post( wpautop( $result ) ); ?></div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Content fallback -->
    <?php if ( ! $task && ! $solution && ! $what_done && get_the_content() ) : ?>
        <section class="tz-section tz-section--tight">
            <div class="tz-container" style="max-width:760px;">
                <div class="tz-reveal"><?php the_content(); ?></div>
            </div>
        </section>
    <?php endif; ?>

    <!-- CTA -->
    <section class="tz-section">
        <div class="tz-container">
            <div class="tz-cta-banner tz-reveal">
                <h2 class="tz-h2">Хочу такое мероприятие</h2>
                <p class="tz-lead" style="margin-inline:auto;">Расскажите формат — рассчитаем стоимость и подготовим предложение.</p>
                <?php get_template_part( 'template-parts/tz-cta-row' ); ?>
            </div>
        </div>
    </section>

<?php endwhile; ?>

<?php get_template_part( 'template-parts/tz-sticky-cta' ); ?>
<?php get_footer(); ?>
