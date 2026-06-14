<?php
/**
 * BarPro — Event Premium (TЗ v1.0) bootstrap
 *
 * Подключает дополнительные стили/скрипты по ТЗ, SEO, ACF-поля.
 *
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BARPRO_TZ_VERSION', '1.1.0' );

/**
 * Подключить TZ CSS/JS поверх существующих ассетов
 */
function barpro_tz_enqueue() {
    wp_enqueue_style(
        'barpro-tz',
        BARPRO_THEME_URI . '/assets/css/tz.css',
        [ 'barpro-style' ],
        BARPRO_TZ_VERSION
    );

    wp_enqueue_script(
        'barpro-tz',
        BARPRO_THEME_URI . '/assets/js/tz.js',
        [], // Fix 6: jQuery не используется — убрана зависимость
        BARPRO_TZ_VERSION,
        true
    );

    // Мёрджим данные калькулятора в уже существующий объект barproAjax (C5 fix).
    // wp_localize_script перезаписывает весь объект — вместо этого используем
    // wp_add_inline_script чтобы безопасно добавить только недостающие поля.
    if ( function_exists( 'barpro_price_config' ) ) {
        $pricing = barpro_price_config();
        $tz_data = wp_json_encode( [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'barpro_nonce' ),
            'pricing' => [
                'guestsMin' => $pricing['guests_min'],
                'guestsMax' => $pricing['guests_max'],
            ],
        ] );
        // Если barproAjax уже задан (из barpro-main/barpro-bundle) — дополняем,
        // иначе создаём с нуля для случая когда barpro-tz грузится первым.
        wp_add_inline_script(
            'barpro-tz',
            "(function(){var d={$tz_data};if(typeof window.barproAjax==='object'&&window.barproAjax){Object.assign(window.barproAjax,{nonce:d.nonce,ajaxurl:d.ajaxurl,pricing:Object.assign({},d.pricing,window.barproAjax.pricing)});}else{window.barproAjax=d;}})();",
            'before'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'barpro_tz_enqueue', 20 );

/**
 * Открыть кэш-страниц шаблонов даже без файла page-XYZ.php в корне.
 * Регистрируем шаблоны, которые лежат в корне темы (page-team.php, page-cocktails.php).
 *
 * WordPress 6+ автоматически распознаёт page-{slug}.php — здесь только подстраховка
 * для случая, когда slug страницы отличается от имени файла.
 */
function barpro_tz_register_templates( $templates ) {
    $tz = [
        'page-team.php'         => 'Команда (TЗ)',
        'page-cocktails.php'    => 'Коктейли (TЗ)',
        'page-calculator.php'   => 'Калькулятор (TЗ)',
        'page-catering.php'     => 'Кейтеринг (TЗ)',
        'page-bar-catering.php' => 'Бар + Кейтеринг (TЗ)',
    ];
    return array_merge( $templates, $tz );
}
add_filter( 'theme_page_templates', 'barpro_tz_register_templates' );

/**
 * Принудительно использовать корневые шаблоны при определённых slug.
 * Полезно для свежей установки, когда страница ещё не выбрала шаблон.
 */
function barpro_tz_template_include( $template ) {
    if ( ! is_page() ) return $template;

    $slug = get_post_field( 'post_name', get_queried_object_id() );

    $map = [
        'team'         => 'page-team.php',
        'cocktails'    => 'page-cocktails.php',
        'calculator'   => 'page-calculator.php',
        'catering'     => 'page-catering.php',
        'bar-catering' => 'page-bar-catering.php',
    ];

    if ( isset( $map[ $slug ] ) ) {
        $candidate = get_theme_file_path( '/' . $map[ $slug ] );
        if ( file_exists( $candidate ) ) {
            return $candidate;
        }
    }
    return $template;
}
add_filter( 'template_include', 'barpro_tz_template_include', 99 );

/**
 * Подключение модуля SEO (расширенного)
 */
require_once BARPRO_THEME_DIR . '/inc/tz-seo.php';

/**
 * Подключение ACF-полей (если ACF Pro установлен)
 */
require_once BARPRO_THEME_DIR . '/inc/tz-acf.php';

/**
 * Автосоздание страниц по ТЗ при активации темы.
 */
function barpro_tz_create_pages() {
    $pages = [
        'calculator'   => [ 'Калькулятор',     'page-calculator.php' ],
        'team'         => [ 'Команда',         'page-team.php' ],
        'cocktails'    => [ 'Коктейли',        'page-cocktails.php' ],
        // 'cases' намеренно исключён: CPT case_study использует rewrite slug='cases'
        // с has_archive=true. Создание page с тем же slug создаёт конфликт приоритетов.
        // Архив кейсов будет доступен по /cases/ автоматически через WordPress rewrite.
        'catering'     => [ 'Кейтеринг',       'page-catering.php' ],
        'bar-catering' => [ 'Бар + Кейтеринг', 'page-bar-catering.php' ],
    ];

    foreach ( $pages as $slug => $row ) {
        if ( get_page_by_path( $slug ) ) continue;
        $page_id = wp_insert_post( [
            'post_title'   => $row[0],
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ] );
        if ( $page_id && ! is_wp_error( $page_id ) && $row[1] ) {
            update_post_meta( $page_id, '_wp_page_template', $row[1] );
        }
    }
}
add_action( 'after_switch_theme', 'barpro_tz_create_pages' );
