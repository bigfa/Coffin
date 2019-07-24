<article class="sandraItem">
    <div class="sandraItem-inner">
        <div class="sandraItem-image"><a style="background-image: url(<?php echo coffin_get_background_image($post->ID);?>);" href="<?php the_permalink();?>" title="<?php the_title();?>"></a></div>
        <div class="sandraItem-meta">
            <div class="source-wrapper"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID'));?>"><?php echo get_avatar(get_the_author_meta('ID'),26);?></a></div>
            <h2 class="sandraItem-title"><a href="<?php the_permalink();?>" aria-label="<?php the_title();?>" title="<?php the_title();?>"><?php the_title();?></a></h2>
        </div>
        <div class="sandraItem-info u-clearfix">
            <time><?php echo get_the_date('m d,Y');?></time><span class="middotDivider"></span><?php the_category(',')?>
        </div>
    </div>
</article>