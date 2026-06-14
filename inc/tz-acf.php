<?php
/**
 * BarPro — ACF Pro полей для страниц по ТЗ.
 *
 * Регистрируется через PHP API ACF — если ACF Pro не установлен,
 * страницы продолжают работать со значениями по умолчанию.
 *
 * @package BarPro_Premium
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * ACF Local JSON — автоматическая синхронизация групп полей (Fix 4).
 *
 * ACF сохраняет JSON-схему групп полей в папку acf-json/ при каждом
 * сохранении группы в WP Admin. При переносе на другой сервер или
 * переустановке ACF поля автоматически восстанавливаются из этих файлов
 * без ручного импорта. Папка включена в git и в архив темы.
 *
 * Документация: https://www.advancedcustomfields.com/resources/local-json/
 */
add_filter( 'acf/settings/save_json', function (): string {
    return get_stylesheet_directory() . '/acf-json';
} );

add_filter( 'acf/settings/load_json', function ( array $paths ): array {
    // Убираем дефолтный путь ACF, добавляем путь темы первым
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
} );

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}


/**
 * Универсальный SEO-блок (Title / Description) для всех страниц и CPT
 */
acf_add_local_field_group( [
    'key'      => 'group_tz_seo',
    'title'    => 'SEO',
    'fields'   => [
        [ 'key' => 'field_tz_seo_title',       'label' => 'SEO Title',       'name' => 'seo_title',       'type' => 'text',     'maxlength' => 70 ],
        [ 'key' => 'field_tz_seo_description', 'label' => 'SEO Description', 'name' => 'seo_description', 'type' => 'textarea', 'rows' => 3, 'maxlength' => 170 ],
        [
            'key'   => 'field_tz_faq_items', 'label' => 'FAQ (Schema.org)', 'name' => 'faq_items',
            'type'  => 'repeater', 'layout' => 'block', 'button_label' => 'Добавить вопрос',
            'sub_fields' => [
                [ 'key' => 'field_tz_faq_q', 'label' => 'Вопрос', 'name' => 'question', 'type' => 'text' ],
                [ 'key' => 'field_tz_faq_a', 'label' => 'Ответ',  'name' => 'answer',   'type' => 'textarea', 'rows' => 3 ],
            ],
        ],
    ],
    'location' => [
        [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ] ],
        [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'case_study' ] ],
        [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'cocktail' ] ],
    ],
    'position' => 'normal',
] );

/**
 * Hero / контент страницы /calculator
 */
acf_add_local_field_group( [
    'key'    => 'group_tz_calculator',
    'title'  => 'Калькулятор — Hero',
    'fields' => [
        [ 'key' => 'field_tz_calc_title',    'label' => 'Заголовок Hero',    'name' => 'calc_hero_title',    'type' => 'text' ],
        [ 'key' => 'field_tz_calc_subtitle', 'label' => 'Подзаголовок Hero', 'name' => 'calc_hero_subtitle', 'type' => 'textarea', 'rows' => 2 ],
        [ 'key' => 'field_tz_calc_video',    'label' => 'Видео-фон (URL .mp4)', 'name' => 'calc_hero_video', 'type' => 'url' ],
        [ 'key' => 'field_tz_calc_image',    'label' => 'Фон (изображение)', 'name' => 'calc_hero_image',   'type' => 'image', 'return_format' => 'url' ],
    ],
    'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-calculator.php' ] ] ],
] );

/**
 * Команда — Hero + Основатель
 */
acf_add_local_field_group( [
    'key'    => 'group_tz_team',
    'title'  => 'Команда — Hero и Основатель',
    'fields' => [
        [ 'key' => 'field_tz_team_hero',     'label' => 'Фон Hero',          'name' => 'team_hero_image', 'type' => 'image', 'return_format' => 'url' ],
        [ 'key' => 'field_tz_founder_name',  'label' => 'Имя основателя',    'name' => 'founder_name',    'type' => 'text' ],
        [ 'key' => 'field_tz_founder_role',  'label' => 'Должность',          'name' => 'founder_role',    'type' => 'text' ],
        [ 'key' => 'field_tz_founder_photo', 'label' => 'Фото основателя',   'name' => 'founder_photo',   'type' => 'image', 'return_format' => 'url' ],
        [ 'key' => 'field_tz_founder_story', 'label' => 'История компании',  'name' => 'founder_story',   'type' => 'wysiwyg', 'tabs' => 'visual' ],
        [ 'key' => 'field_tz_founder_mission','label' => 'Миссия',           'name' => 'founder_mission', 'type' => 'textarea', 'rows' => 3 ],
        [ 'key' => 'field_tz_founder_philo', 'label' => 'Философия',         'name' => 'founder_philosophy', 'type' => 'textarea', 'rows' => 3 ],
    ],
    'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-team.php' ] ] ],
] );

