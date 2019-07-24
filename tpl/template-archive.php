<?php
/*
Template Name: 文章归档模版
*/
?>
<?php get_header(); ?>
    <header class="page-archive-header layoutSingleColumn layoutSingleColumn--wide">
        <h1 class="page-archive-title"><?php the_title();?></h1>
    </header>
    <div class="layoutSingleColumn layoutSingleColumn--wide">
        <div class="list-archive-wrapper">
            <?php
            $args = array(
                'posts_per_page' => -1,
                'post_type' => array('post'),
                'ignore_sticky_posts' => 1,
            );
            $the_query = new WP_Query( $args );
            $year=0;
            $mon=0;
            $all = array();
            $output = '';
            $i= 0;
            while ( $the_query->have_posts() ) : $the_query->the_post();
                $i++;
                $year_tmp = get_the_time('Y');
                $mon_tmp = get_the_time('n');
                $y = $year;
                $m = $mon;
                if ($mon != $mon_tmp && $mon > 0) $output .= '</ul></div>';
                if ($year != $year_tmp) {
                    $year = $year_tmp;
                    $all[$year] = array();
                }
                if ($mon != $mon_tmp) {
                    $i = 0;
                    $mon = $mon_tmp;
                    $output .= "<div class='list list--archive'><h3 class='month-title'>" . $year . ' - ' . $mon . '</h3>'  . "<ul class='blockGroup is-ordered'>" ;
                }
                $output .= '<li class="archive-item"><a class="archive-item-title" href="'.get_permalink() .'">' . get_the_title() . '</a></li>';
            endwhile;
            wp_reset_postdata();
            $output .= '</ul></div>';
            echo $output;      ?>
        </div>
    </div>
<?php get_footer(); ?>