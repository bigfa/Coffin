<?php

/**
 * The template for displaying posts related to the current post
 *
 * @package Bigfa
 * @subpackage Coffin
 * @since Hera 2.0.0
 */
?>
<section class="cRelated--area">
    <h3 class="cRelated--heroTitle"><?php _e('Related Posts', 'Coffin'); ?></h3>
    <div class="cRelated--list">
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
            <div class="cRelated--item">
                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>">
                    <?php if (coffin_is_has_image(get_the_ID())) : ?>
                        <div class="cRelated--image">
                            <img src="<?php echo coffin_get_background_image(get_the_ID(), 400, 200); ?>" class="cover" alt="<?php the_title(); ?>" />
                        </div>
                    <?php endif; ?>
                    <div class="cRelated--title">
                        <?php the_title(); ?>
                    </div>
                    <div class="cRelated--meta">
                        <time datetime="<?php echo get_the_date('c'); ?>">
                            <?php echo human_time_diff(get_the_time('U'), current_time('U')) . __('ago', 'Coffin'); ?>
                        </time>
                        <span class="middotDivider"></span>
                        <?php the_category(',') ?>
                        <span class="middotDivider"></span>
                        <?php echo coffin_get_post_read_time_text(get_the_ID()); ?>
                    </div>
                </a>
            </div>
        <?php endwhile;
        wp_reset_postdata(); ?>
    </div>
</section>