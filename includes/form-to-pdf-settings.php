<?php
/*
function ftp_options_menu_link(){
  add_options_page(
    'Form to Pdf Options',      		     // page title
    'Add Form pdf button Link',          // menu title
    'manage_options',                    // capability
    'ftp-options',                       // menu slug
    'ftp_options_content'                // content callback
    );
}
add_action( 'admin_menu', 'ftp_options_menu_link' );


function ftp_options_content(){
  global $ftp_options;

include(plugin_dir_path( __FILE__ )."simplehtmldom\simple_html_dom.php");
$thepage = get_permalink( get_page_by_title("Hello Test", OBJECT, 'post')->ID );
$html = file_get_html($thepage);
$form = $html->find("form", 0);
$forminput = $form->find("input");



  ob_start(); ?>
    <div class="wrap">
<h1>
    <?php
    foreach($forminput as $element) 
      echo $element->getAttribute('type') . '<br>';
    ?>
    </h1>
      <h1><?php _e( 'Form to Pdf Link Settings', 'ftp_domain' ); ?></h1>
      <p><?php _e( 'Settings for Form to pdf Link', 'ftp_domain' ); ?></p>
      <form method="post" action="options.php">
        <?php settings_fields( 'ftp_settings_group' ); ?>
        <table class="form-table">
          <tbody>
            <tr>
              <th scope="row"><label for="ftp_settings[page_url]"><?php _e( 'Url Form Page', 'ftp_domain' )?></label></th>
              <td><input name="ftp_settings[ftp_url]" type="text" id="ftp_settings[ftp_url]" value="<?php echo $ftp_options['ftp_url']; ?>" class="regular-text">
                <p class="description"><?php _e( 'Enter the Form page you want to modify', 'ftp_domain' ); ?></p></td>
            </tr>
            <tr>
              <th scope="row"><label for="ftp_settings[page_param]"><?php _e( 'Page Name', 'ftp_domain' )?></label></th>
              <td><input name="ftp_settings[page_param]" type="text" id="ftp_settings[page_param]" value="<?php echo $ftp_options['page_param']; ?>" class="regular-text">

                <p class="description"><?php _e( 'Name of the page you want to add information', 'ftp_domain' ); ?></p></td>
            </tr>
          </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'ftp_domain'); ?>"></p>
      </form>
    </div>
  <?php
  echo ob_get_clean();
}

function ftp_register_settings(){
  register_setting( 'ftp_settings_group', 'ftp_settings' );
}
add_action( 'admin_init', 'ftp_register_settings' );
