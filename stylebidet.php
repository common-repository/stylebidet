<?php

/*
Plugin Name: StyleBidet
Plugin URI: https://www.verynewmedia.com/
Description: Tired of MS Word-pasted content and overzealous editors destroying your lovingly-crafted site styles with green text and purple backgrounds? Well, not anymore.
Author: Lawrie Malen
Version: 1.0.0
Author URI: https://www.verynewmedia.com/
License: GPL
Copyright (C) 2010-2020 Lawrie Malen // Very New Media
Created: 02-02-2010
Updated: 07-12-2020

*/

///
//	Create version option on activation
///

if (!defined('VNM_STYLEBIDET_VERSION')) {
	define('VNM_STYLEBIDET_VERSION', '1.0.0');
}

///
//	Define the options that can be set
///

if (!defined('VNM_STYLEBIDET_OPTIONS')) {
	define('VNM_STYLEBIDET_OPTIONS', array(
		'clean_output',
		'clean_save',
		'clean_script',
		'remove_text_color',
		'clean_acf',
	));
}

///
//	Add option on activation
///

function vnmStyleBidet_install() {
	add_option('vnmStyleBidet_version', VNM_STYLEBIDET_VERSION);
	
	//	Define all the opyions as ON by default
	
	foreach (VNM_STYLEBIDET_OPTIONS as $option) {
		add_option('vnmStyleBidet_' . $option, 1);
	}
}

///
//	Delete option on deactivation
///

function vnmStyleBidet_deactivate() {
	delete_option('vnmStyleBidet_version');
	
	foreach (VNM_STYLEBIDET_OPTIONS as $option) {
		delete_option('vnmStyleBidet_' . $option);
	}
}

///
//	Translations
///

