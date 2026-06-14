<?php
/**
 * BarPro Premium Theme Functions
 *
 * @package BarPro_Premium
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================
// SECURITY HARDENING
// ============================================

// Скрыть версию WordPress
remove_action('wp_head', 'wp_generator');

// Отключить XML-RPC (если не используется)
add_filter('xmlrpc_enabled', '__return_false');

// Отключить редактор файлов в админке
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// Скрыть версию из CSS/JS
function barpro_remove_version_scripts_styles($src) {
    if ($src && strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'barpro_remove_version_scripts_styles', 9999);
add_filter('script_loader_src', 'barpro_remove_version_scripts_styles', 9999);

// Security headers
function barpro_security_headers(): void {
    if ( is_admin() ) return;

    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-XSS-Protection: 1; mode=block' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );

    /*
     * CSP: мягкий режим с поддержкой типовых маркетинговых плагинов.
     *
     * 'unsafe-inline' необходим для WordPress (Gutenberg, inline styles).
     *
     * Базовые домены покрывают:
     *   - Google Fonts, Google Tag Manager, Google Analytics (GA4)
     *   - Яндекс.Метрику (mc.yandex.ru, yandex.ru)
     *   - VK Pixel (vk.com)
     *   - Telegram виджет (telegram.org)
     *   - jsDelivr CDN (GSAP, Lenis, SplitType в dev-режиме)
     *
     * Для добавления своих доменов используйте фильтр:
     *
     *   add_filter( 'barpro_csp_extra_domains', function( $domains ) {
     *       $domains['script-src'][] = 'chat.example.com';
     *       $domains['frame-src'][]  = 'chat.example.com';
     *       return $domains;
     *   });
     *
     * CDN-домены:
     *   - dev-режим:  GSAP/Lenis/SplitType подключаются с jsDelivr
     *   - prod-режим: CDN не нужны — всё собрано в Vite bundle
     */
    $use_vite_dist = file_exists( get_theme_file_path( '/assets/dist/.vite/manifest.json' ) );

    // Базовые источники скриптов
    $script_domains = [
        "'self'",
        "'unsafe-inline'",
        // Google Tag Manager + Analytics
        'www.googletagmanager.com',
        'www.google-analytics.com',
        'ssl.google-analytics.com',
        // Яндекс.Метрика
        'mc.yandex.ru',
        'yastatic.net',
        // VK Pixel
        'vk.com',
        'userapi.com',
        // Telegram виджет
        'telegram.org',
    ];

    // Базовые источники стилей
    $style_domains = [
        "'self'",
        "'unsafe-inline'",
        'fonts.googleapis.com',
    ];

    // Базовые источники фреймов (виджеты, чаты)
    $frame_domains = [
        'www.googletagmanager.com',
        'telegram.org',
        'vk.com',
    ];

    // Базовые источники connect (XHR/fetch аналитики)
    $connect_domains = [
        "'self'",
        'www.google-analytics.com',
        'mc.yandex.ru',
        'vk.com',
    ];

    if ( ! $use_vite_dist ) {
        $script_domains[] = 'cdn.jsdelivr.net';
        $style_domains[]  = 'cdn.jsdelivr.net';
    }

    /*
     * Фильтр для расширения CSP из functions.php дочерней темы или плагина.
     * Структура: [ 'script-src' => [], 'style-src' => [], 'frame-src' => [], 'connect-src' => [] ]
     */
    $extra = apply_filters( 'barpro_csp_extra_domains', [
        'script-src'  => [],
        'style-src'   => [],
        'frame-src'   => [],
        'connect-src' => [],
    ] );

    $script_src  = implode( ' ', array_unique( array_merge( $script_domains,  (array) ( $extra['script-src']  ?? [] ) ) ) );
    $style_src   = implode( ' ', array_unique( array_merge( $style_domains,   (array) ( $extra['style-src']   ?? [] ) ) ) );
    $frame_src   = implode( ' ', array_unique( array_merge( $frame_domains,   (array) ( $extra['frame-src']   ?? [] ) ) ) );
    $connect_src = implode( ' ', array_unique( array_merge( $connect_domains, (array) ( $extra['connect-src'] ?? [] ) ) ) );

    $csp = implode( '; ', [
        "default-src 'self'",
        "script-src {$script_src}",
        "style-src {$style_src}",
        "font-src 'self' fonts.gstatic.com data:",
        "img-src 'self' data: https: blob: mc.yandex.ru",
        "connect-src {$connect_src}",
        "media-src 'self'",
        "frame-src {$frame_src}",
        "frame-ancestors 'none'",
    ] );

    header( "Content-Security-Policy: {$csp}" );
}
add_action( 'send_headers', 'barpro_security_headers' );

