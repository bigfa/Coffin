<?php
define('COFFIN_VERSION', wp_get_theme()->get('Version'));
define('COFFIN_SETTING_KEY', 'coffin_setting');
define('COFFIN_ARCHIVE_VIEW_KEY', 'coffin_post_view');
define('COFFIN_POST_VIEW_KEY', 'coffin_post_view');
define('COFFIN_POST_LIKE_KEY', 'coffin_post_like');

load_theme_textdomain('Coffin', get_template_directory() . '/languages');


require('inc/setting.php');
require('inc/base.php');
require('inc/comment.php');
require('inc/update.php');

function coffin_get_background_image($post_id, $width = null, $height = null)
{
    global $coffinSetting;
    if (has_post_thumbnail($post_id)) {
        $timthumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
        $output       = $timthumb_src[0];
    } elseif (get_post_meta($post_id, '_banner', true)) {
        $output = get_post_meta($post_id, '_banner', true);
    } else {
        $content         = get_post_field('post_content', $post_id);
        $defaltthubmnail = $coffinSetting->get_setting('default_thumbnail') ? $coffinSetting->get_setting('default_thumbnail') : get_template_directory_uri() . '/build/images/default.jpeg';
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        $n = count($strResult[1]);
        if ($n > 0) {
            $output = $strResult[1][0];
        } else {
            $output = $defaltthubmnail;
            return $output;
        }
    }

    if ($height && $width) {
        if ($coffinSetting->get_setting('upyun')) {
            $output = $output . "!/both/{$width}x{$height}";
        }

        if ($coffinSetting->get_setting('oss')) {
            $output = $output . "?x-oss-process=image/crop,w_{$width},h_{$height}";
        }

        if ($coffinSetting->get_setting('qiniu')) {
            $output = $output . "?imageView2/1/w/{$width}/h/{$height}";
        }
    }

    return $output;
}

function coffin_is_has_image($post_id)
{
    static $has_image;
    global $post;
    if (has_post_thumbnail($post_id)) {
        $has_image = true;
    } elseif (get_post_meta($post_id, '_banner', true)) {
        $has_image = true;
    } else {
        $content = get_post_field('post_content', $post_id);
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        $n = count($strResult[1]);
        if ($n > 0) {
            $has_image = true;
        } else {
            $has_image = false;
        }
    }

    return $has_image;
}


/**
 * Get link items by categroy id
 *
 * @since Coffin 2.1.0
 *
 * @param term id
 * @return link item list
 */

function get_the_link_items($id = null)
{
    $bookmarks = get_bookmarks('orderby=date&category=' . $id);
    $output = '';
    if (!empty($bookmarks)) {
        $output .= '<div class="link-items">';
        foreach ($bookmarks as $bookmark) {
            $image = $bookmark->link_image ? '<img src="' . $bookmark->link_image . '" alt="' . $bookmark->link_name . '" class="avatar">' : get_avatar($bookmark->link_notes, 64);
            $output .=  '<a class="link-item" href="' . $bookmark->link_url . '" title="' . $bookmark->link_description . '" target="_blank" >
             ' . $image . '
             <strong>' . $bookmark->link_name . '</strong><span class="sitename">' . $bookmark->link_description . '</span></a>';
        }
        $output .= '</div>';
    } else {
        $output = __('No links yet', 'Hera');
    }
    return $output;
}

/**
 * Get link items
 *
 * @since Coffin 2.1.0
 *
 * @return link iterms
 */

function get_link_items()
{
    $linkcats = get_terms('link_category');
    $result = '';
    if (!empty($linkcats)) {
        foreach ($linkcats as $linkcat) {
            $result .=  '<h3 class="link-title">' . $linkcat->name . '</h3>';
            if ($linkcat->description) $result .= '<div class="link-description">' . $linkcat->description . '</div>';
            $result .=  get_the_link_items($linkcat->term_id);
        }
    } else {
        $result = get_the_link_items();
    }
    return $result;
}
