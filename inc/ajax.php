<?php
/**
 * BarPro Premium — AJAX handlers
 *
 * Включает:
 *  - Honeypot anti-spam
 *  - Async email через WP Action Scheduler / wp_schedule_single_event
 *  - Rate limiting (счётчик)
 *  - Полная валидация
 *
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Запрет кэширования AJAX-ответов (Fix 5).
 *
 * LiteSpeed Cache, WP Rocket, FastCGI Cache и Nginx FastCGI могут кэшировать
 * ответы admin-ajax.php, если не выставлены явные заголовки no-store.
 *
 * WordPress сам вызывает nocache_headers() внутри wp-admin/admin-ajax.php,
 * но некоторые серверные кэши игнорируют Cache-Control и смотрят на Pragma.
 * Дублируем здесь для надёжности на уровне темы.
 */
add_action( 'wp_ajax_calculate_price',        'barpro_ajax_no_cache', 1 );
add_action( 'wp_ajax_nopriv_calculate_price', 'barpro_ajax_no_cache', 1 );
add_action( 'wp_ajax_save_lead',              'barpro_ajax_no_cache', 1 );
add_action( 'wp_ajax_nopriv_save_lead',       'barpro_ajax_no_cache', 1 );

function barpro_ajax_no_cache(): void {
    // Полный стек заголовков против кэширования
    header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
    header( 'Pragma: no-cache' );
    header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
    // LiteSpeed-специфичный заголовок
    header( 'X-LiteSpeed-Cache-Control: no-cache' );
}



/**
 * Тарифные константы калькулятора.
 * Значения редактируются в Customizer → Калькулятор.
 * Fallback — умолчания, соответствующие базовому прайсу.
 *
 *   Формула: (guests × GUESTS_MULTIPLIER × PRICE_PER_GUEST) + (hours × PRICE_PER_HOUR)
 */
function barpro_price_config(): array {
    return [
        'guests_multiplier' => (int) get_theme_mod( 'barpro_price_guests_multiplier', 2 ),
        'price_per_guest'   => (int) get_theme_mod( 'barpro_price_per_guest',         450 ),
        'price_per_hour'    => (int) get_theme_mod( 'barpro_price_per_hour',          3000 ),
        'discount_pct'      => (int) get_theme_mod( 'barpro_price_discount_pct',      10 ),
        'guests_min'        => (int) get_theme_mod( 'barpro_price_guests_min',        10 ),
        'guests_max'        => (int) get_theme_mod( 'barpro_price_guests_max',        300 ),
        'hours_min'         => (int) get_theme_mod( 'barpro_price_hours_min',         2 ),
        'hours_max'         => (int) get_theme_mod( 'barpro_price_hours_max',         12 ),
    ];
}

add_action( 'wp_ajax_calculate_price',        'barpro_calculate_price' );
add_action( 'wp_ajax_nopriv_calculate_price', 'barpro_calculate_price' );

function barpro_calculate_price(): void {
    check_ajax_referer( 'barpro_nonce', 'nonce' );
    barpro_check_rate_limit( 'calc' ); // завершает через wp_die при превышении лимита

    $cfg    = barpro_price_config();
    $guests = intval( $_POST['guests'] ?? 0 );
    $hours  = intval( $_POST['hours']  ?? 0 );

    if ( $guests < $cfg['guests_min'] || $guests > $cfg['guests_max'] ) {
        wp_send_json_error( [
            'message' => sprintf( 'Гостей должно быть от %d до %d', $cfg['guests_min'], $cfg['guests_max'] )
        ], 400 );
    }
    if ( $hours < $cfg['hours_min'] || $hours > $cfg['hours_max'] ) {
        wp_send_json_error( [
            'message' => sprintf( 'Длительность от %d до %d часов', $cfg['hours_min'], $cfg['hours_max'] )
        ], 400 );
    }

    $services       = isset( $_POST['services'] ) ? array_map( 'intval', (array) $_POST['services'] ) : [];
    $base_price     = ( $guests * $cfg['guests_multiplier'] * $cfg['price_per_guest'] )
                    + ( $hours  * $cfg['price_per_hour'] );
    $services_price = 0;

    foreach ( $services as $service_id ) {
        $services_price += intval( get_post_meta( $service_id, '_service_price', true ) );
    }

    $total    = $base_price + $services_price;
    $discount = $total * ( $cfg['discount_pct'] / 100 );
    $final    = $total - $discount;

    wp_send_json_success( [
        'original' => intval( $total ),
        'discount' => intval( $discount ),
        'final'    => intval( $final ),
    ] );
}

/* ──────────────────────────────────────────────────────────
   SAVE LEAD
   ────────────────────────────────────────────────────────── */

