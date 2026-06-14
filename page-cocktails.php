<?php
/**
 * Template Name: Коктейли (ТЗ v1.0)
 * URL: /cocktails
 *
 * @package BarPro_Premium
 */
get_header();

$has_acf = function_exists( 'get_field' );

get_template_part( 'template-parts/tz-hero', null, [
    'title'      => 'Коктейльная карта BarPro',
    'subtitle'   => 'Авторские, классические, безалкогольные — собираем карту под формат вашего события.',
    'eyebrow'    => 'Меню',
    'image_url'  => $has_acf ? ( get_field( 'cocktails_hero_image' ) ?: '' ) : '',
    'parallax'   => true,
    'cta'        => false,
    'breadcrumbs'=> [
        [ 'label' => 'Главная',   'url' => home_url( '/' ) ],
        [ 'label' => 'Коктейли',  'url' => get_permalink() ],
    ],
] );
?>

<!-- Конструктор коктейльной карты -->
<section class="tz-section tz-section--tight" id="cc">
    <div class="tz-container">
        <div class="tz-glass tz-reveal" style="padding:1.8rem; border-radius:20px;">
            <span class="tz-eyebrow">Конструктор карты</span>
            <h2 class="tz-h2" style="margin-bottom:1rem;">Подберём коктейли под ваше событие</h2>

            <div id="tzCocktailConstructor">
                <div style="display:grid; gap:1.2rem; grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">

                    <div>
                        <div class="tz-calc__label">Тип мероприятия</div>
                        <div class="tz-choices">
                            <?php
                            $cc_types = [
                                'wedding'   => 'Свадьба',
                                'corporate' => 'Корпоратив',
                                'birthday'  => 'День рождения',
                                'festival'  => 'Фестиваль',
                                'private'   => 'Частное'
                            ];
                            $first = true;
                            foreach ( $cc_types as $v => $label ) : ?>
                                <label class="tz-choice <?php echo $first ? 'is-selected' : ''; ?>">
                                    <input type="radio" name="ccType" value="<?php echo esc_attr( $v ); ?>" <?php checked( $first ); ?>>
                                    <?php echo esc_html( $label ); ?>
                                </label>
                            <?php $first = false; endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label for="ccGuests" class="tz-calc__label">Количество гостей</label>
                        <input type="number" id="ccGuests" class="tz-input" min="1" max="500" value="50">
                    </div>

                    <div>
                        <div class="tz-calc__label">Формат бара</div>
                        <div class="tz-choices">
                            <label class="tz-choice is-selected">
                                <input type="radio" name="ccFormat" value="bar" checked>
                                Барная стойка
                            </label>
                            <label class="tz-choice">
                                <input type="radio" name="ccFormat" value="welcome">
                                Welcome-зона
                            </label>
                            <label class="tz-choice">
                                <input type="radio" name="ccFormat" value="cabin">
                                Кабинетный
                            </label>
                        </div>
                    </div>
                </div>

                <div style="margin-top:1.4rem; display:flex; gap:.6rem; flex-wrap:wrap;">
                    <button type="button" class="tz-btn tz-btn--primary" id="tzCocktailGo">Подобрать коктейли</button>
                </div>

                <div id="ccResult" class="tz-lead" style="margin-top:1.2rem;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Фильтр -->
