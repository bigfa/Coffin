<?php

function coffin_enqueue_scripts() {
    global $wp_styles,$pagenow,$wp_query;;

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
        wp_enqueue_script( 'comment-reply' );

    $timer = @filemtime(TEMPLATEPATH .'/build/css/app.css');
    $jstimer = @filemtime(TEMPLATEPATH .'/build/js/app.js');
    wp_enqueue_style('coffin', get_template_directory_uri() . '/build/css/app.css' , array(), $timer , 'screen');
    wp_enqueue_script( 'coffin', get_template_directory_uri() . '/build/js/app.js' , array('jquery'), $jstimer , true );
    wp_localize_script( 'coffin' , 'coffin' , array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
      ) );

    $custom_css = "";
    wp_add_inline_style( 'coffin', $custom_css );

}
add_action( 'wp_enqueue_scripts', 'coffin_enqueue_scripts' );