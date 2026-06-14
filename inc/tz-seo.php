<?php
/**
 * BarPro — TZ SEO (Title/Description/OG/Twitter/Canonical/JSON-LD/FAQ)
 *
 * @package BarPro_Premium
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Получить SEO-конфигурацию для текущей страницы.
 * Учитывает ACF-поля seo_title / seo_description, иначе fallback.
 */
function barpro_tz_seo_data() {
    $title       = '';
    $description = '';
    $type        = 'website';
    $url         = is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) );
    $image       = '';

    if ( function_exists( 'get_field' ) ) {
        if ( is_singular() ) {
            $title       = get_field( 'seo_title' )       ?: '';
            $description = get_field( 'seo_description' ) ?: '';
        }
    }

    if ( is_singular() ) {
        if ( ! $title )       $title       = get_the_title() . ' — ' . get_bloginfo( 'name' );
        if ( ! $description ) $description = get_the_excerpt() ?: wp_strip_all_tags( wp_trim_words( get_the_content(), 28 ) );
        if ( has_post_thumbnail() ) $image  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        $type = 'article';
    } elseif ( is_post_type_archive() ) {
        $title       = post_type_archive_title( '', false ) . ' — ' . get_bloginfo( 'name' );
        $description = get_bloginfo( 'description' );
    } elseif ( is_front_page() || is_home() ) {
        $title       = get_bloginfo( 'name' ) . ' — выездной бар и кейтеринг под ключ';
        $description = get_bloginfo( 'description' ) ?: 'Премиум выездной бар, кейтеринг и персонал для свадеб, корпоративов и фестивалей.';
    }

    if ( ! $description ) $description = 'BarPro — выездной бар и кейтеринг под ключ в Москве и области.';
    if ( ! $image ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) $image = wp_get_attachment_image_url( $logo_id, 'full' );
    }

    return [
        'title'       => wp_strip_all_tags( $title ),
        'description' => wp_strip_all_tags( $description ),
        'type'        => $type,
        'url'         => $url,
        'image'       => $image,
        'site_name'   => get_bloginfo( 'name' ),
        'locale'      => 'ru_RU',
    ];
}

/**
 * Вывести meta description / canonical / OG / Twitter
 */
function barpro_tz_seo_head() {
    if ( is_admin() ) return;
    $d = barpro_tz_seo_data();

    echo "\n<!-- BarPro SEO -->\n";
    printf( '<meta name="description" content="%s">' . "\n", esc_attr( $d['description'] ) );

    // Canonical — пропускаем если уже добавлен Yoast SEO, Rank Math или AIOSEO
    $has_seo_plugin = defined( 'WPSEO_VERSION' )       // Yoast SEO
                   || defined( 'RANK_MATH_VERSION' )    // Rank Math
                   || defined( 'AIOSEO_VERSION' );      // All in One SEO
    if ( ! $has_seo_plugin ) {
        printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $d['url'] ) );
    }

    // OpenGraph
    printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( $d['locale'] ) );
    printf( '<meta property="og:type" content="%s">' . "\n",   esc_attr( $d['type'] ) );
    printf( '<meta property="og:title" content="%s">' . "\n",  esc_attr( $d['title'] ) );
    printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $d['description'] ) );
    printf( '<meta property="og:url" content="%s">' . "\n",    esc_url( $d['url'] ) );
    printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $d['site_name'] ) );
    if ( $d['image'] ) {
        printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $d['image'] ) );
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    printf( '<meta name="twitter:title" content="%s">' . "\n",       esc_attr( $d['title'] ) );
    printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $d['description'] ) );
    if ( $d['image'] ) {
        printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $d['image'] ) );
    }
    echo "<!-- /BarPro SEO -->\n\n";
}
add_action( 'wp_head', 'barpro_tz_seo_head', 4 );

/**
 * Принудительно фильтр title через title-tag
 */
function barpro_tz_document_title( $parts ) {
    $d = barpro_tz_seo_data();
    if ( $d['title'] ) {
        $parts['title'] = $d['title'];
        unset( $parts['site'] );
    }
    return $parts;
}
add_filter( 'document_title_parts', 'barpro_tz_document_title' );

/**
 * FAQ-схема (если на странице есть FAQ через ACF — повторяющееся поле faq_items: question / answer)
 */
function barpro_tz_faq_schema() {
    if ( ! is_singular() || ! function_exists( 'get_field' ) ) return;
    $faq = get_field( 'faq_items' );
    if ( ! is_array( $faq ) || empty( $faq ) ) return;

    $entities = [];
    foreach ( $faq as $row ) {
        $q = $row['question'] ?? '';
        $a = $row['answer']   ?? '';
        if ( ! $q || ! $a ) continue;
        $entities[] = [
            '@type' => 'Question',
            'name'  => wp_strip_all_tags( $q ),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => wp_strip_all_tags( $a ),
            ],
        ];
    }
    if ( empty( $entities ) ) return;

    $schema = [
        '@context'  => 'https://schema.org',
        '@type'     => 'FAQPage',
        'mainEntity'=> $entities,
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'barpro_tz_faq_schema', 6 );

