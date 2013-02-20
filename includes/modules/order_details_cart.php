<?php

/* -----------------------------------------------------------------------------------------
   $Id: order_details_cart.php 63 2012-10-20 17:29:32Z McUles $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(order_details.php,v 1.8 2003/05/03); www.oscommerce.com
   (c) 2003	 nextcommerce (order_details.php,v 1.16 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

// include needed functions
require_once DIR_FS_INC.'xtc_check_stock.inc.php';
require_once DIR_FS_INC.'xtc_get_products_stock.inc.php';
require_once DIR_FS_INC.'xtc_remove_non_numeric.inc.php';
require_once DIR_FS_INC.'xtc_get_short_description.inc.php';
require_once DIR_FS_INC.'xtc_format_price.inc.php';
require_once DIR_FS_INC.'xtc_get_attributes_model.inc.php';

$module_content = array ();
$any_out_of_stock = '';
$mark_stock = '';

for ($i = 0, $n = sizeof($products); $i < $n; $i ++)
{
	if (STOCK_CHECK == 'true')
	{
		$mark_stock = xtc_check_stock($products[$i]['id'], $products[$i]['quantity']);
		if ($mark_stock)
		{
			$_SESSION['any_out_of_stock'] = 1;
		}
	}

	$image = '';
	if ($products[$i]['image'] != '')
	{
		$image = DIR_WS_THUMBNAIL_IMAGES.$products[$i]['image'];
	}

	$module_content[$i] = array (
		'PRODUCTS_NAME' => $products[$i]['name'].$mark_stock,
		'PRODUCTS_QTY'  =>  xtc_draw_input_field('cart_quantity[]',  $products[$i]['quantity'],  'size="2"').xtc_draw_hidden_field('products_id[]',  $products[$i]['id']),
		'PRODUCTS_MODEL' => $products[$i]['model'],
		'PRODUCTS_SHIPPING_TIME'=>$products[$i]['shipping_time'],
		'PRODUCTS_TAX' => number_format($products[$i]['tax'], TAX_DECIMAL_PLACES),
		'PRODUCTS_IMAGE' => $image,
		'IMAGE_ALT' => $products[$i]['name'],
		'DELETE' => xtc_draw_checkbox_field('cart_delete[]', $products[$i]['id']),
		'PLUS' => '<input type="image" name="plus['.$i.']" src="templates/'.CURRENT_TEMPLATE.'/img/plus.gif" />',
		'MINUS' => '<input type="image" name="minus['.$i.']" src="templates/'.CURRENT_TEMPLATE.'/img/minus.gif" />',
		'BOX_DELETE' => '<input type="image" name="delete['.$i.']" src="templates/'.CURRENT_TEMPLATE.'/img/delete.gif" //>',
		'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products[$i]['id'], $products[$i]['name'])),
		'PRODUCTS_PRICE' => $xtPrice->xtcFormat($products[$i]['price'] * $products[$i]['quantity'], true),
		'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat(($products[$i]['price']), true),
		'PRODUCTS_SHORT_DESCRIPTION' => xtc_get_short_description($products[$i]['id']),
		'ATTRIBUTES' => '');

	// Product options names
	$attributes_exist = ((isset ($products[$i]['attributes']) && is_array($products[$i]['attributes'])) ? 1 : 0);

	if ($attributes_exist == 1)
	{
		reset($products[$i]['attributes']);
		while (list ($option, $value) = each($products[$i]['attributes']))
		{
			if (ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true')
			{
				$attribute_stock_check = xtc_check_stock_attributes($products[$i][$option]['products_attributes_id'], $products[$i]['quantity']);
				if ($attribute_stock_check)
				{
					$_SESSION['any_out_of_stock'] = 1;
				}
			}
			if ($_SESSION['alter'] && $_SESSION['alter_prod']==$i."-".$option)
			{
				$_SESSION['alter'] = false;
				unset($_SESSION['alter_prod']);
				$temp=preg_split("(\{|\})", $products[$i]['id']);
				$products_id=$temp[0];
				for ($j = 1; $j<sizeof($temp); $j=$j+2)
				{
					if ($option == (int) $temp[$j])
					{
						$opt = (int) $temp[$j+1];
					}
				}
				$attr_query=xtc_db_query("SELECT products_options_values_id,products_options_values_name FROM ". TABLE_PRODUCTS_OPTIONS_VALUES." pov, ".TABLE_PRODUCTS_ATTRIBUTES." pa
                                          WHERE pa.products_id = ".$products_id."
                                          AND pa.options_values_id = pov.products_options_values_id
                                          AND pov.language_id = ". $_SESSION['languages_id'] ."
                                          AND pa.options_id = " . $option . "
                                          ORDER BY pov.products_options_values_id");
				$select='<select name="attributes" onChange="this.form.submit()">';

				while ($attr_res=xtc_db_fetch_array($attr_query))
				{
					$selected=$products[$i]['attributes'][$option]==$attr_res['products_options_values_id']?' selected="selected"':'';
					$select.='<option value="'.$i.'-'.$products[$i]['attributes'][$option].'-'.$attr_res['products_options_values_id'].'-'.$opt.'"'.$selected.'>'.$attr_res['products_options_values_name'].'</option>';
				} //EOWHILE

				$select.='</select>';
				$module_content[$i]['ATTRIBUTES'][] = array(
					'ID' =>$products[$i][$option]['products_attributes_id'],
					'MODEL'=>xtc_get_attributes_model(xtc_get_prid($products[$i]['id']), $products[$i][$option]['products_options_values_name'], $products[$i][$option]['products_options_name']),
					'NAME' => $products[$i][$option]['products_options_name'],
					'VALUE_NAME' => $select, //$products[$i][$option]['products_options_values_name'].$attribute_stock_check,
					'ALTER' => '<input type="submit" name="alter" value="send" class="alter">'
				);
			} else
			{
				$module_content[$i]['ATTRIBUTES'][] = array(
					'ID' =>$products[$i][$option]['products_attributes_id'],
					'MODEL'=>xtc_get_attributes_model(xtc_get_prid($products[$i]['id']), $products[$i][$option]['products_options_values_name'], $products[$i][$option]['products_options_name']),
					'NAME' => $products[$i][$option]['products_options_name'],
					'VALUE_NAME' => $products[$i][$option]['products_options_values_name'].$attribute_stock_check,
					'ALTER' => '<input type="submit" name="alter" value="' . $i . '-' . $option . '" class="alter">'
				);
			}
		}
	}
}

$total_content = '';
$total = $_SESSION['cart']->show_total();

if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1' && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00')
{
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
	{
		$price = $total-$_SESSION['cart']->show_tax(false);
	} else
	{
		$price = $total;
	}
	$discount = $xtPrice->xtcGetDC($price, $_SESSION['customers_status']['customers_status_ot_discount']);
	$total_content = $_SESSION['customers_status']['customers_status_ot_discount'].' % '.SUB_TITLE_OT_DISCOUNT.' -'.xtc_format_price($discount, $price_special = 1, $calculate_currencies = false).'<br />';
}
if ($_SESSION['customers_status']['customers_status_show_price'] == '1')
{
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) $total-=$discount;
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) $total-=$discount;
	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) $total-=$discount;
	$total_content .= SUB_TITLE_SUB_TOTAL.$xtPrice->xtcFormat($total, true).'<br />';
} else
{
	$total_content .= TEXT_INFO_SHOW_PRICE_NO.'<br />';
}
// display only if there is an ot_discount
if ($customer_status_value['customers_status_ot_discount'] != 0)
{
	$total_content .= TEXT_CART_OT_DISCOUNT.$customer_status_value['customers_status_ot_discount'].'%';
}
if (SHOW_SHIPPING=='true')
{
	$module_smarty->assign('SHIPPING_INFO', ' '.SHIPPING_EXCL.' <a href="'.xtc_href_link(FILENAME_CONTENT, 'coID='.SHIPPING_INFOS).'">'.SHIPPING_COSTS.'</a>');
}

$module_smarty->assign('UST_CONTENT', $_SESSION['cart']->show_tax());
$module_smarty->assign('TOTAL_CONTENT', $total_content);
$module_smarty->assign('language', $_SESSION['language']);
$module_smarty->assign('module_content', $module_content);

$module_smarty->caching = 0;
$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/order_details.html');

$smarty->assign('MODULE_order_details', $module);
?>