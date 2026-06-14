<?php
/**
 * Archive: case_study (TЗ /cases)
 * URL: /cases
 *
 * @package BarPro_Premium
 */
get_header();
$has_acf = function_exists( 'get_field' );

get_template_part( 'template-parts/tz-hero', null, [
    'title'      => 'Реализованные мероприятия',
    'subtitle'   => 'Свадьбы, корпоративы, фестивали — выбирайте формат и смотрите подробности.',
    'eyebrow'    => 'Портфолио',
    'parallax'   => true,
    'cta'        => false,
    'breadcrumbs'=> [
        [ 'label' => 'Главная', 'url' => home_url( '/' ) ],
        [ 'label' => 'Кейсы',   'url' => get_post_type_archive_link( 'case_study' ) ],
    ],
] );
?>

<section class="tz-section">
    <div class="tz-container">

        <!-- Фильтр -->
        <div class="tz-filter tz-reveal" data-tz-filter="#casesGrid">
            <button class="tz-filter__btn is-active" data-cat="all">Все</button>
            <button class="tz-filter__btn" data-cat="weddings">Свадьбы</button>
            <button class="tz-filter__btn" data-cat="corporate">Корпоративы</button>
            <button class="tz-filter__btn" data-cat="birthdays">Дни рождения</button>
            <button class="tz-filter__btn" data-cat="festivals">Фестивали</button>
        </div>

        <div id="casesGrid" class="tz-grid tz-grid--3">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
                $terms = wp_get_post_terms( get_the_ID(), 'event_type', [ 'fields' => 'slugs' ] );
                $cat   = is_wp_error( $terms ) || empty( $terms ) ? 'private' : implode( ' ', $terms );

                $guests = $has_acf ? get_field( 'case_guests' ) : '';
                $date   = $has_acf ? get_field( 'case_date' )   : '';
                ?>
                <a class="tz-case tz-reveal" data-cat="<?php echo esc_attr( $cat ); ?>" href="<?php the_permalink(); ?>">
                    <?php if ( has_post_thumbnail() ) :
                        the_post_thumbnail( 'case-large', [ 'loading' => 'lazy', 'decoding' => 'async' ] );
                    else : ?>
                        <div style="position:absolute; inset:0; background:linear-gradient(135deg, #1F2937, #0F172A);"></div>
                    <?php endif; ?>

                    <div class="tz-case__body">
                        <span class="tz-case__type"><?php echo esc_html( $cat ); ?></span>
                        <h3 class="tz-case__title"><?php the_title(); ?></h3>
                        <p class="tz-case__meta">
                            <?php if ( $guests ) : ?>👥 <?php echo esc_html( $guests ); ?> гостей<?php endif; ?>
                            <?php if ( $guests && $date ) echo ' · '; ?>
                            <?php if ( $date )   : ?>📅 <?php echo esc_html( $date ); ?><?php endif; ?>
                        </p>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); else : ?>
                <p class="tz-lead">Кейсы скоро появятся</p>
            <?php endif; ?>
        </div>

        <div style="margin-top:2.5rem; text-align:center;">
            <?php the_posts_pagination( [
                'mid_size' => 1,
                'prev_text' => '←',
                'next_text' => '→',
            ] ); ?>
        </div>
    </div>
</section>

<section class="tz-section">
    <div class="tz-container">
        <div class="tz-cta-banner tz-reveal">
            <h2 class="tz-h2">Хочу такое мероприятие</h2>
            <p class="tz-lead" style="margin-inline:auto;">Расскажите формат — мы соберём похожее под ваш бюджет.</p>
            <?php get_template_part( 'template-parts/tz-cta-row' ); ?>
        </div>
    </div>
</section>

<?php get_template_part( 'template-parts/tz-sticky-cta' ); ?>
<?php get_footer(); ?>
