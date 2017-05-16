<?php
/**
 * Plugin Name: Form to pdf
 * Description: Adds a button link of pdf template filled with dynamique data
 * Version: 1.0.0
 * Author: Saad Maxell
 * Tested up to: 4.4.0
 **/

// exit if plugin accessed directly
if( !defined( 'ABSPATH' ) ) exit;

$ftp_options = get_option('ftp_settings');

//require_once( plugin_dir_path(__FILE__) . '/includes/form-to-pdf-content.php' );
require_once( plugin_dir_path(__FILE__) . '/includes/form-to-pdf-scripts.php' ); 
//require_once( plugin_dir_path(__FILE__) . '/includes/easy-wp-smtp/easy-wp-smtp.php' );












add_action('admin_menu', 'ftp_admin_menus');

function ftp_admin_menus() {

	/* main menu */

		$top_menu_item = 'slb_options_admin_page';

	    add_menu_page( '', 'Form to pdf', 'manage_options', $top_menu_item, $top_menu_item, 'dashicons-email-alt' );

    /* submenu items */

	    // Chosing file to work with
	    add_submenu_page( $top_menu_item, '', 'Choose File', 'manage_options', $top_menu_item, $top_menu_item );

	    // Adjust setting
	    $my_plugins_page = add_submenu_page( $top_menu_item, '', 'Settings', 'manage_options', 'fpt_settings', 'fpt_settings' );

	//    add_action( 'load-' . $my_plugins_page, 'so20659191_enqueue' );


	    // Edit/Delete
	    add_submenu_page( $top_menu_item, '', 'Manage Mail Options', 'manage_options', 'fpt_manage_mail', 'fpt_manage_mail' );

}



function slb_get_input_types(){

	include_once(plugin_dir_path( __FILE__ )."\includes\simplehtmldom\simple_html_dom.php");

	$pageUrl = get_permalink( slb_get_option('slb_manage_subscription_page_id') );

	$input_types = array();

	$html = file_get_html($pageUrl);

	$form = $html->find("form", 0);

	$inputs = $form->find("input");

	foreach ($inputs as $key => $input) {
		if($input->getAttribute('type') !== 'submit'){
			array_push($input_types, $input->getAttribute('type'));
		}
	}

	return $input_types;

	//$content_post = get_post(get_option('slb_select_attachment_id'));

}








