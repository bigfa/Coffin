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

function coffin_get_post_views($post_id = 0)
{

    $views_number = (int)get_post_meta($post_id, COFFIN_POST_VIEW_KEY, true);

    /**
     * Filters the returned views for a post.
     *
     * @since Coffin 2.1.0
     */
    return apply_filters('coffin_get_post_views', $views_number, $post_id);
}

/**
 * Get post views
 *
 * @since Coffin 2.1.0
 *
 * @param post id
 * @return post views
 */

function coffin_get_post_views_text($zero = false, $one = false, $more = false, $post = 0)
{
    $views = coffin_get_post_views($post);
    if ($views == 0) {
        return $zero ? $zero : __('No views yet', 'Coffin');
    } elseif ($views == 1) {
        return $one ? $one : __('1 view', 'Coffin');
    } else {
        return $more ? str_replace('%d', $views, $more) : sprintf(__('%d views', 'Coffin'), $views);
    }
}

function coffin_get_post_image_count($post_id)
{
    $content = get_post_field('post_content', $post_id);
    $content = apply_filters('the_content', $content);
    preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
    return count($strResult[1]);
}


function coffin_get_post_read_time($post_id)
{
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed is 200 wpm

    $image_count = coffin_get_post_image_count($post_id);
    if ($image_count > 0) {
        $reading_time += ceil($image_count / 10); // Add extra time for images
    }

    return $reading_time;
}

function coffin_get_post_read_time_text($post_id)
{
    $reading_time = coffin_get_post_read_time($post_id);
    if ($reading_time <= 1) {
        return __('1 min read', 'Coffin');
    } else {
        return sprintf(__('%d min read', 'Coffin'), $reading_time);
    }
}
