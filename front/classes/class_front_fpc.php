<?php
/**
* FRONT END CLASS
* @package      FPC
* @author Nicolas GUILLAUME
* @since 1.0
*/
class TC_front_fpc {
    
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    private $return;

    function __construct () {
        self::$instance =& $this;
        
        add_action ('wp_head'                   , array($this , 'tc_set_fp_hook'), 10 );
        add_action ('wp_head'                   , array($this , 'tc_set_colors'), 10 );
        add_action( 'wp_enqueue_scripts'        , array( $this , 'tc_enqueue_plug_resources' ) );

    }//end of construct



    function tc_set_colors() {
        $bg_color               = esc_attr( tc__f( '__get_fpc_option' , 'tc_featured_page_background' ) );
        $text_color             = esc_attr( tc__f( '__get_fpc_option' , 'tc_featured_page_text_color' ) );

        printf('<style id="fpc-colors" type="text/css">%1$s%2$s%3$s%4$s</style>',
            "\n\n",

            ( isset( $bg_color) && ! empty( $bg_color) ) ? sprintf( '.fpc-widget-front .round-div {%1$s : %2$s%3$s}%3$s',
                                        "\nborder-color",
                                        $bg_color,
                                        "\n"
                                    ) : '',

            ( isset( $bg_color) && ! empty( $bg_color) ) ? sprintf( '.fpc-container {%1$s : %2$s%3$s}%3$s',
                                        "\nbackground-color",
                                        $bg_color,
                                        "\n"
                                    ) : '',

            ( isset( $text_color) && ! empty( $text_color) ) ? sprintf( '.fpc-marketing .fpc-widget-front h2, .fpc-widget-front > p {%1$s : %2$s%3$s}%3$s',
                                        "\ncolor",
                                        $text_color,
                                        "\n"
                                    ) : ''
        );//end of printf
    }




    function tc_set_fp_hook() {
        $hook               = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_position' ) );
        switch ( $hook ) {
          case 'wp_nav_menu':
              add_filter ('wp_nav_menu'   , array($this , 'tc_fp_after_menu') , 100 , 2 );
              add_filter ('wp_page_menu'  , array($this , 'tc_fp_after_menu') , 100 , 2 );
              $this -> return = true;
          break;
          
          default:
              add_action ( $hook           , array($this , 'tc_fp_block_display'), 10 , 1 );
              $this -> return = false;
          break;
        }//end switch
    }




    function tc_fp_after_menu( $nav_menu , $args ) {
        //enable the filter only if menu location is primary (for natives wordpress themes, can filtered for other themes )
        $args     = (array)$args;
        $location = '';
        if ( isset($args['theme_location']) ) {
          $location = $args['theme_location'];
        }
        if ( TC_utils_fpc::$instance -> tc_get_theme_config('menu') == $location )
          return $nav_menu.$this->tc_fp_block_display( true );
        else
          return $nav_menu;
    }