/**
 * Поля сотрудников (CPT: team_member)
 */
acf_add_local_field_group( [
    'key'    => 'group_tz_team_member',
    'title'  => 'Сотрудник',
    'fields' => [
        [ 'key' => 'field_tz_tm_role',  'label' => 'Должность',     'name' => 'role',            'type' => 'text' ],
        [ 'key' => 'field_tz_tm_exp',   'label' => 'Опыт',          'name' => 'experience',      'type' => 'text', 'placeholder' => 'например: 7 лет' ],
        [ 'key' => 'field_tz_tm_spec',  'label' => 'Специализация', 'name' => 'specialization',  'type' => 'text' ],
    ],
    'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'team_member' ] ] ],
] );

/**
 * Поля коктейлей (CPT: cocktail)
 */
acf_add_local_field_group( [
    'key'    => 'group_tz_cocktail',
    'title'  => 'Коктейль',
    'fields' => [
        [ 'key' => 'field_tz_ck_ing', 'label' => 'Состав / Ингредиенты', 'name' => 'ingredients', 'type' => 'text' ],
        [
            'key' => 'field_tz_ck_cats', 'label' => 'Категории (если без таксономии)',
            'name' => 'cocktail_categories', 'type' => 'checkbox',
            'choices' => [
                'alcohol'      => 'Алкогольные',
                'non-alcoholic'=> 'Безалкогольные',
                'signature'    => 'Авторские',
                'classic'      => 'Классические',
            ],
        ],
    ],
    'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'cocktail' ] ] ],
] );

/**
 * Hero коктейлей
 */
acf_add_local_field_group( [
    'key'    => 'group_tz_cocktails_page',
    'title'  => 'Коктейли — Hero',
    'fields' => [
        [ 'key' => 'field_tz_co_hero', 'label' => 'Фон Hero', 'name' => 'cocktails_hero_image', 'type' => 'image', 'return_format' => 'url' ],
    ],
    'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-cocktails.php' ] ] ],
] );

/**
 * Кейтеринг — Hero + Галерея
 */
acf_add_local_field_group( [
    'key'    => 'group_tz_catering',
    'title'  => 'Кейтеринг',
    'fields' => [
        [ 'key' => 'field_tz_ct_hero', 'label' => 'Фон Hero', 'name' => 'catering_hero_image', 'type' => 'image', 'return_format' => 'url' ],
        [
            'key' => 'field_tz_ct_gallery', 'label' => 'Галерея блюд',
            'name' => 'catering_gallery', 'type' => 'gallery', 'return_format' => 'array',
        ],
    ],
    'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-catering.php' ] ] ],
] );

/**
 * Бар + Кейтеринг — Hero
 */
acf_add_local_field_group( [
    'key'    => 'group_tz_bc',
    'title'  => 'Бар + Кейтеринг — Hero',
    'fields' => [
        [ 'key' => 'field_tz_bc_hero', 'label' => 'Фон Hero', 'name' => 'bc_hero_image', 'type' => 'image', 'return_format' => 'url' ],
    ],
    'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-bar-catering.php' ] ] ],
] );

/**
 * Кейс (CPT: case_study)
 */
acf_add_local_field_group( [
    'key'    => 'group_tz_case',
    'title'  => 'Кейс',
    'fields' => [
        [ 'key' => 'field_tz_cs_date',     'label' => 'Дата мероприятия', 'name' => 'case_date',     'type' => 'text', 'placeholder' => 'например: июнь 2024' ],
        [ 'key' => 'field_tz_cs_guests',   'label' => 'Количество гостей','name' => 'case_guests',   'type' => 'number' ],
        [ 'key' => 'field_tz_cs_task',     'label' => 'Задача клиента',  'name' => 'case_task',     'type' => 'textarea', 'rows' => 4 ],
        [ 'key' => 'field_tz_cs_solution', 'label' => 'Решение',          'name' => 'case_solution', 'type' => 'textarea', 'rows' => 4 ],
        [ 'key' => 'field_tz_cs_done',     'label' => 'Что было реализовано', 'name' => 'case_done', 'type' => 'wysiwyg', 'tabs' => 'visual' ],
        [ 'key' => 'field_tz_cs_result',   'label' => 'Итоги',            'name' => 'case_result',   'type' => 'textarea', 'rows' => 4 ],
        [ 'key' => 'field_tz_cs_gallery',  'label' => 'Галерея',          'name' => 'case_gallery',  'type' => 'gallery', 'return_format' => 'array' ],
        [ 'key' => 'field_tz_cs_featured', 'label' => 'Показывать на /bar-catering', 'name' => 'is_featured', 'type' => 'true_false', 'ui' => 1 ],
    ],
    'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'case_study' ] ] ],
] );
