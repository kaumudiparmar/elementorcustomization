<?php
/**
 * Plugin Name: TPM Elementor Extended Functions
 * Description: Extended Functions for the elementor
 * Plugin URI:  https://tpm-solutions.ch/
 * Version:     1.0.0
 * Author:      tpm Solutions AG
 * Author URI:  https://tpm-solutions.ch/
 * Text Domain: tpm-extended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


define( 'ELEMENTOR_TPMEXTENDED', __FILE__ );


add_action('elementor/frontend/after_enqueue_styles', function() {
   wp_enqueue_style( 'tpm-portfolio-team-filter-style', plugin_dir_url( __FILE__ ). 'assets/css/tpm-portfolio-team-filter.css');
});

// after_enqueue_scripts
add_action('elementor/frontend/after_enqueue_scripts', function() {
   wp_enqueue_script( 'tpm-portfolio-team-filter-script', plugin_dir_url( __FILE__ ). 'assets/js/tpm-portfolio-team-filter.js', array('jquery'));
});


add_action( 'elementor/widget/render_content', function( $content, $widget ) {    
    
   if ( 'portfolio' === $widget->get_name() ) {

    $widgetname = $widget->get_name();
    $settings = $widget->get_settings();

    if($settings['posts_post_type'] == 'team'){
        include 'modules/'.$widgetname.'-team.php';
    }
   }
      
   return $content;
}, 10, 2 );


