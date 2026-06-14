<?php
/**
 * Template Part: КОМАНДА — editorial grid
 * @package BarPro_Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="studio-section reveal" aria-labelledby="team-heading">
    <div class="studio-section__header">
        <p class="studio-section__eyebrow">Команда</p>
        <h2 id="team-heading" class="studio-section__title">Наша команда</h2>
        <p class="studio-section__sub">8 профессиональных барменов-виртуозов</p>
    </div>

    <div class="team-grid-studio">
        <?php
        $team_posts = barpro_get_cached_posts('barpro_home_team', [
            'post_type'      => 'team_member',
            'posts_per_page' => 4,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);

        if ($team_posts) :
            foreach ($team_posts as $i => $post) : setup_postdata($post);
                $role       = function_exists('get_field')
                    ? get_field('role', get_the_ID())
                    : get_post_meta(get_the_ID(), 'role', true);
                $experience = function_exists('get_field')
                    ? get_field('experience', get_the_ID())
                    : get_post_meta(get_the_ID(), 'experience', true);
                ?>
                <div class="team-card-studio reveal reveal--delay-<?php echo ($i % 4) + 1; ?>">
                    <div class="team-card-studio__photo">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('team-thumb'); ?>
                        <?php else : ?>
                            <span aria-hidden="true">👨‍🍳</span>
                        <?php endif; ?>
                    </div>
                    <div class="team-card-studio__body">
                        <h3 class="team-card-studio__name"><?php the_title(); ?></h3>
                        <?php if ($role) : ?>
                            <p class="team-card-studio__role"><?php echo esc_html($role); ?></p>
                        <?php endif; ?>
                        <?php if ($experience) : ?>
                            <p class="team-card-studio__exp"><?php echo esc_html($experience); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            endforeach;
            wp_reset_postdata();
        else : ?>
            <p class="no-content">Скоро познакомим с командой!</p>
        <?php endif; ?>
    </div>

    <div class="studio-section__more">
        <a href="<?php echo esc_url(home_url('/team')); ?>" class="btn-pill btn-pill--ghost">
            Вся команда →
        </a>
    </div>
</section>
