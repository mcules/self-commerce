<?php

/* -----------------------------------------------------------------------------------------
   $Id: product.php 17 2012-06-04 20:33:29Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class product {

	/**
	 *
	 * Constructor
	 *
	 */
	function product($pID = 0) {
		$this->pID = $pID;
		$this->useStandardImage=false;
		$this->standardImage='noimage.gif';
		if ($pID = 0) {
			$this->isProduct = false;
			return;
		}
		// query for Product
		$group_check = "";
		if (GROUP_CHECK == 'true') {
			$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		}

		$fsk_lock = "";
		if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
			$fsk_lock = ' and p.products_fsk18!=1';
		}

		$product_query = "SELECT ".TABLE_PRODUCTS.".*, ".TABLE_PRODUCTS_DESCRIPTION.".*, ".TABLE_MANUFACTURERS.".manufacturers_name
							FROM ".TABLE_PRODUCTS."
							LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." ON (".TABLE_PRODUCTS_DESCRIPTION.".products_id=".TABLE_PRODUCTS.".products_id AND ".TABLE_PRODUCTS_DESCRIPTION.".language_id = '".(int) $_SESSION['languages_id']."')
							LEFT JOIN ".TABLE_MANUFACTURERS." ON (".TABLE_MANUFACTURERS.".manufacturers_id=".TABLE_PRODUCTS.".manufacturers_id)
							WHERE ".TABLE_PRODUCTS.".products_id = '".$this->pID."'
							".$group_check.$fsk_lock."
							;";

		$product_query = xtDBquery($product_query);

		if (!xtc_db_num_rows($product_query, true)) {
			$this->isProduct = false;
		} else {
			$this->isProduct = true;
			$Data_Temp = xtc_db_fetch_array($product_query, true);
			$Data_Temp['products_details'] = $this->getDetails($this->pID, (int) $_SESSION['languages_id']);
			$this->data = $Data_Temp;
		}

	}

	function getDetails($products_id, $language_id) {
		$counterDetails = 0;
		$Details_query = "SELECT products_details.products_details_name, products_details.products_details_title, products_details_values.products_details_value
							FROM products_details_values
							INNER JOIN products_details ON (products_details_values.products_details_id=products_details.products_details_id)
							WHERE products_details_values.products_id='".$products_id."'
								AND products_details_values.language_id='".$language_id."';";
		$Details_query = xtDBquery($Details_query);
		while($Row = xtc_db_fetch_array($Details_query)) {
			$Title = json_decode($Row['products_details_title']);
			$Return[$Row['products_details_name']]['Title'] = $Title->$language_id;
			$Return[$Row['products_details_name']]['Value'] = $Row['products_details_value'];
		}
		return $Return;
	}

	/**
	 *
	 *  Query for attributes count
	 *
	 */

	function getAttributesCount() {

		$products_attributes_query = xtDBquery("select count(*) as total from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".$this->pID."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."'");
		$products_attributes = xtc_db_fetch_array($products_attributes_query, true);
		return $products_attributes['total'];

	}

	/**
	 *
	 * Query for reviews count
	 *
	 */

	function getReviewsCount() {
		$reviews_query = xtDBquery("select count(*) as total from ".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd where r.products_id = '".$this->pID."' and r.reviews_id = rd.reviews_id and rd.languages_id = '".$_SESSION['languages_id']."' and rd.reviews_text !=''");
		$reviews = xtc_db_fetch_array($reviews_query, true);
		return $reviews['total'];
	}

	/**
	 * Reviews average and count
	 */
	function getReviewsAverage($products_id) {
		if($products_id > 0) {
			$reviews_query = "SELECT AVG(reviews.reviews_rating) as total, COUNT(*) as number
								FROM reviews
								WHERE reviews.products_id=$products_id;";
			$reviews_query = xtDBquery($reviews_query);
			$reviews = xtc_db_fetch_array($reviews_query);
			$reviews['round'] = (int) $reviews['total'];
			return $reviews;
		}
		else {
			return false;
		}
	}

	/**
	 *
	 * select reviews
	 *
	 */

	function getReviews() {

		$data_reviews = array ();
		$reviews_query = xtDBquery("SELECT r.reviews_rating, r.reviews_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text
									FROM ".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd
									WHERE r.products_id = '".$this->pID."'
										AND  r.reviews_id=rd.reviews_id
										AND rd.languages_id = '".$_SESSION['languages_id']."'
										ORDER BY reviews_id DESC");
		if (xtc_db_num_rows($reviews_query, true)) {
			$row = 0;
			$data_reviews = array ();
			while ($reviews = xtc_db_fetch_array($reviews_query, true)) {
				$row ++;
				$data_reviews[] = array (
									'AUTHOR' => $reviews['customers_name'],
									'DATE' => xtc_date_short($reviews['date_added']),
									'RATING' => xtc_image('templates/'.CURRENT_TEMPLATE.'/img/stars_'.$reviews['reviews_rating'].'.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])),
									'TEXT' => $reviews['reviews_text']
								);
				if ($row == PRODUCT_REVIEWS_VIEW) {
					break;
				}
			}
		}
		return $data_reviews;

	}

	/**
	 *
	 * return model if set, else return name
	 *
	 */

	function getBreadcrumbModel() {

		if ($this->data['products_model'] != "")
			return $this->data['products_model'];
		return $this->data['products_name'];

	}

	/**
	 *
	 * get also purchased products related to current
	 *
	 */

	function getAlsoPurchased() {
		global $xtPrice;

		$module_content = array ();

		$fsk_lock = "";
		if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
			$fsk_lock = ' and p.products_fsk18!=1';
		}
		$group_check = "";
		if (GROUP_CHECK == 'true') {
			$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		}

		$orders_query = "select
														                                  p.products_fsk18,
														                                  p.products_id,
														                                  p.products_price,
														                                  p.products_tax_class_id,
														                                  p.products_image,
														                                  pd.products_name,
														                                  p.products_vpe,
						                           										  p.products_vpe_status,
						                           										  p.products_vpe_value,
														                                  pd.products_short_description FROM ".TABLE_ORDERS_PRODUCTS." opa, ".TABLE_ORDERS_PRODUCTS." opb, ".TABLE_ORDERS." o, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
														                                  where opa.products_id = '".$this->pID."'
														                                  and opa.orders_id = opb.orders_id
														                                  and opb.products_id != '".$this->pID."'
														                                  and opb.products_id = p.products_id
														                                  and opb.orders_id = o.orders_id
														                                  and p.products_status = '1'
														                                  and pd.language_id = '".(int) $_SESSION['languages_id']."'
														                                  and opb.products_id = pd.products_id
														                                  ".$group_check."
														                                  ".$fsk_lock."
														                                  group by p.products_id order by o.date_purchased desc limit ".MAX_DISPLAY_ALSO_PURCHASED;
		$orders_query = xtDBquery($orders_query);
		while ($orders = xtc_db_fetch_array($orders_query, true)) {

			$module_content[] = $this->buildDataArray($orders);

		}

		return $module_content;

	}

	/**
	 *
	 *
	 *  Get Cross sells
	 *
	 *
	 */
	function getCrossSells() {
		global $xtPrice;

		$cs_groups = "SELECT products_xsell_grp_name_id FROM ".TABLE_PRODUCTS_XSELL." WHERE products_id = '".$this->pID."' GROUP BY products_xsell_grp_name_id";
		$cs_groups = xtDBquery($cs_groups);
		$cross_sell_data = array ();
		if (xtc_db_num_rows($cs_groups, true)>0) {
		while ($cross_sells = xtc_db_fetch_array($cs_groups, true)) {

			$fsk_lock = '';
			if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
				$fsk_lock = ' and p.products_fsk18!=1';
			}
			$group_check = "";
			if (GROUP_CHECK == 'true') {
				$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
			}

				$cross_query = "select p.products_fsk18,
																														 p.products_tax_class_id,
																								                                                 p.products_id,
																								                                                 p.products_image,
																								                                                 pd.products_name,
																														 						pd.products_short_description,
																								                                                 p.products_fsk18,p.products_price,p.products_vpe,
						                           																									p.products_vpe_status,
						                           																									p.products_vpe_value,
																								                                                 xp.sort_order from ".TABLE_PRODUCTS_XSELL." xp, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
																								                                            where xp.products_id = '".$this->pID."' and xp.xsell_id = p.products_id ".$fsk_lock.$group_check."
																								                                            and p.products_id = pd.products_id and xp.products_xsell_grp_name_id='".$cross_sells['products_xsell_grp_name_id']."'
																								                                            and pd.language_id = '".$_SESSION['languages_id']."'
																								                                            and p.products_status = '1'
																								                                            order by xp.sort_order asc";

			$cross_query = xtDBquery($cross_query);
			if (xtc_db_num_rows($cross_query, true) > 0)
				$cross_sell_data[$cross_sells['products_xsell_grp_name_id']] = array ('GROUP' => xtc_get_cross_sell_name($cross_sells['products_xsell_grp_name_id']), 'PRODUCTS' => array ());

			while ($xsell = xtc_db_fetch_array($cross_query, true)) {

				$cross_sell_data[$cross_sells['products_xsell_grp_name_id']]['PRODUCTS'][] = $this->buildDataArray($xsell);
			}

		}
		return $cross_sell_data;
		}
	}


	/**
	 *
	 * get reverse cross sells
	 *
	 */

	 function getReverseCrossSells() {
	 			global $xtPrice;


			$fsk_lock = '';
			if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
				$fsk_lock = ' and p.products_fsk18!=1';
			}
			$group_check = "";
			if (GROUP_CHECK == 'true') {
				$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
			}

			$cross_query = xtDBquery("SELECT p.products_fsk18, p.products_tax_class_id, p.products_id, p.products_image, pd.products_name, pd.products_short_description, p.products_fsk18,p.products_price,p.products_vpe, p.products_vpe_status, p.products_vpe_value,
																                                                 xp.sort_order from ".TABLE_PRODUCTS_XSELL." xp, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
																                                            where xp.xsell_id = '".$this->pID."' and xp.products_id = p.products_id ".$fsk_lock.$group_check."
																                                            and p.products_id = pd.products_id
																                                            and pd.language_id = '".$_SESSION['languages_id']."'
																                                            and p.products_status = '1'
																                                            order by xp.sort_order asc");


			while ($xsell = xtc_db_fetch_array($cross_query, true)) {

				$cross_sell_data[] = $this->buildDataArray($xsell);
			}


		return $cross_sell_data;



	 }


	function getGraduated() {
		global $xtPrice;

		$staffel_query = xtDBquery("SELECT quantity, personal_offer
									FROM ".TABLE_PERSONAL_OFFERS_BY.(int) $_SESSION['customers_status']['customers_status_id']."
									WHERE products_id = '".$this->pID."'
									ORDER BY quantity ASC");

		$staffel = array ();
		while ($staffel_values = xtc_db_fetch_array($staffel_query, true)) {
			$staffel[] = array ('stk' => $staffel_values['quantity'], 'price' => $staffel_values['personal_offer']);
		}
		$staffel_data = array ();
		for ($i = 0, $n = sizeof($staffel); $i < $n; $i ++) {
			if ($staffel[$i]['stk'] == 1) {
				$quantity = $staffel[$i]['stk'];
				if ($staffel[$i +1]['stk'] != '')
					$quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
			} else {
				$quantity = ' > '.$staffel[$i]['stk'];
				if ($staffel[$i +1]['stk'] != '')
					$quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
			}
			$vpe = '';
			if ($product_info['products_vpe_status'] == 1 && $product_info['products_vpe_value'] != 0.0 && $staffel[$i]['price'] > 0) {
				$vpe = $staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount;
				$vpe = $vpe * (1 / $product_info['products_vpe_value']);
				$vpe = $xtPrice->xtcFormat($vpe, true, $product_info['products_tax_class_id']).TXT_PER.xtc_get_vpe_name($product_info['products_vpe']);
			}
			$staffel_data[$i] = array ('QUANTITY' => $quantity, 'VPE' => $vpe, 'PRICE' => $xtPrice->xtcFormat($staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount, true, $this->data['products_tax_class_id']));
		}

		return $staffel_data;

	}
	/**
	 *
	 * valid flag
	 *
	 */

	function isProduct() {
		return $this->isProduct;
	}

	// beta
	function getBuyNowButton($id, $name) {
		global $PHP_SELF;
		return '<a href="'.xtc_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id='.$id.'&'.xtc_get_all_get_params(array ('action')), 'NONSSL').'">'.xtc_image_button('button_buy_now.gif', TEXT_BUY.$name.TEXT_NOW).'</a>';

	}

	function getVPEtext($product, $price) {
		global $xtPrice;

		require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');

		if (!is_array($product))
			$product = $this->data;

		if ($product['products_vpe_status'] == 1 && $product['products_vpe_value'] != 0.0 && $price > 0) {
			return $xtPrice->xtcFormat($price * (1 / $product['products_vpe_value']), true).TXT_PER.xtc_get_vpe_name($product['products_vpe']);
		}

		return;

	}

	function buildDataArray(&$array, $image='thumbnail') {
		global $xtPrice,$main;

		$tax_rate = $xtPrice->TAX[$array['products_tax_class_id']];

		$products_price = $xtPrice->xtcGetPrice($array['products_id'], $format = true, 1, $array['products_tax_class_id'], $array['products_price'], 1);

		if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
			if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
				if ($array['products_fsk18'] == '0')
					$buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);

			} else {
				$buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
			}
		}

		$shipping_status_name = $main->getShippingStatusName($array['products_shippingtime']);
		$shipping_status_image = $main->getShippingStatusImage($array['products_shippingtime']);

		$Reviews = $this->getReviewsAverage($array['products_id']);
		$Reviews['image'] = xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $Reviews['round'] . '.png' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $Reviews['round']));
		$Return_array = array ('PRODUCTS_NAME' => $array['products_name'],
				'COUNT'=>$array['ID'],
				'PRODUCTS_ID'=>$array['products_id'],
				'PRODUCTS_VPE' => $this->getVPEtext($array, $products_price['plain']),
				'PRODUCTS_IMAGE' => $this->productImage($array['products_image'], $image),
				'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])),
				'PRODUCTS_PRICE' => $products_price['formated'],
				'PRODUCTS_TAX_INFO' => $main->getTaxInfo($tax_rate),
				'PRODUCTS_SHIPPING_LINK' => $main->getShippingLink(),
				'PRODUCTS_BUTTON_DETAILS' => '<a href="'.xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])).'">'.xtc_image_button('button_details.gif', '').'</a>',
				'PRODUCTS_BUTTON_BUY_NOW' => $buy_now,
				'PRODUCTS_SHIPPING_NAME'=>$shipping_status_name,
				'PRODUCTS_SHIPPING_IMAGE'=>$shipping_status_image,
				'PRODUCTS_DESCRIPTION' => $array['products_description'],
				'PRODUCTS_EXPIRES' => $array['expires_date'],
				'PRODUCTS_CATEGORY_URL'=>$array['cat_url'],
				'PRODUCTS_SHORT_DESCRIPTION' => $array['products_short_description'],
				'PRODUCTS_FSK18' => $array['products_fsk18'],
				'PRODUCTS_DETAILS' => $this->getDetails($array['products_id'], (int) $_SESSION['languages_id']),
				'PRODUCTS_MANUFACTURER' => $array['manufacturers_name'],
				'PRODUCTS_REVIEWS' => $Reviews,
				'PRODUCTS_QUANTITY' => $array['products_quantity'],
				'PRODUCTS_BUTTON_PRODUCT_MORE' => '<a href="'.xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])).'">'.xtc_image_button('button_product_more.gif', IMAGE_BUTTON_PRODUCT_MORE).'</a>'
			);
		return $Return_array;
	}

	function buildDataArray2(&$array,$image='info') {
		global $xtPrice,$main;

			$tax_rate = $xtPrice->TAX[$array['products_tax_class_id']];

			$products_price = $xtPrice->xtcGetPrice($array['products_id'], $format = true, 1, $array['products_tax_class_id'], $array['products_price'], 1);

			if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
			if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
				if ($array['products_fsk18'] == '0')
					$buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);

			} else {
				$buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
			}
			}



			$shipping_status_name = $main->getShippingStatusName($array['products_shippingtime']);
			$shipping_status_image = $main->getShippingStatusImage($array['products_shippingtime']);


		return array ('PRODUCTS_NAME' => $array['products_name'],
				'COUNT'=>$array['ID'],
				'PRODUCTS_ID'=>$array['products_id'],
				'PRODUCTS_VPE' => $this->getVPEtext($array, $products_price['plain']),
				'PRODUCTS_IMAGE' => $this->productImage($array['products_image'], $image),
				'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])),
				'PRODUCTS_PRICE' => $products_price['formated'],
				'PRODUCTS_TAX_INFO' => $main->getTaxInfo($tax_rate),
				'PRODUCTS_SHIPPING_LINK' => $main->getShippingLink(),
				'PRODUCTS_BUTTON_DETAILS' => '<a href="'.xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])).'">'.xtc_image_button('button_details.gif', '').'</a>',
				'PRODUCTS_BUTTON_BUY_NOW' => $buy_now,
				'PRODUCTS_SHIPPING_NAME'=>$shipping_status_name,
				'PRODUCTS_SHIPPING_IMAGE'=>$shipping_status_image,
				'PRODUCTS_DESCRIPTION' => $array['products_description'],
				'PRODUCTS_EXPIRES' => $array['expires_date'],
				'PRODUCTS_CATEGORY_URL'=>$array['cat_url'],
				'PRODUCTS_SHORT_DESCRIPTION' => $array['products_short_description'],
				'PRODUCTS_FSK18' => $array['products_fsk18'],
				'PRODUCTS_DETAILS' => $this->getDetails($array['products_id'], (int) $_SESSION['languages_id']),
				'PRODUCTS_MANUFACTURER' => $array['manufacturers_name'],
				'PRODUCTS_REVIEWS' => $this->getReviewsAverage($array['products_id']),
				'PRODUCTS_QUANTITY' => $array['products_quantity'],
				'PRODUCTS_BUTTON_PRODUCT_MORE' => '<a href="'.xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name'])).'">'.xtc_image_button('button_product_more.gif', IMAGE_BUTTON_PRODUCT_MORE).'</a>'
			);
	}

	function productImage($name, $type) {

		switch ($type) {
			case 'info' :
				$path = DIR_WS_INFO_IMAGES;
				break;
			case 'thumbnail' :
				$path = DIR_WS_THUMBNAIL_IMAGES;
				break;
			case 'popup' :
				$path = DIR_WS_POPUP_IMAGES;
				break;
		}

		if ($name == '') {
			if ($this->useStandardImage == 'true' && $this->standardImage != '')
				return $path.$this->standardImage;
		} else {
			// check if image exists
			if (!file_exists($path.$name)) {
				if ($this->useStandardImage == 'true' && $this->standardImage != '')
					$name = $this->standardImage;
			}
			return $path.$name;
		}
	}

}