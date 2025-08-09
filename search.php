<?php get_header(); ?>
<main class="layoutSingleColumn--wide min-height-100">
    <header class="cTerm--header">
        <h3 class="cTerm--name">
            <?php
            printf(__('Search results for: %s', 'Coffin'), '<span>' . get_search_query() . '</span>');
            ?>
        </h3>
    </header>
    <div class="cCard--list">
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