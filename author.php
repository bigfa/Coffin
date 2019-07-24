<?php get_header();
$authorId = get_query_var('author'); ?>
    <header class="author-banner"></header>
    <header class="author-header u-textAlignCenter">
        <div class="layoutSingleColumn layoutSingleColumn--wide xs-layoutSingleColumn--wide">
            <?php
            echo get_avatar($authorId,120);
            echo '<h1 class="title">' . get_the_author_meta( 'display_name', $authorId ) . '</h1>';
            echo '<p>' . get_the_author_meta( 'description', $authorId ) . '</p>';
            ?>
        </div>
    </header>
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