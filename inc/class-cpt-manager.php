<?php
/**
 * BarPro CPT Manager — Class-based архитектура
 *
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Менеджер Custom Post Types.
 * Регистрирует все CPT темы и предоставляет хелперы.
 */
class BarPro_CPT_Manager {

    /**
     * Зарегистрированные CPT.
     * @var array
     */
    private $post_types = [];

    /**
     * Singleton instance.
     * @var BarPro_CPT_Manager|null
     */
    private static $instance = null;

    /**
     * Получить инстанс.
     */
    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Приватный конструктор.
     */
    private function __construct() {
        add_action( 'init', [ $this, 'register_all' ] );
        add_action( 'init', [ $this, 'register_taxonomies' ] );
    }

    /**
     * Зарегистрировать все CPT.
     */
    public function register_all(): void {
        $this->register( 'cocktail', [
            'singular' => 'Коктейль',
            'plural'   => 'Коктейли',
            'slug'     => 'cocktails',
            'icon'     => 'dashicons-coffee',
            'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'has_archive' => true,
            'show_in_rest' => true,
        ] );

        $this->register( 'package', [
            'singular' => 'Пакет',
            'plural'   => 'Пакеты',
            'slug'     => 'packages',
            'icon'     => 'dashicons-products',
            'supports' => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
            'has_archive' => true,
            'show_in_rest' => true,
        ] );

        $this->register( 'team_member', [
            'singular' => 'Участник команды',
            'plural'   => 'Команда',
            'slug'     => 'team',
            'icon'     => 'dashicons-businessman',
            'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
            'has_archive' => false,
            'show_in_rest' => true,
        ] );

        $this->register( 'case_study', [
            'singular' => 'Кейс',
            'plural'   => 'Кейсы',
            'slug'     => 'cases',
            'icon'     => 'dashicons-portfolio',
            'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'has_archive' => true,
            'show_in_rest' => true,
        ] );

        $this->register( 'testimonial', [
            'singular' => 'Отзыв',
            'plural'   => 'Отзывы',
            'slug'     => 'testimonials',
            'icon'     => 'dashicons-format-quote',
            'supports' => [ 'title', 'editor', 'thumbnail' ],
            'has_archive' => false,
            'show_in_rest' => true,
        ] );

        $this->register( 'addon_service', [
            'singular' => 'Доп. услуга',
            'plural'   => 'Доп. услуги',
            'slug'     => 'services',
            'icon'     => 'dashicons-star-filled',
            'supports' => [ 'title', 'editor', 'thumbnail' ],
            'has_archive' => false,
            'show_in_rest' => true,
        ] );

        $this->register( 'lead', [
            'singular'     => 'Лид',
            'plural'       => 'Лиды',
            'slug'         => 'leads',
            'icon'         => 'dashicons-email-alt',
            'supports'     => [ 'title', 'editor' ],
            'has_archive'  => false,
            'show_in_rest' => false,
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
        ] );
    }

    /**
     * Зарегистрировать таксономии.
     */
    public function register_taxonomies(): void {
        // Категории коктейлей
        register_taxonomy( 'cocktail_type', 'cocktail', [
            'label'             => 'Тип коктейля',
            'hierarchical'      => false,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => [ 'slug' => 'cocktail-type' ],
        ] );

        // Тип мероприятия для кейсов
        register_taxonomy( 'event_type', 'case_study', [
            'label'             => 'Тип мероприятия',
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => [ 'slug' => 'event-type' ],
        ] );
    }

    /**
     * Зарегистрировать один CPT.
     *
     * @param string $slug Slug CPT.
     * @param array  $config Конфигурация.
     */
    private function register( string $slug, array $config ): void {
        $this->post_types[ $slug ] = $config;

        $labels = [
            'name'               => $config['plural'],
            'singular_name'      => $config['singular'],
            'add_new'            => 'Добавить',
            'add_new_item'       => 'Добавить ' . $config['singular'],
            'edit_item'          => 'Редактировать ' . $config['singular'],
            'new_item'           => 'Новый ' . $config['singular'],
            'view_item'          => 'Просмотр',
            'search_items'       => 'Поиск',
            'not_found'          => 'Не найдено',
            'not_found_in_trash' => 'Корзина пуста',
            'menu_name'          => $config['plural'],
        ];

        $args = [
            'labels'       => $labels,
            'public'       => $config['public'] ?? true,
            'show_ui'      => $config['show_ui'] ?? true,
            'show_in_menu' => $config['show_in_menu'] ?? true,
            'show_in_rest' => $config['show_in_rest'] ?? true,
            'has_archive'  => $config['has_archive'] ?? false,
            'rewrite'      => [ 'slug' => $config['slug'] ],
            'supports'     => $config['supports'] ?? [ 'title', 'editor', 'thumbnail' ],
            'menu_icon'    => $config['icon'] ?? 'dashicons-admin-post',
            'menu_position'=> 25,
        ];

        register_post_type( $slug, $args );
    }

    /**
     * Получить список зарегистрированных CPT.
     * @return array
     */
    public function get_post_types(): array {
        return array_keys( $this->post_types );
    }

    /**
     * Сбросить кеш главной страницы.
     * Вызывается при save_post И при REST API insert/update.
     */
    public function flush_caches( int $post_id, \WP_Post $post ): void {
        if ( ! in_array( $post->post_type, $this->get_post_types(), true ) ) {
            return;
        }
        $this->do_flush();
    }

    /**
     * Сброс для REST API (rest_after_insert_{$post_type}).
     */
    public function flush_caches_rest( \WP_Post $post ): void {
        if ( ! in_array( $post->post_type, $this->get_post_types(), true ) ) {
            return;
        }
        $this->do_flush();
    }

    /**
     * Физический сброс всех transient-кешей.
     */
    private function do_flush(): void {
        delete_transient( 'barpro_home_packages' );
        delete_transient( 'barpro_home_cocktails' );
        delete_transient( 'barpro_home_team' );
        delete_transient( 'barpro_home_testimonials' );
    }
}

// ── Хуки ──────────────────────────────────────────────────
$barpro_cpt = BarPro_CPT_Manager::instance();

// Сброс при сохранении в классическом редакторе
add_action( 'save_post', [ $barpro_cpt, 'flush_caches' ], 10, 2 );

// Сброс при создании/обновлении через REST API (Gutenberg, мобильное приложение, WP CLI)
foreach ( [ 'cocktail', 'package', 'team_member', 'case_study', 'testimonial', 'addon_service' ] as $_rest_cpt ) {
    add_action( "rest_after_insert_{$_rest_cpt}", [ $barpro_cpt, 'flush_caches_rest' ] );
    add_action( "rest_after_delete_{$_rest_cpt}", [ $barpro_cpt, 'flush_caches_rest' ] );
}
