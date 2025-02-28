<?php
if (post_password_required())
    return;
?>
<div id="comments" class="comments-area">
    <div class="layoutSingleColumn">
        <?php if (have_comments()) : ?>
            <h3 class="comments-title">
                <?php echo number_format_i18n(get_comments_number()); ?> 条评论
            </h3>
            <ol class="comment-list commentlist">
                <?php
                wp_list_comments(array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 42,
                    'format'      => 'html5',
                    'callback'    => 'coffin_comment',
                ));
                ?>
            </ol>
            <?php the_comments_pagination(array(
                'prev_text' => '上一页',
                'next_text' => '下一页',
                'prev_next' => false,
            )); ?>
        <?php endif; ?>
        <?php comment_form(); ?>
    </div>
</div>