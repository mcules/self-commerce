<?php
/* -----------------------------------------------------------------------------------------
    $Id$   
   
    XT-Commerce - community made shopping
    http://www.xt-commerce.com/
    
    H.H.G. group
    Hasan H. GÃ¼rsoy
	 
    Copyright (c) 2005 XT-Commerce & H.H.G. group
		   
    Released under the GNU General Public License 
---------------------------------------------------------------------------------------*/

function xtc_wysiwyg($type, $lang, $langID = '') {

$js_src = DIR_WS_MODULES .'tiny_mce/tiny_mce.js';
$filemanager = DIR_WS_ADMIN.DIR_WS_MODULES .'tiny_mce/plugins/media/filemanager/file_manager.php';

$plugins = 'advlink,advimage,media,table';
$theme_advanced_buttons3_add = 'media,tablecontrols';

	switch($type) {
                // WYSIWYG editor content manager textarea named cont
                case 'content_manager':
                $val ='<script language="javascript" type="text/javascript" src="'.$js_src.'"></script>
								<script language="javascript" type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									plugins : \''.$plugins.'\',
									document_base_url : \''.HTTP_SERVER.DIR_WS_CATALOG.'\',
									theme_advanced_buttons3_add : \''.$theme_advanced_buttons3_add.'\',
									theme : "'.TINY_MODUS.'",
									language : "'.$lang.'",
            			file_browser_callback : "fileBrowserCallBack",									
									content_css : "../templates/'.CURRENT_TEMPLATE.'/'.TINY_CSS.'"
								});
								
            	function fileBrowserCallBack(field_name, url, type, win) {
          			var connector = "'.$filemanager.'";

          			my_field = field_name;
          			my_win = win;

          			switch (type) {
          				case "image":
          					connector += "?type=img";
          					break;
          				case "media":
          					connector += "?type=media";
          					break;
          				case "file":
          					connector += "?type=files";
          					break;
          			}

          			window.open(connector, "file_manager", "modal,width=550,height=600,scrollbars=1");
          		}								
								</script>
								';
                        break;
                // WYSIWYG editor content manager products content section textarea named file_comment
                case 'products_content':
                $val ='<script language="javascript" type="text/javascript" src="'.$js_src.'"></script>
								<script language="javascript" type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									plugins : \''.$plugins.'\',
									document_base_url : \''.HTTP_SERVER.DIR_WS_CATALOG.'\',
									theme_advanced_buttons3_add : \''.$theme_advanced_buttons3_add.'\',
									theme : "'.TINY_MODUS.'",
									language : "'.$lang.'",
            			file_browser_callback : "fileBrowserCallBack",									
									content_css : "../templates/'.CURRENT_TEMPLATE.'/'.TINY_CSS.'"
								});
								
            	function fileBrowserCallBack(field_name, url, type, win) {
          			var connector = "'.$filemanager.'";

          			my_field = field_name;
          			my_win = win;

          			switch (type) {
          				case "image":
          					connector += "?type=img";
          					break;
          				case "media":
          					connector += "?type=media";
          					break;
          				case "file":
          					connector += "?type=files";
          					break;
          			}

          			window.open(connector, "file_manager", "modal,width=550,height=600,scrollbars=1");
          		}								
								</script>
								';
                        break;
                // WYSIWYG editor categories_description textarea named categories_description[langID]
                case 'categories_description':
                $val ='<script language="javascript" type="text/javascript" src="'.$js_src.'"></script>
								<script language="javascript" type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									plugins : \''.$plugins.'\',
									document_base_url : \''.HTTP_SERVER.DIR_WS_CATALOG.'\',
									theme_advanced_buttons3_add : \''.$theme_advanced_buttons3_add.'\',
									theme : "'.TINY_MODUS.'",
									language : "'.$lang.'",
            			file_browser_callback : "fileBrowserCallBack",									
									content_css : "../templates/'.CURRENT_TEMPLATE.'/'.TINY_CSS.'"
								});
								
            	function fileBrowserCallBack(field_name, url, type, win) {
          			var connector = "'.$filemanager.'";

          			my_field = field_name;
          			my_win = win;

          			switch (type) {
          				case "image":
          					connector += "?type=img";
          					break;
          				case "media":
          					connector += "?type=media";
          					break;
          				case "file":
          					connector += "?type=files";
          					break;
          			}

          			window.open(connector, "file_manager", "modal,width=550,height=600,scrollbars=1");
          		}								
								</script>
								';
                        break;
                // WYSIWYG editor products_description textarea named products_description_langID
                case 'products_description':
                $val ='<script language="javascript" type="text/javascript" src="'.$js_src.'"></script>
								<script language="javascript" type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									plugins : \''.$plugins.'\',
									document_base_url : \''.HTTP_SERVER.DIR_WS_CATALOG.'\',
									theme_advanced_buttons3_add : \''.$theme_advanced_buttons3_add.'\',
									theme : "'.TINY_MODUS.'",
									language : "'.$lang.'",
            			file_browser_callback : "fileBrowserCallBack",									
									content_css : "../templates/'.CURRENT_TEMPLATE.'/'.TINY_CSS.'"
								});
								
            	function fileBrowserCallBack(field_name, url, type, win) {
          			var connector = "'.$filemanager.'";

          			my_field = field_name;
          			my_win = win;

          			switch (type) {
          				case "image":
          					connector += "?type=img";
          					break;
          				case "media":
          					connector += "?type=media";
          					break;
          				case "file":
          					connector += "?type=files";
          					break;
          			}

          			window.open(connector, "file_manager", "modal,width=550,height=600,scrollbars=1");
          		}								
								</script>
								';
                        break;
                // WYSIWYG editor products short description textarea named products_short_description_langID
                case 'products_short_description':
                $val ='<script language="javascript" type="text/javascript" src="'.$js_src.'"></script>
								<script language="javascript" type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									plugins : \''.$plugins.'\',
									document_base_url : \''.HTTP_SERVER.DIR_WS_CATALOG.'\',
									theme_advanced_buttons3_add : \''.$theme_advanced_buttons3_add.'\',
									theme : "'.TINY_MODUS.'",
									language : "'.$lang.'",
            			file_browser_callback : "fileBrowserCallBack",									
									content_css : "../templates/'.CURRENT_TEMPLATE.'/'.TINY_CSS.'"
								});
								
            	function fileBrowserCallBack(field_name, url, type, win) {
          			var connector = "'.$filemanager.'";

          			my_field = field_name;
          			my_win = win;

          			switch (type) {
          				case "image":
          					connector += "?type=img";
          					break;
          				case "media":
          					connector += "?type=media";
          					break;
          				case "file":
          					connector += "?type=files";
          					break;
          			}

          			window.open(connector, "file_manager", "modal,width=550,height=600,scrollbars=1");
          		}								
								</script>
								';
                        break;
                // WYSIWYG editor newsletter textarea named newsletter_body
                case 'newsletter':
                $val ='<script language="javascript" type="text/javascript" src="'.$js_src.'"></script>
								<script language="javascript" type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									elements : "abshosturls",
									plugins : \''.$plugins.'\',
									document_base_url : \''.HTTP_SERVER.DIR_WS_CATALOG.'\',
									theme_advanced_buttons3_add : \''.$theme_advanced_buttons3_add.'\',
									theme : "'.TINY_MODUS.'",
									language : "'.$lang.'",
            			file_browser_callback : "fileBrowserCallBack",
                  relative_urls : false,
                  remove_script_host : false,									
									content_css : "../templates/'.CURRENT_TEMPLATE.'/'.TINY_CSS.'"
								});
								
            	function fileBrowserCallBack(field_name, url, type, win) {
          			var connector = "'.$filemanager.'";

          			my_field = field_name;
          			my_win = win;

          			switch (type) {
          				case "image":
          					connector += "?type=img";
          					break;
          				case "media":
          					connector += "?type=media";
          					break;
          				case "file":
          					connector += "?type=files";
          					break;
          			}

          			window.open(connector, "file_manager", "modal,width=550,height=600,scrollbars=1");
          		}								
								</script>
								';
                        break;
                // WYSIWYG editor mail textarea named message
                case 'mail':
                $val ='<script language="javascript" type="text/javascript" src="'.$js_src.'"></script>
								<script language="javascript" type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									plugins : \''.$plugins.'\',
									document_base_url : \''.HTTP_SERVER.DIR_WS_CATALOG.'\',
									theme_advanced_buttons3_add : \''.$theme_advanced_buttons3_add.'\',
									theme : "'.TINY_MODUS.'",
									language : "'.$lang.'",
            			file_browser_callback : "fileBrowserCallBack",									
									content_css : "../templates/'.CURRENT_TEMPLATE.'/'.TINY_CSS.'"
								});
								
            	function fileBrowserCallBack(field_name, url, type, win) {
          			var connector = "'.$filemanager.'";

          			my_field = field_name;
          			my_win = win;

          			switch (type) {
          				case "image":
          					connector += "?type=img";
          					break;
          				case "media":
          					connector += "?type=media";
          					break;
          				case "file":
          					connector += "?type=files";
          					break;
          			}

          			window.open(connector, "file_manager", "modal,width=550,height=600,scrollbars=1");
          		}								
								</script>
								';
                        break;
                // WYSIWYG editor gv_mail textarea named message
                case 'gv_mail':
                $val ='<script language="javascript" type="text/javascript" src="'.$js_src.'"></script>
								<script language="javascript" type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									plugins : \''.$plugins.'\',
									document_base_url : \''.HTTP_SERVER.DIR_WS_CATALOG.'\',
									theme_advanced_buttons3_add : \''.$theme_advanced_buttons3_add.'\',
									theme : "'.TINY_MODUS.'",
									language : "'.$lang.'",
            			file_browser_callback : "fileBrowserCallBack",									
									content_css : "../templates/'.CURRENT_TEMPLATE.'/'.TINY_CSS.'"
								});
								
            	function fileBrowserCallBack(field_name, url, type, win) {
          			var connector = "'.$filemanager.'";

          			my_field = field_name;
          			my_win = win;

          			switch (type) {
          				case "image":
          					connector += "?type=img";
          					break;
          				case "media":
          					connector += "?type=media";
          					break;
          				case "file":
          					connector += "?type=files";
          					break;
          			}

          			window.open(connector, "file_manager", "modal,width=550,height=600,scrollbars=1");
          		}								
								</script>
								';
                        break;
    }
    
   	return $val;

}
?>
