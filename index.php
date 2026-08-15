<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main style="max-width: 960px; margin: 8rem auto 4rem; padding: 0 1.5rem;">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <h1><?php the_title(); ?></h1>
                <?php the_content(); ?>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <h1><?php esc_html_e('Page not found', 'iristick-static'); ?></h1>
    <?php endif; ?>
</main>
<?php get_footer(); ?>

