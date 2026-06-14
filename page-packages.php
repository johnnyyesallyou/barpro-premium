<?php
/**
 * Template Name: Страница пакетов
 */
get_header();
?>

<!-- Hero -->
<section class="hero inner-hero" style="min-height:60vh">
    <div class="hero__content">
        <h1 class="hero__title">Тарифы BarPro</h1>
        <p class="hero__subtitle">Готовые решения для любого мероприятия от 10 до 500 гостей</p>
    </div>
</section>

<!-- Основные пакеты -->
<section class="section">
    <h2 class="section-title">Выберите свой пакет</h2>
    <p class="section-subtitle">Все пакеты включают: барменов, оборудование, посуду, ингредиенты</p>
    
    <div class="pricing-grid">
        <?php
        // Получаем пакеты
        $packages_query = new WP_Query([
            'post_type'      => 'package',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'no_found_rows'  => true,
        ]);
        
        if ($packages_query->have_posts()) :
            while ($packages_query->have_posts()) : $packages_query->the_post();
                $price = get_post_meta(get_the_ID(), '_package_price', true);
                $cocktails = get_post_meta(get_the_ID(), '_package_cocktails', true);
                $bartenders = get_post_meta(get_the_ID(), '_package_bartenders', true);
                $hours = get_post_meta(get_the_ID(), '_package_hours', true);
                $is_featured = get_post_meta(get_the_ID(), '_package_featured', true);
                ?>
                <div class="pricing-card <?php echo $is_featured ? 'featured' : ''; ?>">
                    <?php if ($is_featured) : ?>
                        <div class="pricing-bestseller">🔥 Хит продаж</div>
                    <?php endif; ?>
                    
                    <div class="pricing-header">
                        <div class="pricing-badge"><?php the_title(); ?></div>
                        <div class="pricing-icon">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else : ?>
                                <?php
                                if (stripos(get_the_title(), 'Classic') !== false) echo '🍸';
                                elseif (stripos(get_the_title(), 'Premium') !== false) echo '🥂';
                                elseif (stripos(get_the_title(), 'VIP') !== false) echo '🍾';
                                else echo '🍹';
                                ?>
                            <?php endif; ?>
                        </div>
                        <h3 class="pricing-name"><?php the_excerpt(); ?></h3>
                    </div>
                    
                    <?php if ($price) : ?>
                        <div class="pricing-price">
                            <span class="pricing-amount"><?php echo number_format($price, 0, ',', ' '); ?>₽</span>
                            <span class="pricing-period">за мероприятие</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="pricing-features">
                        <?php the_content(); ?>
                        
                        <?php if ($cocktails || $bartenders || $hours) : ?>
                            <div class="package-meta">
                                <?php if ($cocktails) : ?>
                                    <p><strong>Коктейлей:</strong> <?php echo esc_html($cocktails); ?></p>
                                <?php endif; ?>
                                <?php if ($bartenders) : ?>
                                    <p><strong>Барменов:</strong> <?php echo esc_html($bartenders); ?></p>
                                <?php endif; ?>
                                <?php if ($hours) : ?>
                                    <p><strong>Часов:</strong> <?php echo esc_html($hours); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <a href="<?php echo esc_url( home_url('/calculator') ); ?>" class="btn btn-primary">
                        Заказать <?php the_title(); ?>
                    </a>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            ?>
            <!-- Пакеты по умолчанию, если не созданы в админке -->
            <div class="studio-pricing-card reveal">
                <div class="pricing-header">
                    <div class="pricing-badge">Classic</div>
                    <div class="pricing-icon">🍸</div>
                    <h3 class="pricing-name">Классический бар</h3>
                </div>
                <div class="pricing-price">
                    <span class="pricing-amount">60,000₽</span>
                    <span class="pricing-period">за мероприятие</span>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li>100 коктейлей</li>
                        <li>2 бармена</li>
                        <li>4 часа работы</li>
                        <li>Барная стойка 2м</li>
                        <li>Классическое меню</li>
                        <li>Вся посуда</li>
                        <li>Премиум алкоголь</li>
                        <li>Свежие ингредиенты</li>
                        <li>Доставка по Москве</li>
                        <li>Уборка</li>
                    </ul>
                </div>
                <a href="<?php echo esc_url( home_url('/calculator') ); ?>" class="btn btn-primary">Заказать</a>
            </div>
            
            <div class="studio-pricing-card studio-pricing-card--featured reveal">
                <div class="pricing-bestseller">🔥 Хит продаж</div>
                <div class="pricing-header">
                    <div class="pricing-badge">Premium</div>
                    <div class="pricing-icon">🥂</div>
                    <h3 class="pricing-name">Премиум бар</h3>
                </div>
                <div class="pricing-price">
                    <span class="pricing-amount">100,000₽</span>
                    <span class="pricing-period">за мероприятие</span>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li>200 коктейлей</li>
                        <li>3 бармена</li>
                        <li>5 часов работы</li>
                        <li>Барная стойка 4м</li>
                        <li>Расширенное меню</li>
                        <li>Элементы флейринга</li>
                        <li>Premium алкоголь</li>
                        <li>Тематическое оформление</li>
                        <li>Фирменная униформа</li>
                        <li>Персональный менеджер</li>
                        <li>🎁 Пирамида 56 бокалов</li>
                    </ul>
                </div>
                <a href="<?php echo esc_url( home_url('/calculator') ); ?>" class="btn btn-primary">Заказать</a>
            </div>
            
            <div class="studio-pricing-card reveal">
                <div class="pricing-header">
                    <div class="pricing-badge">VIP</div>
                    <div class="pricing-icon">🍾</div>
                    <h3 class="pricing-name">VIP бар</h3>
                </div>
                <div class="pricing-price">
                    <span class="pricing-amount">150,000₽</span>
                    <span class="pricing-period">за мероприятие</span>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li>300 коктейлей</li>
                        <li>4 бармена</li>
                        <li>6 часов работы</li>
                        <li>2 барные стойки</li>
                        <li>Флейринг-шоу 30 мин</li>
                        <li>Пирамида 120 бокалов</li>
                        <li>Luxury алкоголь</li>
                        <li>Эксклюзивные коктейли</li>
                        <li>Крио-бар (опция)</li>
                        <li>Фото/видео съемка</li>
                        <li>Супервайзер на месте</li>
                        <li>🎁 Moet & Chandon</li>
                    </ul>
                </div>
                <a href="<?php echo esc_url( home_url('/calculator') ); ?>" class="btn btn-primary">Заказать</a>
            </div>
            <?php
        endif;
        ?>
    </div>
