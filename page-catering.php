<?php
/**
 * Template Name: Кейтеринг (ТЗ v1.0)
 * URL: /catering
 *
 * @package BarPro_Premium
 */
get_header();
$has_acf = function_exists( 'get_field' );

get_template_part( 'template-parts/tz-hero', null, [
    'title'      => 'Кейтеринг под ключ в Москве и области',
    'subtitle'   => 'Меню, персонал, доставка, сервировка и уборка — забота 360°.',
    'eyebrow'    => 'Кейтеринг',
    'image_url'  => $has_acf ? ( get_field( 'catering_hero_image' ) ?: '' ) : '',
    'parallax'   => true,
    'cta'        => true,
    'breadcrumbs'=> [
        [ 'label' => 'Главная',  'url' => home_url( '/' ) ],
        [ 'label' => 'Кейтеринг','url' => get_permalink() ],
    ],
] );
?>

<!-- Что входит -->
<section class="tz-section tz-section--tight">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:2rem;">
            <span class="tz-eyebrow">Состав услуги</span>
            <h2 class="tz-h2">Что входит в кейтеринг</h2>
        </header>

        <div class="tz-grid tz-grid--4">
            <?php
            $includes = [
                [ '🍽️', 'Меню',       'Авторские позиции, сезонные блюда, спецдиеты.' ],
                [ '🤵', 'Персонал',   'Официанты, повара, метрдотель.' ],
                [ '🚚', 'Доставка',   'По Москве и области, точно по тайм-коду.' ],
                [ '🍴', 'Сервировка', 'Скатерти, посуда, столовое серебро, декор.' ],
                [ '🧹', 'Уборка',     'Снимаем зону по факту мероприятия.' ],
            ];
            foreach ( $includes as $i => $row ) : list( $icon, $title, $text ) = $row; ?>
                <div class="tz-glass tz-card tz-reveal" data-delay="<?php echo $i % 4; ?>">
                    <div style="font-size:2rem;"><?php echo esc_html( $icon ); ?></div>
                    <h3 class="tz-card__title"><?php echo esc_html( $title ); ?></h3>
                    <p class="tz-card__text"><?php echo esc_html( $text ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Форматы -->
<section class="tz-section tz-section--tight">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:2rem;">
            <span class="tz-eyebrow">Форматы</span>
            <h2 class="tz-h2">Под любую задачу</h2>
        </header>

        <div class="tz-grid tz-grid--4">
            <?php
            $formats = [
                [ '🥂', 'Банкет',      'Полноценный стол с переменами блюд.' ],
                [ '🍤', 'Фуршет',      'Канапе, тарталетки, лёгкие закуски.' ],
                [ '☕', 'Кофе-брейк', 'Для конференций и тренингов.' ],
                [ '🔥', 'BBQ',          'Гриль и мангал на открытом воздухе.' ],
            ];
            foreach ( $formats as $i => $row ) : list( $icon, $title, $text ) = $row; ?>
                <div class="tz-glass tz-card tz-reveal" data-delay="<?php echo $i % 4; ?>">
                    <div style="font-size:2rem;"><?php echo esc_html( $icon ); ?></div>
                    <h3 class="tz-card__title"><?php echo esc_html( $title ); ?></h3>
                    <p class="tz-card__text"><?php echo esc_html( $text ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Пакеты -->
<section class="tz-section">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:2rem;">
            <span class="tz-eyebrow">Пакеты</span>
            <h2 class="tz-h2">Готовые решения «под ключ»</h2>
        </header>

        <div class="tz-grid tz-grid--3">
            <?php
            $packs = [
                [
                    'name' => 'Start',
                    'sub'  => 'до 30 гостей',
                    'price'=> 'от 1 800 ₽ / гость',
                    'items'=> [ '5 позиций меню', 'Кофе/чай/вода', '1 официант', 'Базовая сервировка', 'Доставка по Москве' ],
                ],
                [
                    'name' => 'Standard',
                    'sub'  => 'до 100 гостей',
                    'price'=> 'от 2 500 ₽ / гость',
                    'items'=> [ '10 позиций меню', 'Холодные и горячие закуски', '3 официанта', 'Полная сервировка', 'Доставка + сборка', 'Уборка зоны' ],
                    'featured' => true,
                ],
                [
                    'name' => 'Premium',
                    'sub'  => '100+ гостей',
                    'price'=> 'от 3 800 ₽ / гость',
                    'items'=> [ '15+ позиций авторского меню', 'Шеф-повар на месте', 'Метрдотель + бригада', 'Luxury-сервировка', 'Гастростанции', 'Координатор события' ],
                ],
            ];
            foreach ( $packs as $p ) : ?>
                <article class="tz-pack <?php echo ! empty( $p['featured'] ) ? 'tz-pack--featured' : ''; ?> tz-reveal">
                    <h3 class="tz-pack__name"><?php echo esc_html( $p['name'] ); ?></h3>
                    <p class="tz-pack__sub"><?php echo esc_html( $p['sub'] ); ?></p>
                    <div class="tz-pack__price"><?php echo esc_html( $p['price'] ); ?></div>
                    <ul>
                        <?php foreach ( $p['items'] as $it ) : ?>
                            <li><?php echo esc_html( $it ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo esc_url( home_url( '/calculator' ) ); ?>" class="tz-btn tz-btn--primary" style="width:100%;">Заказать <?php echo esc_html( $p['name'] ); ?></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Галерея блюд (Masonry + Lightbox) -->
<section class="tz-section tz-section--tight">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:1.5rem;">
            <span class="tz-eyebrow">Галерея блюд</span>
            <h2 class="tz-h2">Кухня, которой не стыдно показывать</h2>
        </header>

        <?php
        $gallery = $has_acf ? get_field( 'catering_gallery' ) : [];
        if ( $gallery && is_array( $gallery ) ) : ?>
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
        <?php else : ?>
            <p class="tz-lead" style="text-align:center;">Добавьте фото блюд в поле <em>catering_gallery</em> (ACF) — они появятся masonry-сеткой с lightbox.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Встроенный калькулятор -->
<section class="tz-section">
    <div class="tz-container">
        <header class="tz-reveal" style="text-align:center; margin-bottom:1.5rem;">
            <span class="tz-eyebrow">Калькулятор</span>
            <h2 class="tz-h2">Узнайте стоимость за минуту</h2>
            <p class="tz-lead" style="margin-inline:auto;">Полная форма открывается на отдельной странице.</p>
        </header>

        <div class="tz-cta-banner tz-reveal" style="max-width:760px; margin-inline:auto;">
            <?php get_template_part( 'template-parts/tz-cta-row' ); ?>
        </div>
    </div>
</section>

<?php get_template_part( 'template-parts/tz-sticky-cta' ); ?>
<?php get_footer(); ?>
