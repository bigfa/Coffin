<?php

/**
 * The template for displaying posts related to the current post
 *
 * @package Bigfa
 * @subpackage Coffin
 * @since Hera 2.0.0
 */
?>
<section class="related--posts">
    <h3 class="related--posts__title"><?php _e('Related Posts', 'Coffin'); ?></h3>
    <div class="entry--related">
        <?php
        // get same format related posts
        $the_query = new WP_Query(array(
            'post_type' => 'post',
            'post__not_in' => array(get_the_ID()),
            'posts_per_page' => 6,
            'category__in' => wp_get_post_categories(get_the_ID()),
            'ignore_sticky_posts' => 1,
            'tax_query' => get_post_format(get_the_ID()) ? array( // same post format
                array(
                    'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-' . get_post_format(get_the_ID())),
                    'operator' => 'IN'
                )
            ) : array()
        ));
        while ($the_query->have_posts()) : $the_query->the_post(); ?>
            <div class="entry--related__item">
                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>">
                    <?php if (coffin_is_has_image(get_the_ID())) : ?>
                        <div class="entry--related__img">
                            <img src="<?php echo coffin_get_background_image(get_the_ID(), 400, 200); ?>" class="cover" alt="<?php the_title(); ?>" />
                        </div>
                    <?php endif; ?>
                    <div class="entry--related__title">
                        <?php the_title(); ?>
                    </div>
                    <div class="meta">
                        <time datetime="<?php echo get_the_date('c'); ?>" class="humane--time">
                            <?php echo get_the_date('Y-m-d'); ?>
                        </time>
                        <span class="middotDivider"></span>
                        <?php the_category(',') ?>
                    </div>
                </a>
            </div>
        <?php endwhile;
        wp_reset_postdata(); ?>
    </div>
</section>