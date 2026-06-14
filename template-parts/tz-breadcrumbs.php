<?php
/**
 * Breadcrumbs (хлебные крошки) + Schema.org BreadcrumbList
 *
 * @package BarPro_Premium
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$items = $args['items'] ?? [];
if ( empty( $items ) ) {
    // авто-сборка
    $items = [ [ 'label' => 'Главная', 'url' => home_url( '/' ) ] ];
    if ( is_page() ) {
        $items[] = [ 'label' => get_the_title(), 'url' => get_permalink() ];
    } elseif ( is_singular() ) {
        $items[] = [ 'label' => get_post_type_object( get_post_type() )->labels->name ?? '', 'url' => get_post_type_archive_link( get_post_type() ) ?: home_url( '/' ) ];
        $items[] = [ 'label' => get_the_title(), 'url' => get_permalink() ];
    } elseif ( is_post_type_archive() ) {
        $items[] = [ 'label' => post_type_archive_title( '', false ), 'url' => '' ];
    }
}

if ( count( $items ) < 2 ) return;

// JSON-LD
$ld = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [],
];
foreach ( $items as $i => $it ) {
    $ld['itemListElement'][] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'name'     => $it['label'],
        'item'     => $it['url'] ?: null,
    ];
}
?>
<nav class="tz-breadcrumbs" aria-label="Хлебные крошки">
    <?php foreach ( $items as $i => $it ) :
        $is_last = $i === count( $items ) - 1; ?>
        <?php if ( ! $is_last && $it['url'] ) : ?>
            <a href="<?php echo esc_url( $it['url'] ); ?>"><?php echo esc_html( $it['label'] ); ?></a>
            <span class="tz-breadcrumbs__sep" aria-hidden="true">/</span>
        <?php else : ?>
            <span aria-current="page"><?php echo esc_html( $it['label'] ); ?></span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
<script type="application/ld+json"><?php echo wp_json_encode( $ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
