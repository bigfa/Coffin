<?php get_header();
global $coffinSetting;
?>
<?php while (have_posts()) : the_post(); ?>
    <main class="layoutSingleColumn">
        <article class="entry" itemscope="itemscope" itemtype="http://schema.org/Article">
            <header class="entry--header">
                <div class="entry--meta">
                    <time itemprop="datePublished" datetime="<?php echo get_the_date('c'); ?>" class="humane--time"><?php echo get_the_date('m d, Y'); ?></time><span class="middotDivider"></span><?php the_category(',') ?><span class="middotDivider"></span><?php echo get_post_meta(get_the_ID(), COFFIN_POST_VIEW_KEY, true); ?> views
                </div>
                <?php the_title('<h2 class="entry--title" itemprop="headline">', '</h2>'); ?>
                <!-- <div class="entry--author">
                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>"><?php echo get_avatar(get_the_author_meta('ID'), 58); ?></a>
                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" class="author--link"><?php the_author(); ?></a>
                    <?php if ($coffinSetting->get_setting('show_rss_btn')) : ?>
                        <a href="<?php echo get_feed_link('rss2'); ?>" class="btn--feed" target="_blank">订阅</a>
                    <?php endif; ?>
                </div> -->
            </header>
            <meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="<?php the_permalink(); ?>" />
            <meta itemprop="datePublished" content="<?php echo esc_attr(get_the_date('c')); ?>" />
            <meta itemprop="dateModified" content="<?php echo esc_attr(get_the_modified_date('c')); ?>" />
            <div class="grap entry--content" itemprop="articleBody">
                <?php the_content(); ?>
            </div>
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
                    <div class="site--footer__sns">
                        <?php get_template_part('template-part/sns'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    </main>
    <?php
    if (comments_open() || get_comments_number()) :
        comments_template();
    endif;
    ?>
<?php endwhile; ?>
<?php get_footer(); ?>