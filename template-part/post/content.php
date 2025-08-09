<?php global $coffinSetting; ?>
<article class="cCard--item<?php if (get_post_meta('_fullImage')) echo ' sandraItem--full'; ?>" itemscope="itemscope" itemtype="http://schema.org/Article">
    <div class="cCard--image">
        <a style="background-image: url(<?php echo coffin_get_background_image($post->ID, 800, 480); ?>);" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" aria-label="<?php the_title(); ?>">
        </a>
    </div>
    <div class="cCard--content">
        <div class="cCard--meta">
            <h2 class="cCard--title" itemprop="headline">
                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
            </h2>
        </div>
        <div class="cCard--info">
            <time itemprop="datePublished" datetime="<?php echo get_the_date('c'); ?>">
                <?php echo human_time_diff(get_the_time('U'), current_time('U')) . __('ago', 'Coffin'); ?>
            </time>
            <?php if ($coffinSetting->get_setting('home_cat')) : ?>
                <span class="middotDivider"></span>
                <?php the_category(',') ?>
            <?php endif; ?>
            <?php if ($coffinSetting->get_setting('home_views')) : ?>
                <span class="middotDivider"></span>
                <?php echo coffin_get_post_views_text(false, false, false, get_the_ID()); ?>
            <?php endif; ?>
            <?php if ($coffinSetting->get_setting('home_readtime')) : ?>
                <span class="middotDivider"></span>
                <?php echo coffin_get_post_read_time_text(get_the_ID()); ?>
            <?php endif; ?>
        </div>
        <?php do_action('marker_pro_flag', get_the_ID()); ?>
    </div>
</article>