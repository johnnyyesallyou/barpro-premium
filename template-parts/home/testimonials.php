<?php
/**
 * Template Part: ОТЗЫВЫ — animated testimonials
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="studio-testimonials studio-section--dark reveal" aria-labelledby="testimonials-heading">
    <div class="studio-section__header">
        <p class="studio-section__eyebrow">Отзывы</p>
        <h2 id="testimonials-heading" class="studio-section__title">Отзывы клиентов</h2>
        <p class="studio-section__sub">Более 200 довольных клиентов в 2025 году</p>
    </div>

    <div class="studio-testimonials__grid">
        <?php
        $testimonials_posts = barpro_get_cached_posts('barpro_home_testimonials', [
            'post_type'      => 'testimonial',
            'posts_per_page' => 3,
        ]);

        if ($testimonials_posts) :
            foreach ($testimonials_posts as $i => $post) : setup_postdata($post);
                $author_name     = get_post_meta(get_the_ID(), '_testimonial_author', true);
                $author_position = get_post_meta(get_the_ID(), '_testimonial_position', true);
                $rating          = intval(get_post_meta(get_the_ID(), '_testimonial_rating', true));
                ?>
                <div class="studio-testimonial reveal reveal--delay-<?php echo intval( $i + 1 ); ?>">
                    <?php if ($rating) : ?>
                        <div class="studio-testimonial__stars"
                             role="img"
                             aria-label="Оценка: <?php echo intval( $rating ); ?> из 5">
                            <?php for ($s = 1; $s <= 5; $s++) : ?>
                                <span class="star <?php echo $s <= $rating ? 'star--on' : 'star--off'; ?>"
                                      aria-hidden="true">★</span>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                    <blockquote class="studio-testimonial__quote">
                        <?php the_content(); ?>
                    </blockquote>

                    <div class="studio-testimonial__author">
                        <div class="studio-testimonial__avatar" aria-hidden="true">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else : ?>
                                👤
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="studio-testimonial__name">
                                <?php echo esc_html($author_name ?: get_the_title()); ?>
                            </div>
                            <?php if ($author_position) : ?>
                                <div class="studio-testimonial__position">
                                    <?php echo esc_html($author_position); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            endforeach;
            wp_reset_postdata();
        else : ?>
            <p class="no-content">Отзывы скоро появятся.</p>
        <?php endif; ?>
    </div>
</section>
