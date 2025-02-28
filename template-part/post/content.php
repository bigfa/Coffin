<article class="sandraItem<?php if (get_the_ID() == 102750) echo ' sandraItem--full'; ?>">
    <div class="sandraItem-image">
        <a style="background-image: url(<?php echo coffin_get_background_image($post->ID); ?>);" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">

        </a>
    </div>
    <div class="sandraItem--content">
        <div class="sandraItem-meta">
            <h2 class="sandraItem-title"><a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
        </div>
        <div class="sandraItem-info">
            <time itemprop="datePublished" datetime="<?php echo get_the_date('c'); ?>" class="humane--time">
                <?php echo get_the_date('m d,Y'); ?></time><span class="middotDivider"></span><?php the_category(',') ?>
        </div>
        <?php if (get_the_terms(get_the_ID(), 'country')) :
            $country = get_the_terms(get_the_ID(), 'country');
            $country = array_shift($country);
            $slug = $country->slug;
        ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/flags/<?php echo $slug; ?>.svg" alt="<?php echo $country->name; ?>" class="flag">
        <?php endif; ?>
    </div>
</article>