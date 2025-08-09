<?php
if (post_password_required())
    return;
global $coffinSetting;
?>
<div id="comments" class="cComment--area">
    <h3 class="cComment--heroTitle">
        <?php if (get_comments_number() > 0) {
            printf(
                _n('%s Comment', '%s Comments', get_comments_number(), 'Coffin'),
                number_format_i18n(get_comments_number())
            );
        } else {
            _e('Comments', 'Coffin');
        } ?>
    </h3>
    <ol class="cComment--list">
        <?php
        if (have_comments()) {
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 42,
                'format'      => 'html5',
                'callback'    => 'coffin_comment',
            ));
        } else { ?>
            <li class="no--comment">
                <?php if ($coffinSetting->get_setting('no_reply_text')) {
                    echo $coffinSetting->get_setting('no_reply_text');
                } else {
                    _e('This post has no comment yet', 'Coffin');
                } ?>
            </li>
        <?php } ?>
    </ol>
    <?php the_comments_pagination(array(
        'prev_next' => false,
    )); ?>
    <?php comment_form(); ?>
</div>