add_action( 'wp_ajax_save_lead',        'barpro_save_lead' );
add_action( 'wp_ajax_nopriv_save_lead', 'barpro_save_lead' );

function barpro_save_lead(): void {
    check_ajax_referer( 'barpro_nonce', 'nonce' );
    barpro_check_rate_limit( 'lead' ); // завершает через wp_die при превышении лимита

    /* ── 1. HONEYPOT — если заполнено, это бот ─────────── */
    if ( ! empty( $_POST['website'] ) || ! empty( $_POST['hp_name'] ) ) {
        // Отвечаем «успехом» чтобы бот не знал, что был пойман
        wp_send_json_success( [ 'message' => 'Заявка принята!', 'lead_id' => 0 ] );
    }

    /* ── 2. Валидация email ────────────────────────────── */
    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => 'Некорректный email адрес' ], 400 );
    }

    /* ── 3. Время заполнения формы — слишком быстро = бот */
    $form_start = intval( $_POST['_form_time'] ?? 0 );
    if ( $form_start > 0 && ( time() - $form_start ) < 3 ) {
        wp_send_json_success( [ 'message' => 'Заявка принята!', 'lead_id' => 0 ] );
    }

    /* ── 4. Sanitize данных ────────────────────────────── */
    $name    = sanitize_text_field( $_POST['name']    ?? 'Лид из калькулятора' );
    $phone   = sanitize_text_field( $_POST['phone']   ?? '' );
    $message = sanitize_textarea_field( $_POST['message'] ?? '' );
    $source  = sanitize_text_field( $_POST['source']  ?? 'website' );

    /* ── 5. Парсим JSON из калькулятора ────────────────── */
    $service_names = [
        'bar'      => 'Бар',
        'catering' => 'Кейтеринг',
        'combo'    => 'Комбо',
    ];

    $calc_data    = json_decode( $message, true );
    $is_calc_lead = json_last_error() === JSON_ERROR_NONE && is_array( $calc_data );

    if ( $is_calc_lead ) {
        $service_type = sanitize_text_field( $calc_data['serviceType'] ?? 'bar' );
        $guests       = intval( $calc_data['guests'] ?? 0 );
        $hours        = intval( $calc_data['hours']  ?? 0 );
        $services_raw = isset( $calc_data['services'] ) ? json_encode( $calc_data['services'] ) : '';
        $prices       = $calc_data['prices'] ?? [];
        $total_price  = intval( $prices['final'] ?? 0 );
        $post_title   = sprintf(
            '%s — %s (%d гостей, %d ч) — %s₽',
            $email,
            $service_names[ $service_type ] ?? $service_type,
            $guests, $hours,
            number_format( $total_price, 0, ',', ' ' )
        );
    } else {
        $service_type = '';
        $guests       = 0;
        $hours        = 0;
        $services_raw = '';
        $total_price  = 0;
        $post_title   = 'Лид: ' . $email;
    }

    /* ── 6. Сохранить в БД ─────────────────────────────── */
    $lead_id = wp_insert_post( [
        'post_title'   => $post_title,
        'post_content' => $message,
        'post_type'    => 'lead',
        'post_status'  => 'publish',
        'meta_input'   => [
            '_lead_email'        => $email,
            '_lead_name'         => $name,
            '_lead_phone'        => $phone,
            '_lead_date'         => current_time( 'mysql' ),
            '_lead_service_type' => $service_type,
            '_lead_guests'       => $guests,
            '_lead_hours'        => $hours,
            '_lead_total_price'  => $total_price,
            '_lead_services'     => $services_raw,
            '_lead_source'       => $source,
            '_lead_status'       => 'new',
        ],
    ], true );

    if ( is_wp_error( $lead_id ) ) {
        error_log( 'BarPro: wp_insert_post failed: ' . $lead_id->get_error_message() );
        wp_send_json_error( [ 'message' => 'Ошибка сохранения заявки' ], 500 );
    }

    /* ── 7. Async email — не блокирует AJAX-ответ ───────── */
    barpro_schedule_lead_email( $lead_id, [
        'email'        => $email,
        'name'         => $name,
        'phone'        => $phone,
        'service_type' => $service_type,
        'service_name' => $service_names[ $service_type ] ?? $service_type,
        'guests'       => $guests,
        'hours'        => $hours,
        'total_price'  => $total_price,
        'message'      => $message,
        'is_calc'      => $is_calc_lead,
    ] );

    /* ── 8. Ответ клиенту (email уже ставится в очередь) ── */
    wp_send_json_success( [
        'message' => 'Заявка принята! Мы свяжемся с вами в течение 5 минут.',
        'lead_id' => $lead_id,
    ] );
}

