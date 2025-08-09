<?php
/*
Template Name: Archive
Template Post Type: page
*/
?>
<?php get_header(); ?>
<div class="layoutSingleColumn layoutSingleColumn--wide">
    <div class="cArticle">
        <header class="cArticle--header">
            <h2 class="cArticle--title"><?php the_title(); ?></h2>
        </header>
        <div class="list-archive-wrapper">
            <?php
            $args = array(
                'posts_per_page' => -1,
                'post_type' => array('post'),
                'ignore_sticky_posts' => 1,
            );
            $the_query = new WP_Query($args);
            $posts = [];

            $all = [];
            $output = '';
            while ($the_query->have_posts()) : $the_query->the_post();
                $posts[] = [
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'time' => get_the_time('Y-m-d'),
                    'year' => get_the_time('Y'),
                    'mon' => get_the_time('n'),
                    'readtime' => '<span class="middotDivider"></span>' . coffin_get_post_read_time_text(get_the_ID()),
                ];
            endwhile;
            wp_reset_postdata();
            // group post by year
            foreach ($posts as $key => $val) {
                $all[$val['year']][$val['mon']][] = $val;
            }

            // list years

            $years = array_keys($all);
            // echo '<nav class="year-nav">';
            // foreach ($years as $year) {
            //     echo '<span class="year-item">' . $year . '</span>';
            // }
            // echo '</nav>';

            // list posts
            foreach ($all as $year => $months) {
                echo '<div class="year-wrapper" id="year-' . $year . '">';
                echo '<h2 class="year-title">' . $year . '</h2>';
                foreach ($months as $mon => $posts) {
                    echo '<h3 class="month-title">' . $mon . ' 月</h3>';
                    echo '<ul class="month-posts">';
                    foreach ($posts as $post) {
                        echo '<li class="month-post"><a href="' . $post['permalink'] . '" class="post-title">' . $post['title'] . '</a><span class="post-time">' . $post['time'] . $post['readtime'] . '</span></li>';
                    }
                    echo '</ul>';
                }
                echo '</div>';
            }

            ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>