<?php
/**
 * Вспомогательные функции для шаблонов
 *
 * @package BarPro_Premium
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Получить иконку для типа коктейля
 */
function barpro_get_cocktail_icon($is_alcoholic) {
    return $is_alcoholic ? '🍸' : '🍹';
}

/**
 * Форматирование цены
 */
function barpro_format_price($price) {
    return number_format($price, 0, ',', ' ') . '₽';
}

/**
 * Получить excerpt с определённой длиной
 */
function barpro_custom_excerpt($length = 20) {
    $excerpt = get_the_excerpt();
    $words = explode(' ', $excerpt, $length + 1);
    
    if (count($words) > $length) {
        array_pop($words);
        $excerpt = implode(' ', $words) . '...';
    }
    
    return $excerpt;
}

/**
 * Вывод рейтинга звёздами
 */
function barpro_star_rating($rating) {
    $rating = intval($rating);
    $output  = '<span class="star-rating" role="img" aria-label="' . $rating . ' из 5">';
    for ($i = 1; $i <= 5; $i++) {
        $cls      = $i <= $rating ? 'star--on' : 'star--off';
        $output  .= '<span class="' . $cls . '" aria-hidden="true">★</span>';
    }
    $output .= '</span>';
    return $output;
}

/**
 * Breadcrumbs
 */
function barpro_breadcrumbs(): void {
    if ( is_front_page() ) return;

    $cpt_labels = [
        'cocktail'   => 'Коктейли',
        'package'    => 'Пакеты',
        'team_member'=> 'Команда',
        'case_study' => 'Кейсы',
        'testimonial'=> 'Отзывы',
        'addon_service' => 'Услуги',
    ];

    $items = [ '<a href="' . esc_url( home_url( '/' ) ) . '">Главная</a>' ];

    if ( is_singular() ) {
        $post_type = get_post_type();

        // Архив CPT если есть
        if ( isset( $cpt_labels[ $post_type ] ) ) {
            $archive_url = get_post_type_archive_link( $post_type );
            if ( $archive_url ) {
                $items[] = '<a href="' . esc_url( $archive_url ) . '">' . esc_html( $cpt_labels[ $post_type ] ) . '</a>';
            } else {
                $items[] = esc_html( $cpt_labels[ $post_type ] );
            }
        }

        $items[] = esc_html( get_the_title() );

    } elseif ( is_post_type_archive() ) {
        $post_type = get_query_var( 'post_type' );
        $items[] = esc_html( $cpt_labels[ $post_type ] ?? post_type_archive_title( '', false ) );

    } elseif ( is_page() ) {
        // Родительские страницы
        $ancestors = get_post_ancestors( get_the_ID() );
        foreach ( array_reverse( $ancestors ) as $ancestor_id ) {
            $items[] = '<a href="' . esc_url( get_permalink( $ancestor_id ) ) . '">'
                . esc_html( get_the_title( $ancestor_id ) ) . '</a>';
        }
        $items[] = esc_html( get_the_title() );

    } elseif ( is_archive() ) {
        $items[] = esc_html( get_the_archive_title() );

    } elseif ( is_search() ) {
        $items[] = 'Поиск: ' . esc_html( get_search_query() );

    } elseif ( is_404() ) {
        $items[] = 'Страница не найдена';
    }

    echo '<nav class="breadcrumbs" aria-label="Хлебные крошки">';
    echo implode( ' <span aria-hidden="true">/</span> ', $items );
    echo '</nav>';
}

/**
 * Навигация для постов
 */
function barpro_post_navigation() {
    the_posts_navigation([
        'prev_text' => '<span class="nav-subtitle">Предыдущая</span>',
        'next_text' => '<span class="nav-subtitle">Следующая</span>',
        'screen_reader_text' => 'Навигация по записям'
    ]);
}

/**
 * Класс для body в зависимости от страницы
 */
function barpro_body_classes($classes) {
    // Добавляем класс для типа страницы
    if (is_singular('cocktail')) {
        $classes[] = 'single-cocktail';
    }
    if ( is_singular( 'case_study' ) ) {
        $classes[] = 'single-case';
    }
    if (is_page_template('page-calculator.php')) {
        $classes[] = 'page-calculator';
    }
    
    return $classes;
}
add_filter('body_class', 'barpro_body_classes');

