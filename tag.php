<?php get_header(); ?>

<main class="layoutSingleColumn--wide min-height-100">
    <header class="archive-header u-textAlignCenter">
        <?php
        the_archive_title('<h3 class="page-title">', '</h3>');
        the_archive_description('<div class="taxonomy-description">', '</div>');
        ?>
    </header>
    <div class="tag--card">
        <?php
        // get related tags
        $tags = get_tags(array('orderby' => 'count', 'order' => 'DESC', 'number' => 10));
        if ($tags) {
            echo '<div class="tag--card--content">';
            foreach ($tags as $tag) {
                echo '<a href="' . get_tag_link($tag->term_id) . '" class="tag--card--item">' . $tag->name . '</a>';
            }
            echo '</div>';
        }
        ?>
    </div>
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