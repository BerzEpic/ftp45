<?php
//C:\xampp\htdocs\wordpress\wp-content\plugins\form-to-pdf\includes
function ftp_add_admin_scripts(){

	wp_register_script( 'pdf-worker-script', plugins_url( '/js/pdf.worker.js', __FILE__ ), '',true );
	wp_enqueue_script( 'pdf-worker-script' );
	
	wp_register_script( 'pdf-script', plugins_url( '/js/pdf.js', __FILE__ ), '',true );
	wp_enqueue_script( 'pdf-script' );

	wp_register_script( 'custom-script', plugins_url( '/js/custom.js', __FILE__ ), array('jquery'), '',true );
	wp_enqueue_script( 'custom-script' );

  //wp_enqueue_style( 'styles-css', plugins_url() . '/form-to-pdf/css/styles.css' );
}


function ftp_add_main_scripts(){
	wp_register_script('jquery-validate-min', 
                      plugins_url('/js/jquery.validate.min.js', __FILE__ ), 
                      array( 'jquery' ) 
                     );
	wp_enqueue_script( 'jquery-validate-min' );
	wp_register_script( 'ftp-main-script', plugins_url( '/js/ftp_main.js', __FILE__ ), array('jquery'), '',true );

	wp_localize_script('ftp-main-script', 'users_obj', array(
		"ajax_url" => admin_url("admin-ajax.php")
		));

	
	wp_enqueue_script( 'ftp-main-script' );

}

add_action( 'admin_enqueue_scripts', 'ftp_add_admin_scripts' );
add_action( 'wp_enqueue_scripts', 'ftp_add_main_scripts' );