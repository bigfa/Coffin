<?php

function coffin_setup(){
    add_theme_support( 'title-tag' );

    add_theme_support( 'post-thumbnails' );

    register_nav_menus( array(
        'top'    => 'Top Menu',
    ) );

    add_theme_support( 'html5', array(
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
}

add_action( 'after_setup_theme', 'coffin_setup' );

function coffin_send_analystic(){
    $current_version = get_option('_coffin_version');
    $api_url = "https://dev.fatesinger.com/_/api/";
    $theme_data = coffin_get_theme();
    if ( $current_version == $theme_data['theme_version'] || $theme_data['site_url'] == 'localhost' ) return;
    $send_body = array_merge(array('action' => 'coffin_send_analystic'), $theme_data);
    $send_for_check = array(
        'body' => $send_body,
        'sslverify' => false,
        'timeout' => 300,
    );
    $response = wp_remote_post($api_url, $send_for_check);
    if ( !is_wp_error($response ) ) update_option( '_coffin_version' , $theme_data['theme_version'] );
}

add_action('after_switch_theme','coffin_send_analystic');

function coffin_get_theme(){
    global $wp_version;
    $theme_name = get_option('template');

    if(function_exists('wp_get_theme')){
        $theme_data = wp_get_theme($theme_name);
        $theme_version = $theme_data->Version;
    } else {
        $theme_data = get_theme_data( PURE_THEME_URL . '/style.css');
        $theme_version = $theme_data['Version'];
    }

    $site_url = home_url();

    return compact('wp_version', 'theme_name', 'theme_version', 'site_url' );

}

require('tgm-plugin-activation/plugins.php');