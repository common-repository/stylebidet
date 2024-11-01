=== StyleBidet ===
Contributors: indextwo
Donate link: http://www.verynewmedia.com/
Tags: style, clean, css, script, purify, HTMLPurifier, design
Requires at least: 2.0.0
Tested up to: 5.5.3
Requires PHP: 5.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Tired of MS Word-pasted content and overzealous editors destroying your site styles with green Comic-Sans text & purple backgrounds? Well **not anymore**.

== Description ==

As site designers, developers & owners we all have clients & editors who, even with the best of intentions, can make a hash job of editing a page or a post; for example, making a headline italic, and purple, and Papyrus (despite the site's overall aesthetic of non-italic cobalt-blue Open Sans everywhere else); or the oft-seen act of making **all of the text red & bold so that it all stands out all the time**. 

What StyleBidet does is subtly take back some of that design control to ensure that sites look the way they were intended to. Via the handy Settings panel, you can optionally set StyleBidet to:

- Remove inline `style` attributes when saving & displaying post content;
- Remove `<style>` & `<font>` tags when saving & displaying content;
- Remove `<script>` tags when saving & displaying content;
- Remove the **Text Color** button from the WordPress Classic/WYSIWYG Editor;
- **New!** Remove inline `style/font` attributes & tags when saving & displaying **Advanced Custom Fields**.

== Installation ==

To install and run StyleBidet, all you need to do is:

1. Click **New Plugin** & search for 'StyleBidet', or download and then upload `StyleBidet` to your `/wp-content/plugins/` directory;
2. Activate the plugin through the **Plugins** menu in WordPress;
3. Set up StyleBidet via **Settings -> StyleBidet Settings**
4. Alternatively, you can configure StyleBidet with constants - see below.

= Configure with constants =

Instead of using the super-friendly settings page, you can optionally configure StyleBidet with constants in your code. This can be particularly helpful if you have a client who likes to poke around in the settings, or if you just want to set the options and forget about it.

You can set these in your site's `wp-config.php` file or in the active theme's `functions.php` file - just remember that if you do the latter and then change the theme, your settings will be lost.

= Set StyleBidet's Settings =

You can define any or all of the settings below:

`define('VNM_STYLEBIDET_SETTINGS', array(
	'clean_output'		=> true,	//  Remove style & font tags & attributes from content at the point it is displayed
	'clean_save'		=> true,	//  Permanently remove style & font tags & attributes from content at the point it is saved. 
	'remove_text_color'	=> true		//  Remove the Text Color button from the TinyMCE editor
));`
'clean_acf'			=> true,	//  Remove style & font tags & attributes from ACF field content

If you have left the Settings page visible (see below), you'll see a lock icon next to any options you have set in code, and the option can't be changed. If there are any options that you _haven't_ pre-set, you can still toggle these on & off and save your settings. If you've pre-set _all_ of the options in code, then the **Save Settings** button will be disabled, and you should probably hide the StyleBidet Settings page at this point.

To set just one option in code, for example:

`define('VNM_STYLEBIDET_SETTINGS', array('clean_output' => true));`

This will lock the *Remove style/font tags & attributes when displaying* option to enabled, but leave the rest changeable via the settings page.

= Hide StyleBidet's Settings Page =

The following will hide the settings page from wp-admin. This should only be done if you're adding the `VNM_STYLBIDET_SETTINGS` constant as above; or _after_ you've ensured all of the settings are set & working as expected.

`define('VNM_STYLEBIDET_SHOW_SETTINGS', false);`

== Frequently Asked Questions ==

= I added StyleBidet, but now all of my fancy styles have disappeared! What's going on? =

If you're comfortable and happy adding plenty of custom styles to each and every post and page, then this plugin is most definitely not for you. It assumes that your website's styling really should be taken care of by your theme's stylesheet, and so ruthlessly removes anything else.

= I installed this on a client's site and now they're complaining that they can't make all the text red & purple anymore. =

Hopefully this goes without saying, but if you're thinking of installing this on a client's site in response to their penchant for `creative additions`, you *really* need to check with your client first. This plugin was developed in response to clients making such changes and then realising they'd made a terrible mistake, unintentionally sullying the appearance of a site they'd paid good money to have designed. Always use this plugin responsibly and with client consent.

= I installed this on my site and now a bunch of stuff looks weird / isn't animating / has disappeared. =

Similarly to the point above: if you know or suspect that the site has a lot of inline `<script>` tags in the content for countdown timers, animating SVGs etc. then this plugin may not be the solution for you. Inline `<script>` tags generally aren't safe or a great idea, and StyleBidet will clean them out by default if _any_ options are enabled.

= The 'Remove script tags' button is locked. WTH? =

Inline `<script>` tags generally aren't safe or a great idea, and StyleBidet will clean them out by default if _any_ options are enabled. Because it does this by default, the `Remove script tags` option is locked to to **ON**, mostly just to let you know that it _will_ be removing them.

= I want to keep inline styling, but remove inline script tags. How do I do this? =

As with the question above: this plugin may not be the solution for you. Inline `<script>` tags generally aren't safe or a great idea, and StyleBidet will clean them out by default if _any_ options are enabled.

= Does this work with custom post types? =

StyleBidet will work with any post type that can either be displayed or saved.

= Does this work with Advanced Custom Fields? =

It totally does! As of version `1.0.0`, StyleBidet can optionally clean out your ACF text, textarea & WYSIWYG fields.

= Does this work with Gutenberg and/or Classic Editor? =

StyleBidet works with both! It was originally developed with the 'classic' WYSIWYG editor, and more recently updated to ensure it works with Gutenberg content.

== Changelog ==

= 1.0.0 =
- Removed dependency on htmlLawed and made all cleaning operations native;
- Added option for cleaning ACF fields

= 0.6.0 =
Initial public release of StyleBidet.

== Upgrade Notice ==

= 1.0.0 =
If upgrading from previous versions of StyleBidet, the `Remove script tags` option is now locked into the `ON` position, as it will remove `<script>` tags if any other option is enabled by default.

= 0.6.0 =
Initial public release of StyleBidet.