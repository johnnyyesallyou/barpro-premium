<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class('studio-theme no-js'); ?>>
<?php wp_body_open(); ?>
<script>document.body.classList.remove('no-js');</script>

<!-- Custom cursor (desktop only) -->
<div class="studio-cursor"        aria-hidden="true"></div>
<div class="studio-cursor-follower" aria-hidden="true"></div>

<!-- Skip link -->
<a class="skip-link screen-reader-text" href="#main-content">Перейти к содержимому</a>

<!-- Nav overlay -->
<div class="studio-nav__overlay" id="navOverlay" aria-hidden="true"></div>

<!-- Mobile drawer -->
<nav class="studio-nav__drawer" id="navDrawer" aria-label="Мобильное меню">
    <?php
    wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => '',
        'fallback_cb'    => function() {
            $links = [
                '/calculator' => 'Рассчитать',
                '/cocktails'  => 'Коктейли',
                '/packages'   => 'Пакеты',
                '/catering'   => 'Кейтеринг',
                '/cases'      => 'Кейсы',
            ];
            foreach ($links as $url => $label) {
                printf('<a href="%s">%s</a>', esc_url(home_url($url)), esc_html($label));
            }
        },
    ]);
    ?>
</nav>

<!-- Studio Header -->
<header class="studio-nav" id="studioNav" role="banner">

    <!-- Logo -->
    <?php if (has_custom_logo()) : ?>
        <div class="studio-nav__logo"><?php the_custom_logo(); ?></div>
    <?php else : ?>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="studio-nav__logo">
            <?php bloginfo('name'); ?>
        </a>
    <?php endif; ?>

    <!-- Desktop menu -->
    <?php
    wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'studio-nav__menu',
        'fallback_cb'    => function() {
            echo '<ul class="studio-nav__menu">';
            $links = [
                '/cocktails'  => 'Коктейли',
                '/packages'   => 'Пакеты',
                '/catering'   => 'Кейтеринг',
                '/cases'      => 'Кейсы',
            ];
            foreach ($links as $url => $label) {
                printf(
                    '<li><a href="%s">%s</a></li>',
                    esc_url(home_url($url)),
                    esc_html($label)
                );
            }
            echo '</ul>';
        },
    ]);
    ?>

    <!-- Right side -->
    <div class="studio-nav__cta">
        <a href="<?php echo esc_url(home_url('/calculator')); ?>"
           class="btn-pill btn-pill--gold">
            Рассчитать стоимость
        </a>
        <button class="studio-nav__burger" id="burgerBtn"
                aria-label="Открыть меню" aria-expanded="false" aria-controls="navDrawer">
            <span></span><span></span><span></span>
        </button>
    </div>

</header>

<main id="main-content" tabindex="-1">
