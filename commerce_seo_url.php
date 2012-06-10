<?php
include ('includes/configure.php');

if(!empty($_GET['linkurl']) && empty($_GET['error']))
{
	$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
	mysql_select_db(DB_DATABASE);
	$data = mysql_fetch_array(mysql_query(" SELECT * FROM bluegate_seo_url WHERE url_text = '".mysql_escape_string($_GET['linkurl'])."' LIMIT 1"));

	function get_cpath ($category)
	{
		$cPath = $category;
		while($category != '0')
		{
			$category_data = mysql_fetch_array(mysql_query(" SELECT parent_id FROM categories WHERE categories_id = '".mysql_escape_string($category)."' LIMIT 1"));
			if($category_data['parent_id'] != '0')
				$cPath = $category_data['parent_id'].'_'.$cPath;
			
				if($category_data['parent_id'] == 0)
					break;
			$category = $category_data['parent_id'];
		}
		return $cPath;
	}
	
	unset($_GET['linkurl']);
	
	if($data['products_id'] != '')
	{
		$cat_data = mysql_fetch_array(mysql_query(" SELECT categories_id FROM bluegate_seo_url WHERE url_text = '".substr($data['url_text'],0,strrpos($data['url_text'],'/'))."' LIMIT 1"));

		$_GET['cPath'] = get_cpath($cat_data['categories_id']);

		
		if(preg_match('/_/i',$_GET['cPath']))
			$_GET['cat'] = substr($_GET['cPath'],strrpos( $_GET['cPath'], '_' )+1);
		else
			$_GET['cat'] = $_GET['cPath'];
		
		$_GET['products_id'] = $data['products_id'];
		include('product_info.php');
	}
	elseif($data['categories_id'] != '')
	{
		$_GET['cPath'] = get_cpath($data['categories_id']);
		if(preg_match('/_/i',$_GET['cPath']))
			$_GET['cat'] = substr($_GET['cPath'],strrpos( $_GET['cPath'], '_' )+1);
		else
			$_GET['cat'] = $_GET['cPath'];
		include('index.php');
	}
	elseif ($data['content_group'] != '')
	{
		$_GET['coID'] = $data['content_group'];
		include('shop_content.php');
	}
	elseif ($data['blog_id'] != '' )
	{		
		$_GET['blog_item'] = $data['blog_id'];
		
		include('blog.php');
	}
	elseif ($data['blog_cat'] != '' )
	{		
		$_GET['blog_cat'] = $data['blog_cat'];
		
		include('blog.php');
	}
	elseif ($data['url_text'] != '' && $data['language_id'] != '')
		include('index.php');
	else {
		$_GET['error'] = '404';
		header('Location: '.HTTP_SERVER.'/404.php');
	}
	
} elseif(!empty($_GET['error'] )) {
	header('Location: '.HTTP_SERVER.'/404.php');
}
else 
	include('index.php');

?>