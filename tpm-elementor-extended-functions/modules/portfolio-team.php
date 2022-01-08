<?php
/**
 * TPMTeamMember class for extend filter functionality
 */


class TPMTeamMember 
{
  
  function __construct()
  {
  }

  public function render($content, $widget){
      ob_start();
      $settings = $widget->get_settings();

      $widget->query_posts();

      $wp_query = $widget->get_query();

      if ( ! $wp_query->found_posts ) {
        return;
      }

      $this->get_posts_tags($widget, $wp_query);

      $this->render_loop_header($widget, $wp_query);

      while ( $wp_query->have_posts() ) {
        $wp_query->the_post();

        $this->render_post($widget);
      }

      $this->render_loop_footer();

      wp_reset_postdata();
      
    return ob_get_clean();
  }

  public function get_posts_tags( $widget, $wp_query ){
      $taxonomy = $widget->get_settings( 'taxonomy' );

    foreach ( $wp_query->posts as $post ) {
      if ( ! $taxonomy ) {
        $post->tags = [];

        continue;
      }

      $tags = wp_get_post_terms( $post->ID, $taxonomy );

      $tags_slugs = [];

      foreach ( $tags as $tag ) {
        $tags_slugs[ $tag->term_id ] = $tag;
      }

      $post->tags = $tags_slugs;
    }
  }

  public function render_loop_header($widget, $wp_query){
    if ( $widget->get_settings( 'show_filter_bar' ) ) {
      $this->render_filter_menu($widget, $wp_query);
    }
    ?>
    <div class="elementor-portfolio elementor-grid elementor-posts-container">
    <?php
  }

  public function render_filter_menu($widget, $wp_query) {
      $taxonomy = $widget->get_settings( 'taxonomy' );

      if ( ! $taxonomy ) {
        return;
      }

      $terms = [];

      foreach ( $wp_query->posts as $post ) {
        $terms += $post->tags;
      }

      if ( empty( $terms ) ) {
        return;
      }

      usort( $terms, function( $a, $b ) {
        return strcmp( $a->name, $b->name );
      } );

      ?>
      <ul class="elementor-portfolio__filters">
        <li class="elementor-portfolio__filter elementor-active" data-filter="__all"><?php echo __( 'Gesamtes Team', 'elementor-pro' ); ?></li>
        <?php foreach ( $terms as $term ) { ?>
          <li class="elementor-portfolio__filter" data-filter="<?php echo esc_attr( $term->term_id ); ?>"><?php echo $term->name; ?></li>
        <?php } ?>
      </ul>
      <?php
    }

  public function render_loop_footer(){
    ?>
    </div>
    <?php

  }

  public function render_post($widget){
      $this->render_post_header();
      $this->render_thumbnail($widget);
      $this->render_post_footer();
  }

  public function render_post_header(){
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

  public function render_thumbnail($widget){
    //$settings = $widget->get_settings();

   /* $settings['thumbnail_size'] = [
      'id' => get_post_thumbnail_id(),
    ];*/

    //$thumbnail_html = Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail_size' );

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

    <?php $this->render_title($widget);     
          $this->render_custom_acf_data(); ?>
    </div>
    <?php 
  }

  public function render_custom_acf_data(){
      $member_position = get_field('member_position');
      $member_email_address = get_field('member_email_address');
      $member_phone_number = get_field('member_phone_number');
      ?>

      <?php if($member_position !=''){ ?>
        <div class="elementor-portfolio-team-member-position"><?php echo $member_position; ?></div>
      <?php } ?>
      <?php if($member_email_address !=''){ ?>
        <div class="elementor-portfolio-team-member-email"><a href="mailto:<?php echo $member_email_address;  ?>"><?php echo $member_email_address; ?></a></div>
      <?php } ?>
      <?php if($member_phone_number !=''){ ?>
        <div class="elementor-portfolio-team-member-phone"><a href="tel:<?php echo $member_phone_number; ?>"><?php echo $member_phone_number; ?></a></div>
      <?php } 
      
  }

  public function render_title($widget){
    if ( ! $widget->get_settings( 'show_title' ) ) {
      return;
    }

    $tag = $widget->get_settings( 'title_tag' );
    ?>
    <<?php echo $tag; ?> class="elementor-portfolio-team-member-item__title">
    <?php the_title(); ?>
    </<?php echo $tag; ?>>
    <?php
  }

  public function render_post_footer(){
  ?>
    </div> <!-- elementor-portfolio-item_acf_content -->  
    <!-- </a> -->
    </article>
  <?php
  }  

}

$TPMTeamMember = new TPMTeamMember();
$content = $TPMTeamMember->render($content, $widget);