/**
 * Rate Limiting — максимум $limit запросов за $window секунд с одного IP.
 *
 * Хранит [count, window_start] — TTL transient = $window, не сбрасывается.
 * wp_cache_add / object cache использовать нельзя без persistent cache,
 * поэтому храним window_start в значении и сами проверяем истечение.
 */
function barpro_check_rate_limit( string $action, int $limit = 5, int $window = 60 ): bool {
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'barpro_rl_' . $action . '_' . md5( $ip );

    $data = get_transient( $key );

    $now = time();

    if ( $data === false ) {
        // Первый запрос — начать новое окно
        set_transient( $key, [ 'count' => 1, 'start' => $now ], $window );
        return true;
    }

    // Если окно истекло — начать новое (transient ещё не удалён object cache'ом)
    if ( $now - ( $data['start'] ?? $now ) >= $window ) {
        set_transient( $key, [ 'count' => 1, 'start' => $now ], $window );
        return true;
    }

    // Окно активно — проверить счётчик
    if ( (int) ( $data['count'] ?? 0 ) >= $limit ) {
        // wp_send_json_error вызывает wp_die() — выполнение прерывается здесь.
        // return после него недостижим, поэтому убран (мёртвый код).
        wp_send_json_error(
            [ 'message' => 'Слишком много запросов. Подождите минуту.' ],
            429
        );
    }

    // Увеличить счётчик, сохраняя оригинальный start (TTL пересчитается от start)
    $remaining_ttl = $window - ( $now - ( $data['start'] ?? $now ) );
    set_transient( $key, [ 'count' => $data['count'] + 1, 'start' => $data['start'] ], max( 1, $remaining_ttl ) );

    return true;
}

// ============================================
// THEME CONSTANTS
// ============================================

// Константы темы
define('BARPRO_VERSION', '1.1.0');
define('BARPRO_THEME_DIR', get_template_directory());
define('BARPRO_THEME_URI', get_template_directory_uri());

/**
 * Подключение стилей и скриптов
 */
