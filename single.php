<?php get_header();
global $coffinSetting;
?>
<?php while (have_posts()) : the_post(); ?>
    <main class="layoutSingleColumn">
        <article class="entry" itemscope="itemscope" itemtype="http://schema.org/Article">
            <header class="entry--header">
                <?php do_action('marker_pro_flag', get_the_ID()); ?>
                <div class="entry--meta">
                    <time itemprop="datePublished" datetime="<?php echo get_the_date('c'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('U')) . __('ago', 'Coffin'); ?></time><span class="middotDivider"></span><?php the_category(',') ?><span class="middotDivider"></span><?php echo coffin_get_post_views_text(false, false, false, get_the_ID()); ?><span class="middotDivider"></span>
                    <?php echo coffin_get_post_read_time_text(get_the_ID()); ?>
                </div>
                <?php the_title('<h2 class="entry--title" itemprop="headline">', '</h2>'); ?>
            </header>
            <meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="<?php the_permalink(); ?>" />
            <meta itemprop="datePublished" content="<?php echo esc_attr(get_the_date('c')); ?>" />
            <meta itemprop="dateModified" content="<?php echo esc_attr(get_the_modified_date('c')); ?>" />
            <div class="grap entry--content" itemprop="articleBody">
                <?php the_content(); ?>
            </div>
            <?php wp_link_pages(array(
                'before'      => '<div class="nav-links nav-links__comment">',
                'after'       => '</div>',
                'pagelink'    => '%',
                'separator'   => '<span class="screen-reader-text">, </span>',
            )); ?>
            <?php if ($coffinSetting->get_setting('postlike')) : ?>
                <div class="entry__action">
                    <button class="button--like like-btn" aria-label="like the post">
                        <svg class="icon--active" viewBox="0 0 1024 1024" width="32" height="32">
                            <path d="M780.8 204.8c-83.2-44.8-179.2-19.2-243.2 44.8L512 275.2 486.4 249.6c-64-64-166.4-83.2-243.2-44.8C108.8 275.2 89.6 441.6 185.6 537.6l32 32 153.6 153.6 102.4 102.4c25.6 25.6 57.6 25.6 83.2 0l102.4-102.4 153.6-153.6 32-32C934.4 441.6 915.2 275.2 780.8 204.8z"></path>
                        </svg>
                        <svg class="icon--default" viewBox="0 0 1024 1024" width="32" height="32">
                            <path d="M332.8 249.6c38.4 0 83.2 19.2 108.8 44.8L467.2 320 512 364.8 556.8 320l25.6-25.6c32-32 70.4-44.8 108.8-44.8 19.2 0 38.4 6.4 57.6 12.8 44.8 25.6 70.4 57.6 76.8 108.8 6.4 44.8-6.4 89.6-38.4 121.6L512 774.4 236.8 492.8C204.8 460.8 185.6 416 192 371.2c6.4-44.8 38.4-83.2 76.8-108.8C288 256 313.6 249.6 332.8 249.6L332.8 249.6M332.8 185.6C300.8 185.6 268.8 192 243.2 204.8 108.8 275.2 89.6 441.6 185.6 537.6l281.6 281.6C480 832 499.2 838.4 512 838.4s32-6.4 38.4-19.2l281.6-281.6c96-96 76.8-262.4-57.6-332.8-25.6-12.8-57.6-19.2-89.6-19.2-57.6 0-115.2 25.6-153.6 64L512 275.2 486.4 249.6C448 211.2 390.4 185.6 332.8 185.6L332.8 185.6z"></path>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($coffinSetting->get_setting('show_copylink')) : ?>
                <div class="post--share">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <g>
                            <path d="M18.36 5.64c-1.95-1.96-5.11-1.96-7.07 0L9.88 7.05 8.46 5.64l1.42-1.42c2.73-2.73 7.16-2.73 9.9 0 2.73 2.74 2.73 7.17 0 9.9l-1.42 1.42-1.41-1.42 1.41-1.41c1.96-1.96 1.96-5.12 0-7.07zm-2.12 3.53l-7.07 7.07-1.41-1.41 7.07-7.07 1.41 1.41zm-12.02.71l1.42-1.42 1.41 1.42-1.41 1.41c-1.96 1.96-1.96 5.12 0 7.07 1.95 1.96 5.11 1.96 7.07 0l1.41-1.41 1.42 1.41-1.42 1.42c-2.73 2.73-7.16 2.73-9.9 0-2.73-2.74-2.73-7.17 0-9.9z"></path>
                        </g>
                    </svg>
                    <span class="text"><?php _e('Copy link.', 'Farallon') ?></span> <span class="link"><?php the_permalink(); ?></span>
                </div>
            <?php endif; ?>
            <?php echo get_the_tag_list('<div class="entry--tags">', '', '</div>'); ?>
            <div class="authorCard">
                <div class="authorCard--imageWrapper">
                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>"><?php echo get_avatar(get_the_author_meta('ID'), 72); ?></a>
                </div>
                <div class="authorCard--content">
                    <h3 class="authorCard--title"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>"><?php the_author(); ?></a></h3>
                    <div class="authorCard--description">
                        <?php the_author_meta('description'); ?>
                    </div>
                </div>
                <?php if ($coffinSetting->get_setting('footer_sns')) : ?>
                    <div class="entry--author__sns">
                        <?php get_template_part('template-part/sns'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            if ($coffinSetting->get_setting('post_navigation'))
                get_template_part('template-part/post', 'navigation');
            ?>
        </article>

        <?php
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>
        <?php
        if ($coffinSetting->get_setting('related'))
            get_template_part('template-part/single-related');
        ?>
    </main>
<?php
endwhile;
?>

<?php get_footer(); ?>