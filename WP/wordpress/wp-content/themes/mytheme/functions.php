<?php

function enqueue_styles() {

    wp_enqueue_style( 'owl.theme.default.min', get_stylesheet_directory_uri() .'/assets/css/owl.theme.default.min.css');
    wp_enqueue_style( 'owl.caruousel.min', get_stylesheet_directory_uri() .'/assets/css/owl.carousel.min.css');
    wp_enqueue_style( 'maincss', get_stylesheet_directory_uri() .'/assets/css/style.css');

}
add_action('wp_enqueue_scripts', 'enqueue_styles');

function enqueue_scripts () {

    wp_enqueue_script( 'owl.carousel.min', get_template_directory_uri() . '/assets/owl.carousel.min.js' , array(), '2.3.4', true );

//    wp_register_script('html5-shim', 'http://html5shim.googlecode.com/svn/trunk/html5.js');
//    wp_enqueue_script('html5-shim');
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');

show_admin_bar(false);
add_theme_support( 'post-thumbnails' );
