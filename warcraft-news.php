<?php
  /*
   Plugin Name: Warcraft News
   Plugin URI: http://www.baraans-corner.de/wordpress-plugins/warcraft-news/
   Description: Shows news feeds of a World of Warcraft guild or World of Warcraft Character provided by the new battle.net armory in a widget. Includes caching of the data to prevent the armory to block wordpress's IP address.
   Version: 0.9.1.1
   Author: Baraan
   Author URI: http://www.baraans-corner.de/

   Copyright 2010-present Baraan <baraan@baraans-corner.de>

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


   PHP Simple HTML DOM Parser (http://simplehtmldom.sourceforge.net/) written by
   S.C. Chen (me578022@gmail.com) licensed under The MIT License.
   (Check simple_html_dom.php for more information.)
   Modified for this plugin to make name collisions with other plugins improbable.
  */

// to parse HTML
require_once('simple_html_dom.php');
// The widget code
require_once('warcraft-news-widgets.php');

/**
 * Returns news.
 */
function warcraft_news_html($instance){
	$rewrite_items = get_option('warcraft_news_rewrite_items');
	$cache_time = intval(get_option('warcraft_news_global_cache_time'))*60;
	$cache_dir = ABSPATH . "wp-content/plugins/warcraft-news/cache";

	if(!is_writable($cache_dir)){
		return __("Cache directory not writable. Please make sure wordpress can write the cache files.", 'warcraft_news');
	}

	if(isset($instance['guild'])){
		$guild_news = true;
		$name = strtolower($instance['guild']);
	}
	elseif(isset($instance['char'])){
		$guild_news = false;
		$name = strtolower($instance['char']);
	}
	else{
		return "No guild *and* no char found. Exiting!";
	}
	$realm = strtolower($instance['realm']);
	$region = strtolower($instance['region']);
	$lang = strtolower($instance['lang']);
	$item_count = $instance['item_count'];


	$warcraft_news_base_url = "http://$region.battle.net";
	if($guild_news){
		$url = $warcraft_news_base_url . "/wow/$lang/guild/$realm/$name/news";
		$cache_file = "$cache_dir/guild-$region-$realm-$name-$lang";
	}
	else{
		$char_base = $warcraft_news_base_url . "/wow/$lang/character/$realm/$name/";
		$url = $char_base . "feed";
		$cache_file = "$cache_dir/char-$region-$realm-$name-$lang";
	}
	$last_cache_update = time()-filemtime($cache_file);
	$data = new warcraft_news_simple_html_dom();


	if( !(file_exists($cache_file) && filesize($cache_file) > 20000 && $last_cache_update < $cache_time) ){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		$str = curl_exec($curl);
		curl_close($curl);

		if(strlen($str) > 20000){
			$data->load($str);
			$data->save($cache_file);
			$last_cache_update = 0;
		}
		else{
			// string too short, most probably not the data we want.
			// try the cache, even though it might be older.
			if(file_exists($cache_file) && filesize($cache_file) > 20000){
				$data->load_file($cache_file);
			}
			else{
				return "Armory not reachable or guild not found. Tried to fetch <a href='$url'>this Link</a>. Armory might be blocking, please wait and try to increase cache time.";
			}
		}
	}
	else{
		// cache hit
		$data->load_file($cache_file);
	}

	if($guild_news){
		$news_div = $data->getElementById("news-list");
		if(!$news_div) return "No news found!";
		$news_list = $news_div->find("ul", 0);
	}
	else{
		$news_list = $data->find("ul.activity-feed-wide", 0);
	}

	if(!$news_list) return "No news found!";
	$news_li = $news_list->children();
	if(!$news_li) return "No news found!";
	
	$news_li = array_slice($news_li, 0, $item_count);

	$html .= "<ul>";
	foreach($news_li as $li){
		if($guild_news){
			warcraft_news_mangle_links($li, $warcraft_news_base_url, $lang, $rewrite_items);
		}
		else{
			warcraft_news_mangle_links($li, $warcraft_news_base_url, $lang, $rewrite_items, $char_base);
		}
		$element = $li->find("dd", 0)->innertext();
		$element .= " (". $li->find("dt", 0)->innertext() .")";
		$html .= "<li>". $element ."</li>";
	}

	$html .= "<li><a href='$url'>More news</a> ";
	if($last_cache_update < 60){
		$html .= "(".__('Just updated', 'warcraft_news').")";
	}
	else if($last_cache_update < 120){
		$html .= "(".__('Last updated about a minute ago').")";
	}
	else{
		$html .= "(Last updated about ". floor($last_cache_update/60) ." minutes ago)";
	}
	$html .= "</li>";

	$html .= "</ul>";

	return $html;
}


