<?php if ( ! defined( 'ABSPATH' ) ) exit; get_header(); ?>

    <div class="container">
        <div class="content-area">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php the_excerpt(); ?>
                    </article>
                <?php endwhile; ?>
                
                <div class="pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size' => 2,
                        'prev_text' => '← Назад',
                        'next_text' => 'Вперёд →',
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <p>Записи не найдены.</p>
            <?php endif; ?>
        </div>
    </div>

<?php get_footer(); ?>
