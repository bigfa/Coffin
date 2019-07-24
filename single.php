<?php get_header();?>
<?php while ( have_posts() ) : the_post();?>
    <div class="layoutSingleColumn">
        <article class="u-paddingTop50">
            <header class="entry-header">
                <div class="entry-meta">
                    <?php echo get_the_date('m d, Y');?></time><span class="middotDivider"></span><?php the_category(',')?>
                </div>
                <?php the_title( '<h2 class="entry-title">', '</h2>' );?>
            </header>
            <div class="grap">
                <?php the_content();?>
            </div>
            <div class="post-actions">
                    <?php if(function_exists('wp_postlike')) wp_postlike(get_the_ID(),'<svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20"><path d="M533.504 268.288q33.792-41.984 71.68-75.776 32.768-27.648 74.24-50.176t86.528-19.456q63.488 5.12 105.984 30.208t67.584 63.488 34.304 87.04 6.144 99.84-17.92 97.792-36.864 87.04-48.64 74.752-53.248 61.952q-40.96 41.984-85.504 78.336t-84.992 62.464-73.728 41.472-51.712 15.36q-20.48 1.024-52.224-14.336t-69.632-41.472-79.872-61.952-82.944-75.776q-26.624-25.6-57.344-59.392t-57.856-74.24-46.592-87.552-21.504-100.352 11.264-99.84 39.936-83.456 65.536-61.952 88.064-35.328q24.576-5.12 49.152-1.536t48.128 12.288 45.056 22.016 40.96 27.648q45.056 33.792 86.016 80.896z"></path></svg>');?>
                </div>
            <?php echo get_the_tag_list('<div class="tag-list">','','</div>');?>
            <div class="narrot-card">
                <div class="narrot-card-imageWrapper">
                    <a href="<?php echo get_author_posts_url( get_the_author_meta('ID') )?>"><?php echo get_avatar(get_the_author_meta( 'ID'),64);?></a>
                </div>
                <div class="narrot-card-content"><a href="<?php echo get_author_posts_url( get_the_author_meta('ID') )?>"><?php the_author();?></a></div>
                <div class="narrot-card-description">
                    <p><?php the_author_meta('description');?></p>
                </div>
            </div>
        </article>
    </div>
    <?php
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;
    ?>
<?php endwhile; ?>
<?php get_footer(); ?>