function warcraft_news_mangle_links($element, $base_url, $lang = en, $rewrite_items = true, $char_base = ""){
	$wh_lang = ($lang == "en")?("www"):($lang);
	if(!empty($char_base)){
		$links = $element->find("a[href^=achievement]");
		foreach($links as $link){
			$link->href = $char_base . $link->href;
		}
	}

	$links = $element->find("a[href^=/wow]");
	foreach($links as $link){
		$link->href = $base_url . $link->href;
		if($rewrite_items){
			$link->href = str_replace($base_url ."/wow/". $lang ."/item/", "http://$wh_lang.wowhead.com/item=", $link->href);
		}
	}
}

function warcraft_news_init() {
	if(get_option('warcraft_news_wowhead')){
		wp_enqueue_script('wowhead', "http://static.wowhead.com/widgets/power.js");
	}
}    
 
add_action('init', 'warcraft_news_init');
//add_shortcode('warcraft-guild', 'wg_test');
//function wg_test(){ return warcraft_news_html(); }


/**
 * Set the default settings on activation on the plugin.
 */
function warcraft_news_activation_hook() {
	return warcraft_news_restore_config(false);
}
register_activation_hook(__FILE__, 'warcraft_news_activation_hook');


/**
 * Add the Warcraft News menu to the Settings menu
 */
function warcraft_news_restore_config($force=false) {
	if($force || (get_option('warcraft_news_wowhead', "NOTSET") == "NOTSET")){
		update_option('warcraft_news_wowhead', false);
	}

	if($force || (get_option('warcraft_news_rewrite_items', "NOTSET") == "NOTSET")){
		update_option('warcraft_news_rewrite_items', true);
	}

	if($force || !(get_option('warcraft_news_global_cache_time'))){
		update_option('warcraft_news_global_cache_time', "15");
	}
}

/**
 * Add the Reaction Buttons menu to the Settings menu
 */
function warcraft_news_admin_menu() {
	add_options_page('Warcraft News', 'Warcraft News', 8, 'warcraft_news', 'warcraft_news_submenu');
}
add_action('admin_menu', 'warcraft_news_admin_menu');

/**
 * Displays the Reaction Button admin menu
 */
