<?php
/**
 * Template Name: Бар + Кейтеринг (ТЗ v1.0)
 * URL: /bar-catering — главная коммерческая страница
 *
 * @package BarPro_Premium
 */
get_header();
$has_acf = function_exists( 'get_field' );

get_template_part( 'template-parts/tz-hero', null, [
    'title'      => 'Бар и кейтеринг под&nbsp;ключ',
    'subtitle'   => 'Одно решение вместо нескольких подрядчиков. Бар, кейтеринг, персонал, мебель и логистика — под одной командой.',
    'eyebrow'    => 'Комплексное решение',
    'image_url'  => $has_acf ? ( get_field( 'bc_hero_image' ) ?: '' ) : '',
    'parallax'   => true,
    'cta'        => true,
    'breadcrumbs'=> [
        [ 'label' => 'Главная',         'url' => home_url( '/' ) ],
        [ 'label' => 'Бар + Кейтеринг', 'url' => get_permalink() ],
    ],
] );
?>

<!-- Проблемы клиента -->
<section class="tz-section tz-section--tight">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:2rem;">
            <span class="tz-eyebrow">Когда планируете сами</span>
            <h2 class="tz-h2">Что обычно идёт не так</h2>
        </header>

        <div class="tz-grid tz-grid--4">
            <?php
            $problems = [
                [ '01', 'Искать кейтеринг',     'Сравнивать меню, цены, дегустировать в нескольких компаниях.' ],
                [ '02', 'Искать бар',           'Подбирать барменов, согласовывать карту, обсуждать алкоголь.' ],
                [ '03', 'Искать персонал',      'Официанты, хостес, водители — каждый со своим графиком.' ],
                [ '04', 'Контролировать всех',  'Координация по тайм-коду, риски накладок и задержек.' ],
            ];
            foreach ( $problems as $i => $row ) : list( $num, $title, $text ) = $row; ?>
                <div class="tz-glass tz-problem tz-reveal" data-delay="<?php echo $i % 4; ?>">
                    <div class="tz-problem__num"><?php echo esc_html( $num ); ?></div>
                    <h3 class="tz-card__title" style="margin-top:.4rem;"><?php echo esc_html( $title ); ?></h3>
                    <p class="tz-card__text"><?php echo esc_html( $text ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Решение -->
<section class="tz-section tz-section--tight">
    <div class="tz-container">
        <div class="tz-glass tz-reveal" style="padding:clamp(1.5rem,4vw,3rem); text-align:center;">
            <span class="tz-eyebrow">Решение</span>
            <h2 class="tz-h2">BarPro берёт организацию на себя</h2>
            <p class="tz-lead" style="margin-inline:auto;">Один договор, один менеджер, одна команда. Вы празднуете — мы делаем работу.</p>
        </div>
    </div>
</section>

<!-- Что входит -->
<section class="tz-section tz-section--tight">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:2rem;">
            <span class="tz-eyebrow">Полный пакет</span>
            <h2 class="tz-h2">Что входит</h2>
        </header>

        <div class="tz-grid tz-grid--4">
            <?php
            $included = [
                [ '🍸', 'Бар',         'Мобильная стойка, карта коктейлей.' ],
                [ '🍽️', 'Кейтеринг',  'Меню под формат, шеф-повар на месте.' ],
                [ '🍹', 'Бармены',     'Сертифицированные, с шоу-навыками.' ],
                [ '🤵', 'Официанты',   'Опрятный дресс-код, тренинг сервиса.' ],
                [ '🍴', 'Посуда',      'Бокалы, тарелки, столовые приборы.' ],
                [ '🪑', 'Мебель',      'Столы, барные стулья, лаунж-зоны.' ],
                [ '🚚', 'Логистика',   'Доставка, монтаж, демонтаж.' ],
                [ '🛡', 'Координация', 'Менеджер на площадке по тайм-коду.' ],
            ];
            foreach ( $included as $i => $row ) : list( $icon, $title, $text ) = $row; ?>
                <div class="tz-glass tz-card tz-reveal" data-delay="<?php echo $i % 4; ?>">
                    <div style="font-size:1.8rem;"><?php echo esc_html( $icon ); ?></div>
                    <h3 class="tz-card__title"><?php echo esc_html( $title ); ?></h3>
                    <p class="tz-card__text"><?php echo esc_html( $text ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Лучшие кейсы -->
<section class="tz-section tz-section--tight">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:2rem;">
            <span class="tz-eyebrow">Кейсы</span>
            <h2 class="tz-h2">Реализованные мероприятия</h2>
        </header>

        <?php
        $cases = new WP_Query( [
            'post_type'      => 'case_study',
            'posts_per_page' => 6,
            'meta_query'     => [
                [ 'key' => 'is_featured', 'value' => '1', 'compare' => '=' ],
            ],
        ] );
        if ( ! $cases->have_posts() ) {
            wp_reset_postdata();
            $cases = new WP_Query( [ 'post_type' => 'case_study', 'posts_per_page' => 6 ] );
        }
        ?>

        <?php if ( $cases->have_posts() ) : ?>
            <div class="tz-grid tz-grid--3">
                <?php while ( $cases->have_posts() ) : $cases->the_post();
                    $terms  = wp_get_post_terms( get_the_ID(), 'event_type', [ 'fields' => 'slugs' ] );
                    $cat    = is_wp_error( $terms ) || empty( $terms ) ? 'private' : implode( ' ', $terms );
                    $guests = $has_acf ? get_field( 'case_guests' ) : '';
                    ?>
                    <a class="tz-case tz-reveal" data-cat="<?php echo esc_attr( $cat ); ?>" href="<?php the_permalink(); ?>">
                        <?php if ( has_post_thumbnail() ) :
                            the_post_thumbnail( 'case-large', [ 'loading' => 'lazy', 'decoding' => 'async' ] );
                        else : ?>
                            <div style="position:absolute; inset:0; background:linear-gradient(135deg,#1F2937,#0F172A);"></div>
                        <?php endif; ?>
                        <div class="tz-case__body">
                            <span class="tz-case__type"><?php echo esc_html( $cat ); ?></span>
                            <h3 class="tz-case__title"><?php the_title(); ?></h3>
                            <?php if ( $guests ) : ?><p class="tz-case__meta">👥 <?php echo esc_html( $guests ); ?> гостей</p><?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <div style="text-align:center; margin-top:2rem;">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>" class="tz-btn tz-btn--ghost">Все кейсы →</a>
            </div>
        <?php else : ?>
            <p class="tz-lead" style="text-align:center;">Кейсы появятся, как только вы добавите их через панель администратора → <em>Кейсы</em>.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Калькулятор (CTA-блок) -->
<section class="tz-section">
    <div class="tz-container">
        <div class="tz-cta-banner tz-reveal">
            <span class="tz-eyebrow">Калькулятор</span>
            <h2 class="tz-h2">Рассчитайте комплексное предложение</h2>
            <p class="tz-lead" style="margin-inline:auto;">Один клик — и вы знаете диапазон стоимости.</p>
            <?php get_template_part( 'template-parts/tz-cta-row' ); ?>
        </div>
    </div>
</section>

<!-- Финальный CTA -->
<section class="tz-section tz-section--tight">
    <div class="tz-container" style="max-width:760px;">
        <div class="tz-reveal" style="text-align:center;">
            <h2 class="tz-h2">Получить комплексное предложение</h2>
            <p class="tz-lead" style="margin-inline:auto;">Менеджер свяжется в течение 15 минут.</p>
            <?php get_template_part( 'template-parts/tz-cta-row' ); ?>
        </div>
    </div>
</section>

<?php get_template_part( 'template-parts/tz-sticky-cta' ); ?>
<?php get_footer(); ?>
