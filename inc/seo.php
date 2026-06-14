<?php
/**
 * BarPro Premium — SEO module
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================
// SEO & PERFORMANCE OPTIMIZATIONS
// ============================================

/**
 * Schema.org LocalBusiness — все данные из Customizer
 */
function barpro_add_schema_org() {
    if ( ! is_front_page() ) return;

    $phone   = get_theme_mod( 'barpro_phone', '' );
    $email   = get_theme_mod( 'barpro_email', '' );
    $city    = get_theme_mod( 'barpro_city',  'Москва' );
    $address = get_theme_mod( 'barpro_address', '' );
    $hours   = get_theme_mod( 'barpro_hours',   'Mo-Su 09:00-23:00' );
    $ig      = get_theme_mod( 'barpro_instagram', '' );
    $vk      = get_theme_mod( 'barpro_vk',       '' );
    $tg      = get_theme_mod( 'barpro_telegram',  '' );

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'LocalBusiness',
        'name'        => get_bloginfo( 'name' ),
        'description' => get_bloginfo( 'description' ),
        'url'         => esc_url( home_url( '/' ) ),
        'hasMenu'     => esc_url( home_url( '/cocktails/' ) ),
        'priceRange'  => '₽₽₽',
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $address ?: '',
            'addressLocality' => $city,
            'addressCountry'  => 'RU',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => get_theme_mod( 'barpro_lat', '55.7558' ),
            'longitude' => get_theme_mod( 'barpro_lng', '37.6173' ),
        ],
    ];

    if ( $phone )  $schema['telephone']     = esc_html( $phone );
    if ( $email )  $schema['email']         = esc_html( $email );
    if ( $hours )  $schema['openingHours']  = esc_html( $hours );

    // SameAs: соцсети
    $same_as = array_filter( [ $ig, $vk, $tg ] );
    if ( $same_as ) $schema['sameAs'] = array_values( $same_as );

    // Логотип
    if ( $logo_id = get_theme_mod( 'custom_logo' ) ) {
        $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
        if ( $logo_url ) $schema['image'] = $logo_url;
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'barpro_add_schema_org', 5 );

/**
 * FAQ Schema для комбо-пакетов
 */
function barpro_add_faq_schema() {
    if (!is_page_template('page-bar-catering.php')) return;
    
    $faq = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'Можно ли заказать только бар или только кейтеринг?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Да, конечно! Скидка 15% действует только при заказе комбо. Отдельные услуги — по стандартным ценам.'
                ]
            ],
            [
                '@type' => 'Question',
                'name' => 'Скидка 15% — это на всё?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Да, на общую сумму бар + кейтеринг. Например: бар 100,000₽ + кейтеринг 150,000₽ = 250,000₽. Со скидкой 15% = 212,500₽. Экономия 37,500₽!'
                ]
            ],
            [
                '@type' => 'Question',
                'name' => 'Кто координирует бар и кейтеринг в день мероприятия?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'У вас один менеджер на всё. Он координирует и барную зону, и кейтеринг. Вы общаетесь с одним человеком.'
                ]
            ],
        ]
    ];
    
    echo '<script type="application/ld+json">' . wp_json_encode($faq, JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'barpro_add_faq_schema', 5);


// Open Graph & Twitter Cards перенесены в inc/tz-seo.php (barpro_tz_seo_head).
// Функция barpro_add_open_graph удалена во избежание дублирования тегов в <head>.




// Preconnect/preload Google Fonts перенесены в functions.php (barpro_preload_assets).
// Функция barpro_preload_resources удалена во избежание дублирования тегов.




// Defer скриптов реализован в functions.php (barpro_script_loader_tag).
// Функция barpro_defer_scripts удалена во избежание двойного применения фильтра.



// Lazy loading атрибуты обрабатываются в functions.php через the_post_thumbnail_html фильтр.
// WordPress 5.5+ добавляет loading="lazy" автоматически — дополнительный фильтр не нужен.
