<?php
/*
function ftp_add_footer($content){

  global $ftp_options;

  /*$footer_output =  '<hr>';
  $footer_output .= '<div class="footer-content">';
  $footer_output .= '<span class="dashicons dashicons-facebook"></span>Find me on <a style="color:' . $ftp_options['link_color'] . '"target="_blank" href="' . $ftp_options['facebook_url'] . '">Facebook</a>';
  $footer_output .= '</div>'; */
/*
  $footer_output =  '<hr>';
  $footer_output .= '<div class="footer-content">';
  $footer_output .= '<h2>'.$ftp_options['page_param'].'</h2>';
  $footer_output .= '<h2>'.get_the_title().'</h2>';
  $footer_output .= '</div>'; 

//$ftp_options['page_param']
  if( !strcasecmp(get_the_title(), 'hello test') ){
      return $content . $footer_output;
  }

  return $content;

}
add_filter('the_content', 'ftp_add_footer');