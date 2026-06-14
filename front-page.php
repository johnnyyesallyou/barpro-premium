<?php
/**
 * Главная страница — шаблон
 * Секции вынесены в template-parts/home/
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

get_template_part( 'template-parts/home/hero' );

get_template_part( 'template-parts/home/bento' );

get_template_part( 'template-parts/home/showcase' );

get_template_part( 'template-parts/home/pricing' );

get_template_part( 'template-parts/home/cocktails' );

get_template_part( 'template-parts/home/team' );

get_template_part( 'template-parts/home/testimonials' );

get_template_part( 'template-parts/home/catering' );

get_template_part( 'template-parts/home/cta' );

get_footer();