function barpro_enqueue_assets() {

    // ── Определяем: Vite production bundle или dev файлы ──────────────
    $dist_manifest = BARPRO_THEME_DIR . '/assets/dist/.vite/manifest.json';
    $use_vite_dist  = file_exists( $dist_manifest );

    if ( $use_vite_dist ) {
        // ── PRODUCTION: один минифицированный bundle из Vite ──────────
        $manifest = json_decode( file_get_contents( $dist_manifest ), true );

        // CSS bundle
        $styles_file = $manifest['assets/css/src/main.css']['file'] ?? null;
        if ( $styles_file ) {
            wp_enqueue_style(
                'barpro-bundle',
                BARPRO_THEME_URI . '/assets/dist/' . $styles_file,
                [], BARPRO_VERSION
            );
        }

        // Main JS bundle
        $main_file = $manifest['assets/js/src/main.js']['file'] ?? null;
        if ( $main_file ) {
            wp_enqueue_script(
                'barpro-bundle',
                BARPRO_THEME_URI . '/assets/dist/' . $main_file,
                [], BARPRO_VERSION, true
            );
        }

        // Motion bundle — все страницы
        $motion_file = $manifest['assets/js/src/motion.js']['file'] ?? null;
        if ( $motion_file ) {
            wp_enqueue_script(
                'barpro-motion',
                BARPRO_THEME_URI . '/assets/dist/' . $motion_file,
                ['barpro-bundle'], BARPRO_VERSION, true
            );
        }

    } else {
        // ── DEVELOPMENT: отдельные файлы ──────────────────────────────

        // Google Fonts с display=swap
        wp_enqueue_style(
            'barpro-fonts',
            'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,700;1,400&family=Jost:wght@300;400;500;600&display=swap',
            [], null
        );

        // CSS: один связанный bundle через studio.css (@import)
        wp_enqueue_style( 'barpro-design-system', BARPRO_THEME_URI . '/assets/css/design-system.css', [], BARPRO_VERSION );
        wp_enqueue_style( 'barpro-layout',        BARPRO_THEME_URI . '/assets/css/layout.css',        ['barpro-design-system'], BARPRO_VERSION );
        wp_enqueue_style( 'barpro-premium',       BARPRO_THEME_URI . '/assets/css/premium.css',       ['barpro-layout'], BARPRO_VERSION );
        wp_enqueue_style( 'barpro-studio',        BARPRO_THEME_URI . '/assets/css/studio.css',        ['barpro-premium'], BARPRO_VERSION );
        wp_enqueue_style( 'barpro-style',         get_stylesheet_uri(),                               ['barpro-studio'], BARPRO_VERSION );

        // JS: внешние библиотеки — только на страницах с анимациями
        $needs_motion = is_front_page()
            || is_page_template( 'page-calculator.php' )
            || is_singular( [ 'cocktail', 'package', 'case_study' ] )
            || is_post_type_archive( [ 'cocktail', 'case_study', 'package' ] );

        if ( $needs_motion ) {
            wp_enqueue_script( 'gsap',              'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js',          [], null, true );
            wp_enqueue_script( 'gsap-scrolltrigger','https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/ScrollTrigger.min.js', ['gsap'], null, true );
            wp_enqueue_script( 'split-type',        'https://cdn.jsdelivr.net/npm/split-type@0.3.4/umd/index.min.js',     [], null, true );
            wp_enqueue_script( 'lenis',             'https://cdn.jsdelivr.net/npm/lenis@1.1.14/dist/lenis.min.js',        [], null, true );
        }

        // Собственные скрипты
        wp_enqueue_script( 'barpro-main', BARPRO_THEME_URI . '/assets/js/main.js', ['jquery'], BARPRO_VERSION, true );

        if ( $needs_motion ) {
            $motion_deps = [ 'gsap', 'gsap-scrolltrigger', 'split-type', 'lenis' ];

            // В dev-режиме модули подключаются как отдельные скрипты (глобальные функции).
            // В prod-сборке Vite объединяет их в bundle автоматически.
            $modules = [
                'barpro-mod-lenis'       => 'lenis.js',
                'barpro-mod-nav'         => 'nav.js',
                'barpro-mod-drawer'      => 'drawer.js',
                'barpro-mod-cursor'      => 'cursor.js',
                'barpro-mod-hero'        => 'hero.js',
                'barpro-mod-split'       => 'split-type.js',
                'barpro-mod-scroll'      => 'scroll-animations.js',
                'barpro-mod-magnetic'    => 'magnetic.js',
                'barpro-mod-micro'       => 'micro.js',
                'barpro-mod-counters'    => 'counters.js',
                'barpro-mod-transitions' => 'page-transitions.js',
            ];

            $prev_dep = $motion_deps;
            foreach ( $modules as $handle => $file ) {
                wp_enqueue_script(
                    $handle,
                    BARPRO_THEME_URI . '/assets/js/modules/' . $file,
                    $prev_dep,
                    BARPRO_VERSION,
                    true
                );
                $prev_dep = [ $handle ];
            }

            // motion.js — координатор, после всех модулей
            wp_enqueue_script( 'barpro-motion', BARPRO_THEME_URI . '/assets/js/motion.js', $prev_dep, BARPRO_VERSION, true );
        }

        // Только главная
        if ( is_front_page() ) {
            wp_enqueue_script( 'barpro-premium-interactions', BARPRO_THEME_URI . '/assets/js/premium-interactions.js', ['barpro-motion'], BARPRO_VERSION, true );
        }
    }

    // Локализация AJAX + конфиг калькулятора (всегда)
    $localize_handle = $use_vite_dist ? 'barpro-bundle' : 'barpro-main';

    $pricing = barpro_price_config();

    wp_localize_script( $localize_handle, 'barproAjax', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'barpro_nonce' ),
        'debug'   => defined( 'WP_DEBUG' ) && WP_DEBUG,
        'pricing' => [
            'guestsMultiplier' => $pricing['guests_multiplier'],
            'pricePerGuest'    => $pricing['price_per_guest'],
            'pricePerHour'     => $pricing['price_per_hour'],
            'discountPct'      => $pricing['discount_pct'],
            'guestsMin'        => $pricing['guests_min'],
            'guestsMax'        => $pricing['guests_max'],
            'hoursMin'         => $pricing['hours_min'],
            'hoursMax'         => $pricing['hours_max'],
        ],
    ]);
}
add_action( 'wp_enqueue_scripts', 'barpro_enqueue_assets' );

/**
 * Добавить defer/async к внешним скриптам через script_loader_tag
 */
