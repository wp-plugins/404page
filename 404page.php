<?php
/*
Plugin Name: 404page
Plugin URI: http://smartware.cc/wp-404page
Description: Define any of your pages as 404 page
Version: 1.0
Author: smartware.cc
Author URI: http://smartware.cc
License: GPL2
*/

/*  Copyright 2013  smartware.cc  (email : sw@smartware.cc)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// redirect 404 page
function swcc_404page( $template ) {
  global $wp_query;
  $template404 = $template;
  $pageid = swcc_404page_get_page_id();
  if ( $pageid > 0 ) {
    $wp_query = null;
    $wp_query = new WP_Query();
    $wp_query->query( 'page_id=' . $pageid );
    $wp_query->the_post();
    $template404 = get_page_template();
    rewind_posts();
  }
  return $template404;
}

// show admin page
function swcc_404page_admin() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
  ?>
  <div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2>404page</h2>
    <div class="metabox-holder has-right-sidebar">
      <div class="inner-sidebar">
        <div class="postbox">
          <h3><span>the 404page plugin</span></h3>
					<div class="inside">
						<ul>
						  <li><a href="http://wordpress.org/extend/plugins/404page/">Please rate the plugin</a></li>
							<li><a href="http://smartware.cc/wp-404page/">Plugin homepage</a></li>
							<li><a href="http://smartware.cc/">Author homepage</a></li>
						</ul>
					</div>
				</div>
			</div>
      <div id="post-body">
        <div id="post-body-content">
          <form method="post">
          <p>
          <?php
            if( !empty( $_POST['submit'] ) ) {
              $pageid = swcc_404page_get_page_id();
              if ( $_POST['404pageid'] != $pageid ) {
                if ( update_option( '404page_page_id', $_POST['404pageid'] ) ) {
                  add_settings_error( 'swcc_404page_err', esc_attr( 'settings_updated' ), __('Settings saved.'), 'updated' );
                } else {
                  add_settings_error( 'swcc_404page_err', esc_attr( 'settings_updated' ), 'An unexpected error occured while saving the settings!', 'error' );
                }
              }
            }
            $pageid = swcc_404page_get_page_id();
            if ( $pageid < 0 ) {
              add_settings_error( 'swcc_404page_err', esc_attr( 'settings_updated' ), 'The page you have selected as 404 page does not exist anymore. Please choose another page.', 'error' );
            }
            settings_errors( 'swcc_404page_err' );
            ?>
            <label for="404pageid">Page to be displayed as 404 page: </label>
            <?php
            wp_dropdown_pages( array( 'name' => '404pageid', 'echo' => 1, 'show_option_none' => '&mdash; NONE (WP Default) &mdash;', 'option_none_value' => '0', 'selected' => $pageid ) );
            ?>
          </p>
          <p class="submit">
            <input type="submit" name="submit" class="button-primary" value="<?php _e('Save'); ?>" />
          </p>
        </form>
        </div>
      </div>
    </div>
  </div>
  <?php
}

// init backend
function swcc_404page_adminmenu() {
  add_submenu_page( 'options-general.php', '404page', '404page', 'manage_options', '404page', 'swcc_404page_admin' );
}

// returns the id of the 404 page if one is defined, returns 0 if none is defined, returns -1 if the defined page id does not exist
function swcc_404page_get_page_id() {  
  $pageid = get_option( '404page_page_id', 0 );
  if ( $pageid != 0 ) {
    $page = get_post( $pageid );
    if ( !$page || $page->post_status != 'publish' ) {
      $pageid = -1;
    }
  }
  return $pageid;
}

add_filter( '404_template', 'swcc_404page' );
add_action( 'admin_menu', 'swcc_404page_adminmenu' );
?>