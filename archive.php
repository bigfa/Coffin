<?php get_header(); ?>

<main class="layoutSingleColumn--wide min-height-100">
    <header class="archive-header u-textAlignCenter">
        <?php
        the_archive_title('<h3 class="page-title">', '</h3>');
        the_archive_description('<div class="taxonomy-description">', '</div>');
        ?>
    </header>
    <div class="sandraList">
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-part/post/content'); ?>
        <?php endwhile; ?>
    </div>
    <?php the_posts_pagination(array(
        'prev_next' => false,
        'before_page_number' => '',
    )); ?>
</main>
<?php get_footer(); ?>