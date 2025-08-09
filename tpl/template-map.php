<?php
/*
Template Name: Map
Template Post Type: page
*/
?>
<?php get_header(); ?>
<header class="page-archive-header layoutSingleColumn layoutSingleColumn--wide">
    <h1 class="page-archive-title"><?php the_title(); ?></h1>
</header>
<style>
    .footer-map {
        margin-bottom: 60px;
    }
</style>
<div class="layoutSingleColumn layoutSingleColumn--wide">
    <?php if (function_exists('marker_pro_init')) {
        marker_pro_init();
    } else {
        echo '需安装Marker Pro';
    } ?>
</div>
<?php get_footer(); ?>