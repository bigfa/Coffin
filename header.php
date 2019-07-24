<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php header("Link: </icons.svg>; rel=preload; as=image", false);?>
    <meta charset="<?php bloginfo('charset');?>">
    <meta name="viewport" content="initial-scale=1.0,minimal-ui">
    <?php wp_head();?>
    <link type="image/vnd.microsoft.icon" href="<?php echo get_template_directory_uri();?>/build/images/favicon.png" rel="shortcut icon">
</head>
<body <?php body_class('is-noJs');?>>
<script>document.body.className = document.body.className.replace(/(^|\s)is-noJs(\s|$)/, "$1is-js$2")</script>
<header class="metabar metabar--bordered metabar--top u-clearfix">
    <div class="layoutSingleColumn--wide">
        <div class="u-floatLeft">
            <a href="/"><img class="logo" src="<?php echo get_template_directory_uri();?>/build/images/logo.png" width=38 /></a>
        </div>
        <?php if ( has_nav_menu( 'top' ) ) : ?>
            <div class="u-floatRight">
                <?php wp_nav_menu( array( 'theme_location' => 'top','menu_class'=>'subnav-ul','container'=>'ul'));?>
            </div>
        <?php endif; ?>
    </div>
</header>