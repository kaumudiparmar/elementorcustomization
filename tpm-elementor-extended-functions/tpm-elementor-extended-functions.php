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


add_action( 'elementor/widgets/widgets_registered', function( $widgets_manager ) {

   class Customized_Portfolio extends \ElementorPro\Modules\Posts\Widgets\Portfolio {

      protected function render_filter_menu() {
         $taxonomy = $this->get_settings( 'taxonomy' );

         if ( ! $taxonomy ) {
            return;
         }

         $terms = [];

         foreach ( $this->get_query()->posts as $post ) {
            $terms += $post->tags;
         }

         if ( empty( $terms ) ) {
            return;
         }

         usort( $terms, function( $a, $b ) {
            return strcmp( $a->name, $b->name );
         } );

         $current_posttype = $this->get_query()->posts[0]->post_type;
         $filter_title = __( 'Alle', 'tpm-extended' );

         switch ($current_posttype) {
            case 'project':
               $filter_title = __( 'Alle Projekte', 'tpm-extended' );               
               break;

            case 'team':
               $filter_title = __( 'Gesamtes Team', 'tpm-extended' );               
               break;

            default:
               $filter_title = __( 'Alle', 'tpm-extended' );               
               break;
         }

         ?>
         <ul class="elementor-portfolio__filters">
            <li class="elementor-portfolio__filter elementor-active" data-filter="__all"><?php print_r($filter_title); ?></li>
            <?php foreach ( $terms as $term ) { ?>
               <li class="elementor-portfolio__filter" data-filter="<?php echo esc_attr( $term->term_id ); ?>"><?php echo $term->name; ?></li>
            <?php } ?>
         </ul>
         <?php
      }

     
     public function render() {
         $this->query_posts();

         $wp_query = $this->get_query();

         if ( ! $wp_query->found_posts ) {
            return;
         }

         $this->get_posts_tags();
         $this->render_loop_header();

         while ( $wp_query->have_posts() ) {
            $wp_query->the_post();
            if(get_post_type() == 'project'){
                  $this->render_project_post();
            }elseif(get_post_type() == 'team'){
                  $this->render_team_post();
            }else{
                  $this->render_post();   
            }
            
         }

         $this->render_loop_footer();
         wp_reset_postdata();
      }

      /* project post type structure for portfolio START*/
      // Override the render_post as you need
      public function render_project_post() {
        
         $this->render_project_post_header();
         $this->render_project_post_thumbnail();
         $this->render_project_post_footer();
      }

      // Override the render_project_post_header as you need
      public function render_project_post_header(){
         global $post;

         $tags_classes = array_map( function( $tag ) {
         return 'elementor-filter-' . $tag->term_id;
         }, $post->tags );

         $classes = [
         'elementor-portfolio-item',
         'elementor-portfolio-project-item',
         'elementor-post',
         implode( ' ', $tags_classes ),
         ];

         ?>
         <article <?php post_class( $classes ); ?>>
           <div class="elementor-portfolio-project-item_acf_content">
         <?php 
      }

      // Override the render_thumbnail as you need
      public function render_project_post_thumbnail() {  
         ?>
         <div class="elementor-portfolio-project-item_img ">
            <img src="<?php echo get_the_post_thumbnail_url(); ?>" class="project-img" />
         </div>

         <div class="elementor-portfolio-project-data">
            <?php    $this->render_project_taxonomy();
                     $this->render_project_title();
                     $this->render_project_readmore();
            ?>
         </div>
         <?php 
      }

      public function render_project_taxonomy(){
         $taxonomy = $this->get_settings( 'taxonomy' );
         $term_obj_lists = get_the_terms( get_the_ID(), $taxonomy);
         ?>
         <ul class="portfolio-project-tax-lists">
            <?php echo $term_obj_lists[0]->name; ?>
         </ul>
         <?php
      }

      public function render_project_title(){
          if ( ! $this->get_settings( 'show_title' ) ) {
            return;
          }

          $tag = $this->get_settings( 'title_tag' );
          ?>
          <<?php echo $tag; ?> class="elementor-portfolio-project-item__title">
          <?php the_title(); ?>
          </<?php echo $tag; ?>>
          <?php
        }

      public function render_project_readmore(){
         ?>
         <div class="portfolio-project-readmore">
            <a class="port-proj-btn-link" href="<?php echo get_permalink(get_the_ID());?>"><?php _e( 'Mehr', 'tpm-extended' ); ?></a>
         </div>
         <?php
      }

      // Override the render_post_footer as you need  
      public function render_project_post_footer(){
         ?>
         </div>
         </article>
         <?php
      }
      /* project post type structure for portfolio END */



      /* team post type structure for Team START*/ 

      // Override the render_post as you need
      public function render_team_post() {
         $this->render_team_post_header();
         $this->render_team_post_thumbnail();
         $this->render_team_post_footer();
      }

      public function render_team_post_header(){
          global $post;

          $tags_classes = array_map( function( $tag ) {
            return 'elementor-filter-' . $tag->term_id;
          }, $post->tags );

          $classes = [
            'elementor-portfolio-item',
            'elementor-portfolio-team-member-item',
            'elementor-post',
            implode( ' ', $tags_classes ),
          ];

          ?>
          <article <?php post_class( $classes ); ?>>
            <!-- <a class="elementor-post__thumbnail__link" href="<?php echo get_permalink(); ?>"> -->
              <div class="elementor-portfolio-item_acf_content">
          <?php 
      }

      public function render_team_post_thumbnail(){
         $flip_img_icon = plugins_url( '/assets/images/flip.png',ELEMENTOR_TPMEXTENDED);
         $member_image = get_the_post_thumbnail_url();
         $member_free_time_image = get_field('member_free_time_image');
         $member_free_time_image_enable = ($member_free_time_image !='') ? 'active_free_time' : 'not_active_free_time' ;
         ?>
         <div class="elementor-portfolio-team-member-item_img "> <!-- elementor-portfolio-item__img elementor-post__thumbnail -->
         <?php if($member_free_time_image_enable == 'active_free_time'){ ?>
             <a class="port-team-flip-btn" href="javascript:void(0);"><img src="<?php echo $flip_img_icon; ?>" class="team-img-flip-icon" /></a>  
         <?php } ?>

         <?php //echo $thumbnail_html; ?>
         <img src="<?php echo get_the_post_thumbnail_url(); ?>" class="team-member-img" style="display: block;" />

         <?php if($member_free_time_image_enable == 'active_free_time'){ ?>
         <img src="<?php echo $member_free_time_image; ?>" class="team-free-img" style="display: none;" />
         <?php } ?>
         </div>
         <div class="elementor-portfolio-team-member-data">

         <?php $this->render_team_title();     
             $this->render_team_post_acf_data(); ?>
         </div>
         <?php 
      }

      public function render_team_title(){
          if ( ! $this->get_settings( 'show_title' ) ) {
            return;
          }

          $tag = $this->get_settings( 'title_tag' );
          ?>
          <<?php echo $tag; ?> class="elementor-portfolio-team-member-item__title">
          <?php the_title(); ?>
          </<?php echo $tag; ?>>
          <?php
        }

      public function render_team_post_acf_data(){
         $member_position = get_field('member_position');
         $member_email_address = get_field('member_email_address');
         $member_phone_number = get_field('member_phone_number');
         
         if($member_position !=''){ ?>
           <div class="elementor-portfolio-team-member-position"><?php echo $member_position; ?></div>
         <?php } 
          if($member_email_address !=''){ ?>
           <div class="elementor-portfolio-team-member-email"><a href="mailto:<?php echo $member_email_address;  ?>"><?php echo $member_email_address; ?></a></div>
         <?php } 
          if($member_phone_number !=''){ ?>
           <div class="elementor-portfolio-team-member-phone"><a href="tel:<?php echo $member_phone_number; ?>"><?php echo $member_phone_number; ?></a></div>
         <?php } 
      }     
      
      /* Override the render_post_footer as you need*/
      public function render_team_post_footer(){
      ?>
        </div>
        </article> 
      <?php 
      }  
      /* team post type structure for Team END*/      
   }

   $widgets_manager->register_widget_type( new Customized_Portfolio() );
}, 250 );