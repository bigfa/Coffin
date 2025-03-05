<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="initial-scale=1.0,minimal-ui">
    <?php wp_head(); ?>
    <link type="image/vnd.microsoft.icon" href="<?php echo get_template_directory_uri(); ?>/build/images/favicon.png" rel="shortcut icon">
</head>

<body <?php body_class('is-noJs'); ?>>
    <?php
    global $coffinSetting;
    if ($coffinSetting->get_setting('darkmode')) : ?>
        <script>
            window.DEFAULT_THEME = "auto";
            if (localStorage.getItem("theme") == null) {
                localStorage.setItem("theme", window.DEFAULT_THEME);
            }
            if (localStorage.getItem("theme") == "dark") {
                document.querySelector("body").classList.add("dark");
            }
            if (localStorage.getItem("theme") == "auto") {
                document.querySelector("body").classList.add("auto");
            }
        </script>
    <?php endif; ?>
    <header class="metabar">
        <div class="layoutSingleColumn--wide metabar--inner">
            <a href="<?php echo home_url(); ?>" class="u-flex"><img class="logo logo--rounded" src="<?php echo get_template_directory_uri(); ?>/build/images/logo.png" width=38 /></a>
            <?php if (has_nav_menu('coffin')) : ?>
                <?php wp_nav_menu(array('theme_location' => 'coffin', 'menu_class' => 'subnav-ul', 'container' => 'ul')); ?>
            <?php endif; ?>
            <?php echo get_search_form(); ?>
        </div>
    </header>