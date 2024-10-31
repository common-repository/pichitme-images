<?php
/*
Plugin name: PicHit.Me Images
Plugin URI: http://pichit.me/
Description: Search and use PicHit.Me images in your posts and pages without ever leaving WordPress.
Author: iGoMoon AB
Author URI: http://www.igomoon.com/
Version: 1.0
*/

/*  Copyright 2014  Pichit

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
//Require class

$token = get_option('pichit_token');

require('pichit.inc.php');
add_action('admin_enqueue_scripts', 'custom_add_styles');
function custom_add_styles(){
	wp_enqueue_style('pichit_styles', plugins_url('/style.css', __FILE__),false,'1.1','all');
}

$pichitClass = new pichit();
if($pichitClass->check_token(get_option('pichit_token')['token'])){
	//Enqueue admin scripts/styles
	add_action('admin_enqueue_scripts', 'custom_add_script');
	function custom_add_script(){
	    wp_enqueue_script('custom', plugins_url('scripts.js', __FILE__), array('media-views'), false, true);
	}
	//Add labels to WP.Media
	add_filter('media_view_strings', 'custom_media_string', 10, 2);
	function custom_media_string($strings,  $post){
	    $strings['customMenuTitle'] = __('PicHit.Me Images', 'custom');
	    $strings['customButton'] = __('PicHit.Me', 'custom');
	    return $strings;
	}
	
	//Add a PicHit button to Wordpress Editor
	add_action('media_buttons_context',  'add_my_custom_button');
	function add_my_custom_button($context) {
	  //append the icon
	  $context .= '<a href="#" id="insert-media-button" class="button pichit-images insert-media add_media" data-editor="content" title="Add Media"><span class="wp-media-buttons-icon"></span>Add PicHit.Me Image</a>';
	  return $context;
	}
}
//Add a message on top of admin board when not registered.
add_action( 'admin_notices', 'notice_registrate' );
function notice_registrate () {
	if(!get_option('pichit_token') || get_option('pichit_token') == ''){?>
		<div class="pichit-warning">
			<a href="/wp-admin/options-general.php?page=pichit_register_account">Register your PicHit.Me Account!</a>
		</div>
		<?php
	}
}


//Register admin settings page
if(is_admin()) {
	add_action('admin_menu','ep_admin_menu');
	add_action('admin_init','ep_register_mysettings');
}
function ep_admin_menu () {
	add_options_page('PicHit.Me','PicHit.Me', 8,'pichit_register_account','pichit_settings_page');
}

//Markup for admin settingspage
function pichit_settings_page () {
	$pichitClass = new pichit();
	if(isset($_POST['pichit_username']) && isset($_POST['pichit_password'])) {
		if(!empty($_POST['pichit_username']) && !empty($_POST['pichit_password'])) {
			$username = $_POST['pichit_username'];
			$password = $_POST['pichit_password'];
			$token = $pichitClass->get_token($username,$password);
			$array = array('username'=>$username, 'password'=>$password,'token'=>$token);
			if($pichitClass->check_token($token)){
				if(get_option('pichit_token')) {
					update_option('pichit_token', $array);
				}else {
					add_option('pichit_token', $array);
				}
			}else {
				echo 'invalid login';
			}
		}
	}
?>
	<div class="wrap pichit_admin">
	<h1>Login to your PicHit.Me account</h1>
	<div class="pichit-box" style="padding:0;">
	<form method="POST">
	<?php settings_fields( 'pichit-settings-group' ); ?>
	<div class="inputs">
		<input type="text" name="pichit_username" placeholder="Username" value="<?php echo get_option('pichit_token')['username']; ?>"/><br>
		<input type="password" name="pichit_password" placeholder="Password" value="not visible" />
	</div>
	<?php echo submit_button('GO!');?>
	<?php
		if(get_option('pichit_token') && $pichitClass->check_token(get_option('pichit_token')['token'])) {
			echo '<div class="activation-message" style="background:#45d197;">Your PicHit.Me account is activated.</div>';
		}else {
			echo '<div class="activation-message" style="background:#ff7b87;">Please activate your PicHit.Me account.</div>';
		}
	?>
	</form>
	</div>
	<div class="pichit-box" style="background: #000;">
		<h3>Support</h3>
		<a style="color:transparent;" href=""><center>?</center></a>
	</div>
	<div class="pichit-box" style="background: #ff00aa;">
		<h3>PicHit.Me</h3>
		<a style="color:transparent;padding:50px;" href="http://pichit.me"><img src="/wp-content/plugins/pichitme-images/white-pichit-logo.png" /></a>
	</div>
	</div>
<?php

}

?>