function vnmStyleBidet_load_textdomain() {
	load_plugin_textdomain('stylebidet', false, basename(dirname(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'vnmStyleBidet_load_textdomain');

///
//	Add to Settings menu
///

function vnmStyleBidet_settingsMenu() {
	if (defined('VNM_STYLEBIDET_SHOW_SETTINGS')) {
		if (!VNM_STYLEBIDET_SHOW_SETTINGS) {
			return;
		}
	}
	
	add_options_page(__('StyleBidet Settings', 'stylebidet'), __('StyleBidet Settings', 'stylebidet'), 'manage_options', 'vnmStyleBidetSettings', 'vnmStyleBidet_settingsPage');
}

add_action('admin_menu', 'vnmStyleBidet_settingsMenu');

///
//	Enqueue scripts
///

function vnmStyleBidet_loadScripts($hook) {
	global $post;
	
	$scriptPath = plugin_dir_path(__FILE__) . '/assets/';
	$scriptURI = plugins_url('/assets/', __FILE__);
	
	///
	//	CSS for admin settings page
	///
	
	if ($hook == 'settings_page_vnmStyleBidetSettings') {
		wp_enqueue_style('responsiflex', $scriptURI . '/responsiflex.css', array(), filemtime($scriptPath . '/responsiflex.css'));
		wp_enqueue_style('stylebidet', $scriptURI . '/vnm-admin.css', array(), filemtime($scriptPath . '/vnm-admin.css'));
	}
}

add_action('admin_enqueue_scripts', 'vnmStyleBidet_loadScripts', 20, 1);

///
//	Get the settings - these might be wp_options values, or constants set in wp-config
///

function vnmStyleBidet_getOptions($singleOption = false) {
	
	$optionsArray = array();
	$optionNamesArray = VNM_STYLEBIDET_OPTIONS;
	
	//	Just want a single option? Then we'll just need to return the value
	
	if ($singleOption) {
		$optionValue = get_option('vnmStyleBidet_' . $singleOption);
		
		//	Constant defined instead?
		
		if (defined('VNM_STYLEBIDET_SETTINGS')) {
			if (isset(VNM_STYLEBIDET_SETTINGS[$singleOption])) {
				$optionValue = VNM_STYLEBIDET_SETTINGS[$singleOption];
			}
		}
		
		return $optionValue;
	}
	
	//	Otherwise, let's return them all as an array of kay/value pairs
	
	foreach ($optionNamesArray as $option) {
		$optionsArray[$option] = get_option('vnmStyleBidet_' . $option);
		$optionsArray[$option . '_editable'] = true;
	}
	
	//	Now let's see if any of those are overwritten by the constants
	
	if (defined('VNM_STYLEBIDET_SETTINGS')) {
		foreach (VNM_STYLEBIDET_SETTINGS as $option=>$value) {
			$optionsArray[$option] = $value;
			$optionsArray[$option . '_editable'] = false;
		}
	}
	
	return $optionsArray;
}

///
//	Settings page
///

function vnmStyleBidet_settingsPage() {
	
	//	Let's get the options set in the db by default
	
	$optionsArray = vnmStyleBidet_getOptions();
	
	//	Let's check if any data has been posted
	
	$shouldUpdate = false;
	$updated = false;
	$updateFailed = false;
	
	if (isset($_REQUEST['updateVNMStyleBidetSettings'])) {
		
		//	Does the current user have permission to complete this action?
		
		if (empty($_POST) || !check_admin_referer('vnm_stylebidet_nonce_action', 'stylebidet_admin_nonce') || !apply_filters('stylebidet_user_allowed', current_user_can('manage_options'))) {
			$updateFailed = true;
		}
		
		//	Does the nonce check pass?
		
		if (isset($_POST['nonce']) && sanitize_html_class($_POST['action']) === 'stylebidet_update_settings' && wp_verify_nonce(sanitize_html_class($_POST['nonce']), 'stylebidet_update_settings')) {
			$shouldUpdate = true;
		} else {
			$updateFailed = true;
		}
	}
	
	foreach ($optionsArray as $option=>$value) {
		
		//	Save, if required
		
		if ($shouldUpdate) {
			if (array_key_exists($option, $_POST)) {
				if (isset($_POST[$option]) && sanitize_html_class($_POST[$option]) === 'on') {
					
					update_option('vnmStyleBidet_' . $option, 1);	// It's checked, so we switch the option on
					
				} else {
					
					update_option('vnmStyleBidet_' . $option, 0);	// It's unchecked, so switch the option off
					
				}
			}
			
			$updated = true;
		}
	}
	
	//	Now, because we fetched that array (for checking for valid values) BEFORE we updated them, the switch values are going to be out of date, SO:
	
	$updatedOptionsArray = vnmStyleBidet_getOptions();
	
	foreach ($updatedOptionsArray as $updOption=>$updValue) {
		${$updOption} = $updValue;
	}
	
	?>
	
	<div class="wrap vnmadmin-wrapper">
		
		<?php
			///
			//	Intro
			///
		?>
		
		<section class="wrapper">
			<h3 class="section-title border-bottom">
				<?php _e('StyleBidet - Clean out those styles&trade;', 'stylebidet'); ?>
			</h3>
			
			<div class="padded-section">
				<div class="boxcontainer">
					<div class="box vnmadmin-badge-box">
						<div class="vnmadmin-badge"></div>
					</div>
					
					<div class="box flex-stretch">
						<p>
							<?php _e('StyleBidet gives you granular control to:', 'stylebidet'); ?>
						</p>
						
						<ul class="list">
							<li><?php printf(__('Remove inline %sstyle=""%s attributes when saving &amp; displaying post content;', 'stylebidet'), '<code>', '</code>'); ?>
							<li><?php printf(__('Remove %s%sstyle%s%s &amp; %s%sfont%s%s tags when saving &amp; displaying content;', 'stylebidet'), '<code>', '&lt;', '&gt;', '</code>', '<code>', '&lt;', '&gt;', '</code>'); ?>
							<li><?php printf(__('Remove the %sText Color%s button from the classic WordPress editor.', 'stylebidet'), '<strong>', '</strong>'); ?>
							<li><?php printf(__('%sNew!%s Remove %sstyle/font%s tags &amp; attributes from ACF custom fields', 'stylebidet'), '<strong>', '</strong>', '<code>', '</code>'); ?>
						</ul>
						
						<p>
							<?php _e('Just set your options below and you\'re good to go!', 'stylebidet'); ?>
						</p>
					</div>
				</div>
			</div>
		</section>
		
		<?php
			///
			//	Update messages & warnings
			///
		?>
		
		<?php if ($updated) : ?>
			<section class="wrapper updated">
				<div class="padded-section">
					<?php if ($updated && ($clean_output || $clean_save)) : ?>
						<span class="dashicons dashicons-thumbs-up notice-icon tertiarycolor"></span>
						<?php _e('StyleBidet options updated. Enjoy the cleanliness!', 'styebidet'); ?>
					<?php endif; ?>
					
					<?php if (!$clean_output && !$clean_save) : ?>
						<br /><span class="dashicons dashicons-flag notice-icon secondarycolor"></span>
						<?php _e('StyleBidet cleaning on both output &amp; saving content are turned off. Did you mean to do that?', 'styebidet'); ?>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>
		
		<?php if ($updateFailed) : ?>
			<section class="wrapper updated">
				<div class="padded-section">
					<span class="dashicons dashicons-warning notice-icon secondarycolor"></span>
					<?php _e('Uh oh, it looks like you can\'t do that.', 'styebidet'); ?>
				</div>
			</section>
		<?php endif; ?>
		
		<?php
			///
			//	Settings
			///
		?>
		
		<section class="wrapper">
			<h3 class="section-title">
				<div class="boxcontainer alignmiddle">
					<span class="dashicons dashicons-admin-settings notice-icon spotcolor"></span>
					
					<span class="box title-text flex-stretch">
						<?php _e('StyleBidet Settings', 'stylebidet'); ?>
					</span>
				</div>
			</h3>
			
			<div class="padded-section">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=vnmStyleBidetSettings" class="form" method="post">
					
					<input type="hidden" name="action" value="stylebidet_update_settings" />
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('stylebidet_update_settings'); ?>" />
					<?php wp_nonce_field('vnm_stylebidet_nonce_action','stylebidet_admin_nonce' ); ?>
					
					<table class="form-table">
						
						<?php
							///
							//	Remove styles & tags on ouput
							///
						?>
						
						<tr>
							<th>
								<label for="clean_output">
									<?php printf(__('Remove %sstyle/font%s tags &amp; attributes when displaying:', 'stylebidet'), '<code>', '</code>'); ?>
								</label>
							</th>
							
							<td>
								<div class="checkslider">
									<input type="hidden" name="clean_output" value="" />
									<input type="checkbox" name="clean_output" id="clean_output" autocomplete="off" <?php if ($clean_output == 1) { ?>checked="checked" <?php } ?> <?php if (!$clean_output_editable) { ?>disabled="disabled" <?php } ?>/>
									<span class="slider dashicons-before easeoutquint">
										<label for="clean_output" class="slider-button"></label>
									</span>
								</div>
								
								<span class="description">
									<?php printf(__('If enabled, this will remove %sstyle%s &amp; %sfont%s tags &amp; attributes from content at the point it is displayed.', 'stylebidet'), '<code>', '</code>', '<code>', '</code>'); ?>
									<br />
									<?php _e('This is useful if you want to clean up the display of older content that might not be updated &amp; re-saved any time soon.', 'stylebidet'); ?>
								</span>
							</td>
						</tr>
						
						<?php
							///
							//	Permanently remove on save
							///
						?>
						
						<tr>
							<th>
								<label for="clean_save">
									<?php printf(__('Remove %sstyle/font%s tags &amp; attributes when saving:', 'stylebidet'), '<code>', '</code>'); ?>
								</label>
							</th>
							
							<td>
								<div class="checkslider">
									<input type="hidden" name="clean_save" value="" />
									<input type="checkbox" name="clean_save" id="clean_save" autocomplete="off" <?php if ($clean_save == 1) { ?>checked="checked" <?php } ?> <?php if (!$clean_save_editable) { ?>disabled="disabled" <?php } ?>/>
									<span class="slider dashicons-before easeoutquint">
										<label for="clean_save" class="slider-button"></label>
									</span>
								</div>
								
								<span class="description">
									<?php printf(__('If enabled, this will %spermanently%s remove %sstyle%s &amp; %sfont%s tags &amp; attributes from content at the point it is saved.', 'stylebidet'), '<strong>', '</strong>', '<code>', '</code>', '<code>', '</code>'); ?>
								</span>
							</td>
						</tr>
						
						<?php
							///
							//	Permanently remove script tags
							///
						?>
						
						<tr>
							<th>
								<label for="clean_script">
									<?php printf(__('Remove %sscript%s tags when displaying &amp; saving:', 'stylebidet'), '<code>', '</code>'); ?>
								</label>
							</th>
							
							<td>
								<div class="checkslider">
									<input type="hidden" name="clean_script" value="" />
									<input type="checkbox" name="clean_script" id="clean_script" autocomplete="off" checked="checked" disabled="disabled" />
									<span class="slider dashicons-before easeoutquint">
										<label for="clean_script" class="slider-button"></label>
									</span>
								</div>
								
								<span class="description">
									<?php printf(__('Any %sscript%s tags will be removed from content at the point it is displayed, and/or %spermanently%s removed when content is saved if either of the above %sstyle/font%s options are enabled.', 'stylebidet'), '<code>', '</code>', '<strong>', '</strong>', '<code>', '</code>'); ?>
								</span>
							</td>
						</tr>
						
						<?php
							///
							//	Text Color Button
							///
						?>
						
						<tr>
							<th>
								<label for="remove_text_color">
									<?php printf(__('Remove %sText Color%s button from the WYSIWYG/Classic Editor:', 'stylebidet'), '<strong>', '</strong>'); ?>
								</label>
							</th>
							
							<td>
								<div class="checkslider">
									<input type="hidden" name="remove_text_color" value="" />
									<input type="checkbox" name="remove_text_color" id="remove_text_color" autocomplete="off" <?php if ($remove_text_color == 1) { ?>checked="checked" <?php } ?> <?php if (!$remove_text_color_editable) { ?>disabled="disabled" <?php } ?>/>
									<span class="slider dashicons-before easeoutquint">
										<label for="remove_text_color" class="slider-button"></label>
									</span>
								</div>
								
								<span class="description">
									<?php printf(__('If enabled, this will remove the %sText Color%s button from the TinyMCE editor (usually visible in the second row of \'Kitchen Sink\' buttons).', 'stylebidet'), '<strong>', '</strong>'); ?>
								</span>
							</td>
						</tr>

						<?php
							///
							//	Clean up ACF Fields!
							///
						?>
						
						<tr>
							<th>
								<label for="clean_acf">
									<?php printf(__('Remove %sfont/style%s tags &amp; attributes when displaying &amp; saving ACF fields:', 'stylebidet'), '<code>', '</code>'); ?>
								</label>
							</th>
							
							<td>
								<div class="checkslider">
									<input type="hidden" name="clean_acf" value="" />
									<input type="checkbox" name="clean_acf" id="clean_acf" autocomplete="off" <?php if ($clean_acf == 1) { ?>checked="checked" <?php } ?> <?php if (!$clean_acf_editable) { ?>disabled="disabled" <?php } ?>/>
									<span class="slider dashicons-before easeoutquint">
										<label for="clean_acf" class="slider-button"></label>
									</span>
								</div>
								
								<span class="description">
									<?php printf(__('If enabled, this will remove %sstyle/font%s tags &amp; attributes from %sACF%s %stext/textarea/WYSIWYG%s fields.', 'stylebidet'), '<code>', '</code>', '<a href="https://en-gb.wordpress.org/plugins/advanced-custom-fields/">', '</a>', '<code>', '</code>'); ?>
								</span>
							</td>
						</tr>
						
					</table>
					
					<?php
						//	We should only show the update button if any of these options can actually be saved
						
						$showUpdate = false;
						
						foreach (VNM_STYLEBIDET_OPTIONS as $option) {
							if ($optionsArray[$option . '_editable'] && !$showUpdate) {
								$showUpdate = true;
							}
						}
					?>
					
					<p class="submit">
						<button type="submit" name="updateVNMStyleBidetSettings" id="submit" class="button-primary<?php if (!$showUpdate) { echo ' disabled'; } ?>">
							<?php _e('Update Settings', 'stylebidet'); ?>
						</button>
						
						<?php if (!$showUpdate) : ?>
							<br /><span class="description">
								<?php printf(__('All of the StyleBidet settings are defined as constants and so aren\'t editable here. You should probably hide this settings page with the constant %s.'), '<code>define(\'VNM_STYLEBIDET_SHOW_SETTINGS\', false)</code>'); ?>
							</span>
						<?php endif; ?>
					</p>
					
				</form>
			</div>
			
		</section>
		
		<section class="boxcontainer justify alignmiddle credits">
			<div class="vnm-logo box">
				<a href="https://www.verynewmedia.com/" target="_blank">Very New Media&trade;</a>
			</div>
			
			<div class="vnm-rating-request box">
				<?php
					printf(
						__('Is StyleBidet making your life a little easier? %sWhy not leave us a %s review?%s %s', 'stylebidet'),
						'<a href="https://wordpress.org/plugins/stylebidet/" target="_blank">',
						'<span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>',
						'</a>',
						'<span class="dashicons dashicons-smiley"></span>'
					);
				?>
			</div>
		</section>
	</div>
	
	<?php
}

/////
///
//	Let's do the job we came here for!
///
/////

function vnmStyleBidet_cleanUpContent($content, $forOutput = false) {
	
	//	Determine is this is for direct ouput, or for saving as part of the $content object
	
	if (!$forOutput || is_array($content)) {
		$theHTML = stripslashes($content['post_content']);
	} else {
		$theHTML = $content;
	}
	
	//	Get the options
	
	$optionsArray = vnmStyleBidet_getOptions();
	
	$cleanStyleOutput = $optionsArray['clean_output'];
	$cleanStyleSave = $optionsArray['clean_save'];
	$cleanScript = $optionsArray['clean_script'];
	
	//	If any of the options are enabled, then let's skip through the content
	
	if ($cleanStyleOutput || $cleanStyleSave || $cleanScript) {
		
		//	Remove inline styles
		
		if (($cleanStyleOutput && $forOutput) || ($cleanStyleSave && !$forOutput)) {

			//	Get the standard 'allowed' tags from wp_kses and remove the `style` attribute from all of them; and the <font> tag

			$allowedHTML = wp_kses_allowed_html('post');

			foreach ($allowedHTML as $attr=>$arr) {
				if (isset($arr['style'])) {
					unset($allowedHTML[$attr]['style']);
				}

				if ($attr == 'font') {
					unset($allowedHTML['font']);
				}

				//	Just in case the allowed_html array has been filtered to explicitly allow <style> tags... let's remove it again

				if ($attr == 'style') {
					unset($allowedHTML['style']);
				}
			}

			$allowedHTML = apply_filters('stylebidet_allowed_html', $allowedHTML);

			//	Filter the HTML

			$theHTML = wp_kses($theHTML, $allowedHTML);
		}
		
		//	Script tags will automatically be removed because they're not an allowed tag
		
		if ($cleanScript) {
			//	Do nothin'
		}
	}
	
	//	Return the appropriate variable
	
	if (!$forOutput || is_array($content)) {
		$content['post_content'] = $theHTML;
	} else {
		$content = $theHTML;
	}
	
	return $content;
}

///
//	Wrapper function for the_content filter
///

function vnmStyleBidet_cleanUpOutput($content) {
	return vnmStyleBidet_cleanUpContent($content, true);
}

///
//	Get the options and, if true, filter the content
///

//	When a post is saved:

add_filter('wp_insert_post_data', 'vnmStyleBidet_cleanUpContent');

//	When a post is displayed:

add_filter('the_content', 'vnmStyleBidet_cleanUpOutput');

//	Clean up ACF Fields!

function vnmStyleBidet_cleanUpACFFields($value, $postID, $field) {
	$cleanACF = vnmStyleBidet_getOptions('clean_acf');

	if ($cleanACF) {
		$value = vnmStyleBidet_cleanUpContent($value, true);
	}
	
	return $value;
}

add_filter('acf/format_value/type=text', 'vnmStyleBidet_cleanUpACFFields', 10, 3);
add_filter('acf/format_value/type=textarea', 'vnmStyleBidet_cleanUpACFFields', 10, 3);
add_filter('acf/format_value/type=wysiwyg', 'vnmStyleBidet_cleanUpACFFields', 10, 3);
add_filter('acf/load_value/type=text', 'vnmStyleBidet_cleanUpACFFields', 10, 3);
add_filter('acf/load_value/type=textarea', 'vnmStyleBidet_cleanUpACFFields', 10, 3);
add_filter('acf/load_value/type=wysiwyg', 'vnmStyleBidet_cleanUpACFFields', 10, 3);
add_filter('acf/update_value/type=text', 'vnmStyleBidet_cleanUpACFFields', 10, 3);
add_filter('acf/update_value/type=textarea', 'vnmStyleBidet_cleanUpACFFields', 10, 3);
add_filter('acf/update_value/type=wysiwyg', 'vnmStyleBidet_cleanUpACFFields', 10, 3);

///
//	Remove that accursed font color button from the Classic Editor
///

function vnmStyleBidet_disableMCEColorButton($options) {
	$removeTextColorButton = vnmStyleBidet_getOptions('remove_text_color');

	if ($removeTextColorButton) {
		$options['theme_advanced_disable'] = 'forecolor';
	}
	
	return $options;
}

add_filter('tiny_mce_before_init', 'vnmStyleBidet_disableMCEColorButton', 10, 1);

///
//	WP ACTIONS
///

register_activation_hook( __FILE__, 'vnmStyleBidet_install');
register_deactivation_hook( __FILE__, 'vnmStyleBidet_deactivate');

?>