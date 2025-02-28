<?php

class coffinComment
{

    public function __construct()
    {
        global $coffinSetting;
        add_action('rest_api_init', array($this, 'register_routes'));
        if ($coffinSetting->get_setting('show_author') &&  !is_admin())
            add_filter('get_comment_author', array($this, 'get_comment_author_hack'), 10, 3);
        if ($coffinSetting->get_setting('show_parent'))
            add_filter('get_comment_text',  array($this, 'hack_get_comment_text'), 0, 2);
        if ($coffinSetting->get_setting('disable_comment_link'))
            add_filter('get_comment_author_link', array($this, 'get_comment_author_link_hack'), 10, 3);
        if ($coffinSetting->get_setting('friend_icon') && !is_admin())
            add_filter('get_comment_author', array($this, 'show_friend_icon'), 10, 3);
    }

    function is_friend($url = '')
    {
        if (empty($url)) {
            return false;
        }
        $urls = get_bookmarks();
        foreach ($urls as $bookmark) {
            // check if the url is contained in the bookmark
            if (strpos($bookmark->link_url, $url) !== false) {
                return true;
            }
        }
    }

    function show_friend_icon($comment_author, $comment_id, $comment)
    {
        $comment_author_url = $comment->comment_author_url;
        // get domain name
        $comment_author_url = parse_url($comment_author_url, PHP_URL_HOST);

        return $this->is_friend($comment_author_url) ?  $comment_author . '<svg viewBox="0 0 64 64" fill="none" role="presentation" aria-hidden="true" focusable="false" class="friend--icon" title="Friend of author."><path fill-rule="evenodd" clip-rule="evenodd" d="M56.48 38.3C58.13 36.58 60 34.6 60 32c0-2.6-1.88-4.57-3.52-6.3-.95-.97-1.98-2.05-2.3-2.88-.33-.82-.35-2.17-.38-3.49-.02-2.43-.07-5.2-2-7.13-1.92-1.92-4.7-1.97-7.13-2h-.43c-1.17-.02-2.29-.04-3.07-.38-.87-.37-1.9-1.35-2.87-2.3C36.58 5.89 34.6 4 32 4c-2.6 0-4.57 1.88-6.3 3.53-.97.94-2.05 1.97-2.88 2.3-.82.32-2.17.34-3.49.37-2.43.03-5.2.08-7.13 2-1.92 1.93-1.97 4.7-2 7.13v.43c-.02 1.17-.04 2.29-.38 3.06-.37.88-1.35 1.9-2.3 2.88C5.89 27.43 4 29.4 4 32c0 2.6 1.88 4.58 3.53 6.3.94.98 1.97 2.05 2.3 2.88.32.82.34 2.17.37 3.49.03 2.43.08 5.2 2 7.13 1.93 1.93 4.7 1.98 7.13 2h.43c1.17.02 2.29.04 3.06.38.88.37 1.9 1.34 2.88 2.3C27.43 58.13 29.4 60 32 60c2.6 0 4.58-1.88 6.3-3.52.98-.95 2.05-1.98 2.88-2.3.82-.33 2.17-.35 3.49-.38 2.43-.02 5.2-.07 7.13-2 1.93-1.92 1.98-4.7 2-7.13v-.43c.02-1.17.04-2.29.38-3.07.37-.87 1.34-1.9 2.3-2.87zM33.1 45.15c-.66.47-1.55.47-2.22 0C27.57 42.8 18 35.76 18 28.9c0-6.85 6.5-10.25 13.26-4.45.43.37 1.05.37 1.48 0 6.76-5.8 13.27-2.4 13.26 4.45 0 6.56-9.57 13.9-12.89 16.24z" fill="#FFC017"></path></svg>' : $comment_author;
    }

    function get_comment_author_link_hack($comment_author_link, $comment_author, $comment_id)
    {
        return $comment_author;
    }