<section class="tz-section tz-section--tight">
    <div class="tz-container">
        <div class="tz-filter tz-reveal" role="tablist" data-tz-filter="#cocktailsGrid">
            <button class="tz-filter__btn is-active" data-cat="all">Все</button>
            <button class="tz-filter__btn" data-cat="alcohol">Алкогольные</button>
            <button class="tz-filter__btn" data-cat="non-alcoholic">Безалкогольные</button>
            <button class="tz-filter__btn" data-cat="signature">Авторские</button>
            <button class="tz-filter__btn" data-cat="classic">Классические</button>
        </div>

        <?php
        $cocktails = new WP_Query( [
            'post_type'      => 'cocktail',
            'posts_per_page' => 60,
            'orderby'        => 'menu_order date',
            'order'          => 'ASC',
        ] );
        ?>

        <div id="cocktailsGrid" class="tz-grid tz-grid--4">
            <?php if ( $cocktails->have_posts() ) : ?>
                <?php while ( $cocktails->have_posts() ) : $cocktails->the_post();
                    // Категория из таксономии или ACF
                    $cats = wp_get_post_terms( get_the_ID(), 'cocktail_type', [ 'fields' => 'slugs' ] );
                    if ( is_wp_error( $cats ) || empty( $cats ) ) {
                        $cats = $has_acf ? ( get_field( 'cocktail_categories' ) ?: [ 'classic' ] ) : [ 'classic' ];
                    }
                    $cat_str = is_array( $cats ) ? implode( ' ', $cats ) : (string) $cats;

                    $ingredients = $has_acf ? get_field( 'ingredients' ) : '';
                    ?>
                    <article class="tz-glass tz-cocktail tz-reveal" data-cat="<?php echo esc_attr( $cat_str ); ?>">
                        <div class="tz-cocktail__img">
                            <?php if ( has_post_thumbnail() ) :
                                the_post_thumbnail( 'cocktail-thumb', [ 'loading' => 'lazy', 'decoding' => 'async' ] );
                            else : ?>
                                <div class="placeholder" style="display:flex;align-items:center;justify-content:center;color:#666;">🍸</div>
                            <?php endif; ?>
                        </div>
                        <div class="tz-cocktail__body">
                            <span class="tz-cocktail__cat"><?php echo esc_html( $cat_str ); ?></span>
                            <h3 class="tz-cocktail__name"><?php the_title(); ?></h3>
                            <?php if ( has_excerpt() || get_the_content() ) : ?>
                                <p class="tz-cocktail__desc"><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: get_the_content(), 18 ) ); ?></p>
                            <?php endif; ?>
                            <?php if ( $ingredients ) : ?>
                                <p class="tz-cocktail__ingredients"><strong>Состав:</strong> <?php echo esc_html( $ingredients ); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else :
                // Демо-карточки, чтобы блок не был пустым до наполнения CPT
                $demo = [
                    [ 'name' => 'Aperol Spritz', 'cat' => 'classic alcohol',      'desc' => 'Лёгкий итальянский аперитив с просекко.', 'ing' => 'Aperol, Prosecco, содовая, апельсин' ],
                    [ 'name' => 'Moscow Mule',   'cat' => 'classic alcohol',      'desc' => 'Освежающий коктейль с водкой и имбирём.', 'ing' => 'Водка, имбирный эль, лайм' ],
                    [ 'name' => 'Virgin Mojito', 'cat' => 'non-alcoholic',         'desc' => 'Безалкогольный мохито с мятой.', 'ing' => 'Мята, лайм, содовая, тростниковый сахар' ],
                    [ 'name' => 'BarPro Signature', 'cat' => 'signature alcohol', 'desc' => 'Авторский коктейль шеф-бармена.', 'ing' => 'Джин, биттер, домашний сироп' ],
                ];
                foreach ( $demo as $d ) : ?>
                    <article class="tz-glass tz-cocktail tz-reveal" data-cat="<?php echo esc_attr( $d['cat'] ); ?>">
                        <div class="tz-cocktail__img"><div class="placeholder" style="display:flex;align-items:center;justify-content:center;color:#666;">🍸</div></div>
                        <div class="tz-cocktail__body">
                            <span class="tz-cocktail__cat"><?php echo esc_html( $d['cat'] ); ?></span>
                            <h3 class="tz-cocktail__name"><?php echo esc_html( $d['name'] ); ?></h3>
                            <p class="tz-cocktail__desc"><?php echo esc_html( $d['desc'] ); ?></p>
                            <p class="tz-cocktail__ingredients"><strong>Состав:</strong> <?php echo esc_html( $d['ing'] ); ?></p>
                        </div>
                    </article>
                <?php endforeach;
            endif; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="tz-section">
    <div class="tz-container">
        <div class="tz-cta-banner tz-reveal">
            <h2 class="tz-h2">Собрать карту под ваше мероприятие?</h2>
            <p class="tz-lead" style="margin-inline:auto;">Подскажем форматы, объём и стоимость — рассчитайте за минуту.</p>
            <?php get_template_part( 'template-parts/tz-cta-row' ); ?>
        </div>
    </div>
</section>

<?php get_template_part( 'template-parts/tz-sticky-cta' ); ?>
<?php get_footer(); ?>
