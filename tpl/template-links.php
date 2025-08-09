<?php
/*
Template Name: Links
Template Post Type: page
*/
get_header();
?>


<main class="layoutSingleColumn layoutSingleColumn--wide">
    <article class="cArticle" itemscope="itemscope" itemtype="http://schema.org/Article">
        <?php while (have_posts()) : the_post(); ?>
            <header class="cArticle--header">
                <h2 class="cArticle--title" itemprop="headline"><?php the_title(); ?></h2>
            </header>
            <?php echo get_link_items(); ?>
        <?php endwhile; ?>
    </article>
</main>

<?php get_footer(); ?>