function barpro_script_attributes( $tag, $handle, $src ) {
    // Скрипты которые должны быть defer (не зависят от DOM при парсинге)
    $defer_scripts = [
        'gsap', 'gsap-scrolltrigger', 'split-type', 'lenis',
        'barpro-motion', 'barpro-premium-interactions',
    ];
    // Скрипты которые можно async (полностью независимы)
    $async_scripts = [];

    if ( in_array( $handle, $defer_scripts, true ) ) {
        return str_replace( ' src=', ' defer src=', $tag );
    }
    if ( in_array( $handle, $async_scripts, true ) ) {
        return str_replace( ' src=', ' async src=', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'barpro_script_attributes', 10, 3 );

/**
 * Preload критических ресурсов (шрифты, LCP)
 */
function barpro_preload_assets() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="preload" href="' . esc_url( BARPRO_THEME_URI . '/assets/css/design-system.css' ) . '" as="style">' . "\n";
}
add_action( 'wp_head', 'barpro_preload_assets', 1 );

/**
 * Lazy loading для всех миниатюр
 */
// wp_lazy_loading_enabled включён по умолчанию в WordPress 5.5+ — фильтр не нужен.
add_filter( 'the_post_thumbnail_html', function( $html ) {
    if ( strpos( $html, 'loading=' ) === false ) {
        $html = str_replace( '<img ', '<img loading="lazy" decoding="async" ', $html );
    }
    return $html;
} );

/**
 * Transient-кеш для WP_Query.
 * Возвращает массив ['posts'=>[], 'total'=>int, 'max_pages'=>int].
 * Используйте barpro_get_cached_posts() если нужны только посты.
 */
function barpro_get_cached_query( $cache_key, $query_args ) {
    $cached = get_transient( $cache_key );
    if ( $cached !== false ) {
        return $cached;
    }

    $query_args['update_post_term_cache'] = false;
    $query_args['update_post_meta_cache'] = false;

    $query  = new WP_Query( $query_args );
    $result = [
        'posts'     => $query->posts,
        'total'     => (int) $query->found_posts,
        'max_pages' => (int) $query->max_num_pages,
    ];
    wp_reset_postdata();

    set_transient( $cache_key, $result, HOUR_IN_SECONDS );
    return $result;
}

/**
 * Упрощённый кеш — только посты (без пагинации).
 * Для главной страницы где нет пагинации.
 */
function barpro_get_cached_posts( $cache_key, $query_args ) {
    $cached = get_transient( $cache_key );
    if ( $cached !== false ) {
        return $cached;
    }

    $query_args['no_found_rows']          = true;
    $query_args['update_post_term_cache'] = false;
    $query_args['update_post_meta_cache'] = false;

    $query = new WP_Query( $query_args );
    $posts = $query->posts;
    wp_reset_postdata();

    set_transient( $cache_key, $posts, HOUR_IN_SECONDS );
    return $posts;
}

// Сбрасывать кеш при сохранении любого из используемых CPT
foreach ( [ 'package', 'cocktail', 'team_member', 'testimonial' ] as $_cpt ) {
    add_action( "save_post_{$_cpt}", function() {
        delete_transient( 'barpro_home_packages' );
        delete_transient( 'barpro_home_cocktails' );
        delete_transient( 'barpro_home_team' );
        delete_transient( 'barpro_home_testimonials' );
    } );
}

/**
 * Настройка темы
 */
function barpro_setup() {
    // Поддержка заголовков
    add_theme_support('title-tag');
    
    // Поддержка миниатюр
    add_theme_support('post-thumbnails');
    
    // Размеры изображений
    add_image_size('cocktail-thumb', 400, 500, true);
    add_image_size('team-thumb', 400, 400, true);
    add_image_size('case-large', 1200, 600, true);
    add_image_size('case-small', 400, 300, true);
    
    // Поддержка HTML5
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ]);
    
    // Поддержка логотипа
    add_theme_support('custom-logo', [
        'height' => 60,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ]);
    
    // Регистрация меню
    register_nav_menus([
        'primary' => __('Главное меню', 'barpro'),
        'footer' => __('Меню в футере', 'barpro'),
    ]);
}
add_action('after_setup_theme', 'barpro_setup');

/**
 * Регистрация виджетов
 */
function barpro_widgets_init() {
    register_sidebar([
        'name' => __('Футер 1', 'barpro'),
        'id' => 'footer-1',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
    
    register_sidebar([
        'name' => __('Футер 2', 'barpro'),
        'id' => 'footer-2',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
    
    register_sidebar([
        'name' => __('Футер 3', 'barpro'),
        'id' => 'footer-3',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
}
add_action('widgets_init', 'barpro_widgets_init');

/**
 * Custom Post Type: Коктейли
 */

// ============================================
// МОДУЛЬНАЯ АРХИТЕКТУРА — подключение модулей
// ============================================
require_once BARPRO_THEME_DIR . '/inc/class-cpt-manager.php'; // CPT классы
require_once BARPRO_THEME_DIR . '/inc/meta.php';
require_once BARPRO_THEME_DIR . '/inc/ajax.php';
require_once BARPRO_THEME_DIR . '/inc/seo.php';
require_once BARPRO_THEME_DIR . '/inc/template-functions.php';
require_once BARPRO_THEME_DIR . '/inc/customizer.php';

/**
 * Подключение дополнительных файлов
 */

// ============================================
// BarPro TЗ v1.0 — Event Premium upgrade
// ============================================
require_once BARPRO_THEME_DIR . '/inc/tz-bootstrap.php';
