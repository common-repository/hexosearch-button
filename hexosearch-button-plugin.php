<?php
/* 
Plugin Name: HexoSearch Button Plugin
Plugin URI: http://www.hexosearch.com/
Version: v1.0
Author: HexoSearch
Description: HexoSearch button allows visitors to vote for your blog posts to boost their ranking in <a href="http://www.hexosearch.com" target="_blank">HexoSearch - the world's first flash-actionscript search engine</a>.
 
Copyright 2009  HexoSearch

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

*/
if ( !defined('WP_CONTENT_URL') )
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );

$pluginUrlBasename = plugin_basename(dirname(__FILE__));
$pluginUrlPath = WP_PLUGIN_URL.'/'.$pluginUrlBasename; // /wp-content/plugins/add-to-any

if (!class_exists("HexoSearchButtonPlugin")) {
	class HexoSearchButtonPlugin {
		var $adminOptionsName = "HexoSearchButtonPluginAdminOptions";
		function HexoSearchButtonPlugin() { //constructor
			
		}
		function init() {
			$this->getAdminOptions();
		}
		//Returns an array of admin options
		function getAdminOptions() {
			$hexosearchAdminOptions = array('design' => 'icon_small',
				'test_mode' => 'disable', 
				'language_type' => 'default',
				'display_posts' => '1',
				'display_posts_front' => '1',
				'display_feed' => '1',
				'display_pages' => '1',
				'design_text_text' => 'Vote',
				'design_text_small' => 'Vote',
				'design_image_text' => '');
			$hsOptions = get_option($this->adminOptionsName);
			if (!empty($hsOptions)) {
				foreach ($hsOptions as $key => $option)
					$hexosearchAdminOptions[$key] = $option;
				
			}				
			update_option($this->adminOptionsName, $hexosearchAdminOptions);
			return $hexosearchAdminOptions;
		}
		
		function getButton(){
			global $post;
			global $pluginUrlPath;
			$hsOptions = $this->getAdminOptions();
			$link = "<a href='";
			switch($hsOptions['test_mode']){
				case "disable":
					$link .= "http://www.hexosearch.com/se/submit.aspx?";
					break;
				case "enable":
					$link .= "http://www.hexosearch.com/se/submit_test.aspx?";
					break;
			}
			switch($hsOptions['language_type']){
				case "default":
					$link .= "zlvz=";
					break;
				case "any":
					$link .= "zlvz=0";
					break;
				case "as2":
					$link .= "zlvz=1";
					break;
				case "as3":
					$link .= "zlvz=2";
					break;
				case "flex":
					$link .= "zlvz=3";
					break;
			}
			$link .= "&zqz=";
			$link .= "&zurlz=".js_escape(get_permalink($post->ID));
			$link .= "&ztz=".js_escape($post->post_title);
			$link .= "'>";
			switch($hsOptions['design']){
				case "icon_small":
					$image = "<img src='".$pluginUrlPath."/logo16x16.png' width='16' height='16' border='0' style='vertical-align:middle' />";
					$embed = $link.$image."</a>";
					break;
				case "icon_big":
					$image = "<img src='".$pluginUrlPath."/logo24x24.png' width='24' height='24' border='0' style='vertical-align:middle' />";
					$embed = $link.$image."</a>";
					break;
				case "text":
					$embed = "<span style='vertical-align:middle'>".$link.$hsOptions['design_text_text']."</a>"."</span>";
					break;
				case "image":
					$image = "<img src='".$hsOptions['design_image_text']."' border='0' style='vertical-align:middle' />";
					$embed = $link.$image."</a>";
					break;
				case "both":
					$image = "<img src='".$pluginUrlPath."/logo16x16.png' width='16' height='16' border='0' style='padding:0px 5px 0px 0px;vertical-align:middle' />";
					$embed = $link.$image."</a> "."<span style='vertical-align:middle'>".$link.$hsOptions['design_text_small']."</a>"."</span>";
					break;
			}
			
			return $embed;
		}
		
		function addHexoSearchButton($content='') {
			$is_feed = is_feed();
			$hsOptions = $this->getAdminOptions();
			if ( 
				( 
					strpos($content, '<!--hexosearch-->')===false || 									
					strpos($content, '<!--nohexosearch-->')!==false												
				) &&											
				(
					( ! is_page() && $hsOptions['display_posts']=='-1' ) || 				
					( is_home() && $hsOptions['display_posts_front']=='-1' ) ||  	
					( is_category() && $hsOptions['display_posts_front']=='-1' ) ||  	
					( is_tag() && $hsOptions['display_posts_front']=='-1' ) ||  	
					( is_date() && $hsOptions['display_posts_front']=='-1' ) ||  		
					( is_author() && $hsOptions['display_posts_front']=='-1' ) ||  	
					( is_search() && $hsOptions['display_posts_front']=='-1' ) ||  
					( $is_feed && ($hsOptions['display_feed']=='-1' ) || 				
					
					( is_page() && $hsOptions['display_posts_pages']=='-1' ) ||					
					( (strpos($content, '<!--nohexosearch-->')!==false) )								
				)
				)
			)	
				return $content;
			
			$content .= $this->getButton();
			return $content;
		}
		
		function printAdminHead() {
			if (isset($_GET['page']) && $_GET['page'] == 'hexosearch-button-plugin.php') {
				global $wp_version;
				
				//if ($wp_version < "2.6")
				//	return;
			?>
		
			<style type="text/css">
			.desc{font-size:11px; font-style:italic; color:#666666; padding-top:5px; float:left;}
			.right{font-size:11px;}
            </style>
		<?php
			}
		}
		
		//Prints out the admin page
		function printAdminPage() {
			global $pluginUrlPath;
			$hsOptions = $this->getAdminOptions();
								
			if (isset($_POST['update_hexosearchButtonPluginSettings'])) { 
				if (isset($_POST['hexosearchDesign'])) {
					$hsOptions['design'] = $_POST['hexosearchDesign'];
				}	
				if (isset($_POST['hexosearchTest'])) {
					$hsOptions['test_mode'] = $_POST['hexosearchTest'];
				}	
				if (isset($_POST['hexosearchType'])) {
					$hsOptions['language_type'] = $_POST['hexosearchType'];
				}	
				if (isset($_POST['hexosearchDisplay_posts'])) {
					$hsOptions['display_posts'] = $_POST['hexosearchDisplay_posts'];
				}else{
					$hsOptions['display_posts'] = '-1';
				}
				if (isset($_POST['hexosearchDisplay_posts_front'])) {
					$hsOptions['display_posts_front'] = $_POST['hexosearchDisplay_posts_front'];
				}else{
					$hsOptions['display_posts_front'] = '-1';
				}
				if (isset($_POST['hexosearchDisplay_feed'])) {
					$hsOptions['display_feed'] = $_POST['hexosearchDisplay_feed'];
				}else{
					$hsOptions['display_feed'] = '-1';
				}
				if (isset($_POST['hexosearchDisplay_pages'])) {
					$hsOptions['display_pages'] = $_POST['hexosearchDisplay_pages'];
				}else{
					$hsOptions['display_pages'] = '-1';
				}
				if (isset($_POST['hexosearchDesign_text_text'])) {
					$hsOptions['design_text_text'] = $_POST['hexosearchDesign_text_text'];
				}else{
					$hsOptions['design_text_text'] = '';
				}
				if (isset($_POST['hexosearchDesign_text_small'])) {
					$hsOptions['design_text_small'] = $_POST['hexosearchDesign_text_small'];
				}else{
					$hsOptions['design_text_small'] = '';
				}
				if (isset($_POST['hexosearchDesign_image_text'])) {
					$hsOptions['design_image_text'] = $_POST['hexosearchDesign_image_text'];
				}else{
					$hsOptions['design_image_text'] = '';
				}
				update_option($this->adminOptionsName, $hsOptions);
				
				?>
			<div class="updated"><p><strong><?php _e("Settings Updated.", "HexoSearchButtonPlugin");?></strong></p></div>
			<?php
			} ?>
            <div class=wrap>
                <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                    <h2>HexoSearch Button Plugin</h2>
                    <table width="100%" cellpadding="5 40" cellspacing="5" border="0">
                        <tr>
                            <td valign="top" width="20%" rowspan="5">Button</td>
                            <td valign="top" class="right"><label for="hexosearchDesign_icon_small"><input type="radio" id="hexosearchDesign_icon_small" name="hexosearchDesign" value="icon_small" <?php if ($hsOptions['design'] == "icon_small") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?> style="margin:9px 0;vertical-align:middle" /> <img src="<?php echo $pluginUrlPath.'/logo16x16.png'; ?>" width="16" height="16" border="0" style="padding:9px;vertical-align:middle" onclick="this.parentNode.firstChild.checked=true"/></label>
                            </td>
                        </tr>
                        <tr>
                        	<td valign="top" class="right"><label for="hexosearchDesign_icon_big"><input type="radio" id="hexosearchDesign_icon_big" name="hexosearchDesign" value="icon_big" <?php if ($hsOptions['design'] == "icon_big") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?> style="margin:9px 0;vertical-align:middle" /> <img src="<?php echo $pluginUrlPath.'/logo24x24.png'; ?>" width="24" height="24" border="0" style="padding:9px;vertical-align:middle" onclick="this.parentNode.firstChild.checked=true"/></label></td>
                        </tr>
                        <tr>
                        	<td valign="top" class="right"><label for="hexosearchDesign_image"><input type="radio" id="hexosearchDesign_image" name="hexosearchDesign" value="image" <?php if ($hsOptions['design'] == "image") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?> style="margin:9px 0;vertical-align:middle" /> <span style="margin:0 9px;vertical-align:middle"><?php _e("Image URL"); ?>:</label> <input name="hexosearchDesign_image_text" type="text" size="50" onclick="e=document.getElementById('hexosearchDesign_image');e.checked=true" style="vertical-align:middle;" value="<?php echo ( trim($hsOptions['design_image_text']) != '' ) ? stripslashes($hsOptions['design_image_text']) : ""; ?>" /></td>
                        </tr>
                        <tr>
                        	<td valign="top" class="right"><label for="hexosearchDesign_text"><input type="radio" id="hexosearchDesign_text" name="hexosearchDesign" value="text" <?php if ($hsOptions['design'] == "text") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?> style="margin:9px 0;vertical-align:middle" /> <span style="margin:0 9px;vertical-align:middle"><?php _e("Text only"); ?>:</label> <input name="hexosearchDesign_text_text" type="text" size="50" onclick="e=document.getElementById('hexosearchDesign_text');e.checked=true" style="vertical-align:middle;width:150px" value="<?php echo ( trim($hsOptions['design_text_text']) != '' ) ? stripslashes($hsOptions['design_text_text']) : "Vote"; ?>" /></td>
                        </tr>
                        <tr>
                            <td valign="top" class="right"><label for="hexosearchDesign_both"><input type="radio" id="hexosearchDesign_both" name="hexosearchDesign" value="both" <?php if ($hsOptions['design'] == "both") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?> style="margin:9px 0;vertical-align:middle" /> <span style="margin:0 9px;vertical-align:middle"><?php _e("Icon/Text"); ?>: <img src="<?php echo $pluginUrlPath.'/logo16x16.png'; ?>" width="16" height="16" border="0" style="padding:9px 0px 9px 9px;vertical-align:middle" onclick="this.parentNode.firstChild.checked=true"/></label> <input name="hexosearchDesign_text_small" type="text" size="50" onclick="e=document.getElementById('hexosearchDesign_both');e.checked=true" style="vertical-align:middle;width:150px" value="<?php echo ( trim($hsOptions['design_text_small']) != '' ) ? stripslashes($hsOptions['design_text_small']) : "Vote"; ?>" />
                            </td>
                        </tr>
                        <tr>
                        	<td><br /></td>
                        </tr>
                        <tr>
                        	<td valign="top" rowspan="5">Placement</td>
                            <td class="right"><label for="hexosearchDisplay_posts">
                                    <input name="hexosearchDisplay_posts" id="hexosearchDisplay_posts"  value="1"
                                        onclick="e=getElementsByName('hexosearchDisplay_posts_front')[0];f=getElementsByName('hexosearchDisplay_feed')[0];
                                            if(!this.checked){e.checked=false;e.disabled=true; f.checked=false;f.disabled=true}else{e.checked=true;e.disabled=false; f.checked=true;f.disabled=false}"
                                        onchange="e=getElementsByName('hexosearchDisplay_posts_front')[0];f=getElementsByName('hexosearchDisplay_feed')[0];
                                            if(!this.checked){e.checked=false;e.disabled=true; f.checked=false;f.disabled=true}else{e.checked=true;e.disabled=false; f.checked=true;f.disabled=false}"
                                        type="checkbox"<?php if($hsOptions['display_posts'] != '-1'){ _e('checked="checked"', "HexoSearchButtonPlugin"); } ?> />
                                    Display Share/Save button at the bottom of posts <strong>*</strong>
                                </label>
                            </td>
                        </tr>
                        <tr>
                        	<td class="right"> &nbsp;&nbsp;&nbsp;&nbsp;
                                <label for="hexosearchDisplay_posts_front">
                                    <input name="hexosearchDisplay_posts_front" id="hexosearchDisplay_posts_front" type="checkbox" value="1"<?php 
                                        if($hsOptions['display_posts_front'] != '-1'){ _e('checked="checked"', "HexoSearchButtonPlugin"); }
                                        if($hsOptions['display_posts'] == '-1'){ _e('disabled="disabled"', "HexoSearchButtonPlugin"); }
                                        ?> />
                                    Display Share/Save button at the bottom of posts on the front page
                                </label>
                            </td>
                        </tr>
                        <tr>
                        	<td class="right">&nbsp;&nbsp;&nbsp;&nbsp;
                                <label for="hexosearchDisplay_feed">
                                    <input name="hexosearchDisplay_feed" id="hexosearchDisplay_feed" type="checkbox" value="1"<?php 
                                        if($hsOptions['display_feed'] != '-1'){ _e('checked="checked"', "HexoSearchButtonPlugin"); }
                                        if($hsOptions['display_posts'] == '-1'){ _e('disabled="disabled"', "HexoSearchButtonPlugin"); }
                                        ?> />
                                    Display Share/Save button at the bottom of posts in the feed
                                </label>
                            </td>
                        </tr>
                        <tr>
                        	<td class="right">
                                <label for="hexosearchDisplay_pages">
                                    <input name="hexosearchDisplay_pages" id="hexosearchDisplay_pages" value="1" type="checkbox"<?php if($hsOptions['display_pages'] != '-1'){ _e('checked="checked"', "HexoSearchButtonPlugin"); } ?> />
                                    Display Share/Save button at the bottom of pages <strong>*</strong>
                                </label>
                            </td>
                        </tr>
                        <tr>
                        	<td class="desc"><strong>*</strong> <?php _e("If unchecked, be sure to place the following code in <a href=\"theme-editor.php\">your template pages</a> (within <code>index.php</code>, <code>single.php</code>, and/or <code>page.php</code>)", "add-to-any"); ?>:<br/>
                	<code>&lt;?php if( function_exists('addHexoSearch') ) { addHexoSearch(); } ?&gt;</code></td>
                   		</tr>
                        <tr>
                        	<td><br /></td>
                        </tr>
                		<tr>
                        	<td valign="top" rowspan="5">Pre-define language type<br /><span class="desc">Selecting "Do not specify" will ask users to input the blog post's language type upon voting.</span></td>
                            <td valign="top" class="right"><label for="hexosearchType_default"><input type="radio" id="hexosearchType_default" name="hexosearchType" value="default" <?php if ($hsOptions['language_type'] == "default") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?> /> Do not specify</label></td>
                        </tr>
                        <tr>
                        	<td valign="top" class="right"><label for="hexosearchType_any"><input type="radio" id="hexosearchType_any" name="hexosearchType" value="any" <?php if ($hsOptions['language_type'] == "any") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?>/> Any</label></td>
                        </tr>
                        <tr>
                        	<td valign="top" class="right"><label for="hexosearchType_as2"><input type="radio" id="hexosearchType_as2" name="hexosearchType" value="as2" <?php if ($hsOptions['language_type'] == "as2") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?>/> Actionscript 2</label></td>
                        </tr>
                        <tr>
                        	<td valign="top" class="right"><label for="hexosearchType_as3"><input type="radio" id="hexosearchType_as3" name="hexosearchType" value="as3" <?php if ($hsOptions['language_type'] == "as3") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?>/> Actionscript 3</label></td>
                        </tr>
                        <tr>
                        	<td valign="top" class="right"><label for="hexosearchType_flex"><input type="radio" id="hexosearchType_flex" name="hexosearchType" value="flex" <?php if ($hsOptions['language_type'] == "flex") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?>/> Flex</label></td>
                        </tr>
                        <tr>
                        	<td><br /></td>
                        </tr>
                        <tr>
                        	<td valign="top" rowspan="2">Test mode<br /><span class="desc">You must disable the test mode to enable voting.</span></td>
                        	<td valign="top" class="right"><label for="hexosearchTest_disable"><input type="radio" id="hexosearchTest_disable" name="hexosearchTest" value="disable" <?php if ($hsOptions['test_mode'] == "disable") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?> /> Disable</label></td>
                        </tr>
                        <tr>
                        	<td valign="top" class="right"><label for="hexosearchTest_enable"><input type="radio" id="hexosearchTest_enable" name="hexosearchTest" value="enable" <?php if ($hsOptions['test_mode'] == "enable") { _e('checked="checked"', "HexoSearchButtonPlugin"); }?>/> Enable</label></td>
                        </tr>
                    </table>
                    <div class="submit">
                    <input type="submit" name="update_hexosearchButtonPluginSettings" value="<?php _e('Update Settings', 'HexoSearchButtonPlugin') ?>" /></div>
            	</form>
 			</div>
			<?php
		}//End function printAdminPage()
	}
} //End Class HexoSearchButtonPlugin

if (class_exists("HexoSearchButtonPlugin")) {
	$hexosearch_button_plugin = new HexoSearchButtonPlugin();
}

//Initialize the admin panel
if (!function_exists("HexoSearchButtonPlugin_ap")) {
	function HexoSearchButtonPlugin_ap() {
		global $hexosearch_button_plugin;
		if (!isset($hexosearch_button_plugin)) {
			return;
		}
		if (function_exists('add_options_page')) {
		add_options_page('HexoSearch Button', 'HexoSearch Button', 9, basename(__FILE__), array(&$hexosearch_button_plugin, 'printAdminPage'));
		}
	}	
}

function addHexoSearch()
{
	if (!isset($hexosearch_button_plugin)) {
		$hexosearch_button_plugin = new HexoSearchButtonPlugin();
	}
	echo $hexosearch_button_plugin->addHexoSearchButton();
}

add_action('the_content', array(&$hexosearch_button_plugin, 'addHexoSearchButton'), 98);

if (isset($hexosearch_button_plugin)) {
	//Actions
	add_action('admin_menu', 'HexoSearchButtonPlugin_ap');
	add_action('admin_head', array(&$hexosearch_button_plugin, 'printAdminHead'));
	add_action('activate_hexosearch-button-plugin/hexosearch-button-plugin.php',  array(&$hexosearch_button_plugin, 'init'));
	//Filters
}

?>