<?php
/**
 * Template для архива коктейлей (Меню)
 */
get_header();
?>

<!-- Hero -->
<section class="hero inner-hero" style="min-height:50vh">
    <div class="hero__content">
        <h1 class="hero__title">Меню премиум коктейлей</h1>
        <p class="hero__subtitle">50+ авторских и классических коктейлей от виртуозных барменов</p>
    </div>
</section>

<!-- Меню коктейлей -->
<section class="section">
    <h2 class="section-title">Наше меню</h2>
    <p class="section-subtitle">Более 50 премиум коктейлей на любой вкус</p>
    
    <div class="cocktail-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php
                $composition = get_post_meta(get_the_ID(), '_cocktail_composition', true);
                $is_alcoholic = get_post_meta(get_the_ID(), '_cocktail_is_alcoholic', true);
                $strength = get_post_meta(get_the_ID(), '_cocktail_strength', true);
                $volume = get_post_meta(get_the_ID(), '_cocktail_volume_ml', true);
                $price = get_post_meta(get_the_ID(), '_cocktail_price', true);
                ?>
                <div class="cocktail-card-studio reveal">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="cocktail-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('cocktail-thumb'); ?>
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="cocktail-image cocktail-image--placeholder">
                            <span class="cocktail-placeholder-icon" aria-hidden="true"><?php echo $is_alcoholic ? '🍸' : '🍹'; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="cocktail-content">
                        <div class="cocktail-type">
                            <?php echo $is_alcoholic ? '🍸 Алкогольный' : '🍹 Безалкогольный'; ?>
                        </div>
                        
                        <h3 class="cocktail-name">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <?php if ($composition) : ?>
                            <p class="cocktail-composition"><?php echo esc_html($composition); ?></p>
                        <?php endif; ?>
                        
                        <div class="cocktail-tags">
                            <?php if ($strength) : ?>
                                <span class="cocktail-strength"><?php echo esc_html($strength); ?></span>
                            <?php endif; ?>
                            <?php if ($volume) : ?>
                                <span class="cocktail-volume"><?php echo esc_html($volume); ?>мл</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="cocktail-footer">
                            <?php if ($price) : ?>
                                <span class="cocktail-price"><?php echo number_format($price, 0, ',', ' '); ?>₽</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Коктейли не найдены.</p>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <div class="pagination">
        <?php
        the_posts_pagination([
            'mid_size' => 2,
            'prev_text' => '← Назад',
            'next_text' => 'Вперёд →',
        ]);
        ?>
    </div>
</section>

<!-- Преимущества -->
<section class="section section-dark">
    <h2 class="section-title">Почему наши коктейли — лучшие?</h2>
    <div class="cards-grid">
        <div class="card">
            <div class="card-icon">🥃</div>
            <h3 class="card-title">Премиум алкоголь</h3>
            <p>Только проверенные бренды: Grey Goose, Hendrick's, Bacardi Superior, Patron, Moet & Chandon</p>
        </div>
        
        <div class="card">
            <div class="card-icon">🍋</div>
            <h3 class="card-title">Свежие ингредиенты</h3>
            <p>Закупаем фрукты, травы, соки утром перед мероприятием. Никаких концентратов</p>
        </div>
        
        <div class="card">
            <div class="card-icon">👨‍🍳</div>
            <h3 class="card-title">Мастерство барменов</h3>
            <p>10+ лет опыта, чемпионы России по флейрингу, международные сертификаты</p>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section section-cta">
    <h2 class="cta-title">Попробуйте все 50 коктейлей</h2>
    <p class="cta-subtitle">Закажите выездной бар с полным меню</p>
    <a href="<?php echo esc_url( home_url('/calculator') ); ?>" class="btn btn-primary" class="btn-large">Рассчитать стоимость</a>
</section>


<?php get_footer(); ?>
