<?php get_header(); ?>

<main class="layoutSingleColumn--wide min-height-100">
    <header class="cTerm--header">
        <?php if (get_term_meta(get_queried_object_id(), '_thumb', true)) : ?>
            <img src="<?php echo get_term_meta(get_queried_object_id(), '_thumb', true); ?>" alt="<?php single_term_title('', true); ?>" class="cTerm--image">
        <?php endif; ?>
        <div class="cTerm--content">
            <h1 class="cTerm--name"><?php single_term_title('', true); ?></h1>
            <?php the_archive_description('<div class="cTerm--description"', '</div>'); ?>
        </div>
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