<?php get_header(); ?>
    <div class="layoutSingleColumn--wide xs-layoutSingleColumn--wide">
        <div class="sandraGroup">
            <div class="sandraList u-clearfix">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part('template-part/post/content');?>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination( array(
                'prev_text' => 'Previous page',
                'next_text' => 'Next page',
                'prev_next' => false,
                'before_page_number' => '',
            ) );?>
        </div>
    </div>
<?php get_footer(); ?>