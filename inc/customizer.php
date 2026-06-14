<?php
/**
 * Theme Customizer
 *
 * @package BarPro_Premium
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление настроек в Customizer
 */
function barpro_customize_register($wp_customize) {
    
    // Секция: Контакты
    $wp_customize->add_section('barpro_contacts', [
        'title' => __('Контактная информация', 'barpro'),
        'priority' => 30,
    ]);
    
    // Телефон
    $wp_customize->add_setting('barpro_phone', [
        'default' => '+7 (999) 999-99-99',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('barpro_phone', [
        'label' => __('Телефон', 'barpro'),
        'section' => 'barpro_contacts',
        'type' => 'text',
    ]);
    
    // Email
    $wp_customize->add_setting('barpro_email', [
        'default' => 'info@barpro.ru',
        'sanitize_callback' => 'sanitize_email',
    ]);
    
    $wp_customize->add_control('barpro_email', [
        'label' => __('Email', 'barpro'),
        'section' => 'barpro_contacts',
        'type' => 'email',
    ]);
    
    // WhatsApp
    $wp_customize->add_setting('barpro_whatsapp', [
        'default' => '79999999999',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('barpro_whatsapp', [
        'label' => __('WhatsApp (только цифры)', 'barpro'),
        'section' => 'barpro_contacts',
        'type' => 'text',
        'description' => 'Например: 79999999999',
    ]);
    
    // Секция: Hero секция
    $wp_customize->add_section('barpro_hero', [
        'title' => __('Главная секция (Hero)', 'barpro'),
        'priority' => 40,
    ]);
    
    // Заголовок Hero
    $wp_customize->add_setting('barpro_hero_title', [
        'default' => 'BarPro Premium',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('barpro_hero_title', [
        'label' => __('Заголовок', 'barpro'),
        'section' => 'barpro_hero',
        'type' => 'text',
    ]);
    
    // Подзаголовок Hero
    $wp_customize->add_setting('barpro_hero_subtitle', [
        'default' => 'Эстетичный выездной бар премиум класса',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('barpro_hero_subtitle', [
        'label' => __('Подзаголовок', 'barpro'),
        'section' => 'barpro_hero',
        'type' => 'text',
    ]);
    
    // Фоновое изображение Hero
    $wp_customize->add_setting('barpro_hero_bg', [
        'sanitize_callback' => 'absint',
    ]);
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'barpro_hero_bg', [
        'label' => __('Фоновое изображение', 'barpro'),
        'section' => 'barpro_hero',
        'mime_type' => 'image',
    ]));
    
    // Секция: Социальные сети
    $wp_customize->add_section('barpro_social', [
        'title' => __('Социальные сети', 'barpro'),
        'priority' => 50,
    ]);
    
    // VK
    $wp_customize->add_setting('barpro_vk', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    
    $wp_customize->add_control('barpro_vk', [
        'label' => __('VK', 'barpro'),
        'section' => 'barpro_social',
        'type' => 'url',
    ]);
    
    // Instagram
    $wp_customize->add_setting('barpro_instagram', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    
    $wp_customize->add_control('barpro_instagram', [
        'label' => __('Instagram', 'barpro'),
        'section' => 'barpro_social',
        'type' => 'url',
    ]);
    
    // Telegram
    $wp_customize->add_setting('barpro_telegram', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    
    $wp_customize->add_control('barpro_telegram', [
        'label' => __('Telegram', 'barpro'),
        'section' => 'barpro_social',
        'type' => 'url',
    ]);
    
    // Секция: Цвета темы
    $wp_customize->add_section('barpro_colors', [
        'title' => __('Цвета темы', 'barpro'),
        'priority' => 60,
    ]);
    
    // Основной золотой цвет
    $wp_customize->add_setting('barpro_gold_color', [
        'default' => '#d4af37',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'barpro_gold_color', [
        'label' => __('Золотой цвет', 'barpro'),
        'section' => 'barpro_colors',
    ]));
    
    // Тёмный фон
    $wp_customize->add_setting('barpro_dark_bg', [
        'default' => '#0a0a0a',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'barpro_dark_bg', [
        'label' => __('Тёмный фон', 'barpro'),
        'section' => 'barpro_colors',
    ]));
    // ── Hero Video ────────────────────────────────────────
    $wp_customize->add_section( 'barpro_video', [
        'title'    => __( 'Hero видео', 'barpro' ),
        'priority' => 35,
    ] );

    foreach ( [
        'barpro_hero_video_mp4'  => [ 'MP4 видео (основной формат)', 'text' ],
        'barpro_hero_video_webm' => [ 'WebM видео (меньше размер, опционально)', 'text' ],
        'barpro_hero_poster'     => [ 'Постер (превью до загрузки видео)', 'text' ],
    ] as $key => [ $label, $type ] ) {
        $wp_customize->add_setting( $key, [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => __( $label, 'barpro' ),
            'section' => 'barpro_video',
            'type'    => $type,
        ] );
    }

    // Город
    $wp_customize->add_setting('barpro_city', [
        'default'           => 'Москва',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('barpro_city', [
        'label'   => __('Город', 'barpro'),
        'section' => 'barpro_contacts',
        'type'    => 'text',
    ]);

    // Адрес (для Schema)
    $wp_customize->add_setting('barpro_address', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('barpro_address', [
        'label'   => __('Адрес (улица, дом)', 'barpro'),
        'section' => 'barpro_contacts',
        'type'    => 'text',
    ]);

    // Часы работы (для Schema, формат Mo-Su 09:00-23:00)
    $wp_customize->add_setting('barpro_hours', [
        'default'           => 'Mo-Su 09:00-23:00',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('barpro_hours', [
        'label'       => __('Часы работы (Schema)', 'barpro'),
        'description' => 'Пример: Mo-Su 09:00-23:00',
        'section'     => 'barpro_contacts',
        'type'        => 'text',
    ]);

    // Координаты
    $wp_customize->add_setting('barpro_lat', [
        'default'           => '55.7558',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('barpro_lat', [
        'label'   => __('Широта (latitude)', 'barpro'),
        'section' => 'barpro_contacts',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('barpro_lng', [
        'default'           => '37.6173',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('barpro_lng', [
        'label'   => __('Долгота (longitude)', 'barpro'),
        'section' => 'barpro_contacts',
        'type'    => 'text',
    ]);
    // ── Калькулятор — тарифные настройки ─────────────────
    $wp_customize->add_section( 'barpro_calculator', [
        'title'       => __( 'Калькулятор — тарифы', 'barpro' ),
        'description' => __( 'Параметры расчёта стоимости. Формула: (гости × множитель × цена_гость) + (часы × цена_час)', 'barpro' ),
        'priority'    => 40,
    ] );

    $calc_fields = [
        'barpro_price_guests_multiplier' => [ 'Множитель гостей (обычно 2)',       '2'    ],
        'barpro_price_per_guest'         => [ 'Цена за одного гостя (₽)',           '450'  ],
        'barpro_price_per_hour'          => [ 'Цена за час работы (₽)',             '3000' ],
        'barpro_price_discount_pct'      => [ 'Скидка с калькулятора (%)',          '10'   ],
        'barpro_price_guests_min'        => [ 'Минимум гостей',                     '10'   ],
        'barpro_price_guests_max'        => [ 'Максимум гостей',                    '300'  ],
        'barpro_price_hours_min'         => [ 'Минимум часов',                      '2'    ],
        'barpro_price_hours_max'         => [ 'Максимум часов',                     '12'   ],
    ];

    foreach ( $calc_fields as $id => [ $label, $default ] ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'absint',
        ] );
        $wp_customize->add_control( $id, [
            'label'   => __( $label, 'barpro' ),
            'section' => 'barpro_calculator',
            'type'    => 'number',
        ] );
    }
}
add_action('customize_register', 'barpro_customize_register');

/**
 * Вывод CSS-переменных из customizer (gold + bg + studio tokens).
 * Используем wp_add_inline_style вместо прямого echo в wp_head —
 * это CSP-friendly (стиль аттачится к зарегистрированному хэндлу) и
 * гарантирует правильный порядок после style.css :root переменных.
 */
function barpro_customizer_css(): void {
    $gold = sanitize_hex_color( get_theme_mod( 'barpro_gold_color', '#d4af37' ) ) ?: '#d4af37';
    $bg   = sanitize_hex_color( get_theme_mod( 'barpro_dark_bg',    '#080808' ) ) ?: '#080808';

    $css = ":root {
        --gold-primary:  {$gold};
        --accent:        {$gold};
        --dark-bg:       {$bg};
        --bg:            {$bg};
        --accent-dim:    {$gold}1f;
        --accent-glow:   {$gold}59;
        --border-gold:   {$gold}40;
    }";

    wp_add_inline_style( 'barpro-style', $css );
}
add_action( 'wp_enqueue_scripts', 'barpro_customizer_css', 25 );

/**
 * Получить настройку темы
 */
function barpro_get_option($key, $default = '') {
    return get_theme_mod('barpro_' . $key, $default);
}
