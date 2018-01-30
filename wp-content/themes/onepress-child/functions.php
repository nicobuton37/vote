<?php
/**
 * OnePress Child Theme Functions
 *
 */
/**
 * Enqueue child theme style
 */
add_action( 'wp_enqueue_scripts', 'onepress_child_enqueue_styles', 15 );
function onepress_child_enqueue_styles() {
    wp_enqueue_style( 'onepress-child-style', get_stylesheet_directory_uri() . '/style.css' );
}

add_action('init', 'test', 20);

function test()
{
    var_dump(get_user_meta(14, 'student_budget'));
//    die();
}
