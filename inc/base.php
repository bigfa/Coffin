<?php
class coffinBase
{

    public function __construct()
    {
        global $coffinSetting;
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('excerpt_length', array($this, 'excerpt_length'));
        add_filter('excerpt_more', array($this, 'excerpt_more'));
        add_filter("the_excerpt", array($this, 'custom_excerpt_length'), 999);
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption'
        ));
        add_theme_support('title-tag');
        register_nav_menu('coffin', __('Primary Menu', 'Coffin'));
        add_theme_support('post-formats', array('status'));
        add_filter('pre_option_link_manager_enabled', '__return_true');
        add_action('widgets_init', array($this, 'widgets_init'));
        add_action('wp_head', array($this, 'head_output'), 11);
        add_action('edit_category_form_fields', array($this, 'add_category_cover_form_item'));
        add_action('edited_terms', array($this, 'update_my_category_fields'));
        add_theme_support('post-thumbnails');
        // add_filter('template_include', array($this, 'category_card_template'), 1);
        if ($coffinSetting->get_setting('toc'))
            add_filter('the_content', array($this, 'coffin_toc'));
        if ($coffinSetting->get_setting('gravatar_proxy'))
            add_filter('get_avatar_url', array($this, 'gravatar_proxy'), 10, 3);

        add_action('admin_enqueue_scripts', array($this, 'admin_enquenue_scripts'));

        if ($coffinSetting->get_setting('exclude_status'))
            add_filter('pre_get_posts', array($this, 'exclude_post_format'));
        if ($coffinSetting->get_setting('image_zoom'))
            add_filter('the_content', array($this, 'image_zoom'));

        if ($coffinSetting->get_setting('rss_tag'))
            add_action('rss2_head', array($this, 'add_rss_tag'));
    }


    function add_rss_tag()
    {
        global $coffinSetting;
        echo $coffinSetting->get_setting('rss_tag');
    }


    function image_zoom($content)
    {
        $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>(.*?)<\/a>/i";
        $replacement = '<a$1href=$2$3.$4$5 data-action="imageZoomIn" $6>$7</a>';
        $content = preg_replace($pattern, $replacement, $content);
        return $content;
    }

    function exclude_post_format($query)
    {
        if ($query->is_home() && $query->is_main_query()) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-status'),
                    'operator' => 'NOT IN'
                )
            ));
        }
    }

    function update_my_category_fields($term_id)
    {
        if (isset($_POST['taxonomy']) && $_POST['taxonomy'] == 'category') :
            if ($_POST['_category_cover']) {
                update_term_meta($term_id, '_thumb', $_POST['_category_cover']);
            } else {
                delete_term_meta($term_id, '_thumb');
            }

            if ($_POST['_category_card']) {
                update_term_meta($term_id, '_card', 1);
            } else {
                delete_term_meta($term_id, '_card');
            }

        endif;
    }

    function category_card_template($template_path)
    {
        global $wp_query;
        if (is_category()) {
            $category_id = get_queried_object_id();
            $card = get_term_meta($category_id, '_card', true);
            if ($card) {
                $template_path = get_template_directory() . '/category-travel.php';
            }
        }
        return $template_path;
    }

    //Adds the custom title box to the category editor
    function add_category_cover_form_item($category)
    {
        $cover  = get_term_meta($category->term_id, '_thumb', true);
        // $card  = get_term_meta($category->term_id, '_card', true); 
?>
        <table class="form-table">
            <tr class="form-field">
                <th scope="row" valign="top"><label for="_category_cover"><?php _e('Cover', 'Coffin'); ?></label></th>
                <td><input name="_category_cover" id="_category_cover" type="text" size="40" aria-required="false" value="<?php echo $cover; ?>" class="regular-text ltr" />
                    <p class="description"><button id="upload-categoryCover" class="button"><?php _e('Upload', 'Coffin'); ?></button></p>
                    <p class="description"><?php _e('Category cover url.', 'Coffin'); ?></p>
                </td>
            </tr>
        </table>
<?php }

    function gravatar_proxy($url, $id_or_email, $args)
    {
        global $coffinSetting;
        $url = str_replace(array("www.gravatar.com", "cn.gravatar.com", "0.gravatar.com", "1.gravatar.com", "2.gravatar.com", "secure.gravatar.com"), $coffinSetting->get_setting('gravatar_proxy'), $url);
        return $url;
    }

    function coffin_toc($content)
    {
        global $coffinSetting;
        $toc_start = $coffinSetting->get_setting('toc_start') ? $coffinSetting->get_setting('toc_start') : 3;
        preg_match_all('/<h([' . $toc_start . '-6]).*?>(.*?)<\/h[' . $toc_start . '-6]>/i', $content, $matches, PREG_SET_ORDER);

        if ($matches && is_singular()) {
            $toc = '<ul>';
            $previous_level = 3;
            $count = 1;

            foreach ($matches as $match) {
                $level = $match[1];
                $title = $match[2];
                if ($level > $previous_level) {
                    $toc .= '<ul>';
                } elseif ($level < $previous_level) {
                    $toc .= str_repeat('</ul></li>', $previous_level - $level);
                } else {
                    $toc .= '</li>';
                }

                $toc .= sprintf('<li><a href="#toc-%s">%s</a>', $count, $title);
                $content = str_replace($match[0], sprintf('<h%s id="toc-%s">%s</h%s>', $level, $count, $title, $level), $content);

                $previous_level = $level;
                $count++;
            }

            $toc .= str_repeat('</li></ul>', $previous_level - 2);
            $toc .= '</ul>';

            $content = '<details class="coffin--toc" open><summary>' . __('Table of content', 'Coffin') . '</summary>' . $toc . '</details>' . $content;
        }

        return $content;
    }

    function head_output()
    {
        global $wp, $post, $coffinSetting;
        $current_url = home_url(add_query_arg(array(), $wp->request));

        //echo '<link type="image/vnd.microsoft.icon" href="/favicon.png" rel="shortcut icon">';

        $description = '';
        $blog_name = get_bloginfo('name');
        $ogmeta = '<meta property="og:title" content="' . wp_get_document_title() . '">';
        $ogmeta .= '<meta property="og:url" content="' . $current_url . '">';
        if (is_singular()) {
            $ID = $post->ID;
            $author = $post->post_author;
            if (get_post_meta($ID, "_desription", true)) {
                $description = get_post_meta($ID, "_desription", true);
            } else {
                $description = $post->post_title . '，' . __('author', 'Coffin') . ':' . get_the_author_meta('nickname', $author) . '，' . __('published on', 'Coffin') . get_the_date('Y-m-d');
            }
            echo '<meta name="description" content="' . $description . '">';
            $ogmeta .= '<meta property="og:image" content="' . coffin_get_background_image($ID) . '">';
            $ogmeta .= '<meta property="og:description" content="' . $description . '">';
            $ogmeta .= '<meta property="og:type" content="article">';
            $twitter_meta = '<meta name="twitter:card" content="summary_large_image">';
            $twitter_meta .= '<meta name="twitter:image:src" content="' . coffin_get_background_image($post->ID) . '">';
            $twitter_meta .= '<meta name="twitter:site" content="@fatesinger">';
            $twitter_meta .= '<meta name="twitter:title" content="' . $post->post_title . '">';
            $twitter_meta .= '<meta name="twitter:description" content="' . $description . '">';
            echo $twitter_meta;
        } else {
            if (is_home()) {
                $description = $coffinSetting->get_setting('description');
            } elseif (is_category()) {
                $description = single_cat_title('', false) . " - " . trim(strip_tags(category_description()));
            } elseif (is_tag()) {
                $description = trim(strip_tags(tag_description()));
            } else {
                $description = $coffinSetting->get_setting('description');
            }
            $description = mb_substr($description, 0, 220, 'utf-8');
            if ($coffinSetting->get_setting('og_default_thumb')) {
                $ogmeta .= '<meta property="og:image" content="' . $coffinSetting->get_setting('og_default_thumb') . '">';
            }
            if ($description) $ogmeta .= '<meta property="og:description" content="' . $description . '">';
            $ogmeta .= '<meta property="og:type" content="website">';
            if ($description) echo '<meta name="description" content="' . $description . '">';
        }
        echo $ogmeta;
    }

    function widgets_init()
    {

        register_sidebar(array(
            'name'          => __('Homepage Top', 'Coffin'),
            'id'            => 'topbar',
            'description'   => __('Homepage Top', 'Coffin'),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h3 class="heading-title">',
            'after_title'   => '</h3>',
        ));

        register_sidebar(array(
            'name'          => __('Homepage Bottom', 'Coffin'),
            'id'            => 'footerbar',
            'description'   => __('Homepage Bottom', 'Coffin'),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h3 class="heading-title">',
            'after_title'   => '</h3>',
        ));

        register_sidebar(array(
            'name'          => __('Single Pgae Bottom', 'Coffin'),
            'id'            => 'singlefooterbar',
            'description'   => __('Single Pgae Bottom', 'Coffin'),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h3 class="heading-title">',
            'after_title'   => '</h3>',
        ));
    }

    function custom_excerpt_length($excerpt)
    {
        if (has_excerpt()) {
            $excerpt = wp_trim_words(get_the_excerpt(), apply_filters("excerpt_length", 80));
        }
        return $excerpt;
    }

    function excerpt_more($more)
    {
        return '...';
    }

    function excerpt_length($length)
    {
        return 80;
    }

    function admin_enquenue_scripts()
    {
        // check if is category edit page and enquenue wp media
        if (isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'category') {
            wp_enqueue_media();
            wp_enqueue_script('coffin-setting', get_template_directory_uri() . '/build/js/setting.min.js', ['jquery'], COFFIN_VERSION, true);
            wp_localize_script(
                'coffin-setting',
                'obvInit',
                [
                    'is_single' => is_singular(),
                    'post_id' => get_the_ID(),
                    'restfulBase' => esc_url_raw(rest_url()),
                    'nonce' => wp_create_nonce('wp_rest'),
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'success_message' => __('Setting saved success!', 'Coffin'),
                    'upload_title' => __('Upload Image', 'Coffin'),
                    'upload_button' => __('Set Category Image', 'Coffin'),
                ]
            );
        }
    }

    function enqueue_styles()
    {
        global $coffinSetting;
        wp_dequeue_style('global-styles');
        wp_enqueue_style('coffin-style', get_template_directory_uri() . '/build/css/app.min.css', array(), COFFIN_VERSION, 'all');
        if ($coffinSetting->get_setting('css')) {
            wp_add_inline_style('coffin-style', $coffinSetting->get_setting('css'));
        }

        if ($coffinSetting->get_setting('banner')) {
            wp_add_inline_style('coffin-style', '.site-header{background-image:url(' . $coffinSetting->get_setting('banner') . ');}');
        }

        if ($coffinSetting->get_setting('disable_block_css')) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
            wp_dequeue_style('wc-blocks-style');
        }
    }

    function enqueue_scripts()
    {
        global $coffinSetting;
        wp_enqueue_script('coffin-script', get_template_directory_uri() . '/build/js/app.min.js', [], COFFIN_VERSION, true);
        wp_localize_script(
            'coffin-script',
            'obvInit',
            [
                'is_single' => is_singular(),
                'post_id' => get_the_ID(),
                'restfulBase' => esc_url_raw(rest_url()),
                'nonce' => wp_create_nonce('wp_rest'),
                'darkmode' => !!$coffinSetting->get_setting('darkmode'),
                'version' => COFFIN_VERSION,
                'is_archive' => is_archive(),
                'archive_id' => get_queried_object_id(),
                'hide_home_cover' => !!$coffinSetting->get_setting('hide_home_cover'),
                'comment_success_text' => __('Comment submitted successfully!', 'Coffin'),
                'now_text' => __('Just now', 'Coffin'),
                'copy_success_text' => __('Link copied to clipboard!', 'Coffin'),
                'like_success_text' => __('Thanks for your like!', 'Coffin'),
            ]
        );
        if ($coffinSetting->get_setting('javascript')) {
            wp_add_inline_script('coffin-script', $coffinSetting->get_setting('javascript'));
        }
        if (is_singular()) wp_enqueue_script("comment-reply");
    }
}
global $coffinBase;
$coffinBase = new coffinBase();