/* ──────────────────────────────────────────────────────────
   ASYNC EMAIL — ставим в очередь, выполняем позже
   ────────────────────────────────────────────────────────── */

/**
 * Запланировать отправку email через wp_schedule_single_event.
 * Выполнится при следующем WP Cron (обычно < 1 мин).
 * Не блокирует текущий AJAX-запрос.
 */
function barpro_schedule_lead_email( int $lead_id, array $data ): void {
    // Сохраняем данные в transient — cron подхватит
    set_transient( 'barpro_lead_email_' . $lead_id, $data, 10 * MINUTE_IN_SECONDS );

    wp_schedule_single_event( time() + 5, 'barpro_send_lead_email', [ $lead_id ] );
}
add_action( 'barpro_send_lead_email', 'barpro_do_send_lead_email' );

/**
 * Реальная отправка email (выполняется в cron-контексте).
 */
function barpro_do_send_lead_email( int $lead_id ): void {
    $data = get_transient( 'barpro_lead_email_' . $lead_id );
    if ( ! $data ) {
        // fallback — перечитать из мета
        $data = [
            'email'       => get_post_meta( $lead_id, '_lead_email', true ),
            'name'        => get_post_meta( $lead_id, '_lead_name',  true ),
            'phone'       => get_post_meta( $lead_id, '_lead_phone', true ),
            'service_name'=> get_post_meta( $lead_id, '_lead_service_type', true ),
            'guests'      => get_post_meta( $lead_id, '_lead_guests', true ),
            'hours'       => get_post_meta( $lead_id, '_lead_hours',  true ),
            'total_price' => get_post_meta( $lead_id, '_lead_total_price', true ),
            'message'     => '',
            'is_calc'     => false,
        ];
    }

    $admin_email = get_option( 'admin_email' );
    $site_name   = get_bloginfo( 'name' );

    /* Тема письма */
    $subject = sprintf(
        '[%s] Новый лид #%d — %s',
        $site_name,
        $lead_id,
        sanitize_text_field( $data['email'] )
    );

    /* Тело письма */
    if ( $data['is_calc'] ?? false ) {
        $body = sprintf(
            "Новый лид из калькулятора:\n\n"
            . "Email:       %s\n"
            . "Имя:         %s\n"
            . "Телефон:     %s\n\n"
            . "Тип услуги:  %s\n"
            . "Гостей:      %d\n"
            . "Часов:       %d\n"
            . "Итого:       %s ₽\n\n"
            . "Детали: %s\n\n"
            . "Редактировать лид: %s",
            sanitize_email( $data['email'] ),
            sanitize_text_field( $data['name']         ?? '' ),
            sanitize_text_field( $data['phone']        ?? 'не указан' ),
            sanitize_text_field( $data['service_name'] ?? '' ),
            intval( $data['guests'] ),
            intval( $data['hours'] ),
            number_format( intval( $data['total_price'] ), 0, ',', ' ' ),
            sanitize_textarea_field( $data['message'] ?? '' ),
            get_edit_post_link( $lead_id, 'raw' ) ?? admin_url( 'post.php?post=' . $lead_id . '&action=edit' )
        );
    } else {
        $body = sprintf(
            "Новый контактный лид:\n\nEmail: %s\nИмя: %s\nТелефон: %s\nСообщение: %s\n\nРедактировать: %s",
            sanitize_email( $data['email'] ),
            sanitize_text_field( $data['name']  ?? '' ),
            sanitize_text_field( $data['phone'] ?? '' ),
            sanitize_textarea_field( $data['message'] ?? '' ),
            get_edit_post_link( $lead_id, 'raw' ) ?? ''
        );
    }

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $site_name . ' <' . $admin_email . '>',
    ];

    $sent = wp_mail( $admin_email, $subject, $body, $headers );

    if ( ! $sent ) {
        error_log( 'BarPro: async email failed for lead #' . $lead_id );
        // Повторить через 2 минуты (один retry)
        if ( ! get_transient( 'barpro_email_retry_' . $lead_id ) ) {
            set_transient( 'barpro_email_retry_' . $lead_id, 1, 5 * MINUTE_IN_SECONDS );
            wp_schedule_single_event( time() + 120, 'barpro_send_lead_email', [ $lead_id ] );
        }
    } else {
        // Пометить как отправлено
        update_post_meta( $lead_id, '_lead_email_sent', current_time( 'mysql' ) );
        delete_transient( 'barpro_lead_email_' . $lead_id );
        delete_transient( 'barpro_email_retry_' . $lead_id );
    }
}
