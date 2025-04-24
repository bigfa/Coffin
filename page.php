<?php get_header(); ?>
<div class="layoutSingleColumn">
    <article class="entry" itemscope="itemscope" itemtype="http://schema.org/Article">
        <?php while (have_posts()) : the_post(); ?>
            <header class="entry--header">
                <?php the_title('<h2 class="entry--title" itemprop="headline">', '</h2>'); ?>
            </header>
            <div class="grap entry--content" itemprop="articleBody">
                <?php the_content(); ?>
            </div>
            <?php wp_link_pages(array(
                'before'      => '<div class="nav-links nav-links__comment">',
                'after'       => '</div>',
                'pagelink'    => '%',
                'separator'   => '<span class="screen-reader-text">, </span>',
            )); ?>
            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        <?php endwhile; ?>
    </article>
</div>
<?php get_footer(); ?>