function warcraft_news_submenu() {

	// check if the cache dir is writable and complain if not.
	$cache_dir = ABSPATH . "wp-content/plugins/warcraft-news/cache";
	if(!is_writable($cache_dir)){
		warcraft_news_message("Cache dir ($cache_dir) not writable. Please make sure wordpress can write into the cache directory for the plugin to work.");
	}

	// restore the default config
	if (isset($_REQUEST['restore']) && $_REQUEST['restore']) {
		check_admin_referer('warcraft_news_config');
		warcraft_news_restore_config(true);
		warcraft_news_message(__("Restored all settings to defaults. <a href=''>Back</a>", 'warcraft_news'));
	}
	// saves the settings from the page
	else if (isset($_REQUEST['save']) && $_REQUEST['save']) {
		check_admin_referer('warcraft_news_config');
		$error = "";

		// save the different settings
		// boolean values
		foreach ( array('wowhead', 'rewrite_items') as $val ) {
			if ( isset($_POST[$val]) && $_POST[$val] )
				update_option('warcraft_news_'.$val,true);
			else
				update_option('warcraft_news_'.$val,false);
		}

/*		// text values
		foreach ( array('global_cache_time') as $val ) {
			if ( !$_POST[$val] )
				update_option( 'warcraft_news_'.$val, '');
			else
				update_option( 'warcraft_news_'.$val, $_POST[$val] );
		}
*/
			if(!$_POST['global_cache_time'] ||
			   !is_numeric($_POST['global_cache_time']) ||
			   intval($_POST['global_cache_time']) < 0){
				warcraft_news_message(__("Please input a positiv number for the cache time!", 'warcraft_news'));
				update_option('global_cache_time', '15');
			}
			else{
				update_option('warcraft_news_global_cache_time', $_POST['global_cache_time']);
			}


		// done saving
		if($error){
			$error = __("Some settings couldn't be saved. More details in the error message below:<br />", 'warcraft_news') . $error;
			warcraft_news_message($error);
		}
		else{
			warcraft_news_message(__("Changes saved. <a href=''>Back</a>", 'warcraft_news'));
		}
	}
	else {
	/**
	 * Display options.
	 */
	?>
	<form action="<?php echo attribute_escape( $_SERVER['REQUEST_URI'] ); ?>" method="post">
	<?php
		if ( function_exists('wp_nonce_field') )
			 wp_nonce_field('warcraft_news_config');
	?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e("Warcraft News Options", 'warcraft_news'); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row" valign="top">
						<?php _e("Default cache time:", "warcraft_news"); ?>
					</th>
					<td>
						<?php _e("Minimum time to wait before reloadeding the data from the armory. If set to low, the armory might block your IP address for some time.", 'warcraft_news'); ?><br/>
						<input size="80" type="text" name="global_cache_time" value="<?php echo attribute_escape(stripslashes(get_option('warcraft_news_global_cache_time'))); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">
						<?php _e("Rewrite item links to link to Wowhead.", "warcraft_news"); ?>
					</th>
					<td>
						<?php _e("Instead of having the items in the news link to Battle.net, they will berewritten and pointing to <a href='http://www.wowhead.com/'>Wowhead</a>. If you have the Wowhead javascript included (or activate the option below) this will create tooltips if you hover over the item.", 'warcraft_news'); ?><br/>
						<input type="checkbox" name="rewrite_items" <?php checked( get_option('warcraft_news_rewrite_items'), true ) ; ?> />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">
						<?php _e("Include Wowhead Javascript.", "warcraft_news"); ?>
					</th>
					<td>
						<?php _e("Includes the <a href='http://www.wowhead.com/'>Wowhead</a> javascript to add tooltips to items. (Activate the rewrite option above!) Don't activate this if you already have the script included manually or by your theme.", 'warcraft_news'); ?><br/>
						<input type="checkbox" name="wowhead" <?php checked( get_option('warcraft_news_wowhead'), true ) ; ?> />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<span class="submit"><input name="save" value="<?php _e("Save Changes", 'warcraft_news'); ?>" type="submit" /></span>
						<span class="submit"><input name="restore" value="<?php _e("Restore Built-in Defaults", 'warcraft_news'); ?>" type="submit"/></span>
					</td>
				</tr>
			</table>
		</div>
	</form>
<?php
	}
}


/**
 * Add a settings link to the plugins page, so people can go straight from the plugin page to the
 * settings page.
 */
function warcraft_news_filter_plugin_actions( $links, $file ){
	// Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;
	if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if ( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=warcraft_news">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}
add_filter( 'plugin_action_links', 'warcraft_news_filter_plugin_actions', 10, 2 );

/**
 * Update message, used in the admin panel to show messages to users.
 */
function warcraft_news_message($message) {
	echo "<div id=\"message\" class=\"updated fade\"><p>$message</p></div>\n";
}


?>
