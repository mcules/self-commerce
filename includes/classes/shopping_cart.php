<?php
/* -----------------------------------------------------------------------------------------
   $Id: shopping_cart.php 77 2012-12-12 22:04:43Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shopping_cart.php,v 1.32 2003/02/11); www.oscommerce.com
   (c) 2003	 nextcommerce (shopping_cart.php,v 1.21 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC.'xtc_create_random_value.inc.php');
require_once(DIR_FS_INC.'xtc_get_prid.inc.php');
require_once(DIR_FS_INC.'xtc_draw_form.inc.php');
require_once(DIR_FS_INC.'xtc_draw_input_field.inc.php');
require_once(DIR_FS_INC.'xtc_image_submit.inc.php');
require_once(DIR_FS_INC.'xtc_get_tax_description.inc.php');

class shoppingCart {
    var $contents, $total, $weight, $cartID, $content_type;

    /**
     * class constructor
     */
    //function shoppingCart() {
    function __construct() {
        $this->reset();
    }

    /**
     * restore_contents
     *
     * @return unknown
     */
    function restore_contents() {
        if(!isset ($_SESSION['customer_id'])) {
            return false;
        }

        // insert current cart contents in database
        if(is_array($this->contents)) {
            reset($this->contents);
            while(list($products_id,) = each($this->contents)) {
                $qty = $this->contents[$products_id]['qty'];
                $Sql = "SELECT products_id
                		FROM ".TABLE_CUSTOMERS_BASKET."
                		WHERE customers_id = '".(int)$_SESSION['customer_id']."'
                			AND products_id = '".xtc_db_input($products_id)."'";
                $product_query = xtc_db_query($Sql);
                if(!xtc_db_num_rows($product_query)) {
                	$Sql = "INSERT INTO ".TABLE_CUSTOMERS_BASKET." (
                				customers_id,
                				products_id,
                				customers_basket_quantity,
                				customers_basket_date_added
                			)
                			VALUES (
                				'".(int)$_SESSION['customer_id']."',
                				'".xtc_db_input($products_id)."',
                				'".xtc_db_input($qty)."',
                				'".date('Ymd')."'
                			)";
                    xtc_db_query($Sql);
                    if(isset ($this->contents[$products_id]['attributes'])) {
                        reset($this->contents[$products_id]['attributes']);
                        while(list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                        	$Sql = "INSERT INTO ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." (
                        				customers_id,
                        				products_id,
                        				products_options_id,
                        				products_options_value_id
                        			)
                        			VALUES (
                        				'".(int)$_SESSION['customer_id']."',
                        				'".xtc_db_input($products_id)."',
                        				'".(int)$option."', '".(int)$value."'
                        			)";
                            xtc_db_query($Sql);
                        }
                    }
                } else {
                	$Sql = "UPDATE ".TABLE_CUSTOMERS_BASKET."
                			SET customers_basket_quantity = '".xtc_db_input($qty)."'
                			WHERE customers_id = '".(int)$_SESSION['customer_id']."'
                				AND products_id = '".xtc_db_input($products_id)."'";
                    xtc_db_query($Sql);
                }
            }
        }

        // reset per-session cart contents, but not the database contents
        $this->reset(false);

        $Sql = "SELECT products_id, customers_basket_quantity
        		FROM ".TABLE_CUSTOMERS_BASKET."
        		WHERE customers_id = '".(int)$_SESSION['customer_id']."'
        		ORDER BY customers_basket_id";
        $products_query = xtc_db_query($Sql);

        while($products = xtc_db_fetch_array($products_query)) {
            $qty = (int)$products['customers_basket_quantity'];
            $this->contents[$products['products_id']] = array	(
																	'qty' => $qty
																);

            // attributes
            $Sql = "SELECT products_options_id, products_options_value_id
            		FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES."
            		WHERE customers_id = '".(int)$_SESSION['customer_id']."'
            			AND products_id = '".xtc_db_input($products['products_id'])."'
            		ORDER BY customers_basket_attributes_id";
            $attributes_query = xtc_db_query($Sql);
            while($attributes = xtc_db_fetch_array($attributes_query)) {
                $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
            }
        }

        $this->cleanup();
    }

    /**
     * reset
     *
     * @param boolean $reset_database
     */
    function reset($reset_database = false) {

        $this->contents		= array();
        $this->total		= 0;
        $this->tax			= 0;
        $this->weight		= 0;
        $this->content_type	= false;

        if (isset ($_SESSION['customer_id']) && ($reset_database == true)) {
            xtc_db_query("	DELETE FROM ".TABLE_CUSTOMERS_BASKET."
							WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
            xtc_db_query("	DELETE FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES."
							WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
        }

        unset ($this->cartID);
        if (isset ($_SESSION['cartID'])){
            unset ($_SESSION['cartID']);
        }
    }
    /**
     * add_cart
     *
     * @param integer $products_id
     * @param integer $qty
     * @param string $attributes
     * @param boolean $notify
     */
    function add_cart	(
							$products_id,
							$qty = 1,
							$attributes = '',
							$notify = true) {
        global $new_products_id_in_cart;

        $products_id = xtc_get_uprid($products_id, $attributes);
        if ($notify == true) {
            $_SESSION['new_products_id_in_cart'] = $products_id;
        }

        if ($this->in_cart($products_id)) {
            $this->update_quantity($products_id, $qty, $attributes);
        } else {
            $this->contents[$products_id] = array ('qty' => (int)$qty);
            // insert into database
            if (isset ($_SESSION['customer_id'])){
                xtc_db_query("	INSERT INTO ".TABLE_CUSTOMERS_BASKET." (
									customers_id,
									products_id,
									customers_basket_quantity,
									customers_basket_date_added
								)
								VALUES (
									'".(int)$_SESSION['customer_id']."',
									'".xtc_db_input($products_id)."',
									'".(int)$qty."',
									'".date('Ymd')."'
								)");
            }

            if(is_array($attributes)) {
                reset($attributes);
                while (list ($option, $value) = each($attributes)) {
                    $this->contents[$products_id]['attributes'][$option] = $value;
                    // insert into database
                    if (isset ($_SESSION['customer_id'])){
                        xtc_db_query("	INSERT INTO ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." (
											customers_id,
											products_id,
											products_options_id,
											products_options_value_id
										)
										VALUES (
											'".$_SESSION['customer_id']."',
											'".xtc_db_input($products_id)."',
											'".(int)$option."', '".(int)$value."'
										)");
                    }
                }
            }
        }
        $this->cleanup();

        // assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
        $this->cartID = $this->generate_cart_id();
    }

	function modify_attributes(
								$products_id,
								$qty = '1',
								$attributes = '',
								$notify = true
							) {
		$contemp = array();
		$new_key = '';
		while (list ($key, $value) = each($this->contents)) {
			if ($key === $products_id) {
				$temp = preg_split("(\{|\})", $products_id);
				$new_key = $temp[0];
				for ($i = 1; $i < sizeof($temp); $i=$i+2) {
					if ($attributes[$temp[$i+1]]) {
						$new_key .= "{" . $temp[$i] . "}" . $attributes[$temp[$i+1]];
						$changed_key = $temp[$i];
						$changed_value = $attributes[$temp[$i+1]];
					} else {
						$new_key .= "{" . $temp[$i] . "}" . $temp[$i+1];
					}
				}
				if ($contemp[$new_key]['qty']) {
					$contemp[$new_key]['qty'] += $value['qty'];
				} else {
					$contemp[$new_key] = $value;
					$contemp[$new_key]['attributes'][$changed_key] = $changed_value;
				}
			} else {
				if ($key != $new_key) {
					$contemp[$key] = $value;
				} else {
					$contemp[$key]['qty'] += $value['qty'];
				}
			}
		}
		$this->contents = $contemp;
	}

    /**
     * update_quantity
     *
     * @param integer $products_id
     * @param integer $quantity
     * @param unknown_type $attributes
     * @return unknown
     */
    function update_quantity(
								$products_id,
								$quantity = '',
								$attributes = ''
							) {

        // nothing needs to be updated if theres no quantity, so we return true
        if (empty ($quantity)){
            return true; // nothing needs to be updated if theres no quantity, so we return true..
        }
        // xs:booster start
        $pid = strpos($products_id, "{") > 0 ? substr($products_id, 0, strpos($products_id, "{")) : $products_id;
        if(is_array($_SESSION['xtb0']['tx'])) {
            $sum = 0;
			$cc = true;
            foreach($_SESSION['xtb0']['tx'] AS $tx) {
                if($tx['products_id'] == $pid) {
                    $sum += $tx['XTB_QUANTITYPURCHASED'];
                    if($tx['XTB_ALLOW_USER_CHQTY'] == 'false') {
						$cc = false;
					}
                }
            }
            if($quantity != $sum && $cc == false) {
				$quantity = $sum;
			}
        }
        // xs:booster end

        $this->contents[$products_id] = array	(
													'qty' => (int)$quantity);
        // update database
        if(isset($_SESSION['customer_id'])) {
            xtc_db_query("	UPDATE ".TABLE_CUSTOMERS_BASKET."
							SET customers_basket_quantity = '".(int)$quantity."'
							WHERE customers_id = '".(int)$_SESSION['customer_id']."'
								AND products_id = '".xtc_db_input($products_id)."'");
        }

        if(is_array($attributes)) {
            reset($attributes);
            while(list($option, $value) = each($attributes)) {
                $this->contents[$products_id]['attributes'][$option] = $value;
                // update database
                if(isset($_SESSION['customer_id'])){
                    xtc_db_query("	UPDATE ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES."
									SET products_options_value_id = '".(int)$value."'
									WHERE customers_id = '".(int)$_SESSION['customer_id']."'
										AND products_id = '".xtc_db_input($products_id)."'
										AND products_options_id = '".(int)$option."'");
                }
            }
        }
    }

    /**
     * cleanup
     *
     */
    function cleanup() {
        reset($this->contents);
        while(list($key,) = each($this->contents)) {
            if(isset($this->contents[$key]['qty']) && $this->contents[$key]['qty'] < 1) {
                unset($this->contents[$key]);
                // remove from database
                if(isset($_SESSION['customer_id'])) {
                    xtc_db_query("	DELETE FROM ".TABLE_CUSTOMERS_BASKET."
									WHERE customers_id = '".(int)$_SESSION['customer_id']."'
										AND products_id = '".xtc_db_input($key)."'");
                    xtc_db_query("	DELETE FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES."
									WHERE customers_id = '".(int)$_SESSION['customer_id']."'
										AND products_id = '".xtc_db_input($key)."'");
                }
            }
        }
    }

    /**
     * get total number of items in cart
     *
     * @return integer total items
     */
    function count_contents() {
        $total_items = 0;

        if (is_array($this->contents)) {
            reset($this->contents);
            while (list ($products_id,) = each($this->contents)) {
                $total_items += $this->get_quantity($products_id);
            }
        }
        return $total_items;
    }

    /**
     * get_quantity
     *
     * @param integer $products_id
     * @return integer quantity
     */
    function get_quantity($products_id) {
        if (isset ($this->contents[$products_id]['qty'])) {
            return $this->contents[$products_id]['qty'];
        } else {
            return 0;
        }
    }

    /**
     * check if product is in cart
     *
     * @param integer $products_id
     * @return boolean
     */
    function in_cart($products_id) {
        if (isset ($this->contents[$products_id])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * remove a product from cart
     *
     * @param integer $products_id
     */
    function remove($products_id) {
        unset($this->contents[$products_id]);

        // remove from database
        if (isset($_SESSION['customer_id'])) {
            xtc_db_query("	DELETE FROM ".TABLE_CUSTOMERS_BASKET."
							WHERE customers_id = '".(int)$_SESSION['customer_id']."'
								AND products_id = '".xtc_db_input($products_id)."'");
            xtc_db_query("	DELETE FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES."
							WHERE customers_id = '".(int)$_SESSION['customer_id']."'
								AND products_id = '".xtc_db_input($products_id)."'");
        }

        // assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
        $this->cartID = $this->generate_cart_id();
    }

    /**
     * alias for reset
     *
     */
    function remove_all() {
        $this->reset();
    }

    /**
     * get a comma seperated list of ids of all products in cart
     *
     * @return string
     */
    function get_product_id_list() {
        $product_id_list = '';
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list ($products_id,) = each($this->contents)) {
                $product_id_list .= ', '.$products_id;
            }
        }

        return substr($product_id_list, 2);
    }

    /**
     * calculate cart totals
     *
     * @return unknown
     */
    function calculate() {
        global $xtPrice;
        $this->total		= 0;
        $this->weight		= 0;
        $this->tax			= array();
        $this->tax_discount	= array();

        if (!is_array($this->contents)) {
            return 0;
        }
        reset($this->contents);
        while (list ($products_id) = each($this->contents)) {
            $qty = $this->contents[$products_id]['qty'];

            // products price
            $Sql = "SELECT
            			products_id,
            			products_price,
            			products_discount_allowed,
            			products_tax_class_id,
            			products_weight,
            			products_model,
            			products_setup
            		FROM ".TABLE_PRODUCTS."
            		WHERE products_id='".xtc_get_prid($products_id)."'";
            $product_query = xtc_db_query($Sql);
            if ($product = xtc_db_fetch_array($product_query)) {
				$products_price = $xtPrice->xtcGetPrice($product['products_id'], $format = false, $qty, $product['products_tax_class_id'], $product['products_price']);
                $this->total += $products_price * $qty;
                $this->weight += ($qty * $product['products_weight']);

                // attributes price
                $attribute_price = 0;
                if (isset ($this->contents[$products_id]['attributes'])) {
                    reset($this->contents[$products_id]['attributes']);
                    while (list ($option, $value) = each($this->contents[$products_id]['attributes'])) {

                        $values	= $xtPrice->xtcGetOptionPrice(
									$product['products_id'],
									$option,
									$value
								);
                        $this->weight += $values['weight'] * $qty;
                        $this->total += $values['price'] * $qty;
                        $attribute_price += $values['price'];
                    }
                }

                // $this->total hat netto * Stück in der 1. Runde
                // Artikel Rabatt berücksichtigt Gesamt Rabatt auf Bestellung nicht nur weiterrechnen, falls Produkt nicht ohne Steuer
                // $this->total + $this->tax wird berechnet
                if ($product['products_tax_class_id'] != 0) {
					if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
						if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
							// Rabatt für die Steuerberechnung
							// der eigentliche Rabatt wird im order-details_cart abgezogen
							$products_price_tax = $products_price - ($products_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
							$attribute_price_tax = $attribute_price - ($attribute_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
						}
					}

                    $products_tax = $xtPrice->TAX[$product['products_tax_class_id']];
                    $products_tax_description = xtc_get_tax_description($product['products_tax_class_id']);

                    // price incl tax
                    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '1') {
                        if(!isset($this->tax[$product['products_tax_class_id']])){ $this->tax[$product['products_tax_class_id']]['value'] = 0; }
                        if($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
                            $this->tax[$product['products_tax_class_id']]['value'] += ((($products_price_tax + $attribute_price_tax) / (100 + $products_tax)) * $products_tax) * $qty;
                        } else {
                            $this->tax[$product['products_tax_class_id']]['value'] += ((($products_price + $attribute_price) / (100 + $products_tax)) * $products_tax) * $qty;
                        }
                        $this->tax[$product['products_tax_class_id']]['desc'] = TAX_ADD_TAX.$products_tax_description.TAX_SHORT_DISPLAY;
                    }
                    // excl tax + tax at checkout
                    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
                        if (!isset($this->tax[$product['products_tax_class_id']])) { $this->tax[$product['products_tax_class_id']]['value'] = 0; }
                        if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1) {
                            $this->tax[$product['products_tax_class_id']]['value'] += (($products_price_tax+$attribute_price_tax) / 100) * ($products_tax)*$qty;
                            $this->tax_discount[$product['products_tax_class_id']]+=(($products_price_tax+$attribute_price_tax) / 100) * ($products_tax)*$qty;
                        } else {
                            $this->tax[$product['products_tax_class_id']]['value'] += (($products_price+$attribute_price) / 100) * ($products_tax)*$qty;
                            $this->total += (($products_price+$attribute_price) / 100) * ($products_tax)*$qty;
                        }
                        $this->tax[$product['products_tax_class_id']]['desc'] = TAX_NO_TAX.$products_tax_description.TAX_SHORT_DISPLAY;
                    }
                }
            }
        }
        foreach ($this->tax_discount as $value) {
            $this->total += round($value, $xtPrice->get_decimal_places($order->info['currency']));
        }
    }

    /**
     * get price for a product's attribute
     *
     * @param integer $products_id
     * @return float
     */
    function attributes_price($products_id) {
        global $xtPrice;
        $attributes_price = 0;
        if (isset ($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list ($option, $value) = each($this->contents[$products_id]['attributes'])) {
                $values	= $xtPrice->xtcGetOptionPrice(
														$products_id,
														$option,
														$value);
                $attributes_price += $values['price'];
            }
        }
        return $attributes_price;
    }

    function get_products() {
        global $xtPrice,$main;
        if (!is_array($this->contents)) {
			return false;
        }

        $products_array = array ();
        reset($this->contents);
        while(list($products_id,) = each($this->contents)) {
            if($this->contents[$products_id]['qty'] != 0 || $this->contents[$products_id]['qty'] !=''){
                $products_query = xtc_db_query("SELECT p.products_id,
													pd.products_name,
													pd.products_description,
													pd.products_short_description,
													p.products_shippingtime,
													p.products_image,
													p.products_model,
													p.products_price,
													p.products_discount_allowed,
													p.products_weight,
													p.products_tax_class_id
												FROM ".TABLE_PRODUCTS." p,
													".TABLE_PRODUCTS_DESCRIPTION." pd
												WHERE p.products_id='".xtc_get_prid($products_id)."'
													AND pd.products_id = p.products_id
													AND pd.language_id = '".(int)$_SESSION['languages_id']."'");
                if ($products = xtc_db_fetch_array($products_query)) {
                    $prid = $products['products_id'];
                    $products_price = $xtPrice->xtcGetPrice($products['products_id'], $format = false, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price']);
                    $products_array[] = array	(
													'id' => $products_id,
													'name' => $products['products_name'],
													'description' => $products['products_description'],
													'short_description' => $products['products_short_description'],
													'model' => $products['products_model'],
													'image' => $products['products_image'],
													'price' => $products_price + $this->attributes_price($products_id),
													'quantity' => $this->contents[$products_id]['qty'],
													'weight' => $products['products_weight'],
													'shipping_time' => $main->getShippingStatusName($products['products_shippingtime']),
													'final_price' => ($products_price + $this->attributes_price($products_id)),
													'tax_class_id' => $products['products_tax_class_id'],
													'tax' => isset($xtPrice->TAX[$products['products_tax_class_id']]) ? $xtPrice->TAX[$products['products_tax_class_id']] : 0,
													'attributes' => isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : '');
                }
            }
        }

        return $products_array;
    }

	/**
     * show_total
     *
     * @return unknown
     */
    function show_total() {
        $this->calculate();

        return $this->total;
    }

    /**
     * show_weight
     *
     * @return unknown
     */
    function show_weight() {
        $this->calculate();

        return $this->weight;
    }

    /**
     * show_tax
     *
     * @param boolean $format
     * @return unknown
     */
    function show_tax($format = true) {
        global $xtPrice;
        $this->calculate();
        $output	= "";
        $val	= 0;
        $gval	= 0;

        foreach ($this->tax AS $key => $value) {
            if ($this->tax[$key]['value'] > 0 ) {
                $output .= $this->tax[$key]['desc'].": ".$xtPrice->xtcFormat($this->tax[$key]['value'], true)."<br />";
                $val = $this->tax[$key]['value'];
                $gval += $this->tax[$key]['value'];
            }
        }
        if ($format) {
            return $output;
        } else {
            return $gval;
        }
    }

    /**
     * generate_cart_id
     *
     * @param integer $length
     * @return unknown
     */
    function generate_cart_id($length = 5) {
        return xtc_create_random_value($length, 'digits');
    }

    /**
     * get_content_type
     *
     * @return unknown
     */
    function get_content_type() {
        $this->content_type = false;
        if ((DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0)) {
            reset($this->contents);
            while (list ($products_id,) = each($this->contents)) {
                if (isset ($this->contents[$products_id]['attributes'])) {
                    reset($this->contents[$products_id]['attributes']);
                    while (list (, $value) = each($this->contents[$products_id]['attributes'])) {
                        $virtual_check_query	= xtc_db_query("SELECT COUNT(*) AS total
																FROM ".TABLE_PRODUCTS_ATTRIBUTES." pa,
																	".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad
																WHERE pa.products_id = '".(int)$products_id."'
																	AND pa.options_values_id = '".(int)$value."'
																	AND pa.products_attributes_id = pad.products_attributes_id");
                        $virtual_check = xtc_db_fetch_array($virtual_check_query);
                        if ($virtual_check['total'] > 0) {
                            switch ($this->content_type) {
                                case 'physical' :
                                    $this->content_type = 'mixed';
                                    return $this->content_type;
                                    break;

                                default :
                                    $this->content_type = 'virtual';
                                    break;
                            }
                        } else {
                            switch ($this->content_type) {
                                case 'virtual' :
                                    $this->content_type = 'mixed';
                                    return $this->content_type;
                                    break;

                                default :
                                    $this->content_type = 'physical';
                                    break;
                            }
                        }
                    }
                } else {
                    switch ($this->content_type) {
                        case 'virtual' :
                            $this->content_type = 'mixed';
                            return $this->content_type;
                            break;

                        default :
                            $this->content_type = 'physical';
                            break;
                    }
                }
            }
        } else {
            $this->content_type = 'physical';
        }
        return $this->content_type;
    }

    /**
     * unserialize
     *
     * @param unknown_type $broken
     */
    function unserialize($broken) {
        for (reset($broken); $kv = each($broken);) {
            $key = $kv['key'];
            if (gettype($this-> $key) != 'user function'){
                $this-> $key = $kv['value'];
            }
        }
    }

    /**
     * get total number of items in cart disregard gift vouchers
     *
     * amend count_contents to show nil contents for shipping
     * as we don't want to quote for 'virtual' item
     * GLOBAL CONSTANTS if NO_COUNT_ZERO_WEIGHT is true then we don't count any product with a weight
     * which is less than or equal to MINIMUM_WEIGHT
     * otherwise we just don't count gift certificates
     *
     * @return integer
     */
    function count_contents_virtual() {
        $total_items = 0;
        if (is_array($this->contents)) {
            reset($this->contents);
            while (list ($products_id,) = each($this->contents)) {
                $no_count	= false;
                $gv_query	= xtc_db_query("SELECT products_model
											FROM ".TABLE_PRODUCTS."
											WHERE products_id = '".$products_id."'");
                $gv_result	= xtc_db_fetch_array($gv_query);
                if (preg_match('/^GIFT/', $gv_result['products_model'])) { // Hetfield - 2009-08-19 - replaced deprecated function ereg with preg_match to be ready for PHP >= 5.3
                    $no_count = true;
                }
                if (defined('NO_COUNT_ZERO_WEIGHT') && NO_COUNT_ZERO_WEIGHT == 1) {
                    $gv_query	= xtc_db_query("SELECT products_weight
												FROM ".TABLE_PRODUCTS."
												WHERE products_id = '".xtc_get_prid($products_id)."'");
                    $gv_result	= xtc_db_fetch_array($gv_query);

                    if ($gv_result['products_weight'] <= MINIMUM_WEIGHT) {
                        $no_count = true;
                    }
                }
                if (!$no_count){
                    $total_items += $this->get_quantity($products_id);
                }
            }
        }
        return $total_items;
    }
}
?>