<?php get_header(); ?>
    <div class="layoutSingleColumn layoutSingleColumn--page">
        <div class="u-backgroundColorWhite page-wrapper">
            <?php while ( have_posts() ) : the_post();?>
                <header class="entry-header">
                    <?php the_title( '<h2 class="entry-title">', '</h2>' );?>
                </header>
                <div class="grap">
                    <?php the_content();?>
                </div>
                <?php
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            <?php endwhile; ?>
        </div>
    </div>
<?php get_footer(); ?>