</section>

<!-- Дополнительные услуги -->
<section class="section section-dark">
    <h2 class="section-title">Дополнительные услуги</h2>
    <p class="section-subtitle">Усильте любой пакет уникальными опциями</p>
    
    <div class="cards-grid">
        <?php
        $services_query = new WP_Query([
            'post_type'      => 'addon_service',
            'posts_per_page' => 6,
            'no_found_rows'  => true,
        ]);
        
        if ($services_query->have_posts()) :
            while ($services_query->have_posts()) : $services_query->the_post();
                $price = get_post_meta(get_the_ID(), '_service_price', true);
                $icon = get_post_meta(get_the_ID(), '_service_icon', true);
                ?>
                <div class="card">
                    <div class="card-icon"><?php echo $icon ?: '🎭'; ?></div>
                    <h3 class="card-title"><?php the_title(); ?></h3>
                    <?php the_excerpt(); ?>
                    <?php if ($price) : ?>
                        <div class="card-price">+<?php echo number_format($price, 0, ',', ' '); ?>₽</div>
                    <?php endif; ?>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            ?>
            <!-- Услуги по умолчанию -->
            <div class="card">
                <div class="card-icon">🎭</div>
                <h3 class="card-title">Флейринг-шоу Solo</h3>
                <p>30 минут барменского шоу с трюками, огнём, жонглированием</p>
                <div class="card-price">+25,000₽</div>
            </div>
            <div class="card">
                <div class="card-icon">❄️</div>
                <h3 class="card-title">Крио-бар</h3>
                <p>Коктейли с жидким азотом. Дым, пар, спецэффекты</p>
                <div class="card-price">+15,000₽</div>
            </div>
            <div class="card">
                <div class="card-icon">🍹</div>
                <h3 class="card-title">Мохито-бар</h3>
                <p>Отдельная стойка с мохито. 5 вкусов</p>
                <div class="card-price">+12,000₽</div>
            </div>
            <div class="card">
                <div class="card-icon">🎪</div>
                <h3 class="card-title">Флейринг Duo</h3>
                <p>Два бармена, синхронные трюки, 40 минут</p>
                <div class="card-price">+40,000₽</div>
            </div>
            <div class="card">
                <div class="card-icon">🍾</div>
                <h3 class="card-title">Пирамида XL</h3>
                <p>180 бокалов с подсветкой и сухим льдом</p>
                <div class="card-price">+18,000₽</div>
            </div>
            <div class="card">
                <div class="card-icon">🎲</div>
                <h3 class="card-title">Коктейльное казино</h3>
                <p>Интерактив: гости крутят рулетку</p>
                <div class="card-price">+20,000₽</div>
            </div>
            <?php
        endif;
        ?>
    </div>
</section>

<!-- CTA -->
<section class="section section-cta">
    <h2 class="cta-title">Не можете выбрать?</h2>
    <p class="cta-subtitle">Рассчитайте индивидуальную стоимость за 2 минуты</p>
    <a href="<?php echo esc_url( home_url('/calculator') ); ?>" class="btn btn-primary" class="btn-large">Калькулятор стоимости</a>
</section>


<?php get_footer(); ?>
