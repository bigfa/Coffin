<?php get_header(); ?>
<main class="layoutSingleColumn--wide min-height-100">
    <div class="sandraList">
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-part/post/content'); ?>
        <?php endwhile; ?>
    </div>
    <?php the_posts_pagination(array(
        'prev_text' => 'Previous page',
        'next_text' => 'Next page',
        'prev_next' => false,
        'before_page_number' => '',
    )); ?>
</main>
<?php get_footer(); ?>