<?php get_header();
$authorId = get_query_var('author'); ?>

<main class="layoutSingleColumn--wide">
    <header class="author-header u-textAlignCenter">
        <?php
        echo get_avatar($authorId, 86);
        echo '<h2 class="title">' . get_the_author_meta('display_name', $authorId) . '</h2>';
        echo '<p>' . get_the_author_meta('description', $authorId) . '</p>';
        ?>
    </header>
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