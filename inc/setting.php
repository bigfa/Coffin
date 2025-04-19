<?php

class  coffinSetting
{
    public $config;

    function __construct($config = [])
    {
        $this->config = $config;
        add_action('admin_menu', [$this, 'setting_menu']);
        add_action('admin_enqueue_scripts', [$this, 'setting_scripts']);
        add_action('wp_ajax_coffin_setting', array($this, 'setting_callback'));
        //add_action('wp_ajax_nopriv_Coffin_setting', array($this, 'setting_callback'));
    }

    function clean_options(&$value)
    {
        $value = stripslashes($value);
    }

    function setting_callback()
    {
        $data = $_POST[COFFIN_SETTING_KEY];
        array_walk_recursive($data,  array($this, 'clean_options'));
        $this->update_setting($data);
        return wp_send_json([
            'code' => 200,
            'message' => __('Success', 'Coffin'),
            'data' => $this->get_setting()
        ]);
    }

    function setting_scripts()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'coffin') {
            wp_enqueue_style('coffin-setting', get_template_directory_uri() . '/build/css/setting.min.css', array(), COFFIN_VERSION, 'all');
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
                ]
            );
        }
    }

    function setting_menu()
    {
        add_menu_page(__('Theme Setting', 'Coffin'), __('Theme Setting', 'Coffin'), 'manage_options', 'coffin', [$this, 'setting_page'], '', 59);
    }

    function setting_page()
    { ?>
        <div class="wrap">
            <h2><?php _e('Theme Setting', 'Coffin') ?>
                <a href="https://docs.wpista.com/" target="_blank" class="page-title-action"><?php _e('Documentation', 'Coffin') ?></a>
            </h2>
            <div class="pure-wrap">
                <div class="leftpanel">
                    <ul class="nav">
                        <?php foreach ($this->config['header'] as $val) {
                            $id = $val['id'];
                            $title = __($val['title'], 'Coffin');
                            $icon = $val['icon'];
                            $class = ($id == "basic") ? "active" : "";
                            echo "<li class=\"$class\"><span id=\"tab-title-$id\"><i class=\"dashicons-before dashicons-$icon\"></i>$title</span></li>";
                        } ?>
                    </ul>
                </div>
                <form id="pure-form" method="POST" action="options.php">
                    <?php
                    foreach ($this->config['body'] as $val) {
                        $id = $val['id'];
                        $class = $id == "basic" ? "div-tab" : "div-tab hidden";
                    ?>
                        <div id="tab-<?php echo $id; ?>" class="<?php echo $class; ?>">
                            <?php if (isset($val['docs'])) : ?>
                                <div class="pure-docs">
                                    <a href="<?php echo $val['docs']; ?>" target="_blank"><?php _e('Documentation', 'Coffin') ?></a>
                                </div>
                            <?php endif; ?>
                            <table class="form-table">
                                <tbody>
                                    <?php
                                    $content = $val['content'];
                                    foreach ($content as $k => $row) {
                                        switch ($row['type']) {
                                            case 'textarea':
                                                $this->setting_textarea($row);
                                                break;

                                            case 'switch':
                                                $this->setting_switch($row);
                                                break;

                                            case 'input':
                                                $this->setting_input($row);
                                                break;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <div class="pure-save"><span id="pure-save" class="button--save"><?php _e('Save', 'Coffin') ?></span></div>
                </form>
            </div>
        </div>
    <?php }

    function get_setting($key = null)
    {
        $setting = get_option(COFFIN_SETTING_KEY);

        if (!$setting) {
            return false;
        }

        if ($key) {
            if (array_key_exists($key, $setting)) {
                return $setting[$key];
            } else {
                return false;
            }
        } else {
            return $setting;
        }
    }

    function update_setting($setting)
    {
        update_option(COFFIN_SETTING_KEY, $setting);
    }

    function empty_setting()
    {
        delete_option(COFFIN_SETTING_KEY);
    }

    function setting_input($params)
    {
        $default = $this->get_setting($params['name']);
    ?>
        <tr>
            <th scope="row">
                <label for="pure-setting-<?php echo $params['name']; ?>"><?php echo __($params['label'], 'Coffin'); ?></label>
            </th>
            <td>
                <input type="text" id="pure-setting-<?php echo $params['name']; ?>" name="<?php printf('%s[%s]', COFFIN_SETTING_KEY, $params['name']); ?>" value="<?php echo $default; ?>" class="regular-text">
                <?php printf('<br /><br />%s', __($params['description'], 'Coffin')); ?>
            </td>
        </tr>
    <?php }

    function setting_textarea($params)
    { ?>
        <tr>
            <th scope="row">
                <label for="pure-setting-<?php echo $params['name']; ?>"><?php echo __($params['label'], 'Coffin'); ?></label>
            </th>
            <td>
                <textarea name="<?php printf('%s[%s]', COFFIN_SETTING_KEY, $params['name']); ?>" id="pure-setting-<?php echo $params['name']; ?>" class="large-text code" rows="5" cols="50"><?php echo $this->get_setting($params['name']); ?></textarea>
                <?php printf('<br />%s', __($params['description'], 'Coffin')); ?>
            </td>
        </tr>
    <?php }

    function setting_switch($params)
    {
        $val = $this->get_setting($params['name']);
        $val = $val ? 1 : 0;
    ?>
        <tr>
            <th scope="row">
                <label for="pure-setting-<?php echo $params['name']; ?>"><?php echo __($params['label'], 'Coffin'); ?></label>
            </th>
            <td>
                <a class="pure-setting-switch<?php if ($val) echo ' active'; ?>" href="javascript:;" data-id="pure-setting-<?php echo $params['name']; ?>">
                    <i></i>
                </a>
                <br />
                <input type="hidden" id="pure-setting-<?php echo $params['name']; ?>" name="<?php printf('%s[%s]', COFFIN_SETTING_KEY, $params['name']); ?>" value="<?php echo $val; ?>" class="regular-text">
                <?php printf('<br />%s', __($params['description'], 'Coffin')); ?>
            </td>
        </tr>
<?php }
}
global $coffinSetting;
$coffinSetting = new coffinSetting(
    [
        "header" => [
            [
                'id' => 'basic',
                'title' => __('Basic Setting', 'Coffin'),
                'icon' => 'basic'
            ],
            [
                'id' => 'feature',
                'title' => __('Feature Setting', 'Coffin'),
                'icon' => 'slider'

            ],
            [
                'id' => 'singluar',
                'title' => __('Singluar Setting', 'Coffin'),
                'icon' => 'feature'
            ],
            [
                'id' => 'meta',
                'title' => __('SNS Setting', 'Coffin'),
                'icon' => 'social-contact'
            ],
            [
                'id' => 'custom',
                'title' => __('Custom Setting', 'Coffin'),
                'icon' => 'interface'
            ]
        ],
        "body" => [
            [
                'id' => 'basic',
                'content' => [
                    [
                        'type' => 'textarea',
                        'name' => 'description',
                        'label' => __('Description', 'Coffin'),
                        'description' => __('Site description', 'Coffin'),
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'headcode',
                        'label' => __('Headcode', 'Coffin'),
                        'description' => __('You can add content to the head tag, such as site verification tags, and so on.', 'Coffin'),
                    ],
                    [
                        'type' => 'input',
                        'name' => 'logo',
                        'label' => __('Logo', 'Coffin'),
                        'description' => __('Logo address.', 'Coffin'),
                    ],
                    [
                        'type' => 'input',
                        'name' => 'og_default_thumb',
                        'label' => __('Og default thumb', 'Coffin'),
                        'description' => __('Og meta default thumb address.', 'Coffin'),
                    ],
                    [
                        'type' => 'input',
                        'name' => 'favicon',
                        'label' => __('Favicon', 'Coffin'),
                        'description' => __('Favicon address', 'Coffin'),
                    ],
                    [
                        'type' => 'input',
                        'name' => 'title_sep',
                        'label' => __('Title sep', 'Coffin'),
                        'description' => __('Default is', 'Coffin') . '<code>-</code>',
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'disable_block_css',
                        'label' => __('Disable block css', 'Coffin'),
                        'description' => __('Do not load block-style files.', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'gravatar_proxy',
                        'label' => __('Gravatar proxy', 'Coffin'),
                        'description' => __('Gravatar proxy domain,like <code>cravatar.cn</code>', 'Coffin'),
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'rss_tag',
                        'label' => __('RSS Tag', 'Coffin'),
                        'description' => __('You can add tag in rss to verify follow.', 'Coffin'),
                    ],
                ]
            ],
            [
                'id' => 'feature',
                'docs' => 'https://docs.wpista.com/config/feature.html',
                'content' => [
                    [
                        'type' => 'switch',
                        'name' => 'auto_update',
                        'label' => __('Update notice', 'Coffin'),
                        'description' => __('Get theme update notice.', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'upyun',
                        'label' => __('Upyun CDN', 'Coffin'),
                        'description' => __('Make sure all images are uploaded to Upyun, otherwise thumbnails may not display properly.', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'oss',
                        'label' => __('Aliyun OSS CDN', 'Coffin'),
                        'description' => __('Make sure all images are uploaded to Aliyun OSS, otherwise thumbnails may not display properly.', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'qiniu',
                        'label' => __('Qiniu OSS CDN', 'Coffin'),
                        'description' => __('Make sure all images are uploaded to Qiniu OSS, otherwise thumbnails may not display properly.', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'darkmode',
                        'label' => __('Dark Mode', 'Coffin'),
                        'description' => __('Enable dark mode', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'default_thumbnail',
                        'label' => __('Default thumbnail', 'Coffin'),
                        'description' => __('Default thumbnail address', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'back2top',
                        'label' => __('Back to top', 'Coffin'),
                        'description' => __('Enable back to top', 'Coffin')
                    ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'loadmore',
                    //     'label' => __('Load more', 'Coffin'),
                    //     'description' => __('Enable load more', 'Coffin')
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'home_author',
                    //     'label' => __('Author info', 'Coffin'),
                    //     'description' => __('Enable author info in homepage', 'Coffin')
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'home_cat',
                    //     'label' => __('Category info', 'Coffin'),
                    //     'description' => __('Enable category info in homepage', 'Coffin')
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'home_like',
                    //     'label' => __('Like info', 'Coffin'),
                    //     'description' => __('Enable like info in homepage', 'Coffin')
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'home_image_count',
                    //     'label' => __('Image count', 'Coffin'),
                    //     'description' => __('Show image count of the post', 'Coffin')
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'hide_home_cover',
                    //     'label' => __('Hide home cover', 'Coffin'),
                    //     'description' => __('Hide home cover', 'Coffin')
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'exclude_status',
                    //     'label' => __('Exclude status', 'Coffin'),
                    //     'description' => __('Exclude post type status in homepage', 'Coffin')
                    // ],
                ]
            ],

            [
                'id' => 'singluar',
                'content' => [
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'bio',
                    //     'label' => __('Author bio', 'Coffin'),
                    //     'description' => __('Enable author bio', 'Coffin')
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'author_sns',
                    //     'label' => __('Author sns icons', 'Coffin'),
                    //     'description' => __('Show author sns icons, will not show when author bio is off.', 'Coffin')
                    // ],
                    [
                        'type' => 'switch',
                        'name' => 'related',
                        'label' => __('Related posts', 'Coffin'),
                        'description' => __('Enable related posts', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'postlike',
                        'label' => __('Post like', 'Coffin'),
                        'description' => __('Enable post like', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'post_navigation',
                        'label' => __('Post navigation', 'Coffin'),
                        'description' => __('Enable post navigation', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'show_copylink',
                        'label' => __('Copy link', 'Coffin'),
                        'description' => __('Enable copy link', 'Coffin')
                    ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'category_card',
                    //     'label' => __('Category card', 'Coffin'),
                    //     'description' => __('Show post category info after post.', 'Coffin')
                    // ],
                    [
                        'type' => 'switch',
                        'name' => 'show_parent',
                        'label' => __('Show parent comment', 'Coffin'),
                        'description' => __('Enable show parent comment', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'toc',
                        'label' => __('Table of content', 'Coffin'),
                        'description' => __('Enable table of content', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'toc_start',
                        'label' => __('Start heading', 'Coffin'),
                        'description' => __('Start heading,default h3', 'Coffin')
                    ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'show_rss_btn',
                    //     'label' => __('RSS Button', 'Coffin'),
                    //     'description' => __('Show RSS Button in meta', 'Coffin')
                    // ],
                    [
                        'type' => 'switch',
                        'name' => 'disable_comment_link',
                        'label' => __('Disable comment link', 'Coffin'),
                        'description' => __('Disable comment author url', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'no_reply_text',
                        'label' => __('No reply text', 'Coffin'),
                        'description' => __('Text display when no comment in current post.', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'friend_icon',
                        'label' => __('Friend icon', 'Coffin'),
                        'description' => __('Show icon when comment author url is in blogroll.', 'Coffin')
                    ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'image_zoom',
                    //     'label' => __('Post image zoom', 'Coffin'),
                    //     'description' => __('Zoom image when a tag link to image url.', 'Coffin')
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'name' => 'update_time',
                    //     'label' => __('Post update time', 'Coffin'),
                    //     'description' => __('Show the last update time of post.', 'Coffin')
                    // ],
                ]
            ],
            [
                'id' => 'meta',
                'docs' => 'https://docs.wpista.com/config/sns.html',
                'content' => [
                    [
                        'type' => 'switch',
                        'name' => 'footer_sns',
                        'label' => __('SNS Icons', 'Coffin'),
                        'description' => __('Show sns icons in footer, if this setting is on, the footer menu won\',t be displayed.', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'telegram',
                        'label' => __('Telegram', 'Coffin'),
                        'description' => __('Telegram link', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'email',
                        'label' => __('Email', 'Coffin'),
                        'description' => __('Your email address', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'instagram',
                        'label' => __('Instagram', 'Coffin'),
                        'description' => __('Instagram link', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'twitter',
                        'label' => __('Twitter', 'Coffin'),
                        'description' => __('Twitter link', 'Coffin')
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'rss',
                        'label' => __('RSS', 'Coffin'),
                        'description' => __('RSS link', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'github',
                        'label' => __('Github', 'Coffin'),
                        'description' => __('Github link', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'discord',
                        'label' => __('Discord', 'Coffin'),
                        'description' => __('Discord link', 'Coffin')
                    ],
                    [
                        'type' => 'input',
                        'name' => 'mastodon',
                        'label' => __('Mastodon', 'Coffin'),
                        'description' => __('Mastodon link', 'Coffin')
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'custom_sns',
                        'label' => __('Custom', 'Coffin'),
                        'description' => __('Custom sns link,use html.', 'Coffin')
                    ],
                ]
            ],
            [
                'id' => 'custom',
                'content' => [
                    [
                        'type' => 'textarea',
                        'name' => 'css',
                        'label' => __('CSS', 'Coffin'),
                        'description' => __('Custom CSS', 'Coffin')
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'javascript',
                        'label' => __('Javascript', 'Coffin'),
                        'description' => __('Custom Javascript', 'Coffin')
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'copyright',
                        'label' => __('Copyright', 'Coffin'),
                        'description' => __('Custom footer content', 'Coffin')
                    ],
                ]
            ],
        ]
    ]
);
