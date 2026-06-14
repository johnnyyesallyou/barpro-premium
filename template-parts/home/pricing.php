<?php
/**
 * Template Part: PRICING — studio cards
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="studio-pricing reveal" aria-labelledby="pricing-heading">
    <div class="studio-section__header">
        <p class="studio-section__eyebrow">Пакеты</p>
        <h2 id="pricing-heading" class="studio-section__title">Тарифы BarPro</h2>
        <p class="studio-section__sub">Готовые решения для любого мероприятия</p>
    </div>

    <div class="studio-pricing__grid">
        <?php
        $packages = barpro_get_cached_posts('barpro_home_packages', [
            'post_type'      => 'package',
            'posts_per_page' => 3,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);

        if ($packages) :
            foreach ($packages as $i => $post) : setup_postdata($post);
                $price       = get_post_meta(get_the_ID(), '_package_price', true);
                $is_featured = get_post_meta(get_the_ID(), '_package_featured', true);
                ?>
                <div class="studio-pricing-card <?php echo $is_featured ? 'studio-pricing-card--featured' : ''; ?> reveal reveal--delay-<?php echo intval( $i + 1 ); ?>">
                    <div class="studio-pricing-card__tier"><?php the_title(); ?></div>
                    <?php if ($price) : ?>
                        <div class="studio-pricing-card__price">
                            <?php echo number_format($price, 0, ',', ' '); ?>₽
                        </div>
                        <div class="studio-pricing-card__period">за мероприятие</div>
                    <?php endif; ?>
                    <ul class="studio-pricing-card__features">
                        <?php
                        $content = get_the_content();
                        preg_match_all('/<li>(.*?)<\/li>/s', $content, $matches);
                        if ($matches[1]) {
                            foreach (array_slice($matches[1], 0, 6) as $feature) {
                                echo '<li>' . wp_kses_post($feature) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/calculator')); ?>"
                       class="btn-pill btn-pill--<?php echo $is_featured ? 'gold' : 'ghost'; ?> btn-pill--full">
                        Выбрать пакет
                    </a>
                </div>
                <?php
            endforeach;
            wp_reset_postdata();
        else : ?>
            <p class="no-content">Пакеты скоро появятся. <a href="<?php echo esc_url(home_url('/calculator')); ?>">Рассчитайте стоимость</a>.</p>
        <?php endif; ?>
    </div>

    <div class="studio-section__more">
        <a href="<?php echo esc_url(home_url('/packages')); ?>" class="btn-pill btn-pill--ghost">
            Все пакеты →
        </a>
    </div>
</section>
