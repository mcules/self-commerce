<?php
class Checkout {
  function getFormUrl() {
    if (isset ($GLOBALS[$_SESSION['payment']]->form_action_url) && !$GLOBALS[$_SESSION['payment']]->tmpOrders) {
      $form_action_url = $GLOBALS[$_SESSION['payment']]->form_action_url;
    } else {
      $form_action_url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, 'ajax_checkout=1', 'SSL');
    }

    return $form_action_url;
  }


  function getAGB() {
    if (DISPLAY_CONDITIONS_ON_CHECKOUT != 'true') {
      return false;
    }

    if (GROUP_CHECK == 'true') {
      $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
    }
    $shop_content_query = xtc_db_query("SELECT
                                          content_title,
                                          content_heading,
                                          content_text,
                                          content_file
                                        FROM " . TABLE_CONTENT_MANAGER . "
                                        WHERE content_group='3' " . $group_check . "
                                        AND languages_id='" . $_SESSION['languages_id'] . "'");
    $shop_content_data = xtc_db_fetch_array($shop_content_query);
    if ($shop_content_data['content_file'] != '') {
      $fp = @fopen(HTTP_SERVER.DIR_WS_CATALOG . 'media/content/' . $shop_content_data['content_file'], 'r');
      $conditions = @fread($fp, 25000);
      @fclose($fp);
    } else {
      $conditions = $shop_content_data['content_text'];
    }
    return trim(strip_tags(str_replace('<br />', "\n", $conditions)));
  }


  function getRevocation() {
    if (DISPLAY_REVOCATION_ON_CHECKOUT != 'true') {
      return false;
    }

    if (GROUP_CHECK == 'true') {
      $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
    }
    $shop_content_query = "SELECT
                              content_title,
                              content_heading,
                              content_text,
                              content_file
                            FROM " . TABLE_CONTENT_MANAGER . "
                            WHERE content_group='" . REVOCATION_ID . "' " . $group_check . "
                            AND languages_id='" . $_SESSION['languages_id'] . "'";

    $shop_content_query = xtc_db_query($shop_content_query);
    $shop_content_data = xtc_db_fetch_array($shop_content_query);

    if ($shop_content_data['content_file'] != '') {
      ob_start();
      if (strpos($shop_content_data['content_file'], '.txt'))
      include (DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
      if (strpos($shop_content_data['content_file'], '.txt'))
      $revocation = ob_get_contents();
      ob_end_clean();
    } else {
      $revocation = $shop_content_data['content_text'];
    }
    return trim(strip_tags(str_replace('<br />', "\n", $revocation)));
  }


  function getProducts() {
    global $order;
    global $xtPrice;

    $data_products = '<ul>';
    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
      $products_id = $order->products[$i]['id'];
      $products_name = $order->products[$i]['name'];
      $products_price = $xtPrice->xtcFormat($order->products[$i]['price'], true);
      $products_formatted_price = $xtPrice->xtcFormat($order->products[$i]['final_price'], true);
      $products_amount = $order->products[$i]['qty'];
      $products_shipping_time = $order->products[$i]['shipping_time'];
      $products_attributes = $order->products[$i]['attributes'];
      $products_tax_info = $order->products[$i]['tax'];

      $data_products .= '<li id="ajax-checkout-product-'.$products_id.'">';

      if (CHECKOUT_AJAX_PRODUCTS == 'true') {
        $html_update_qty = ' <a href="#decrease" class="products-decrease" rel="'.$products_id.'"></a>
                             <a href="#increase" class="products-increase" rel="'.$products_id.'"></a>
                             <a href="#remove" class="products-remove" rel="'.$products_id.'"></a>';
      } else {
        $html_update_qty = '';
      }
	  $Products_Link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_id);
      $data_products .= '<strong class="products-qty">'.$products_amount.'</strong> x <strong class="products-name"><a href="'.$Products_Link.'">'.$products_name.'</a></strong>'.$html_update_qty.' <strong class="products-price">'.$products_formatted_price.'</strong>';
      $data_products .= '<div class="products-description"><small>' . (!empty($order->products[$i]['short_description'])?trim(strip_tags($order->products[$i]['short_description'])):trim(substr(strip_tags($order->products[$i]['description']),0,300))). ' [...]' . '</small></br></br></div>';
      $data_products .= '<div class="products-thumb"><a href="'.$Products_Link.'"><img style="max-width:80px" src="'.DIR_WS_THUMBNAIL_IMAGES.xtc_get_products_image($order->products[$i]['id']).'" /></a></div><br/>';

      if (ACTIVATE_SHIPPING_STATUS == 'true') {
        $data_products .= '<div class="products-shipping-time">'.SHIPPING_TIME.$products_shipping_time.'</div>';


      }

      $html_attr = '';
      if (isset($products_attributes) && sizeof($products_attributes) > 0) {
        for ($j=0, $m=sizeof($products_attributes); $j<$m; $j++) {
          $products_option_name = $order->products[$i]['attributes'][$j]['option'];
          $products_option_id = $order->products[$i]['attributes'][$j]['option_id'];
          $products_option_value = $order->products[$i]['attributes'][$j]['value'];

          if (CHECKOUT_AJAX_PRODUCTS == 'true') {
            $html_attr .= '<li>'.$products_option_name.': <span class="products-attr-dropdown">'.$this->getAttributesDropdown($products_id, $products_option_id, $products_option_value).'</span></li>';
          } else {
            $html_attr .= '<li>'.$products_option_name.': <span class="products-attr-value">'.$products_option_value.'</span></li>';
          }
        }
      }

      $data_products .= $html_attr ? '<ul class="products-attributes">'.$html_attr.'</ul>' : '';

      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 &&
          $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1 &&
          sizeof($order->info['tax_groups']) > 1) {

        $data_products .= '<div class="products-tax-info">'.str_replace('%s', xtc_display_tax_value($products_tax_info.' %'), TAX_INFO_ADD).'</div>';
      }

      $data_products .= '</li>';
    }

    $data_products .= '</ul>';


    return $data_products;
  }


  function getAttributesDropdown($products_id, $products_option_id, $products_option_value) {
    global $xtPrice;
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '1') {
      $tax_query = xtc_db_query("SELECT products_tax_class_id FROM ".TABLE_PRODUCTS." WHERE products_id = '".$products_id."'");
      $tax_value = xtc_db_fetch_array($tax_query);
    }

    $dropdown = '<select size="1">';
    $query1 = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".$products_id."' AND options_id = '".$products_option_id."'");
    if (xtc_db_num_rows($query1) <= 1) {
      // Only one attribute
      $query1_array = xtc_db_fetch_array($query1);
      $query2 = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE language_id = '".$_SESSION['languages_id']."' AND products_options_values_id = '".$query1_array['options_values_id']."'");
      $query2_array = xtc_db_fetch_array($query2);
      return $query2_array['products_options_values_name'];
    } else {
      // More than one attribute
      while ($query1_array = xtc_db_fetch_array($query1)) {
        $query2 = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE language_id = '".$_SESSION['languages_id']."' AND products_options_values_id = '".$query1_array['options_values_id']."'");
        $query2_array = xtc_db_fetch_array($query2);

        // Check if selected
        if (html_entity_decode($query2_array['products_options_values_name']) == html_entity_decode($products_option_value)) {
          $html_selected = ' selected';
        } else {
          $html_selected = '';
        }

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '0' ||
            $query1_array['options_values_price'] == 0) {
          $tax_new_display = $xtPrice->xtcFormat($query1_array['options_values_price'], true);
        } else {
          $tax_new = ($xtPrice->xtcGetPrice($products_id, $format = true, 1, $tax_value['products_tax_class_id'], $query1_array['options_values_price'], 1));
          $tax_new_display = $xtPrice->xtcFormat($query1_array['options_values_price'], true, $tax_value['products_tax_class_id'], true);
        }

        $dropdown .= '<option value="'.$products_id.'|'.$products_option_id.'|'.$query2_array['products_options_values_id'].'"'.$html_selected.'>'.$query2_array['products_options_values_name'].' '.$query1_array['price_prefix'].' '.$tax_new_display.'</option>';
      }
    }
    $dropdown .= '</select>';
    return $dropdown;
  }

  function getBoxConfigs() {
    $configs = array();
    $query = xtc_db_query("SELECT configuration_value, configuration_key FROM ".TABLE_CONFIGURATION." WHERE configuration_key LIKE 'CHECKOUT_SHOW_%'");
    while ($row = xtc_db_fetch_array($query)) {
      $key = strtolower(str_replace('CHECKOUT_SHOW_', '', $row['configuration_key']));
      $configs[$key] = $row['configuration_value'] == 'true' ? true : false;
    }
    return $configs;
  }

	function getAddresses($type) {
		if ($type == 'shipping') {
			$sess = 'sendto';
		} elseif ($type == 'payment') {
			$sess = 'billto';
		}
		$dropdown = '<select size="1" class="ajax-checkout-address-dropdown">';
		$addresses_query = xtc_db_query("SELECT address_book_id, entry_firstname AS firstname, entry_lastname AS lastname, entry_company AS company, entry_street_address AS street_address, entry_suburb AS suburb, entry_city AS city, entry_postcode AS postcode, entry_state AS state, entry_zone_id AS zone_id, entry_country_id AS country_id FROM ".TABLE_ADDRESS_BOOK." WHERE customers_id = '".$_SESSION['customer_id']."'");
		while ($addresses = xtc_db_fetch_array($addresses_query)) {
			$format_id = xtc_get_address_format_id($address['country_id']);
			$selected = '';
			if ($addresses['address_book_id'] == $_SESSION[$sess]) {
				$selected = ' selected';
			}
			$short_address = xtc_address_format($format_id, $addresses, true, ' ', ', ');
			$strpos = strpos($short_address, ',', 25);
			if ($strpos != false) {
				$short_address = substr($short_address, 0, $strpos+1).' ...';
			}
			$dropdown .= '<option value="'.$addresses['address_book_id'].'"'.$selected.'>'.$short_address.'...</option>';
		
		}
		$dropdown .= '</select>';
		
		return $dropdown;
	}

  function isNewAddressPossible() {
    $addresses_query = xtc_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".$_SESSION['customer_id']."'");
    $count = xtc_db_num_rows($addresses_query);
    return $count < MAX_ADDRESS_BOOK_ENTRIES;
  }

  function isVirtual() {
    global $order;
    if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0)) {
      unset($_SESSION['shipping']);
      return true;
    }
    return false;
  }

  function isFreeShipping() {
    global $order;
    global $xtPrice;
    if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
      switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
        case 'national' :
          if ($order->delivery['country_id'] == STORE_COUNTRY)
            $pass = true;
          break;
        case 'international' :
          if ($order->delivery['country_id'] != STORE_COUNTRY)
            $pass = true;
          break;
        case 'both' :
          $pass = true;
          break;
        default :
          $pass = false;
          break;
      }

      $free_shipping = false;
      if (($pass == true) && ($order->info['total'] - $order->info['shipping_cost'] >= $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
        $free_shipping = true;

      }
    } else {
      $free_shipping = false;
    }
    return $free_shipping;
  }

  function getTaxID($id) {
    $cart_products = $_SESSION['cart']->get_products();
    for ($i=0; $i<count($cart_products); $i++) {
      if ($cart_products[$i]['id'] == $id) {
        $tax_class_id = $cart_products[$i]['tax_class_id'];
        break;
      }
    }
    return $tax_class_id;
  }

  function getShippingBlock() {
    global $xtPrice;
    global $shipping_modules;
    global $order;
    global $shipping_compatible;
    global $checkout;

    if (!is_object($shipping_modules)) {

      $shipping_modules = new shipping;
      $free_shipping = $this->isFreeShipping();
      if ($free_shipping) {
        include (DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/order_total/ot_shipping.php');
      }
    }
    $quotes = $shipping_modules->quote();
    $free_shipping = $checkout->isFreeShipping();
    if ($free_shipping) {
      $_SESSION['shipping'] = array('id' => 'free_free', 'cost' => 0, 'title' => FREE_SHIPPING_TITLE);
    }
    // SHIPPING STUFF

    $module_smarty = new Smarty;
    if (xtc_count_shipping_modules() > 0) {
      $showtax = $_SESSION['customers_status']['customers_status_show_price_tax'];

      $radio_buttons = 0;
      #loop through installed shipping methods...
      $something_checked = false;

      for ($i = 0, $n = sizeof($quotes); $i < $n; $i ++) {
        if (!isset ($quotes[$i]['error'])) {
          for ($j = 0, $n2 = sizeof($quotes[$i]['methods']); $j < $n2; $j ++) {
            # set the radio button to be checked if it is the method chosen

            $quotes[$i]['methods'][$j]['radio_buttons'] = $radio_buttons;

            $checked = (($quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']['id'] || $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'] == $_SESSION['shipping']) ? true : false);
            if (($checked == true) || ($n == 1 && $n2 == 1)) {
              $quotes[$i]['methods'][$j]['checked'] = 1;
              $something_checked = true;
            }

            if (($n > 1) || ($n2 > 1)) {
              if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
                $quotes[$i]['tax'] = '';
              if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
                $quotes[$i]['tax'] = 0;

              $quotes[$i]['methods'][$j]['price'] = $xtPrice->xtcFormat(xtc_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true);

              $quotes[$i]['methods'][$j]['radio_field'] = xtc_draw_radio_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'], $checked).xtc_draw_hidden_field($quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'].'_num', $radio_buttons+1);
              $quotes[$i]['methods'][$j]['value_id'] = $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'];

            } else {
              if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0) {
                $quotes[$i]['tax'] = 0;
              }
              if ((isset ($quotes[$i]['methods'][$j]['title'])) && (isset ($quotes[$i]['methods'][$j]['cost']))) {
                $_SESSION['shipping'] = array ('id' => $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'], 'title' => (($this->isFreeShipping() == true) ? $quotes[$i]['methods'][$j]['title'] : $quotes[$i]['module'].' ('.$quotes[$i]['methods'][$j]['title'].')'), 'cost' => $quotes[$i]['methods'][$j]['cost']);
              }
              $quotes[$i]['methods'][$j]['value_id'] = $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'];
              $quotes[$i]['methods'][$j]['price'] = $xtPrice->xtcFormat(xtc_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax']), true, 0, true).xtc_draw_hidden_field('shipping', $quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id']).xtc_draw_hidden_field($quotes[$i]['id'].'_'.$quotes[$i]['methods'][$j]['id'].'_num', $radio_buttons+1);
            }

            $radio_buttons ++;
          }
        }
      }

      if ($something_checked == false && !empty($_SESSION['shipping']['id']) && !$free_shipping) {
        unset($_SESSION['shipping']);
        $shipping_compatible = false;

      }
      $module_smarty->assign('language', $_SESSION['language']);
      $module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
      $module_smarty->assign('module_content', $quotes);
      $module_smarty->assign('module_choose', CHECKOUT_SHIPPING_CHOOSE);
      $module_smarty->caching = 0;
      $shipping_block = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/ajax_checkout_shipping_block.html');
      return xtc_not_null(MODULE_SHIPPING_INSTALLED) ? $shipping_block : '';
    }
  }

  function getPaymentBlock() {
    global $xtPrice;
    global $payment_modules;
    global $payment_compatible;
    $module_smarty = new Smarty;

    $something_checked = false;
    $selection = $payment_modules->selection();
    $radio_buttons = 0;
    for ($i = 0, $n = sizeof($selection); $i < $n; $i++) {
      $selection[$i]['radio_buttons'] = $radio_buttons;
      if (($selection[$i]['id'] == $_SESSION['payment']) || ($n == 1)) {
        $selection[$i]['checked'] = 1;
        $something_checked = true;
      }
      if (empty($selection[$i]['module_cost']) && isset($GLOBALS[$selection[$i]['id']]->cost)) {
        $selection[$i]['module_cost'] = CHECKOUT_PAYMENT_DUE;
      }
      if (sizeof($selection) > 1) {

        $selection[$i]['selection'] = xtc_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $_SESSION['payment'])).xtc_draw_hidden_field($selection[$i]['id'].'_num', $i+1);
      } else {
        $only_one = 1;
        $selection[$i]['selection'] = xtc_draw_hidden_field('payment', $selection[$i]['id']).xtc_draw_hidden_field($selection[$i]['id'].'_num', $i+1);
        if (!is_array($selection[$i]['fields']) && !isset($selection[$i]['fields'])) {
          if ($_SESSION['payment'] != 'no_payment') $_SESSION['payment'] = $selection[$i]['id'];
        } else {
          // unset($_SESSION['payment']);
          // $something_checked = false;
          // $selection[$i]['checked'] = 0;
        }
      }

      $selection[$i]['value_id'] = $selection[$i]['id'];

      if (!isset($selection[$i]['error'])) {
        $radio_buttons++;
      }
    }

    if ($something_checked == false && !empty($_SESSION['payment'])) {
      unset($_SESSION['payment']);
      unset($_SESSION['checkout_payment_form_data']);
      $payment_compatible = false;
    }

    $module_smarty->assign('language', $_SESSION['language']);
    $module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
    $module_smarty->assign('module_content', $selection);
    $module_smarty->assign('module_choose', CHECKOUT_PAYMENT_CHOOSE);
    $module_smarty->assign('only_one', $only_one);
    $module_smarty->caching = 0;
    $payment_block = $module_smarty->fetch(CURRENT_TEMPLATE . '/module/ajax_checkout_payment_block.html');
    return xtc_not_null(MODULE_PAYMENT_INSTALLED) ? $payment_block : '';
  }

  function getTotalBlock() {
    global $order;
    global $order_total_modules;
    global $xtPrice;
    $total_block = '<table>';
    if (MODULE_ORDER_TOTAL_INSTALLED) {
      $total_block .= $order_total_modules->output();
    }
    $total_block .= '</table>';
    return $total_block;
  }


  function getIp() {
    if (SHOW_IP_LOG == 'true') {
      if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
        $customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
      } else {
        $customers_ip = $_SERVER["REMOTE_ADDR"];
      }
      return $customers_ip;
    } else {
      return '';
    }
  }

  function getSorting() {
    $box_order_query = xtc_db_query("SELECT configuration_value FROM configuration WHERE configuration_key='CHECKOUT_BOX_ORDER' LIMIT 1");
    while ($row = xtc_db_fetch_array($box_order_query)) {
      $sorting = explode('|', $row['configuration_value']);
    }
    return $sorting;
  }

  function getProcessButton() {
    global $_POST;
    $old_post = $_POST;
    $_POST = $_SESSION['checkout_payment_form_data'];
    if (!empty($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') {
      $p = $GLOBALS[$_SESSION['payment']];
      if (is_object($p) && method_exists($p, 'update_status')) {
        $p->update_status();
        $p->pre_confirmation_check();
        $button = $p->process_button();
      }
    }

    $_POST = $old_post;
    return $button ? $button : '';
  }
}
?>