    function register_routes()
    {
        register_rest_route('coffin/v1', '/comment', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_coment_post'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('coffin/v1', '/view', array(
            'methods' => 'get',
            'callback' => array($this, 'handle_post_view'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('coffin/v1', '/like', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_post_like'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('coffin/v1', '/archive/(?P<id>\d+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_archive_view'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('coffin/v1', '/posts', array(
            'methods' => 'get',
            'callback' => array($this, 'handle_posts_request'),
            'permission_callback' => '__return_true',
        ));
    }

    function get_comment_author_hack($comment_author, $comment_id, $comment)
    {
        $post = get_post($comment->comment_post_ID);
        if ($comment->user_id == $post->post_author) {
            $comment_author = $comment_author . '<span class="comment--author__tip">' . __('Author', 'Coffin') . '</span>';
        }
        return $comment_author;
    }

    function handle_posts_request($request)
    {
        $page = $request['page'];
        $query_args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => $page,
            'posts_per_page' => get_option('posts_per_page'),
        );

        if ($request['category']) {
            $query_args['category__in'] = $request['category'];
        }

        if ($request['tag']) {
            $query_args['tag__in'] = $request['tag'];
        }

        if ($request['author']) {
            $query_args['author'] = $request['author'];
        }

        $the_query = new WP_Query($query_args);
        $data = [];
        while ($the_query->have_posts()) {
            $the_query->the_post();
            global $post;
            $data[] = [
                'id' => get_the_ID(),
                'post_title' => get_the_title(),
                'date' => get_the_date(),
                'excerpt' => mb_strimwidth(strip_shortcodes(strip_tags(apply_filters('the_content', $post->post_content))), 0, 150, "..."),
                'author' => get_the_author(),
                'author_avatar_urls' => get_avatar_url(get_the_author_meta('ID'), array('size' => 64)),
                'author_posts_url' => get_author_posts_url(get_the_author_meta('ID')),
                'comment_count' => get_comments_number(),
                'view_count' => (int)get_post_meta(get_the_ID(), COFFIN_POST_VIEW_KEY, true),
                'like_count' => (int)get_post_meta(get_the_ID(), COFFIN_POST_LIKE_KEY, true),
                // 'thumbnail' => coffin_get_background_image(get_the_ID(), 300, 200),
                'permalink' => get_permalink(),
                'categories' => get_the_category(),
                'tags' => get_the_tags(),
                // 'has_image' => coffin_is_has_image(get_the_ID()),
                'day' => get_the_date('d'),
                'post_format' => get_post_format(),
            ];
        }


        return [
            'code' => 200,
            'message' => __('Success', 'Coffin'),
            'data' => $data
        ];
    }

    function handle_archive_view($request)
    {
        $term = get_term($request['id']);
        if (is_wp_error($term)) {
            return [
                'code' => 500,
                'message' => $term->get_error_message()
            ];
        }
        $views = (int)get_term_meta($request['id'], COFFIN_ARCHIVE_VIEW_KEY, true);
        $views++;
        update_term_meta($request['id'], COFFIN_ARCHIVE_VIEW_KEY, $views);
        return [
            'code' => 200,
            'message' => __('Success', 'Coffin'),
            'data' => $views
        ];
    }


    function hack_get_comment_text($comment_text, $comment)
    {
        if (!is_comment_feed() && $comment->comment_parent) {
            $parent = get_comment($comment->comment_parent);
            if ($parent) {
                $parent_link = esc_url(get_comment_link($parent));
                $name        = $parent->comment_author;

                $comment_text =
                    '<a href="' . $parent_link . '" class="comment--parent__link">@' . $name . '</a>'
                    . $comment_text;
            }
        }
        return $comment_text;
    }

    function handle_post_view($data)
    {
        $post_id = $data['id'];
        $post_views = (int)get_post_meta($post_id, COFFIN_POST_VIEW_KEY, true);
        $post_views++;
        update_post_meta($post_id, COFFIN_POST_VIEW_KEY, $post_views);
        return [
            'code' => 200,
            'message' => __('Success', 'Coffin'),
            'data' => $post_views
        ];
    }

    function handle_post_like($request)
    {
        $post_id = $request['id'];
        $post_views = (int)get_post_meta($post_id, COFFIN_POST_LIKE_KEY, true);
        $post_views++;
        update_post_meta($post_id, COFFIN_POST_LIKE_KEY, $post_views);
        return [
            'code' => 200,
            'message' => __('Success', 'Coffin'),
            'data' => $post_views
        ];
    }

    function handle_coment_post($request)
    {
        $comment = wp_handle_comment_submission(wp_unslash($request));
        if (is_wp_error($comment)) {
            $data = $comment->get_error_data();
            if (!empty($data)) {
                return [
                    'code' => 500,
                    'message' => $data
                ];
            } else {
                return [
                    'code' => 500
                ];
            }
        }
        $user = wp_get_current_user();
        do_action('set_comment_cookies', $comment, $user);
        $GLOBALS['comment'] = $comment;
        return [
            'code' => 200,
            'message' => __('Success', 'Coffin'),
            'data' =>  [
                'author_avatar_urls' => get_avatar_url($comment->comment_author_email, array('size' => 64)),
                'comment_author' => $comment->comment_author,
                'comment_author_email' => $comment->comment_author_email,
                'comment_author_url' => $comment->comment_author_url,
                'comment_content' => get_comment_text($comment->comment_ID),
                'comment_date' => date('Y-m-d', strtotime($comment->comment_date)),
                'comment_date_gmt' => $comment->comment_date_gmt,
                'comment_ID' => $comment->comment_ID,
            ]
        ];
    }
}

new coffinComment();

// comment template
function coffin_comment($comment, $args, $depth)

{
    $GLOBALS['comment'] = $comment;
    switch ($comment->comment_type):
        case 'pingback':
        case 'trackback':
?>
            <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
                <div class="pingback-content"><?php comment_author_link(); ?></div>
            <?php
            break;
        default:
            global $post;
            ?>
            <li class="comment" itemtype="http://schema.org/Comment" data-id="<?php comment_ID() ?>" itemscope="" itemprop="comment">
                <div id="comment-<?php comment_ID() ?>" class="comment--block">
                    <div class="comment--info">
                        <div class="comment--avatar">
                            <?php echo get_avatar($comment, 42); ?>
                        </div>
                        <div class="comment--meta">
                            <div class="comment--author" itemprop="author"><?php echo get_comment_author_link(); ?><svg width="16" height="16" viewBox="0 0 64 64" fill="none" role="presentation" aria-hidden="true" focusable="false" class="ge gf">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M56.48 38.3C58.13 36.58 60 34.6 60 32c0-2.6-1.88-4.57-3.52-6.3-.95-.97-1.98-2.05-2.3-2.88-.33-.82-.35-2.17-.38-3.49-.02-2.43-.07-5.2-2-7.13-1.92-1.92-4.7-1.97-7.13-2h-.43c-1.17-.02-2.29-.04-3.07-.38-.87-.37-1.9-1.35-2.87-2.3C36.58 5.89 34.6 4 32 4c-2.6 0-4.57 1.88-6.3 3.53-.97.94-2.05 1.97-2.88 2.3-.82.32-2.17.34-3.49.37-2.43.03-5.2.08-7.13 2-1.92 1.93-1.97 4.7-2 7.13v.43c-.02 1.17-.04 2.29-.38 3.06-.37.88-1.35 1.9-2.3 2.88C5.89 27.43 4 29.4 4 32c0 2.6 1.88 4.58 3.53 6.3.94.98 1.97 2.05 2.3 2.88.32.82.34 2.17.37 3.49.03 2.43.08 5.2 2 7.13 1.93 1.93 4.7 1.98 7.13 2h.43c1.17.02 2.29.04 3.06.38.88.37 1.9 1.34 2.88 2.3C27.43 58.13 29.4 60 32 60c2.6 0 4.58-1.88 6.3-3.52.98-.95 2.05-1.98 2.88-2.3.82-.33 2.17-.35 3.49-.38 2.43-.02 5.2-.07 7.13-2 1.93-1.92 1.98-4.7 2-7.13v-.43c.02-1.17.04-2.29.38-3.07.37-.87 1.34-1.9 2.3-2.87zM33.1 45.15c-.66.47-1.55.47-2.22 0C27.57 42.8 18 35.76 18 28.9c0-6.85 6.5-10.25 13.26-4.45.43.37 1.05.37 1.48 0 6.76-5.8 13.27-2.4 13.26 4.45 0 6.56-9.57 13.9-12.89 16.24z" fill="#FFC017"></path>
                                </svg>
                                <?php echo '<span class="comment-reply-link" onclick="return addComment.moveForm(\'comment-' . $comment->comment_ID . '\', \'' . $comment->comment_ID . '\', \'respond\', \'' . $post->ID . '\')">回复</span>'; ?></div>
                            <div class="comment--time humane--time" itemprop="datePublished" datetime="<?php echo get_comment_date('c'); ?>"><?php echo get_comment_date('M d,Y'); ?></div>
                        </div>
                    </div>
                    <div class="comment--content comment-content" itemprop="description">
                        <?php comment_text(); ?>
                    </div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16" class="u-cursorPointer">
                        <path fill="#6B6B6B" fill-rule="evenodd" d="m3.672 10.167 2.138 2.14h-.002c1.726 1.722 4.337 2.436 5.96.81 1.472-1.45 1.806-3.68.76-5.388l-1.815-3.484c-.353-.524-.849-1.22-1.337-.958-.49.261 0 1.56 0 1.56l.78 1.932L6.43 2.866c-.837-.958-1.467-1.108-1.928-.647-.33.33-.266.856.477 1.598.501.503 1.888 1.957 1.888 1.957.17.174.083.485-.093.655a.56.56 0 0 1-.34.163.43.43 0 0 1-.317-.135s-2.4-2.469-2.803-2.87c-.344-.346-.803-.54-1.194-.15-.408.406-.273 1.065.11 1.447.345.346 2.31 2.297 2.685 2.67l.062.06c.17.175.269.628.093.8-.193.188-.453.33-.678.273a.9.9 0 0 1-.446-.273S2.501 6.84 1.892 6.23c-.407-.406-.899-.333-1.229 0-.525.524.263 1.28 1.73 2.691.384.368.814.781 1.279 1.246m8.472-7.219c.372-.29.95-.28 1.303.244V3.19l1.563 3.006.036.074c.885 1.87.346 4.093-.512 5.159l-.035.044c-.211.264-.344.43-.74.61 1.382-1.855.963-3.478-.248-5.456L11.943 3.88l-.002-.037c-.017-.3-.039-.71.203-.895" clip-rule="evenodd"></path>
                    </svg>
                </div>
    <?php
            break;
    endswitch;
}
