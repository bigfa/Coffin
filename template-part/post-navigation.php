<?php

/**
 * The template for displaying posts in the Status post format
 *
 * @package Bigfa
 * @subpackage Coffin
 * @since Coffin 2.0.0
 */

$previou_post = get_previous_post();
$next_post = get_next_post();
?>
<nav class="navigation post-navigation" aria-label="<?php _e('Post', 'Coffin'); ?>">
    <?php if ($previou_post) : ?>
        <div class="nav-previous">
            <a href="<?php echo get_permalink($previou_post->ID) ?>" rel="prev">
                <span class="meta-nav"><?php _e('Previous', 'Coffin'); ?></span>
                <span class="post-title">
                    <?php echo get_the_title($previou_post->ID) ?>
                </span>
            </a>
            <?php if (coffin_is_has_image($previou_post->ID)) : ?>
                <a href="<?php the_permalink($previou_post->ID); ?>" aria-label="<?php the_title($previou_post->ID); ?>" class="cover--link">
                    <img alt="<?php echo get_the_title($previou_post->ID); ?>" src="<?php echo coffin_get_background_image($previou_post->ID, 400, 120); ?>" class="cover" />
                </a>
            <?php endif ?>
        </div>
    <?php endif ?>
    <?php if ($next_post) : ?>
        <div class="nav-next">
            <a href="<?php echo get_permalink($next_post->ID) ?>" rel="next">
                <span class="meta-nav"><?php _e('Next', 'Coffin'); ?></span>
                <span class="post-title">
                    <?php echo get_the_title($next_post->ID) ?>
                </span>
            </a>
            <?php if (coffin_is_has_image($next_post->ID)) : ?>
                <a href="<?php the_permalink($next_post->ID); ?>" aria-label="<?php the_title($next_post->ID); ?>" class="cover--link">
                    <img src="<?php echo coffin_get_background_image($next_post->ID, 400, 120); ?>" class="cover" alt="<?php echo get_the_title($next_post->ID); ?>" />
                </a>
            <?php endif ?>
        </div>
    <?php endif ?>
</nav>