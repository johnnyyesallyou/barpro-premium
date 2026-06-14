<?php
/**
 * Template Part: COCKTAIL SHOWCASE — ingredient reveal on hover
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="cocktail-showcase studio-section--dark reveal" aria-labelledby="cocktails-heading">
    <div class="studio-section__header">
        <p class="studio-section__eyebrow">Меню</p>
        <h2 id="cocktails-heading" class="studio-section__title">Популярные коктейли</h2>
        <p class="studio-section__sub">Из нашего меню 50+ позиций</p>
    </div>

    <div class="cocktail-showcase__grid">
        <?php
        $all_cocktails = barpro_get_cached_posts('barpro_home_cocktails', [
            'post_type'      => 'cocktail',
            'posts_per_page' => 20,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        if ($all_cocktails) shuffle($all_cocktails);
        $cocktails = array_slice($all_cocktails ?: [], 0, 6);

        if ($cocktails) :
            foreach ($cocktails as $i => $post) : setup_postdata($post);
                $composition  = get_post_meta(get_the_ID(), '_cocktail_composition', true);
                $is_alcoholic = get_post_meta(get_the_ID(), '_cocktail_is_alcoholic', true);
                $strength     = get_post_meta(get_the_ID(), '_cocktail_strength', true);
                $price        = get_post_meta(get_the_ID(), '_cocktail_price', true);
                // Разбить состав на теги-ингредиенты
                $ingredients  = $composition ? array_slice(array_map('trim', explode(',', $composition)), 0, 4) : [];
                ?>
                <div class="cocktail-card-studio reveal reveal--delay-<?php echo ($i % 3) + 1; ?>">

                    <!-- Фото + ingredient reveal -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="cocktail-card-studio__img">
                            <?php the_post_thumbnail('cocktail-thumb'); ?>
                        </div>
                        <?php if ($ingredients) : ?>
                            <div class="cocktail-card-studio__reveal" aria-hidden="true">
                                <?php foreach ($ingredients as $ing) : ?>
                                    <span class="cocktail-card-studio__ingredient"><?php echo esc_html($ing); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="cocktail-card-studio__body">
                        <p class="cocktail-card-studio__type">
                            <?php echo $is_alcoholic ? 'Алкогольный' : 'Безалкогольный'; ?>
                        </p>
                        <h3 class="cocktail-card-studio__name"><?php the_title(); ?></h3>
                        <?php if ($composition) : ?>
                            <p class="cocktail-card-studio__composition"><?php echo esc_html(wp_trim_words($composition, 8)); ?></p>
                        <?php endif; ?>
                        <div class="cocktail-card-studio__footer">
                            <?php if ($price) : ?>
                                <span class="cocktail-card-studio__price"><?php echo number_format($price, 0, ',', ' '); ?>₽</span>
                            <?php endif; ?>
                            <?php if ($strength) : ?>
                                <span class="cocktail-card-studio__tag"><?php echo esc_html($strength); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <?php
            endforeach;
            wp_reset_postdata();
        else : ?>
            <p class="no-content">Меню коктейлей скоро появится.</p>
        <?php endif; ?>
    </div>

    <div class="studio-section__more">
        <a href="<?php echo esc_url(home_url('/cocktails')); ?>" class="btn-pill btn-pill--ghost">
            Всё меню →
        </a>
    </div>
</section>
