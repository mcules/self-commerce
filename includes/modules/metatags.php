<?php
/* -----------------------------------------------------------------------------------------
   based on: 
   (c) 2003	 nextcommerce (metatags.php,v 1.7 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

    //-- Falls die Metas schon gefüllt sind, wird hier nicht überschrieben
    (empty($meta_keyw))    ? $meta_keyw    = META_KEYWORDS:false;
    (empty($meta_descr))   ? $meta_descr   = META_DESCRIPTION:false;
    (empty($meta_title))   ? $meta_title   = TITLE:false;
    (empty($_SESSION['language_code'])) ? $_SESSION['language_code'] = 'de':false;

    if (strstr($_SERVER['SCRIPT_NAME'], FILENAME_PRODUCT_INFO))
        {
        if ($product->isProduct())
            {
            if(!empty($product->data['products_meta_keywords']))
                {
                $meta_keyw=$product->data['products_meta_keywords'];
                }
            else{
                $meta_keyw = $product->data['products_name'].', '.$product->data['products_model'].', '.$meta_keyw;
                }
            if(!empty($product->data['products_meta_description']))
                {
                $meta_descr = $product->data['products_meta_description'];
                }
            else{
                $meta_descr = $product->data['products_name'].' '.$product->data['products_model'].': '.
                                $product->data['products_description'].', '.
                                $meta_descr;
                }
            $meta_title = $product->data['products_name'].' '.$product->data['products_model'].' / '.TITLE;
            }
        }
// categorie sites        
    elseif(!empty($_GET['cPath']))
        {
        if (strpos($_GET['cPath'],'_') > 0)
            {
            $arr    = explode('_',xtc_input_validation($_GET['cPath'],'cPath',''));
            $_cPath = array_pop($arr);
            }
        else{
            $_cPath=(int)$_GET['cPath'];
            }

        $categories_meta_query=xtDBquery("SELECT categories_meta_keywords,
                                            categories_meta_description,
                                            categories_meta_title,
                                            categories_name,
                                            categories_description
                                            FROM ".TABLE_CATEGORIES_DESCRIPTION."
                                            WHERE categories_id='".$_cPath."' and
                                            language_id='".$_SESSION['languages_id']."'");
        $categories_meta = xtc_db_fetch_array($categories_meta_query,true);

        if(!empty($categories_meta['categories_meta_keywords']))
            {
            $meta_keyw = $categories_meta['categories_meta_keywords'];
            }
        else{
            $meta_keyw = $categories_meta['categories_name'].', '.$meta_keyw;
            }

        if(!empty($categories_meta['categories_meta_description']))
            {
            $meta_descr = $categories_meta['categories_meta_description'];
            }
        else{
            $meta_descr = $categories_meta['categories_name'].': '.
                                $categories_meta['categories_description'].', '.
                                $meta_descr;
            }

        if(!empty($categories_meta['categories_meta_title']))
            {
            $meta_title = $categories_meta['categories_meta_title'].' / '.TITLE;
            }
        else{
            $meta_title = $categories_meta['categories_name'].' / '.TITLE;
            }
        }
// content sites        
    elseif(!empty($_GET['coID']))
        {
        $contents_meta_query=xtDBquery("SELECT content_meta_title,
                                            content_title,
                                            content_meta_description,
                                            content_text,
                                            content_meta_keywords
                                            FROM ".TABLE_CONTENT_MANAGER."
                                            WHERE content_group ='".$_GET['coID']."' and
                                            languages_id='".$_SESSION['languages_id']."'");
        $contents_meta = xtc_db_fetch_array($contents_meta_query,true);

        if(!empty($contents_meta['content_meta_title']))
            {
            $meta_title = $contents_meta['content_meta_title'].' / '.TITLE;
            }else{
            $meta_title = $contents_meta['content_title'].' / '.TITLE;
            }
        if(!empty($contents_meta['content_meta_description'])){
            $meta_descr = $contents_meta['content_meta_description'];        
            }else{
            $meta_descr = $contents_meta['content_text'];
            }
        if(!empty($contents_meta['content_meta_keywords'])){
            $meta_keyw =  $contents_meta['content_meta_keywords'] ;
        }else{
            $meta_keyw =  $contents_meta['content_title'].', '.$meta_keyw;       
        }
  }        
?>
<meta name="robots" content="<?php echo META_ROBOTS; ?>" />
<meta name="language" content="<?php echo $_SESSION['language_code']; ?>" />
<meta name="author" content="<?php echo META_AUTHOR; ?>" />
<meta name="publisher" content="<?php echo META_PUBLISHER; ?>" />
<meta name="company" content="<?php echo META_COMPANY; ?>" />
<meta name="page-topic" content="<?php echo META_TOPIC; ?>" />
<meta name="reply-to" content="<?php echo META_REPLY_TO; ?>" />
<meta name="distribution" content="global" />
<meta name="revisit-after" content="<?php echo META_REVISIT_AFTER; ?>" />
<meta http-equiv="content-language" content="<?php echo $_SESSION['language_code']; ?>" />
<meta http-equiv="cache-control" content="no-cache" />
<meta name="keywords" content="<?PHP echo $meta_keyw;?>" />
<meta name="description" content="<?PHP echo substr(strip_tags(str_replace("\r\n", " ", $meta_descr)),0,1000);?>" />

<title><?PHP echo htmlentities($meta_title);?></title>