    /**
    * The template displaying the front page featured page block.
    *
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_fp_block_display() {
        $hook               = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_position' ) );

        //if the hook is loop start, we don't want to display fp in all queries.
        if ( 'loop_start' == $hook && (! is_main_query() || ! in_the_loop() ) )
            return;

        //gets display options
        $tc_show_featured_pages         = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_featured_pages' ) );
        $tc_show_featured_pages_img     = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_featured_pages_img' ) );

        if ( !apply_filters( 'tc_show_fp', 0 != $tc_show_featured_pages && tc__f('__is_home') ) )
          return;

        //gets the featured pages array and sets the fp layout
        $fp_ids                         = apply_filters( 'fpc_featured_pages_ids' , TC_fpc::$instance -> fpc_ids);
        $fp_nb                          = count($fp_ids);
        $fp_per_row                     = apply_filters( 'fpc_per_line', 3 );
        
        //defines the span class
        $span_array = array(
          1 => 12,
          2 => 6,
          3 => 4,
          4 => 3,
          5 => 2,
          6 => 2,
          7 => 2
        );
        $span_value = 4;
        $span_value = ( $fp_per_row > 7) ? 1 : $span_value;
        $span_value = isset( $span_array[$fp_per_row] ) ? $span_array[$fp_per_row] :  $span_value;
        
        //save $args for filter
        $args = array($fp_ids, $fp_nb, $fp_per_row, $span_value);

        ?>

        <?php ob_start(); ?>

            <div class="fpc-container fpc-marketing">
            <script id="fpc-script" type="text/javascript">
                jQuery(document).ready(function () {
                    ! function ($) {
                        //prevents js conflicts
                        "use strict";

                        //adds hover class on hover
                        $(".fpc-widget-front").hover(function () {
                            $(this).addClass("hover")
                        }, function () {
                            $(this).removeClass("hover")
                        });

                        $(window).on( 'load' , function () {
                          //Resizes FP Container dynamically if too small
                          var $FPContainer  = $('.fpc-container'),
                              $FPBlocks     = $('.fpc-span4' , $FPContainer);                        
                          
                          function ChangeFPClass() {
                            var is_resp       = ( $(window).width() > 767 - 15 ) ? false : true;
                            if ( ! is_resp && ( $FPContainer.width() <= 767 ) ) {
                              $FPBlocks.removeClass('fpc-span4').addClass('fpc-span12');
                            } else if ( ! is_resp && ( $FPContainer.width() > 767 ) ) {
                              $FPBlocks.removeClass('fpc-span12').addClass('fpc-span4');
                            }
                          }

                          ChangeFPClass();
                          $(window).resize(function () {
                              setTimeout(ChangeFPClass, 200);
                          });
                        });//end of on load

                    }(window.jQuery)
                });
            </script>
          <?php 
            do_action ('__before_fp') ;

            $j = 1;
            for ($i = 1; $i <= $fp_nb ; $i++ ) {
                  printf('%1$s<div class="fpc-span%2$s fp-%3$s">%4$s</div>%5$s',
                      ( 1 == $j ) ? '<div class="fpc-row-fluid fpc-widget-area" role="complementary">' : '',
                      $span_value,
                      $fp_ids[$i - 1],
                      $this -> tc_fp_single_display( $fp_ids[$i - 1] , $tc_show_featured_pages_img ),
                      ( $j == $fp_per_row || $i == $fp_nb ) ? '</div>' : ''
                  );
            //set $j back to start value if reach $fp_per_row
            $j++;
            $j = ($j == ($fp_per_row + 1)) ? 1 : $j;
            }

            do_action ('__after_fp') ; 
          
            //display edit link for logged in users with edit posts capabilities
            if ( apply_filters('tc_show_fp_edit_link' , is_user_logged_in() ) ) {
              printf('<a class="fpc-edit-link fpc-btn fpc-btn-inverse" href="%1$s" title="%2$s" target="_blank">%2$s</a>',
                admin_url().'customize.php',
                __( 'Edit Featured Pages' , 'customizr' )
              );
            }//end edit attachment condition
          ?>
          </div><!-- .fpc-container -->

        <?php  echo !tc__f( '__is_home_empty') ? apply_filters( 'fpc_after_fp_separator', '<hr class="featurette-divider '.current_filter().'">' ) : ''; ?>

       <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        if ( false == $this -> return)
          echo apply_filters( 'fpc_block_display' , $html, $args );
        else
          return apply_filters( 'fpc_block_display' , $html, $args );
       }





         /**
      * The template displaying one single featured page
      *
      * @package Customizr
      * @since Customizr 3.0
      * @param area are defined in featured-pages templates,show_img is a customizer option
      * @todo better area definition : dynamic
      */
      function tc_fp_single_display( $fp_single_id , $show_img ) {
        //holder declaration
        $fp_holder_img                      = apply_filters ('fp_holder_img' , '<img data-src="holder.js/270x250" alt="Holder Thumbnail" />' );

        //if fps are not set
        if ( null == tc__f( '__get_fpc_option' , 'tc_featured_page_'.$fp_single_id ) || 0 == tc__f( '__get_fpc_option' , 'tc_featured_page_'.$fp_single_id ) ) {
            //admin link if user logged in
            $featured_page_link             = is_user_logged_in() ? apply_filters( 'fpc_link_url', admin_url().'customize.php' , $fp_single_id ) : '';
            $admin_link                     = is_user_logged_in() ? '<a href="'.admin_url().'customize.php" title="'.__( 'Customizer screen' , TC_fpc::$instance -> plug_lang ).'">'.__( 'here' , TC_fpc::$instance -> plug_lang ).'</a>' : '';
            
            //rendering
            $featured_page_id               =  null;
            $featured_page_title            =  apply_filters( 'fpc_title', __( 'Featured page' , TC_fpc::$instance -> plug_lang ) );
            $text                           =  apply_filters( 
                                                'fpc_text', 
                                                sprintf( __( 'Featured page description text : use the page excerpt or set your own custom text in the WordPress customizer screen %s.' , TC_fpc::$instance -> plug_lang ),
                                                  $admin_link 
                                                ),
                                                $fp_single_id,
                                                $featured_page_id
                                              );
            $fp_img                         =  apply_filters ('fpc_img_src' , $fp_holder_img );

        }
          
        else {
            $featured_page_id               = apply_filters( 'fpc_id', esc_attr( tc__f( '__get_fpc_option' , 'tc_featured_page_'.$fp_single_id) ), $fp_single_id );
            $featured_page_link             = apply_filters( 'fpc_link_url', get_permalink( $featured_page_id ), $fp_single_id );
            $featured_page_title            = apply_filters( 'fpc_title', get_the_title( $featured_page_id ), $fp_single_id, $featured_page_id );
            $featured_text                  = apply_filters( 'fpc_text', tc__f( '__get_fpc_option' , 'tc_featured_text_'.$fp_single_id ), $fp_single_id, $featured_page_id );
            $featured_text                  = apply_filters( 'fpc_text_sanitize', strip_tags( html_entity_decode( $featured_text ) ), $fp_single_id, $featured_page_id );

            //get the page/post object
            $page                           = get_post($featured_page_id);
              
            //set page excerpt as default text if no $featured_text
            $text                           = ( empty($featured_text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_excerpt )) : $featured_text ;
            $text                           = ( empty($text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_content )) : $text ;

            //limit text to 200 car
            $default_fp_text_length         = apply_filters( 'fpc_text_length', 200 );
            $text                           = ( strlen($text) > $default_fp_text_length ) ? substr( $text , 0 , strpos( $text, ' ' , $default_fp_text_length) ). ' ...' : $text;
              
            //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
            $fp_img_size                    = apply_filters( 'fpc_img_size' , 'fpc-size' );
            $fp_img_id                      = apply_filters( 'fpc_img_id', false , $fp_single_id , $featured_page_id );

            if ( has_post_thumbnail( $featured_page_id ) && ! $fp_img_id ) {
                  $fp_img_id                = get_post_thumbnail_id( $featured_page_id );

                  //check if fpc-size size exists for attachment and return large if not
                  $image                    = wp_get_attachment_image_src( $fp_img_id , $fp_img_size );
                  $fp_img_size              = ( null == $image[3] ) ? 'medium' : $fp_img_size ;

                  $fp_img                   = get_the_post_thumbnail( $featured_page_id , $fp_img_size);
                  //get height and width
                  $fp_img_height            = $image[2];
                  $fp_img_width             = $image[1];
              }

              //If not uses the first attached image
              else {
                  //look for attachements
                  $tc_args = array(
                    'numberposts'           =>  1,
                    'post_type'             =>  'attachment' ,
                    'post_status'           =>  null,
                    'post_parent'           =>  $featured_page_id,
                    'post_mime_type'        =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' )
                    ); 

                    $attachments            =  ! $fp_img_id ? get_posts( $tc_args) : get_post( $fp_img_id );

                    if ( $attachments) {

                        foreach ( $attachments as $attachment) {
                           //check if fpc-size size exists for attachment and return large if not
                          $image            = wp_get_attachment_image_src( $attachment->ID, $fp_img_size );
                          $fp_img_size      = ( false == $image[3] ) ? 'medium' : $fp_img_size;
                          $fp_img           = wp_get_attachment_image( $attachment->ID, $fp_img_size );
                          //get height and width
                          $fp_img_height    = $image[2];
                          $fp_img_width     = $image[1];
                        }//end foreach

                    }//end if

              }

              //finally we define a default holder if no thumbnail found or page is protected
              $fp_img                 = apply_filters ('fp_img_src' , ( ! isset( $fp_img) || post_password_required($featured_page_id) ) ? $fp_holder_img : $fp_img , $fp_single_id , $featured_page_id );
          }//end if

          //Let's render this
          ob_start();
          ?>

          <div class="fpc-widget-front">
            <?php 
              if ( isset( $show_img) && $show_img == 1 ) { //check if image option is checked
                printf('<div class="thumb-wrapper %1$s">%2$s%3$s</div>',
                   ( $fp_img == $fp_holder_img ) ? 'tc-holder' : '',
                   apply_filters('fpc_round_div' , sprintf('<a class="round-div" href="%1$s" title="%2$s"></a>',
                                                    $featured_page_link,
                                                    $featured_page_title
                                                  ) , 
                                $fp_single_id ),
                   $fp_img
                );
              }//end if image enabled check
              

              //title block
              $tc_fp_title_block  = sprintf('<%1$s>%2$s</%1$s>',
                                    apply_filters( 'fpc_title_tag' , 'h2' ),
                                    $featured_page_title
              );
              echo apply_filters( 'fpc_title_block' , $tc_fp_title_block , $featured_page_title );

              //text block
              $tc_fp_text_block   = sprintf('<p class="fp-text-%1$s">%2$s</p>',
                                    $fp_single_id,
                                    $text
              );
              echo apply_filters( 'fpc_text_block' , $tc_fp_text_block , $fp_single_id , $text);

              //button block
              $tc_fp_button_block = sprintf('<a class="%1$s" href="%2$s" title="%3$s" data-color="%4$s">%5$s</a>',
                                    apply_filters( 'fpc_button_class' ,
                                                sprintf('fpc-btn fpc-btn-primary fp-button %1$s',
                                                    esc_attr( tc__f( '__get_fpc_option' , 'tc_featured_page_button_color') )
                                                ), 
                                                $fp_single_id
                                    ),
                                    $featured_page_link,
                                    $featured_page_title,
                                    esc_attr( tc__f( '__get_fpc_option' , 'tc_featured_page_button_color') ),
                                    apply_filters( 'fpc_button_text' , esc_attr( tc__f( '__get_fpc_option' , 'tc_featured_page_button_text') ) , $fp_single_id )
              );
              echo apply_filters( 'fpc_button_block' , $tc_fp_button_block , $featured_page_link , $featured_page_title , $fp_single_id );

            ?>

          </div><!-- /.fpc-widget-front -->
          
          <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          return apply_filters( 'fpc_single_display' , $html, $fp_single_id, $show_img, $fp_img, $featured_page_link, $featured_page_title, $text );
    }//end of function



    function tc_enqueue_plug_resources() {
        wp_enqueue_style( 
          'fpc-front-style' ,
          plugins_url( 'featured-pages-customizer/front/assets/css/fpc-front.css' ),
          null, 
          null,
          $media = 'all' 
        );

        //holder image
        wp_enqueue_script(
            'holder' ,
            plugins_url( 'featured-pages-customizer/front/assets/js/holder.js' ) ,
            null,
            null, 
            $in_footer = false
        );
    }


} //end of class

