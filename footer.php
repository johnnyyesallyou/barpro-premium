<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
</main><!-- /#main-content -->

<!-- Studio Footer -->
<footer class="studio-footer" role="contentinfo">
    <div class="studio-footer__grid">

        <!-- Brand -->
        <div class="studio-footer__brand">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                <?php bloginfo('name'); ?>
            </a>
            <p><?php echo esc_html(get_bloginfo('description') ?: 'Премиум выездной бар для ваших событий'); ?></p>

            <!-- Socials -->
            <div class="studio-footer__socials">
                <?php if ($ig = get_theme_mod('barpro_instagram')) : ?>
                    <a href="<?php echo esc_url($ig); ?>"
                       class="studio-footer__social-link"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="Instagram">IG</a>
                <?php endif; ?>
                <?php if ($tg = get_theme_mod('barpro_telegram')) : ?>
                    <a href="<?php echo esc_url($tg); ?>"
                       class="studio-footer__social-link"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="Telegram">TG</a>
                <?php endif; ?>
                <?php if ($wa = get_theme_mod('barpro_whatsapp')) : ?>
                    <a href="https://wa.me/<?php echo esc_attr(preg_replace('/\D/','',$wa)); ?>"
                       class="studio-footer__social-link"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="WhatsApp">WA</a>
                <?php endif; ?>
                <?php if ($vk = get_theme_mod('barpro_vk')) : ?>
                    <a href="<?php echo esc_url($vk); ?>"
                       class="studio-footer__social-link"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="ВКонтакте">VK</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Услуги -->
        <div class="studio-footer__col">
            <h4>Услуги</h4>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/packages')); ?>">Пакеты</a></li>
                <li><a href="<?php echo esc_url(home_url('/catering')); ?>">Кейтеринг</a></li>
                <li><a href="<?php echo esc_url(home_url('/bar-catering')); ?>">Бар + Кейтеринг</a></li>
                <li><a href="<?php echo esc_url(home_url('/calculator')); ?>">Калькулятор</a></li>
            </ul>
        </div>

        <!-- Коктейли -->
        <div class="studio-footer__col">
            <h4>Меню</h4>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/cocktails')); ?>">Все коктейли</a></li>
                <li><a href="<?php echo esc_url(home_url('/cases')); ?>">Кейсы</a></li>
                <li><a href="<?php echo esc_url(home_url('/team')); ?>">Команда</a></li>
            </ul>
        </div>

        <!-- Контакты -->
        <div class="studio-footer__col">
            <h4>Контакты</h4>
            <ul>
                <?php if ($phone = get_theme_mod('barpro_phone')) : ?>
                    <li>
                        <a href="tel:<?php echo esc_attr(preg_replace('/\D/','',$phone)); ?>">
                            <?php echo esc_html($phone); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($email = get_theme_mod('barpro_email')) : ?>
                    <li>
                        <a href="mailto:<?php echo esc_attr($email); ?>">
                            <?php echo esc_html($email); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($city = get_theme_mod('barpro_city')) : ?>
                    <li><span><?php echo esc_html($city); ?></span></li>
                <?php endif; ?>
            </ul>
        </div>

    </div>

    <div class="studio-footer__bottom">
        <span>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. Все права защищены.</span>
        <?php
        wp_nav_menu([
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'footer-menu',
            'fallback_cb'    => false,
        ]);
        ?>
    </div>
</footer>

<!-- WhatsApp float -->
<?php if ($wa = get_theme_mod('barpro_whatsapp')) : ?>
<a href="https://wa.me/<?php echo esc_attr(preg_replace('/\D/','',$wa)); ?>?text=<?php echo urlencode('Здравствуйте! Хочу узнать подробнее о ваших услугах.'); ?>"
   class="whatsapp-float"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Написать в WhatsApp">
    <svg viewBox="0 0 32 32" width="28" height="28" fill="currentColor" aria-hidden="true">
        <path d="M16 0c-8.837 0-16 7.163-16 16 0 2.825.737 5.607 2.137 8.048L0 32l7.933-2.127A15.93 15.93 0 0016 32c8.837 0 16-7.163 16-16S24.837 0 16 0zm0 29.467a13.427 13.427 0 01-7.07-1.87l-.507-.292-4.713 1.262 1.262-4.669-.292-.508A13.437 13.437 0 012.533 16C2.533 8.565 8.583 2.515 16 2.515S29.467 8.565 29.467 16 23.417 29.467 16 29.467zm7.46-10.027c-.197-.099-1.163-.573-1.342-.639-.179-.066-.309-.099-.439.099s-.505.639-.619.77c-.114.131-.228.147-.425.049s-.829-.305-1.579-.974c-.584-.521-.978-1.163-1.093-1.36s-.012-.304.087-.403c.088-.088.197-.228.295-.342s.131-.197.197-.328c.066-.131.033-.246-.016-.344s-.439-1.057-.602-1.447c-.158-.379-.319-.327-.439-.333-.114-.006-.244-.007-.374-.007s-.342.049-.521.246-.684.668-.684 1.629.7 1.891.798 2.021c.099.131 1.393 2.128 3.375 2.984.471.203.839.325 1.126.416.473.151.904.129 1.246.078.38-.057 1.163-.476 1.327-.935s.164-.853.115-.935c-.05-.082-.181-.131-.378-.23z"/>
    </svg>
</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