/**
 * Количество слов в посте (корректно для кириллицы — S13 fix).
 * str_word_count считает только Latin-символы, для русского возвращает 0.
 * Используем preg_split с Unicode-флагом.
 */
function barpro_reading_time(): string {
    $content    = get_post_field( 'post_content', get_the_ID() );
    $text       = wp_strip_all_tags( $content );
    $words      = preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
    $word_count = $words ? count( $words ) : 0;
    $minutes    = max( 1, (int) ceil( $word_count / 200 ) ); // 200 слов/мин

    return $minutes . ' мин. чтения';
}

/**
 * Социальные кнопки для шаринга
 */
function barpro_social_share(): void {
    $url   = rawurlencode( get_permalink() );
    $title = rawurlencode( get_the_title() );

    echo '<div class="social-share">';
    echo '<span>Поделиться:</span>';
    printf(
        '<a href="%s" target="_blank" rel="noopener noreferrer">VK</a>',
        esc_url( 'https://vk.com/share.php?url=' . $url )
    );
    printf(
        '<a href="%s" target="_blank" rel="noopener noreferrer">Telegram</a>',
        esc_url( 'https://telegram.me/share/url?url=' . $url . '&text=' . $title )
    );
    printf(
        '<a href="%s" target="_blank" rel="noopener noreferrer">WhatsApp</a>',
        esc_url( 'https://api.whatsapp.com/send?text=' . $title . '+' . $url )
    );
    echo '</div>';
}

/**
 * Получить URL placeholder изображения
 */
function barpro_placeholder_image() {
    return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect fill="%23d4af37" width="400" height="300"/%3E%3Ctext fill="%230a0a0a" font-family="sans-serif" font-size="30" dy="10.5" font-weight="bold" x="50%25" y="50%25" text-anchor="middle"%3EBarPro%3C/text%3E%3C/svg%3E';
}

/**
 * Вывод миниатюры с fallback
 */
function barpro_post_thumbnail( string $size = 'thumbnail', array $attr = [] ): void {
    if ( has_post_thumbnail() ) {
        the_post_thumbnail( $size, $attr );
    } else {
        printf(
            '<img src="%s" alt="%s" loading="lazy" decoding="async">',
            esc_url( barpro_placeholder_image() ),
            esc_attr( get_the_title() )
        );
    }
}

/**
 * Pagination для архивов
 */
function barpro_pagination() {
    global $wp_query;
    
    if ($wp_query->max_num_pages <= 1) {
        return;
    }
    
    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max = intval($wp_query->max_num_pages);
    
    if ($paged >= 1) {
        $links[] = $paged;
    }
    
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }
    
    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }
    
    echo '<div class="pagination"><ul>' . "\n";
    
    if ($paged > 1) {
        echo '<li><a href="' . get_pagenum_link($paged - 1) . '">← Назад</a></li>';
    }
    
    if (!in_array(1, $links)) {
        $class = 1 == $paged ? ' class="active"' : '';
        echo '<li' . $class . '><a href="' . esc_url(get_pagenum_link(1)) . '">1</a></li>';
        
        if (!in_array(2, $links)) {
            echo '<li>...</li>';
        }
    }
    
    sort($links);
    foreach ((array) $links as $link) {
        $class = $paged == $link ? ' class="active"' : '';
        echo '<li' . $class . '><a href="' . esc_url(get_pagenum_link($link)) . '">' . $link . '</a></li>';
    }
    
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links)) {
            echo '<li>...</li>';
        }
        
        $class = $paged == $max ? ' class="active"' : '';
        echo '<li' . $class . '><a href="' . esc_url(get_pagenum_link($max)) . '">' . $max . '</a></li>';
    }
    
    if ($paged < $max) {
        echo '<li><a href="' . get_pagenum_link($paged + 1) . '">Вперёд →</a></li>';
    }
    
    echo '</ul></div>' . "\n";
}