/**
 * Performance hints — preload первого hero-image, если он задан в ACF.
 */
function barpro_tz_preload_hero() {
    if ( ! function_exists( 'get_field' ) || ! is_page() ) return;
    $img = '';
    $candidates = [ 'calc_hero_image', 'cocktails_hero_image', 'catering_hero_image', 'bc_hero_image', 'team_hero_image' ];
    foreach ( $candidates as $k ) {
        $val = get_field( $k );
        if ( $val ) { $img = is_array( $val ) ? ( $val['url'] ?? '' ) : $val; break; }
    }
    if ( $img ) {
        printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $img ) );
    }
}
add_action( 'wp_head', 'barpro_tz_preload_hero', 2 );

/**
 * BreadcrumbList JSON-LD Schema (P6 fix).
 *
 * Генерируется на основе тех же данных что и HTML-хлебные крошки.
 * Не дублирует если Yoast/RankMath уже выводит BreadcrumbList.
 */
function barpro_tz_breadcrumb_schema(): void {
    // Не выводим при активных SEO-плагинах с поддержкой Breadcrumb Schema
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) return;
    if ( is_front_page() || is_home() ) return; // На главной хлебных крошек нет

    $items = [];
    $pos   = 1;

    // Главная — всегда первый элемент
    $items[] = [
        '@type'    => 'ListItem',
        'position' => $pos++,
        'name'     => get_bloginfo( 'name' ),
        'item'     => esc_url( home_url( '/' ) ),
    ];

    if ( is_singular( 'case_study' ) ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => 'Кейсы',
            'item'     => esc_url( get_post_type_archive_link( 'case_study' ) ),
        ];
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => get_the_title(),
            'item'     => esc_url( get_permalink() ),
        ];
    } elseif ( is_post_type_archive( 'case_study' ) ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => 'Кейсы',
            'item'     => esc_url( get_post_type_archive_link( 'case_study' ) ),
        ];
    } elseif ( is_singular( 'cocktail' ) ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => 'Коктейли',
            'item'     => esc_url( home_url( '/cocktails/' ) ),
        ];
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => get_the_title(),
            'item'     => esc_url( get_permalink() ),
        ];
    } elseif ( is_page() ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => get_the_title(),
            'item'     => esc_url( get_permalink() ),
        ];
    }

    if ( count( $items ) < 2 ) return; // Только главная — не выводим

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'barpro_tz_breadcrumb_schema', 5 );

/**
 * Event JSON-LD Schema для кейсов (P6 fix).
 *
 * Каждый кейс — это завершённое мероприятие (Event).
 * Помогает Google понять тип контента и улучшает сниппет.
 */
function barpro_tz_event_schema(): void {
    if ( ! is_singular( 'case_study' ) ) return;

    $has_acf = function_exists( 'get_field' );
    $date    = $has_acf ? get_field( 'case_date' )    : '';
    $guests  = $has_acf ? get_field( 'case_guests' )  : '';
    $city    = get_theme_mod( 'barpro_city', 'Москва' );

    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'Event',
        'name'       => get_the_title(),
        'description'=> wp_strip_all_tags( get_the_excerpt() ?: get_the_title() ),
        'url'        => esc_url( get_permalink() ),
        'eventStatus'=> 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        'location'   => [
            '@type'   => 'Place',
            'name'    => $city,
            'address' => [ '@type' => 'PostalAddress', 'addressLocality' => $city, 'addressCountry' => 'RU' ],
        ],
        'organizer'  => [
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'url'   => esc_url( home_url( '/' ) ),
        ],
    ];

    // Дата — если заполнена в ACF (формат д.м.г или Y-m-d)
    if ( $date ) {
        // Нормализуем к ISO 8601
        $ts = strtotime( $date );
        if ( $ts ) {
            $schema['startDate'] = date( 'Y-m-d', $ts );
            $schema['endDate']   = date( 'Y-m-d', $ts );
        }
    }

    // Аудитория
    if ( $guests ) {
        $schema['maximumAttendeeCapacity'] = (int) preg_replace( '/\D/', '', $guests );
    }

    // Главное фото кейса
    if ( has_post_thumbnail() ) {
        $img_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        if ( $img_url ) $schema['image'] = esc_url( $img_url );
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'barpro_tz_event_schema', 5 );
