<?php
/**
 * BarPro Premium — META module
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Метабоксы для лидов
 */
function barpro_lead_meta_boxes() {
    add_meta_box(
        'lead_details',
        'Детали лида',
        'barpro_lead_meta_box_callback',
        'lead',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'barpro_lead_meta_boxes');

function barpro_lead_meta_box_callback( $post ) {
    // Nonce для status update
    wp_nonce_field( 'barpro_lead_status', 'barpro_lead_nonce' );

    $email       = get_post_meta( $post->ID, '_lead_email',       true );
    $phone       = get_post_meta( $post->ID, '_lead_phone',       true );
    $service_type= get_post_meta( $post->ID, '_lead_service_type',true );
    $guests      = get_post_meta( $post->ID, '_lead_guests',      true );
    $hours       = get_post_meta( $post->ID, '_lead_hours',       true );
    $total_price = get_post_meta( $post->ID, '_lead_total_price', true );
    $services    = get_post_meta( $post->ID, '_lead_services',    true );
    $source      = get_post_meta( $post->ID, '_lead_source',      true );
    $date        = get_post_meta( $post->ID, '_lead_date',        true );
    $status      = get_post_meta( $post->ID, '_lead_status',      true ) ?: 'new';

    $status_labels = [
        'new'       => 'Новый',
        'contacted' => 'Контактирован',
        'converted' => 'Конверсия',
        'rejected'  => 'Отклонён',
    ];
    ?>
    <table class="form-table">
        <tr>
            <th><strong>Email:</strong></th>
            <td>
                <a href="mailto:<?php echo esc_attr( $email ); ?>">
                    <?php echo esc_html( $email ); ?>
                </a>
            </td>
        </tr>
        <tr>
            <th><strong>Телефон:</strong></th>
            <td><?php echo esc_html( $phone ?: 'Не указан' ); ?></td>
        </tr>
        <tr>
            <th><strong>Тип услуги:</strong></th>
            <td><?php
                $types = [ 'bar' => 'Только бар', 'catering' => 'Только кейтеринг', 'combo' => 'Бар + Кейтеринг' ];
                echo esc_html( $types[ $service_type ] ?? $service_type );
            ?></td>
        </tr>
        <tr>
            <th><strong>Гостей:</strong></th>
            <td><?php echo esc_html( $guests ); ?></td>
        </tr>
        <tr>
            <th><strong>Часов:</strong></th>
            <td><?php echo esc_html( $hours ); ?></td>
        </tr>
        <tr>
            <th><strong>Итоговая цена:</strong></th>
            <td>
                <strong style="color:#d4af37;font-size:1.3rem;">
                    <?php echo number_format( (int) $total_price, 0, ',', ' ' ); ?>₽
                </strong>
            </td>
        </tr>
        <?php if ( $services ) : ?>
        <tr>
            <th><strong>Доп. услуги:</strong></th>
            <td><?php echo esc_html( $services ); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><strong>Источник:</strong></th>
            <td><?php echo esc_html( $source ?: 'Неизвестно' ); ?></td>
        </tr>
        <tr>
            <th><strong>Дата:</strong></th>
            <td><?php echo esc_html( $date ); ?></td>
        </tr>
        <tr>
            <th><label for="lead_status"><strong>Статус:</strong></label></th>
            <td>
                <select name="lead_status" id="lead_status">
                    <?php foreach ( $status_labels as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>"
                            <?php selected( $status, $value ); ?>>
                            <?php echo esc_html( $label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Сохранить статус лида (с nonce + capability check).
 */
function barpro_save_lead_status( int $post_id ): void {
    if ( ! isset( $_POST['barpro_lead_nonce'] )
        || ! wp_verify_nonce( $_POST['barpro_lead_nonce'], 'barpro_lead_status' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['lead_status'] ) ) {
        $allowed = [ 'new', 'contacted', 'converted', 'rejected' ];
        $status  = sanitize_text_field( $_POST['lead_status'] );
        if ( in_array( $status, $allowed, true ) ) {
            update_post_meta( $post_id, '_lead_status', $status );
        }
    }
}
add_action( 'save_post_lead', 'barpro_save_lead_status' );

/**
 * Метабоксы для коктейлей
 */
function barpro_cocktail_meta_boxes() {
    add_meta_box(
        'cocktail_details',
        'Детали коктейля',
        'barpro_cocktail_meta_box_callback',
        'cocktail',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'barpro_cocktail_meta_boxes');

/**
 * Метабоксы для форматов кейтеринга
 */
function barpro_catering_meta_boxes() {
    add_meta_box(
        'catering_format_details',
        'Детали формата',
        'barpro_catering_format_meta_box_callback',
        'catering_format',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'barpro_catering_meta_boxes');

function barpro_catering_format_meta_box_callback($post) {
    wp_nonce_field('barpro_catering_meta', 'barpro_catering_nonce');
    
    $price_per_person = get_post_meta($post->ID, '_format_price_per_person', true);
    $min_guests = get_post_meta($post->ID, '_format_min_guests', true);
    $icon = get_post_meta($post->ID, '_format_icon', true);
    ?>
    <p>
        <label><strong>Цена за человека (₽):</strong></label><br>
        <input type="number" name="format_price_per_person" value="<?php echo esc_attr($price_per_person); ?>" style="width: 200px;">
    </p>
    <p>
        <label><strong>Минимум гостей:</strong></label><br>
        <input type="number" name="format_min_guests" value="<?php echo esc_attr($min_guests); ?>" style="width: 200px;">
    </p>
    <p>
        <label><strong>Иконка (emoji):</strong></label><br>
        <input type="text" name="format_icon" value="<?php echo esc_attr($icon); ?>" placeholder="🍽️" style="width: 200px;">
    </p>
    <?php
}

function barpro_save_catering_format_meta($post_id) {
    if (!isset($_POST['barpro_catering_nonce']) || !wp_verify_nonce($_POST['barpro_catering_nonce'], 'barpro_catering_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['format_price_per_person'])) {
        update_post_meta($post_id, '_format_price_per_person', intval($_POST['format_price_per_person']));
    }
    
    if (isset($_POST['format_min_guests'])) {
        update_post_meta($post_id, '_format_min_guests', intval($_POST['format_min_guests']));
    }
    
    if (isset($_POST['format_icon'])) {
        update_post_meta($post_id, '_format_icon', sanitize_text_field($_POST['format_icon']));
    }
}
add_action('save_post_catering_format', 'barpro_save_catering_format_meta');

function barpro_cocktail_meta_box_callback($post) {
    wp_nonce_field('barpro_cocktail_meta', 'barpro_cocktail_nonce');
    
    $composition = get_post_meta($post->ID, '_cocktail_composition', true);
    $is_alcoholic = get_post_meta($post->ID, '_cocktail_is_alcoholic', true);
    $strength = get_post_meta($post->ID, '_cocktail_strength', true);
    $volume = get_post_meta($post->ID, '_cocktail_volume_ml', true);
    $price = get_post_meta($post->ID, '_cocktail_price', true);
    ?>
    <p>
        <label><strong>Состав:</strong></label><br>
        <textarea name="cocktail_composition" rows="3" style="width:100%"><?php echo esc_textarea($composition); ?></textarea>
    </p>
    <p>
        <label>
            <input type="checkbox" name="cocktail_is_alcoholic" value="1" <?php checked($is_alcoholic, '1'); ?>>
            Алкогольный
        </label>
    </p>
    <p>
        <label><strong>Крепость:</strong></label><br>
        <input type="text" name="cocktail_strength" value="<?php echo esc_attr($strength); ?>" placeholder="Например: Средняя, Лёгкая">
    </p>
    <p>
        <label><strong>Объём (мл):</strong></label><br>
        <input type="number" name="cocktail_volume_ml" value="<?php echo esc_attr($volume); ?>">
    </p>
    <p>
        <label><strong>Цена (₽):</strong></label><br>
        <input type="number" name="cocktail_price" value="<?php echo esc_attr($price); ?>">
    </p>
    <?php
}

function barpro_save_cocktail_meta($post_id) {
    if (!isset($_POST['barpro_cocktail_nonce']) || !wp_verify_nonce($_POST['barpro_cocktail_nonce'], 'barpro_cocktail_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['cocktail_composition'])) {
        update_post_meta($post_id, '_cocktail_composition', sanitize_textarea_field($_POST['cocktail_composition']));
    }
    
    update_post_meta($post_id, '_cocktail_is_alcoholic', isset($_POST['cocktail_is_alcoholic']) ? '1' : '0');
    
    if (isset($_POST['cocktail_strength'])) {
        update_post_meta($post_id, '_cocktail_strength', sanitize_text_field($_POST['cocktail_strength']));
    }
    
    if (isset($_POST['cocktail_volume_ml'])) {
        update_post_meta($post_id, '_cocktail_volume_ml', intval($_POST['cocktail_volume_ml']));
    }
    
    if (isset($_POST['cocktail_price'])) {
        update_post_meta($post_id, '_cocktail_price', intval($_POST['cocktail_price']));
    }
}
add_action('save_post_cocktail', 'barpro_save_cocktail_meta');

/* ============================================================
   C7 FIX: Метабоксы для package, team_member, testimonial, addon_service
   Ключи соответствуют get_post_meta() вызовам в шаблонах.
   ============================================================ */

/**
 * ПАКЕТЫ (CPT: package)
 * Ключи: _package_price, _package_cocktails, _package_bartenders, _package_hours, _package_featured
 */
function barpro_package_meta_boxes(): void {
    add_meta_box( 'package_details', 'Детали пакета', 'barpro_package_meta_box_cb', 'package', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'barpro_package_meta_boxes' );

function barpro_package_meta_box_cb( \WP_Post $post ): void {
    wp_nonce_field( 'barpro_package_meta', 'barpro_package_nonce' );
    $price      = get_post_meta( $post->ID, '_package_price',      true );
    $cocktails  = get_post_meta( $post->ID, '_package_cocktails',  true );
    $bartenders = get_post_meta( $post->ID, '_package_bartenders', true );
    $hours      = get_post_meta( $post->ID, '_package_hours',      true );
    $featured   = get_post_meta( $post->ID, '_package_featured',   true );
    ?>
    <table class="form-table">
        <tr><th><label for="pkg_price">Цена (₽)</label></th>
            <td><input type="number" id="pkg_price" name="pkg_price" value="<?php echo esc_attr($price); ?>" style="width:180px"></td></tr>
        <tr><th><label for="pkg_cocktails">Коктейлей</label></th>
            <td><input type="number" id="pkg_cocktails" name="pkg_cocktails" value="<?php echo esc_attr($cocktails); ?>" style="width:180px"></td></tr>
        <tr><th><label for="pkg_bartenders">Барменов</label></th>
            <td><input type="number" id="pkg_bartenders" name="pkg_bartenders" value="<?php echo esc_attr($bartenders); ?>" style="width:180px"></td></tr>
        <tr><th><label for="pkg_hours">Часов работы</label></th>
            <td><input type="number" id="pkg_hours" name="pkg_hours" value="<?php echo esc_attr($hours); ?>" style="width:180px"></td></tr>
        <tr><th>Хит продаж</th>
            <td><label><input type="checkbox" name="pkg_featured" value="1" <?php checked($featured, '1'); ?>> Показывать метку «Хит продаж»</label></td></tr>
    </table>
    <?php
}

function barpro_save_package_meta( int $post_id ): void {
    if ( ! isset( $_POST['barpro_package_nonce'] )
        || ! wp_verify_nonce( $_POST['barpro_package_nonce'], 'barpro_package_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta( $post_id, '_package_price',      intval( $_POST['pkg_price']      ?? 0 ) );
    update_post_meta( $post_id, '_package_cocktails',  intval( $_POST['pkg_cocktails']  ?? 0 ) );
    update_post_meta( $post_id, '_package_bartenders', intval( $_POST['pkg_bartenders'] ?? 0 ) );
    update_post_meta( $post_id, '_package_hours',      intval( $_POST['pkg_hours']      ?? 0 ) );
    update_post_meta( $post_id, '_package_featured',   isset( $_POST['pkg_featured'] ) ? '1' : '0' );
}
add_action( 'save_post_package', 'barpro_save_package_meta' );

/**
 * КОМАНДА (CPT: team_member)
 * Ключи: role, experience, specialization (без префикса — совпадают с ACF field name)
 */
function barpro_team_member_meta_boxes(): void {
    // Регистрируем только если ACF не установлен — иначе ACF создаёт свои поля
    if ( function_exists( 'acf_add_local_field_group' ) ) return;
    add_meta_box( 'team_member_details', 'Сотрудник', 'barpro_team_member_meta_box_cb', 'team_member', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'barpro_team_member_meta_boxes' );

function barpro_team_member_meta_box_cb( \WP_Post $post ): void {
    wp_nonce_field( 'barpro_team_meta', 'barpro_team_nonce' );
    $role    = get_post_meta( $post->ID, 'role',           true );
    $exp     = get_post_meta( $post->ID, 'experience',     true );
    $spec    = get_post_meta( $post->ID, 'specialization', true );
    ?>
    <table class="form-table">
        <tr><th><label for="tm_role">Должность</label></th>
            <td><input type="text" id="tm_role" name="tm_role" value="<?php echo esc_attr($role); ?>" style="width:300px"></td></tr>
        <tr><th><label for="tm_exp">Опыт</label></th>
            <td><input type="text" id="tm_exp" name="tm_exp" value="<?php echo esc_attr($exp); ?>" placeholder="например: 7 лет" style="width:300px"></td></tr>
        <tr><th><label for="tm_spec">Специализация</label></th>
            <td><input type="text" id="tm_spec" name="tm_spec" value="<?php echo esc_attr($spec); ?>" style="width:300px"></td></tr>
    </table>
    <?php
}

function barpro_save_team_member_meta( int $post_id ): void {
    if ( ! isset( $_POST['barpro_team_nonce'] )
        || ! wp_verify_nonce( $_POST['barpro_team_nonce'], 'barpro_team_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta( $post_id, 'role',           sanitize_text_field( $_POST['tm_role'] ?? '' ) );
    update_post_meta( $post_id, 'experience',     sanitize_text_field( $_POST['tm_exp']  ?? '' ) );
    update_post_meta( $post_id, 'specialization', sanitize_text_field( $_POST['tm_spec'] ?? '' ) );
}
add_action( 'save_post_team_member', 'barpro_save_team_member_meta' );

/**
 * ОТЗЫВЫ (CPT: testimonial)
 * Используются в шаблонах через _testimonial_author, _testimonial_position, _testimonial_rating
 */
function barpro_testimonial_meta_boxes(): void {
    add_meta_box( 'testimonial_details', 'Детали отзыва', 'barpro_testimonial_meta_box_cb', 'testimonial', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'barpro_testimonial_meta_boxes' );

function barpro_testimonial_meta_box_cb( \WP_Post $post ): void {
    wp_nonce_field( 'barpro_testimonial_meta', 'barpro_testimonial_nonce' );
    $author   = get_post_meta( $post->ID, '_testimonial_author',   true );
    $position = get_post_meta( $post->ID, '_testimonial_position', true );
    $rating   = get_post_meta( $post->ID, '_testimonial_rating',   true ) ?: '5';
    ?>
    <table class="form-table">
        <tr><th><label for="test_author">Имя клиента</label></th>
            <td><input type="text" id="test_author" name="test_author" value="<?php echo esc_attr($author); ?>" style="width:300px"></td></tr>
        <tr><th><label for="test_position">Должность / Компания</label></th>
            <td><input type="text" id="test_position" name="test_position" value="<?php echo esc_attr($position); ?>" style="width:300px"></td></tr>
        <tr><th><label for="test_rating">Рейтинг (1–5)</label></th>
            <td><select id="test_rating" name="test_rating">
                <?php for ($i = 5; $i >= 1; $i--) : ?>
                    <option value="<?php echo $i; ?>" <?php selected($rating, (string)$i); ?>><?php echo $i; ?> ★</option>
                <?php endfor; ?>
            </select></td></tr>
    </table>
    <?php
}

function barpro_save_testimonial_meta( int $post_id ): void {
    if ( ! isset( $_POST['barpro_testimonial_nonce'] )
        || ! wp_verify_nonce( $_POST['barpro_testimonial_nonce'], 'barpro_testimonial_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta( $post_id, '_testimonial_author',   sanitize_text_field( $_POST['test_author']   ?? '' ) );
    update_post_meta( $post_id, '_testimonial_position', sanitize_text_field( $_POST['test_position'] ?? '' ) );
    $rating = intval( $_POST['test_rating'] ?? 5 );
    update_post_meta( $post_id, '_testimonial_rating', min( 5, max( 1, $rating ) ) );
}
add_action( 'save_post_testimonial', 'barpro_save_testimonial_meta' );

/**
 * ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ (CPT: addon_service)
 * Ключи: _service_price, _service_icon
 */
function barpro_addon_service_meta_boxes(): void {
    add_meta_box( 'addon_service_details', 'Детали услуги', 'barpro_addon_service_meta_box_cb', 'addon_service', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'barpro_addon_service_meta_boxes' );

function barpro_addon_service_meta_box_cb( \WP_Post $post ): void {
    wp_nonce_field( 'barpro_addon_meta', 'barpro_addon_nonce' );
    $price = get_post_meta( $post->ID, '_service_price', true );
    $icon  = get_post_meta( $post->ID, '_service_icon',  true );
    ?>
    <table class="form-table">
        <tr><th><label for="svc_price">Цена (₽)</label></th>
            <td><input type="number" id="svc_price" name="svc_price" value="<?php echo esc_attr($price); ?>" style="width:180px"></td></tr>
        <tr><th><label for="svc_icon">Иконка (emoji)</label></th>
            <td><input type="text" id="svc_icon" name="svc_icon" value="<?php echo esc_attr($icon); ?>" placeholder="🎭" style="width:80px"></td></tr>
    </table>
    <?php
}

function barpro_save_addon_service_meta( int $post_id ): void {
    if ( ! isset( $_POST['barpro_addon_nonce'] )
        || ! wp_verify_nonce( $_POST['barpro_addon_nonce'], 'barpro_addon_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta( $post_id, '_service_price', intval( $_POST['svc_price'] ?? 0 ) );
    update_post_meta( $post_id, '_service_icon',  sanitize_text_field( $_POST['svc_icon']  ?? '' ) );
}
add_action( 'save_post_addon_service', 'barpro_save_addon_service_meta' );
