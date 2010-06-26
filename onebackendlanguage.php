<?php 
/*
Plugin Name: One Backend Language
Plugin URI: http://wordpress.org/extend/plugins/one-backend-language/
Description: Lets you choose your Backend Language for your complete Backend, also if your Frontend is using another Lang (including Multisite)
Version: 1.0
Author: Martin Juhasz
Author URI: http://www.martinjuhasz.de
Tags: translation, translations, admin, english, localization, backend, multisite
*/

function oneBackendLanguageSetLocale( $locale ) {
	if ( is_admin() || false !== strpos($_SERVER['REQUEST_URI'], '/wp-includes/js/tinymce/tiny_mce_config.php')
		|| false !== strpos($_SERVER['REQUEST_URI'], '/wp-login.php' ) ) {
		$pre = get_option('oneBackendLanguage');
		if(!empty($pre)) {
			return $pre;
		} else {
			return 'en_US';
		}
	}
	return $locale;
}
add_filter( 'locale', 'oneBackendLanguageSetLocale' );


add_action('admin_menu', 'oneBackendPluginMenu');

function oneBackendPluginMenu() {
	add_options_page('One Backend Language Options', 'One Backend Language', 'manage_options', 'oneBackendLanguage', 'oneBackendPluginMenuOptions');
}

function oneBackendPluginMenuOptions() {
	
	$set = false;
	
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	if(isset($_POST['oneBackendsubmit']) && !empty($_POST['oneBackendLanguage'])) {
		if(in_array($_POST['oneBackendLanguage'],oneBackendLanguageLocales())) {
			$pre = get_option('oneBackendLanguage');
			if(!empty($pre)) {
				update_option('oneBackendLanguage', $_POST['oneBackendLanguage']);
			} else {
				add_option('oneBackendLanguage', $_POST['oneBackendLanguage']);
			}
			$set = true;
		}
	}
	
	?>
	
	<div id="icon-options-general" class="icon32"><br></div>
	<h2>One Backend Language</h2>
	<?php if($set === true) {
		echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>Options updated. Refresh your page now</strong></p></div>';
	} ?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=oneBackendLanguage">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="default_category">select Backend Language</label></th>
					<td>
						<select name="oneBackendLanguage" id="oneBackendLanguage" class="postform">
							<?php
								
								$locales = oneBackendLanguageLocales();
								foreach($locales as $lang) {
									if($lang == get_option('oneBackendLanguage')) {
										echo '<option selected="selected" value="'.$lang.'">'.$lang.'</option>';
									} else {
										echo '<option value="'.$lang.'">'.$lang.'</option>';
									}
								}
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	
		<p class="submit">
			<input type="submit" name="oneBackendsubmit" class="button-primary" value="Save Changes">
		</p>
		
	</form>
	
	<?php
}



function oneBackendLanguageLocales() {
	$locales = array();

	if ( $handle = opendir( WP_LANG_DIR ) ) {
		rewinddir( $handle );
		while ( false !== ( $file = readdir( $handle ) ) ) {
			$filename = basename( $file );

			if ( false !== strpos( $filename, 'continents-cities' ) )
				continue;

			if ( preg_match( '/^([^.]+)\.mo$/', $filename, $regs ) ) {
				$locale = $regs[1];
				$locales[] = $locale;
			}
		}
		closedir( $handle );
	}
	
	$locales[] = 'en_US';
	$locales = array_unique($locales);

	return $locales;
}

?>