function fpt_settings() {

	echo('

				<h2>Settings</h2>

				');
/*$args = array(
	'ID' => get_option('slb_select_attachment_id'),
    'post_type' => 'attachment',

    ); 
$attachments = get_posts($args);
if ($attachments) {
    foreach ($attachments as $post) {
        echo(the_attachment_link($post->ID, false));
    }
}*/
	$input_types = slb_get_input_types();
	$pdfoption 	 = slb_get_pdf_current_options();
	


	echo('
				<form action="options.php" method="post">
					<div class="container">');
					// outputs a unique nounce for our plugin options
					settings_fields('slb_pdf_settings_options');
					// generates a unique hidden field with our form handling url
					@do_settings_fields('slb_pdf_settings_options');

					echo('
				
					<table class="form-table">
						<tbody>
						  <tr>
							<th scope="row"><label for="">test</label></th>
							<td>
								<input type="text">
								 x
								 <input  type="text">
								 y
								 <button type="button" class="button button-primary">Create/Edit</button>
								<p class="description" id="">some discription</p>
							</td>
							<td rowspan="20">
								<div id="special" >
								    <canvas id="the-canvas" width="500px" style="border:1px solid black"></canvas>
								</div>
								<input id="pdf" type="file"/>
							</td>
						</tr>

						  ');
					foreach ($input_types as $key => $input) {
						echo('
						  
							');
						//if($input == 'text'):
							echo('	
							<tr>
								<th scope="row"><label for="">test</label></th>
								<td>
									<input id="demox['.$key.']" name="x'.$key.'" type="text" name="new_option_name" value="'.$pdfoption['x'.$key].'" width="50px">
									 x
									 <input id="demoy['.$key.']" name="y'.$key.'" type="text" value="'.$pdfoption['y'.$key].'" width="50px">
									 y
									 <button type="button" onclick="position('.$key.')" class="button button-primary">Create/Edit</button>
									 <br>
									 <select name="font">
									 	<option>Choose Font</option>
									 	<option value="arial">Arial</option>
									 	<option value="helvatica">Helvatica</option>
									 	<option value="comic">Comic sans ms</option>
									 </select>
									 <input type="color" name="mycolor" value="#000">
									<p class="description" id="">some discription</p>
								</td>
							</tr>

							');
						}
						
					echo('
						
						</tbody>

					</table>
			
				  
		
						');
			// outputs the WP submit button html
			@submit_button();
			echo('	
				</div>
			 </form>
			

	');
}


function slb_get_pdf_current_options() {

	// setup our return variable
	$current_options 	 = array();
	$input_types_count 	 =  count(slb_get_input_types());

	try {

		// build our current options associative array
		for ($i = 0; $i < $input_types_count; $i++) {
		    $current_options["x".$i] = slb_get_pdf_option("x".$i);
		    $current_options["y".$i] = slb_get_pdf_option("y".$i);
		}
	


	} catch( Exception $e ) {

		// php error

	}

	// return current options
	return $current_options;

}


function slb_get_pdf_option($option_name){
	
	// setup return variable
	$option_value = '';
	$input_types_count = count(slb_get_input_types());


	try {

		// get default option values
		//$defaults = slb_get_default_options();


		for ($i = 0; $i < $input_types_count; $i++) {
			if("x".$i == $option_name){
				$option_value = (get_option("x".$i)) ? get_option("x".$i) : '';
				break;
			}
			if("y".$i == $option_name){
		    	$option_value = (get_option("y".$i)) ? get_option("y".$i) : '';
		    	break;
		    }
		}



	} catch( Exception $e) {

		// php error

	}

	// return option value or it's default
	return $option_value;

}





/*================================= Manage Mail ==============================================================*/

add_action('wp_ajax_users_inputs', 'slb_get_users_inputs');
add_action('wp_ajax_nopriv_users_inputs', 'slb_get_users_inputs');

function slb_get_users_inputs(){
	global $postedata;
	$postedata = $_POST['userValues'];
}




function fpt_manage_mail() {

echo '<div class="wrap" id="swpsmtp-mail">';
    echo '<h2>' . __("Easy WP SMTP Settings", 'easy-wp-smtp') . '</h2>';
    echo '<div id="poststuff"><div id="post-body">';

    $display_add_options = $message = $error = $result = '';

    //$swpsmtp_options = get_option('swpsmtp_options');
    //$smtp_test_mail = get_option('smtp_test_mail');
    //if(empty($smtp_test_mail)){
    //    $smtp_test_mail = array('swpsmtp_to' => '', 'swpsmtp_subject' => '', 'swpsmtp_message' => '', );
    //}

    if (isset($_POST['swpsmtp_form_submit']) && check_admin_referer(plugin_basename(__FILE__), 'swpsmtp_nonce_name')) {
        /* Update settings */
        $swpsmtp_options['from_name_field'] = isset($_POST['swpsmtp_from_name']) ? sanitize_text_field(wp_unslash($_POST['swpsmtp_from_name'])) : '';
        if (isset($_POST['swpsmtp_from_email'])) {
            if (is_email($_POST['swpsmtp_from_email'])) {
                $swpsmtp_options['from_email_field'] = sanitize_email($_POST['swpsmtp_from_email']);
            } else {
                $error .= " " . __("Please enter a valid email address in the 'FROM' field.", 'easy-wp-smtp');
            }
        }

        $swpsmtp_options['smtp_settings']['host'] = sanitize_text_field($_POST['swpsmtp_smtp_host']);
        $swpsmtp_options['smtp_settings']['type_encryption'] = ( isset($_POST['swpsmtp_smtp_type_encryption']) ) ? sanitize_text_field($_POST['swpsmtp_smtp_type_encryption']) : 'none';
        $swpsmtp_options['smtp_settings']['autentication'] = ( isset($_POST['swpsmtp_smtp_autentication']) ) ? sanitize_text_field($_POST['swpsmtp_smtp_autentication']) : 'yes';
        $swpsmtp_options['smtp_settings']['username'] = sanitize_text_field($_POST['swpsmtp_smtp_username']);
        $smtp_password = sanitize_text_field($_POST['swpsmtp_smtp_password']);
        $swpsmtp_options['smtp_settings']['password'] = base64_encode($smtp_password);

        /* Check value from "SMTP port" option */
        if (isset($_POST['swpsmtp_smtp_port'])) {
            if (empty($_POST['swpsmtp_smtp_port']) || 1 > intval($_POST['swpsmtp_smtp_port']) || (!preg_match('/^\d+$/', $_POST['swpsmtp_smtp_port']) )) {
                $swpsmtp_options['smtp_settings']['port'] = '25';
                $error .= " " . __("Please enter a valid port in the 'SMTP Port' field.", 'easy-wp-smtp');
            } else {
                $swpsmtp_options['smtp_settings']['port'] = sanitize_text_field($_POST['swpsmtp_smtp_port']);
            }
        }

        /* Update settings in the database */
        if (empty($error)) {
            update_option('swpsmtp_options', $swpsmtp_options);
            $message .= __("Settings saved.", 'easy-wp-smtp');
        } else {
            $error .= " " . __("Settings are not saved.", 'easy-wp-smtp');
        }
    }

    $user_values = $postedata;
    /* Send test letter */
    $swpsmtp_to = '';
    	if (isset($user_values[0])) {
    		$mytestvar = $user_values[0];
            $to_email = sanitize_text_field($mytestvar);
            if (is_email($to_email)) {
                $swpsmtp_to = $to_email;
            } else {
                $error .= __("Please enter a valid email address in the recipient email field.", 'easy-wp-smtp');
            }
        }
        $swpsmtp_subject = isset($user_values[1]) ? sanitize_text_field($user_values[1]) : '';
        $swpsmtp_message = isset($user_values[2]) ? sanitize_text_field($user_values[2]) : '';
        
        //Save the test mail details so it doesn't need to be filled in everytime.
        //$smtp_test_mail['swpsmtp_to'] = $swpsmtp_to;
        //$smtp_test_mail['swpsmtp_subject'] = $swpsmtp_subject;
        //$smtp_test_mail['swpsmtp_message'] = $swpsmtp_message;
        //update_option('smtp_test_mail', $smtp_test_mail);
        
        if (!empty($swpsmtp_to)) {
            $result = swpsmtp_test_mail($swpsmtp_to, $swpsmtp_subject, $swpsmtp_message);
        }
    
    ?>
    <div class="swpsmtp-yellow-box">
        Please visit the <a target="_blank" href="https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197">Easy WP SMTP</a> plugin's documentation page for usage instructions.
    </div>

    <div class="updated fade" <?php if (empty($message)) echo "style=\"display:none\""; ?>>
        <p><strong><?php echo $message; ?></strong></p>
    </div>
    <div class="error" <?php if (empty($error)) echo "style=\"display:none\""; ?>>
        <p><strong><?php echo $error; ?></strong></p>
    </div>
    <div id="swpsmtp-settings-notice" class="updated fade" style="display:none">
        <p><strong><?php _e("Notice:", 'easy-wp-smtp'); ?></strong> <?php _e("The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'easy-wp-smtp'); ?></p>
    </div>

    <div class="postbox">
        <h3 class="hndle"><label for="title"><?php _e('SMTP Configuration Settings', 'easy-wp-smtp'); ?></label></h3>
        <div class="inside">

            <p>You can request your hosting provider for the SMTP details of your site. Use the SMTP details provided by your hosting provider to configure the following settings.</p>
            
            <form id="swpsmtp_settings_form" method="post" action="">					
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e("From Email Address", 'easy-wp-smtp'); ?></th>
                        <td>
                            <input type="text" name="swpsmtp_from_email" value="<?php echo esc_attr($swpsmtp_options['from_email_field']); ?>"/><br />
                            <p class="description"><?php _e("This email address will be used in the 'From' field.", 'easy-wp-smtp'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("From Name", 'easy-wp-smtp'); ?></th>
                        <td>
                            <input type="text" name="swpsmtp_from_name" value="<?php echo esc_attr($swpsmtp_options['from_name_field']); ?>"/><br />
                            <p class="description"><?php _e("This text will be used in the 'FROM' field", 'easy-wp-smtp'); ?></p>
                        </td>
                    </tr>			
                    <tr class="ad_opt swpsmtp_smtp_options">
                        <th><?php _e('SMTP Host', 'easy-wp-smtp'); ?></th>
                        <td>
                            <input type='text' name='swpsmtp_smtp_host' value='<?php echo esc_attr($swpsmtp_options['smtp_settings']['host']); ?>' /><br />
                            <p class="description"><?php _e("Your mail server", 'easy-wp-smtp'); ?></p>
                        </td>
                    </tr>
                    <tr class="ad_opt swpsmtp_smtp_options">
                        <th><?php _e('Type of Encription', 'easy-wp-smtp'); ?></th>
                        <td>
                            <label for="swpsmtp_smtp_type_encryption_1"><input type="radio" id="swpsmtp_smtp_type_encryption_1" name="swpsmtp_smtp_type_encryption" value='none' <?php if ('none' == $swpsmtp_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('None', 'easy-wp-smtp'); ?></label>
                            <label for="swpsmtp_smtp_type_encryption_2"><input type="radio" id="swpsmtp_smtp_type_encryption_2" name="swpsmtp_smtp_type_encryption" value='ssl' <?php if ('ssl' == $swpsmtp_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('SSL', 'easy-wp-smtp'); ?></label>
                            <label for="swpsmtp_smtp_type_encryption_3"><input type="radio" id="swpsmtp_smtp_type_encryption_3" name="swpsmtp_smtp_type_encryption" value='tls' <?php if ('tls' == $swpsmtp_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('TLS', 'easy-wp-smtp'); ?></label><br />
                            <p class="description"><?php _e("For most servers SSL is the recommended option", 'easy-wp-smtp'); ?></p>
                        </td>
                    </tr>
                    <tr class="ad_opt swpsmtp_smtp_options">
                        <th><?php _e('SMTP Port', 'easy-wp-smtp'); ?></th>
                        <td>
                            <input type='text' name='swpsmtp_smtp_port' value='<?php echo esc_attr($swpsmtp_options['smtp_settings']['port']); ?>' /><br />
                            <p class="description"><?php _e("The port to your mail server", 'easy-wp-smtp'); ?></p>
                        </td>
                    </tr>
                    <tr class="ad_opt swpsmtp_smtp_options">
                        <th><?php _e('SMTP Authentication', 'easy-wp-smtp'); ?></th>
                        <td>
                            <label for="swpsmtp_smtp_autentication"><input type="radio" id="swpsmtp_smtp_autentication" name="swpsmtp_smtp_autentication" value='no' <?php if ('no' == $swpsmtp_options['smtp_settings']['autentication']) echo 'checked="checked"'; ?> /> <?php _e('No', 'easy-wp-smtp'); ?></label>
                            <label for="swpsmtp_smtp_autentication"><input type="radio" id="swpsmtp_smtp_autentication" name="swpsmtp_smtp_autentication" value='yes' <?php if ('yes' == $swpsmtp_options['smtp_settings']['autentication']) echo 'checked="checked"'; ?> /> <?php _e('Yes', 'easy-wp-smtp'); ?></label><br />
                            <p class="description"><?php _e("This options should always be checked 'Yes'", 'easy-wp-smtp'); ?></p>
                        </td>
                    </tr>
                    <tr class="ad_opt swpsmtp_smtp_options">
                        <th><?php _e('SMTP username', 'easy-wp-smtp'); ?></th>
                        <td>
                            <input type='text' name='swpsmtp_smtp_username' value='<?php echo esc_attr($swpsmtp_options['smtp_settings']['username']); ?>' /><br />
                            <p class="description"><?php _e("The username to login to your mail server", 'easy-wp-smtp'); ?></p>
                        </td>
                    </tr>
                    <tr class="ad_opt swpsmtp_smtp_options">
                        <th><?php _e('SMTP Password', 'easy-wp-smtp'); ?></th>
                        <td>
                            <input type='password' name='swpsmtp_smtp_password' value='<?php echo esc_attr(swpsmtp_get_password()); ?>' /><br />
                            <p class="description"><?php _e("The password to login to your mail server", 'easy-wp-smtp'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e('Save Changes', 'easy-wp-smtp') ?>" />
                    <input type="hidden" name="swpsmtp_form_submit" value="submit" />
                    <?php wp_nonce_field(plugin_basename(__FILE__), 'swpsmtp_nonce_name'); ?>
                </p>				
            </form>
        </div><!-- end of inside -->
    </div><!-- end of postbox -->


 

    <?php
    echo '</div></div>'; //<!-- end of #poststuff and #post-body -->
    echo '</div>'; //<!--  end of .wrap #swpsmtp-mail .swpsmtp-mail -->

}




/**
 * Plugin functions for init
 * @return void
 */
function swpsmtp_admin_init() {
    /* Internationalization, first(!) */
    load_plugin_textdomain('easy-wp-smtp', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    if (isset($_REQUEST['page']) && 'swpsmtp_settings' == $_REQUEST['page']) {
        /* register plugin settings */
        swpsmtp_register_settings();
    }
}

/**
 * Register settings function
 * @return void
 */
function swpsmtp_register_settings() {
    $swpsmtp_options_default = array(
        'from_email_field' => '',
        'from_name_field' => '',
        'smtp_settings' => array(
            'host' => 'smtp.example.com',
            'type_encryption' => 'none',
            'port' => 25,
            'autentication' => 'yes',
            'username' => 'yourusername',
            'password' => 'yourpassword'
        )
    );

    /* install the default plugin options */
    if (!get_option('swpsmtp_options')) {
        add_option('swpsmtp_options', $swpsmtp_options_default, '', 'yes');
    }
}









































// register plugin options
add_action('admin_init', 'slb_register_options');
add_action('admin_init', 'slb_register_pdf_options');



function slb_options_admin_page() {
	// get the default values for our options
	$options = slb_get_current_options();
	//slb_add_attachement(); 

	echo('<div class="wrap">
		<h2>Snappy List Builder Options</h2>
		<form action="options.php" method="post">');
			// outputs a unique nounce for our plugin options
			settings_fields('slb_plugin_options');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('slb_plugin_options');
			echo('<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="slb_manage_subscription_page_id">Manage Subscriptions Page</label></th>
						<td>
							'. slb_get_page_select( 'slb_manage_subscription_page_id', 'slb_manage_subscription_page_id', 0, 'id', $options['slb_manage_subscription_page_id'] ) .'
							<p class="description" id="slb_manage_subscription_page_id-description">This is the page where Snappy List Builder will send subscribers to manage their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_manage_subscriptions]</strong>.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="slb_confirmation_page_id">Opt-In Page</label></th>
						<td>
							'. slb_get_page_select( 'slb_confirmation_page_id', 'slb_confirmation_page_id', 0, 'id', $options['slb_confirmation_page_id'] ) .'
							<p class="description" id="slb_confirmation_page_id-description">This is the page where Snappy List Builder will send subscribers to confirm their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_confirm_subscription]</strong>.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="slb_select_attachment_id">Select Attachment</label></th>
						<td>
							'. slb_get_attachment_select( 'slb_select_attachment_id', 'slb_select_attachment_id', 0, 'id', $options['slb_select_attachment_id'] ) .'
							<p class="description" id="slb_select_attachment_id-description">This is the page where Snappy List Builder will send subscribers to confirm their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_confirm_subscription]</strong>.</p>
						</td>
					</tr>

				</tbody>

			</table>');

			// outputs the WP submit button html
			@submit_button();


		echo('</form>

	</div>');

}



function slb_get_attachment_select( $input_name="slb_page", $input_id="", $parent=-1, $value_field="id", $selected_value="" ) {

	// get WP pages
$args = array(
    'post_type' => 'attachment',
    'sort_order' => 'asc',
	'sort_column' => 'post_title',
	'post_mime_type' => 'application/pdf',
    'numberposts' => -1,
    'post_status' => null,
    'post_parent' => null, // any parent
    );

$attachments = get_posts($args);

	// setup our select html
	$select = '<select name="'. $input_name .'" ';

	// IF $input_id was passed in
	if( strlen($input_id) ):

		// add an input id to our select html
		$select .= 'id="'. $input_id .'" ';

	endif;

	// setup our first select option
	$select .= '><option value="">- Select One -</option>';

	// loop over all the pages
	foreach ( $attachments as &$attachment ):

		// get the page id as our default option value
		$value = $attachment->ID;

		// check if this option is the currently selected option
		$selected = '';
		if( $selected_value == $value ):
			$selected = ' selected="selected" ';
		endif;

		// build our option html
		$option = '<option value="' . $value . '" '. $selected .'>';
		$option .= $attachment->post_title;
		$option .= '</option>';

		// append our option to the select html
		$select .= $option;

	endforeach;

	// close our select html tag
	$select .= '</select>';

	// return our new select
	return $select;

}





function slb_get_page_select( $input_name="slb_page", $input_id="", $parent=-1, $value_field="id", $selected_value="" ) {

	// get WP pages
	$pages = get_pages(
		array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'post_type' => 'page',
			'parent' => $parent,
			'status'=>array('draft','publish'),
		)
	);

	// setup our select html
	$select = '<select name="'. $input_name .'" ';

	// IF $input_id was passed in
	if( strlen($input_id) ):

		// add an input id to our select html
		$select .= 'id="'. $input_id .'" ';

	endif;

	// setup our first select option
	$select .= '><option value="">- Select One -</option>';

	// loop over all the pages
	foreach ( $pages as &$page ):

		// get the page id as our default option value
		$value = $page->ID;

		// determine which page attribute is the desired value field
		switch( $value_field ) {
			case 'slug':
				$value = $page->post_name;
				break;
			case 'url':
				$value = get_page_link( $page->ID );
				break;
			default:
				$value = $page->ID;
		}

		// check if this option is the currently selected option
		$selected = '';
		if( $selected_value == $value ):
			$selected = ' selected="selected" ';
		endif;

		// build our option html
		$option = '<option value="' . $value . '" '. $selected .'>';
		$option .= $page->post_title;
		$option .= '</option>';

		// append our option to the select html
		$select .= $option;

	endforeach;

	// close our select html tag
	$select .= '</select>';

	// return our new select
	return $select;

}








// hint: returns default option values as an associative array
function slb_get_default_options() {

	$defaults = array();

	try {

		// get front page id
		$front_page_id = get_option('page_on_front');


		// setup defaults array
		$defaults = array(
			'slb_manage_subscription_page_id'=>$front_page_id,
			'slb_confirmation_page_id'=>$front_page_id,
			'slb_select_attachment_id'=>$front_page_id

		);

	} catch( Exception $e) {

		// php error

	}

	// return defaults
	return $defaults;


}


// 6.9
// hint: returns the requested page option value or it's default
function slb_get_option( $option_name ) {

	// setup return variable
	$option_value = '';


	try {

		// get default option values
		$defaults = slb_get_default_options();

		// get the requested option
		switch( $option_name ) {

			case 'slb_manage_subscription_page_id':
				// subscription page id
				$option_value = (get_option('slb_manage_subscription_page_id')) ? get_option('slb_manage_subscription_page_id') : $defaults['slb_manage_subscription_page_id'];
				break;
			case 'slb_confirmation_page_id':
				// confirmation page id
				$option_value = (get_option('slb_confirmation_page_id')) ? get_option('slb_confirmation_page_id') : $defaults['slb_confirmation_page_id'];
				break;
			case 'slb_select_attachment_id':
				// confirmation page id
				$option_value = (get_option('slb_select_attachment_id')) ? get_option('slb_select_attachment_id') : $defaults['slb_select_attachment_id'];
				break;

		}

	} catch( Exception $e) {

		// php error

	}

	// return option value or it's default
	return $option_value;

}


function slb_get_current_options() {

	// setup our return variable
	$current_options = array();

	try {

		// build our current options associative array
		$current_options = array(
			'slb_manage_subscription_page_id' => slb_get_option('slb_manage_subscription_page_id'),
			'slb_confirmation_page_id' => slb_get_option('slb_confirmation_page_id'),
			'slb_select_attachment_id' => slb_get_option('slb_select_attachment_id'),
		);

	} catch( Exception $e ) {

		// php error

	}

	// return current options
	return $current_options;

}

function slb_get_options_settings() {

	// setup our return data
	$settings = array(
		'group'=>'slb_plugin_options',
		'settings'=>array(
			'slb_manage_subscription_page_id',
			'slb_confirmation_page_id',
			'slb_select_attachment_id'
			),
	);


	return $settings;

}

function slb_get_options_pdf_settings() {

	// setup our return data
	$input_types_count 	 =  count(slb_get_input_types());
	$settings_array = array();
	for($i=0; $i<$input_types_count; $i++){
			array_push($settings_array, "x".$i);
			array_push($settings_array, "y".$i);
		}

	$settings = array(
		'group'=>'slb_pdf_settings_options',
		'settings'=> $settings_array,
	);


	// return option data
	return $settings;
/*
	// setup our return data
	$settings = array(
		'group'=>'slb_pdf_settings_options',
		'settings'=>array(
			'x0',
			'y0',
			'x1',
			'y1'
			),
	); 

	return $settings; 
	*/
}



function slb_register_options() {

	// get plugin options settings

	$options = slb_get_options_settings();

	// loop over settings
	foreach( $options['settings'] as $setting ):

		// register this setting
		register_setting($options['group'], $setting);

	endforeach;

}


function slb_register_pdf_options() {

	// get plugin options settings
	$pdf_options = slb_get_options_pdf_settings();
	foreach( $pdf_options['settings'] as $setting ):

		// register this setting
		register_setting($pdf_options['group'], $setting);

	endforeach;

}





function footag_func( $atts ) {
	return "
<form id='myform' action='' method='post'>
	Email:<br>
  <input type='text' name='email' id='email' required>
  First name:<br>
  <input type='text' name='firstname' id='firstname' required>
  <br>
  Last name:<br>
  <input type='text' name='lastname' id='lastname' required>
  <br><br>
  <input type='submit' value='Submit'>
</form> 
	";
}
add_shortcode( 'footag', 'footag_func' );






/* ======================== easy-wp-smtp ========================*/


if (!function_exists('swpsmtp_plugin_action_links')) {

    function swpsmtp_plugin_action_links($links, $file) {
        /* Static so we don't call plugin_basename on every plugin row. */
        static $this_plugin;
        if (!$this_plugin) {
            $this_plugin = plugin_basename(__FILE__);
        }
        if ($file == $this_plugin) {
            $settings_link = '<a href="options-general.php?page=swpsmtp_settings">' . __('Settings', 'easy-wp-smtp') . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

}

/**
 * Add action links on plugin page in to Plugin Description block
 * @param $links array() action links
 * @param $file  string  relative path to pugin "easy-wp-smtp/easy-wp-smtp.php"
 * @return $links array() action links
 */
if (!function_exists('swpsmtp_register_plugin_links')) {

    function swpsmtp_register_plugin_links($links, $file) {
        $base = plugin_basename(__FILE__);
        if ($file == $base) {
            $links[] = '<a href="options-general.php?page=swpsmtp_settings">' . __('Settings', 'easy-wp-smtp') . '</a>';
        }
        return $links;
    }

}

//plugins_loaded action hook handler
if (!function_exists('swpsmtp_plugins_loaded_handler')) {

    function swpsmtp_plugins_loaded_handler() {
        load_plugin_textdomain('easy-wp-smtp', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

}


/**
 * Function to add plugin scripts
 * @return void
 */
if (!function_exists('swpsmtp_admin_head')) {

    function swpsmtp_admin_head() {
        wp_enqueue_style('swpsmtp_stylesheet', plugins_url('css/style.css', __FILE__));

        if (isset($_REQUEST['page']) && 'swpsmtp_settings' == $_REQUEST['page']) {
            wp_enqueue_script('swpsmtp_script', plugins_url('js/script.js', __FILE__), array('jquery'));
        }
    }

}

/**
 * Function to add smtp options in the phpmailer_init
 * @return void
 */
if (!function_exists('swpsmtp_init_smtp')) {

    function swpsmtp_init_smtp($phpmailer) {
        //check if SMTP credentials have been configured.
        if (!swpsmtp_credentials_configured()) {
            return;
        }
        $swpsmtp_options = get_option('swpsmtp_options');
        /* Set the mailer type as per config above, this overrides the already called isMail method */
        $phpmailer->IsSMTP();
        $from_email = $swpsmtp_options['from_email_field'];
        $phpmailer->From = $from_email;
        $from_name = $swpsmtp_options['from_name_field'];
        $phpmailer->FromName = $from_name;
        $phpmailer->SetFrom($phpmailer->From, $phpmailer->FromName);
        /* Set the SMTPSecure value */
        if ($swpsmtp_options['smtp_settings']['type_encryption'] !== 'none') {
            $phpmailer->SMTPSecure = $swpsmtp_options['smtp_settings']['type_encryption'];
        }

        /* Set the other options */
        $phpmailer->Host = $swpsmtp_options['smtp_settings']['host'];
        $phpmailer->Port = $swpsmtp_options['smtp_settings']['port'];

        /* If we're using smtp auth, set the username & password */
        if ('yes' == $swpsmtp_options['smtp_settings']['autentication']) {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $swpsmtp_options['smtp_settings']['username'];
            $phpmailer->Password = swpsmtp_get_password();
        }
        //PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
        $phpmailer->SMTPAutoTLS = false;

    }

}


if (!function_exists('swpsmtp_get_attachment')) {

    function swpsmtp_get_attachment() {
	include_once(plugin_dir_path( __FILE__ )."includes/fpdf/fpdf.php"); 
	include_once(plugin_dir_path( __FILE__ )."includes/fpdi/fpdi.php"); 
	$pdfoption 	 = slb_get_pdf_current_options();

	// initiate FPDI 
	$pdf =& new FPDI(); 
	// add a page 
	$pdf->AddPage(); 
	// set the sourcefile 
	$pdf->setSourceFile(plugin_dir_path( __FILE__ )."includes/structure.pdf"); 
	// import page 1 
	$tplIdx = $pdf->importPage(1); 
	// use the imported page as the template 
	$pdf->useTemplate($tplIdx, 0, 0); 

	// now write some text above the imported page 
	$pdf->SetFont('Arial'); 
	$pdf->SetTextColor(255,0,0); 
	
	for($i=0; $i<(count($pdfoption)/2); $i++){
		$effectivePosx = $pdfoption['x'.$i]*0.264583333 - 4;
		$effectivePosy = $pdfoption['y'.$i]*0.264583333 - 7;
		$pdf->SetXY($effectivePosx, $effectivePosy); 
		$pdf->Write(5, slb_get_users_inputs()[i]);
		//$pdf->Write(5, "This is the field of".'x'.$i .'  '. 'y'.$i); 
	}
	

	return $pdf->Output('newpdf.pdf', 'S'); 
    }

}




/**
 * Function to test mail sending
 * @return text or errors
 */
if (!function_exists('swpsmtp_test_mail')) {

    function swpsmtp_test_mail($to_email, $subject, $message) {
        if (!swpsmtp_credentials_configured()) {
            return;
        }
        $errors = '';

        $swpsmtp_options = get_option('swpsmtp_options');

        require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
        $mail = new PHPMailer();

        $charset = get_bloginfo('charset');
        $mail->CharSet = $charset;

        $from_name = $swpsmtp_options['from_name_field'];
        $from_email = $swpsmtp_options['from_email_field'];

        $mail->IsSMTP();

        /* If using smtp auth, set the username & password */
        if ('yes' == $swpsmtp_options['smtp_settings']['autentication']) {
            $mail->SMTPAuth = true;
            $mail->Username = $swpsmtp_options['smtp_settings']['username'];
            $mail->Password = swpsmtp_get_password();
        }

        /* Set the SMTPSecure value, if set to none, leave this blank */
        if ($swpsmtp_options['smtp_settings']['type_encryption'] !== 'none') {
            $mail->SMTPSecure = $swpsmtp_options['smtp_settings']['type_encryption'];
        }

        /* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
        $mail->SMTPAutoTLS = false;

        /* Set the other options */
        $mail->Host = $swpsmtp_options['smtp_settings']['host'];
        $mail->Port = $swpsmtp_options['smtp_settings']['port'];
        $mail->SetFrom($from_email, $from_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->MsgHTML($message);
        $mail->AddAddress($to_email);
        $mail->SMTPDebug = 0;
        $attachment = swpsmtp_get_attachment();
        $mail->AddStringAttachment($attachment, 'attachment.pdf');

        /* Send mail and return result */
        if (!$mail->Send())
            $errors = $mail->ErrorInfo;

        $mail->ClearAddresses();
        $mail->ClearAllRecipients();

        if (!empty($errors)) {
            return $errors;
        } else {
            return 'Test mail was sent';
        }
    }

}

if (!function_exists('swpsmtp_get_password')) {

    function swpsmtp_get_password() {
        $swpsmtp_options = get_option('swpsmtp_options');
        $temp_password = $swpsmtp_options['smtp_settings']['password'];
        $password = "";
        $decoded_pass = base64_decode($temp_password);
        /* no additional checks for servers that aren't configured with mbstring enabled */
        if (!function_exists('mb_detect_encoding')) {
            return $decoded_pass;
        }
        /* end of mbstring check */
        if (base64_encode($decoded_pass) === $temp_password) {  //it might be encoded
            if (false === mb_detect_encoding($decoded_pass)) {  //could not find character encoding.
                $password = $temp_password;
            } else {
                $password = base64_decode($temp_password);
            }
        } else { //not encoded
            $password = $temp_password;
        }
        return $password;
    }

}

if (!function_exists('swpsmtp_admin_notice')) {

    function swpsmtp_admin_notice() {
        if (!swpsmtp_credentials_configured()) {
            $settings_url = admin_url() . 'options-general.php?page=swpsmtp_settings';
            ?>
            <div class="error">
                <p><?php printf(__('Please configure your SMTP credentials in the <a href="%s">settings menu</a> in order to send email using Easy WP SMTP plugin.', 'easy-wp-smtp'), esc_url($settings_url)); ?></p>
            </div>
            <?php
        }
    }

}

if (!function_exists('swpsmtp_credentials_configured')) {

    function swpsmtp_credentials_configured() {
        $swpsmtp_options = get_option('swpsmtp_options');
        $credentials_configured = true;
        if (!isset($swpsmtp_options['from_email_field']) || empty($swpsmtp_options['from_email_field'])) {
            $credentials_configured = false;
        }
        if (!isset($swpsmtp_options['from_name_field']) || empty($swpsmtp_options['from_name_field'])) {
            $credentials_configured = false;
            ;
        }
        return $credentials_configured;
    }

}

/**
 * Performed at uninstal.
 * @return void
 */
if (!function_exists('swpsmtp_send_uninstall')) {

    function swpsmtp_send_uninstall() {
        /* delete plugin options */
        delete_site_option('swpsmtp_options');
        delete_option('swpsmtp_options');
    }

}

/**
 * Add all hooks
 */
add_filter('plugin_action_links', 'swpsmtp_plugin_action_links', 10, 2);
add_action('plugins_loaded', 'swpsmtp_plugins_loaded_handler');
add_filter('plugin_row_meta', 'swpsmtp_register_plugin_links', 10, 2);

add_action('phpmailer_init', 'swpsmtp_init_smtp');

//add_action('admin_menu', 'swpsmtp_admin_default_setup');

add_action('admin_init', 'swpsmtp_admin_init');
add_action('admin_enqueue_scripts', 'swpsmtp_admin_head');
add_action('admin_notices', 'swpsmtp_admin_notice');

register_uninstall_hook(plugin_basename(__FILE__), 'swpsmtp_send_uninstall');