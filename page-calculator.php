<?php
/**
 * Template Name: Калькулятор (TЗ v1.0)
 * Описание: 5-шаговый калькулятор по ТЗ. Полностью редактируем из ACF.
 *
 * @package BarPro_Premium
 */
get_header();

$has_acf = function_exists( 'get_field' );

$hero_title    = $has_acf ? ( get_field( 'calc_hero_title' )    ?: '' ) : '';
$hero_subtitle = $has_acf ? ( get_field( 'calc_hero_subtitle' ) ?: '' ) : '';
$hero_video    = $has_acf ? ( get_field( 'calc_hero_video' )    ?: '' ) : '';
$hero_image    = $has_acf ? ( get_field( 'calc_hero_image' )    ?: '' ) : '';

if ( ! $hero_title )    $hero_title    = 'Рассчитайте стоимость мероприятия за&nbsp;1&nbsp;минуту';
if ( ! $hero_subtitle ) $hero_subtitle = 'Выездной бар, кейтеринг и&nbsp;персонал под ключ.';

get_template_part( 'template-parts/tz-hero', null, [
    'title'      => $hero_title,
    'subtitle'   => $hero_subtitle,
    'eyebrow'    => 'Калькулятор',
    'video_url'  => $hero_video,
    'image_url'  => $hero_image,
    'parallax'   => true,
    'cta'        => false,
    'breadcrumbs'=> [
        [ 'label' => 'Главная',     'url' => home_url( '/' ) ],
        [ 'label' => 'Калькулятор', 'url' => get_permalink() ],
    ],
] );
?>

