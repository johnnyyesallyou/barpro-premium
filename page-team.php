<?php
/**
 * Template Name: Команда (TЗ v1.0)
 * URL: /team
 *
 * @package BarPro_Premium
 */
get_header();

$has_acf = function_exists( 'get_field' );

// Контент основателя через ACF
$founder_name    = $has_acf ? get_field( 'founder_name' )    : '';
$founder_role    = $has_acf ? get_field( 'founder_role' )    : '';
$founder_photo   = $has_acf ? get_field( 'founder_photo' )   : '';
$founder_story   = $has_acf ? get_field( 'founder_story' )   : '';
$founder_mission = $has_acf ? get_field( 'founder_mission' ) : '';
$founder_philo   = $has_acf ? get_field( 'founder_philosophy' ) : '';

// Преимущества
$benefits_default = [
    [ 'icon' => '📄', 'title' => 'Работаем по договору',    'text' => 'Прозрачные условия, фиксированные цены, юр. защита заказчика.' ],
    [ 'icon' => '🛡', 'title' => 'Проверенный персонал',    'text' => 'Все сотрудники проходят отбор и обучение. Медкнижки, дресс-код.' ],
    [ 'icon' => '🎯', 'title' => 'Гибкие решения',          'text' => 'Подстраиваем меню, формат и команду под ваш бюджет и площадку.' ],
    [ 'icon' => '⚡', 'title' => 'Срочный выезд',           'text' => 'Готовы выйти на смену в течение 24 часов. Резервные команды.' ],
];

get_template_part( 'template-parts/tz-hero', null, [
    'title'      => 'Команда BarPro',
    'subtitle'   => 'Люди, которые делают мероприятия без лишних хлопот.',
    'eyebrow'    => 'Доверие',
    'image_url'  => $has_acf ? ( get_field( 'team_hero_image' ) ?: '' ) : '',
    'parallax'   => true,
    'cta'        => true,
    'breadcrumbs'=> [
        [ 'label' => 'Главная',  'url' => home_url( '/' ) ],
        [ 'label' => 'Команда',  'url' => get_permalink() ],
    ],
] );
?>

<!-- Блок основателя -->
<section class="tz-section" id="founder">
    <div class="tz-container">
        <div class="tz-founder tz-reveal">
            <div class="tz-founder__photo">
                <?php if ( $founder_photo ) : ?>
                    <img src="<?php echo esc_url( is_array( $founder_photo ) ? $founder_photo['url'] : $founder_photo ); ?>"
                         alt="<?php echo esc_attr( $founder_name ?: 'Основатель BarPro' ); ?>"
                         loading="lazy" decoding="async">
                <?php else : ?>
                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:#666;">
                        Фото основателя
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <span class="tz-eyebrow">Основатель</span>
                <h2 class="tz-h2"><?php echo esc_html( $founder_name ?: 'История BarPro' ); ?></h2>
                <?php if ( $founder_role ) : ?>
                    <p style="color:var(--tz-gold); margin-bottom:1rem;"><?php echo esc_html( $founder_role ); ?></p>
                <?php endif; ?>

                <?php if ( $founder_story ) : ?>
                    <div class="tz-lead" style="margin-bottom:1.4rem;"><?php echo wp_kses_post( wpautop( $founder_story ) ); ?></div>
                <?php else : ?>
                    <p class="tz-lead">BarPro появился из желания сделать мероприятия в Москве по-настоящему стильными. За плечами команды — сотни свадеб, корпоративов и фестивалей. Мы знаем, как организовать вечер, который запомнится надолго.</p>
                <?php endif; ?>

                <div class="tz-grid tz-grid--2">
                    <div class="tz-glass" style="padding:1.2rem;">
                        <h3 class="tz-h3" style="color:var(--tz-gold);">Миссия</h3>
                        <p class="tz-card__text"><?php echo esc_html( $founder_mission ?: 'Снимать стресс организации мероприятий с заказчика и превращать праздник в удовольствие — для всех.' ); ?></p>
                    </div>
                    <div class="tz-glass" style="padding:1.2rem;">
                        <h3 class="tz-h3" style="color:var(--tz-gold);">Философия</h3>
                        <p class="tz-card__text"><?php echo esc_html( $founder_philo ?: 'Премиум-сервис в каждой детали. Гость не должен ждать, а заказчик — переживать.' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Карточки сотрудников -->
<section class="tz-section tz-section--tight" id="team">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:2rem;">
            <span class="tz-eyebrow">Сотрудники</span>
            <h2 class="tz-h2">Профессионалы своего дела</h2>
        </header>

        <?php
        $team = new WP_Query( [
            'post_type'      => 'team_member',
            'posts_per_page' => 24,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        if ( $team->have_posts() ) : ?>
            <div class="tz-grid tz-grid--4">
                <?php while ( $team->have_posts() ) : $team->the_post();
                    $role     = $has_acf ? get_field( 'role' )     : '';
                    $exp      = $has_acf ? get_field( 'experience' ) : '';
                    $spec     = $has_acf ? get_field( 'specialization' ) : '';
                    ?>
                    <article class="tz-glass tz-team-card tz-reveal">
                        <div class="tz-team-card__photo">
                            <?php if ( has_post_thumbnail() ) :
                                the_post_thumbnail( 'team-thumb', [ 'loading' => 'lazy', 'decoding' => 'async' ] );
                            else : ?>
                                <div style="height:100%; display:flex; align-items:center; justify-content:center; color:#666;">Фото</div>
                            <?php endif; ?>
                        </div>
                        <div class="tz-team-card__body">
                            <h3 class="tz-team-card__name"><?php the_title(); ?></h3>
                            <?php if ( $role ) : ?>
                                <div class="tz-team-card__role"><?php echo esc_html( $role ); ?></div>
                            <?php endif; ?>
                            <?php if ( $exp || $spec ) : ?>
                                <p class="tz-team-card__exp">
                                    <?php if ( $exp )  echo esc_html( $exp );  ?>
                                    <?php if ( $exp && $spec ) echo ' · '; ?>
                                    <?php if ( $spec ) echo esc_html( $spec ); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="tz-lead" style="text-align:center;">Сотрудники появятся здесь, как только вы добавите их через панель администратора → <em>Команда</em>.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Преимущества -->
<section class="tz-section">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:2rem;">
            <span class="tz-eyebrow">Почему нам доверяют</span>
            <h2 class="tz-h2">Преимущества работы с BarPro</h2>
        </header>

        <div class="tz-grid tz-grid--4">
            <?php foreach ( $benefits_default as $i => $b ) : ?>
                <div class="tz-glass tz-card tz-reveal" data-delay="<?php echo (int) ( $i % 4 ); ?>">
                    <div style="font-size:2rem;"><?php echo esc_html( $b['icon'] ); ?></div>
                    <h3 class="tz-card__title"><?php echo esc_html( $b['title'] ); ?></h3>
                    <p class="tz-card__text"><?php echo esc_html( $b['text'] ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center; margin-top:2.5rem;">
            <?php get_template_part( 'template-parts/tz-cta-row' ); ?>
        </div>
    </div>
</section>

<?php get_template_part( 'template-parts/tz-sticky-cta' ); ?>
<?php get_footer(); ?>