<section class="tz-section">
    <div class="tz-container">

        <div id="tzCalc" class="tz-calc tz-reveal" aria-label="Калькулятор стоимости мероприятия">

            <!-- Progress -->
            <div class="tz-calc__progress" aria-hidden="true">
                <div class="tz-calc__dot is-active"></div>
                <div class="tz-calc__dot"></div>
                <div class="tz-calc__dot"></div>
                <div class="tz-calc__dot"></div>
                <div class="tz-calc__dot"></div>
            </div>

            <!-- Шаг 1: тип мероприятия -->
            <div class="tz-calc__step is-active" data-step="1">
                <span class="tz-eyebrow">Шаг 1 из 5</span>
                <h2 class="tz-h2">Тип мероприятия</h2>
                <p class="tz-lead">Выберите формат — мы сразу учтём специфику.</p>

                <div class="tz-choices" role="radiogroup" aria-label="Тип мероприятия">
                    <?php
                    $event_types = [
                        'wedding'   => [ '💍', 'Свадьба' ],
                        'corporate' => [ '🏢', 'Корпоратив' ],
                        'birthday'  => [ '🎂', 'День рождения' ],
                        'festival'  => [ '🎪', 'Фестиваль' ],
                        'private'   => [ '🥂', 'Частное мероприятие' ],
                    ];
                    foreach ( $event_types as $val => $row ) :
                        list( $icon, $label ) = $row; ?>
                        <label class="tz-choice">
                            <input type="radio" name="eventType" value="<?php echo esc_attr( $val ); ?>">
                            <div style="font-size:1.6rem; margin-bottom:.3rem;"><?php echo esc_html( $icon ); ?></div>
                            <div><?php echo esc_html( $label ); ?></div>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="tz-calc__nav">
                    <span></span>
                    <button type="button" class="tz-btn tz-btn--primary" data-tz-next>Далее →</button>
                </div>
            </div>

            <!-- Шаг 2: количество гостей -->
            <div class="tz-calc__step" data-step="2">
                <span class="tz-eyebrow">Шаг 2 из 5</span>
                <h2 class="tz-h2">Количество гостей</h2>
                <p class="tz-lead">Диапазон от 1 до 500 человек.</p>

                <label for="tzGuests" class="tz-calc__label">Гостей: <span id="tzGuestsOut">50</span></label>
                <input type="range" id="tzGuests" class="tz-range" min="10" max="300" value="50" aria-label="Количество гостей">

                <div class="tz-calc__nav">
                    <button type="button" class="tz-btn tz-btn--ghost" data-tz-prev>← Назад</button>
                    <button type="button" class="tz-btn tz-btn--primary" data-tz-next>Далее →</button>
                </div>
            </div>

            <!-- Шаг 3: основные услуги -->
            <div class="tz-calc__step" data-step="3">
                <span class="tz-eyebrow">Шаг 3 из 5</span>
                <h2 class="tz-h2">Выбор услуг</h2>
                <p class="tz-lead">Можно выбрать несколько вариантов.</p>

                <div class="tz-choices">
                    <?php
                    $services = [
                        'bar'          => [ '🍸', 'Бар' ],
                        'catering'     => [ '🍽️', 'Кейтеринг' ],
                        'bar-catering' => [ '🥂', 'Бар + Кейтеринг' ],
                        'barmen'       => [ '👨‍🍳', 'Бармены' ],
                        'waiters'      => [ '🤵', 'Официанты' ],
                    ];
                    foreach ( $services as $val => $row ) :
                        list( $icon, $label ) = $row; ?>
                        <label class="tz-choice">
                            <input type="checkbox" name="mainService" value="<?php echo esc_attr( $val ); ?>">
                            <div style="font-size:1.6rem; margin-bottom:.3rem;"><?php echo esc_html( $icon ); ?></div>
                            <div><?php echo esc_html( $label ); ?></div>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="tz-calc__nav">
                    <button type="button" class="tz-btn tz-btn--ghost" data-tz-prev>← Назад</button>
                    <button type="button" class="tz-btn tz-btn--primary" data-tz-next>Далее →</button>
                </div>
            </div>

            <!-- Шаг 4: доп. услуги -->
            <div class="tz-calc__step" data-step="4">
                <span class="tz-eyebrow">Шаг 4 из 5</span>
                <h2 class="tz-h2">Дополнительные услуги</h2>
                <p class="tz-lead">Опционально — добавит атмосферу и комфорт.</p>

                <div class="tz-choices">
                    <?php
                    $extras = [
                        'ice'       => [ '🧊', 'Лёд' ],
                        'dishes'    => [ '🍴', 'Посуда' ],
                        'furniture' => [ '🪑', 'Мебель' ],
                        'bar-show'  => [ '🎭', 'Бармен-шоу' ],
                        'coffee'    => [ '☕', 'Кофе-брейк' ],
                    ];
                    foreach ( $extras as $val => $row ) :
                        list( $icon, $label ) = $row; ?>
                        <label class="tz-choice">
                            <input type="checkbox" name="extra" value="<?php echo esc_attr( $val ); ?>">
                            <div style="font-size:1.6rem; margin-bottom:.3rem;"><?php echo esc_html( $icon ); ?></div>
                            <div><?php echo esc_html( $label ); ?></div>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="tz-calc__nav">
                    <button type="button" class="tz-btn tz-btn--ghost" data-tz-prev>← Назад</button>
                    <button type="button" class="tz-btn tz-btn--primary" data-tz-next>Далее →</button>
                </div>
            </div>

            <!-- Шаг 5: контакты -->
            <div class="tz-calc__step" data-step="5">
                <span class="tz-eyebrow">Шаг 5 из 5</span>
                <h2 class="tz-h2">Ваши контакты</h2>
                <p class="tz-lead">Менеджер свяжется в течение 15&nbsp;минут.</p>

                <?php get_template_part( 'template-parts/honeypot' ); ?>

                <div style="display:grid; gap:.8rem; max-width:520px;">
                    <label for="tzName" class="tz-calc__label">Имя*</label>
                    <input type="text" id="tzName" class="tz-input" required autocomplete="name">

                    <label for="tzPhone" class="tz-calc__label">Телефон*</label>
                    <input type="tel" id="tzPhone" class="tz-input" required autocomplete="tel" placeholder="+7 (___) ___-__-__">

                    <label for="tzEmail" class="tz-calc__label">Email</label>
                    <input type="email" id="tzEmail" class="tz-input" autocomplete="email" placeholder="your@email.com">

                    <label for="tzTg" class="tz-calc__label">Telegram</label>
                    <input type="text" id="tzTg" class="tz-input" placeholder="@username">

                    <label for="tzComment" class="tz-calc__label">Комментарий</label>
                    <textarea id="tzComment" class="tz-textarea" rows="3" placeholder="Дата, площадка, пожелания"></textarea>
                </div>

                <div class="tz-calc__nav">
                    <button type="button" class="tz-btn tz-btn--ghost" data-tz-prev>← Назад</button>
                    <button type="button" class="tz-btn tz-btn--primary" data-tz-next>Получить расчёт →</button>
                </div>
            </div>

            <!-- Result -->
            <div class="tz-calc__step" data-step="6" data-result>
                <span class="tz-eyebrow">Готово</span>
                <h2 class="tz-h2">Ориентировочная стоимость</h2>

                <div class="tz-calc__result">
                    <p class="tz-lead" style="margin-inline:auto;">Точный расчёт мы пришлём в течение 15&nbsp;минут.</p>
                    <div class="tz-calc__price-range" id="tzResultRange">от 35 000 ₽ до 85 000 ₽</div>

                    <ul id="tzResultSummary" style="display:inline-block; text-align:left; list-style:none; padding:0; margin:1rem 0; color:var(--tz-text-muted);"></ul>

                    <?php get_template_part( 'template-parts/tz-cta-row', null, [ 'variant' => 'result' ] ); ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_template_part( 'template-parts/tz-sticky-cta' ); ?>
<?php get_footer(); ?>
