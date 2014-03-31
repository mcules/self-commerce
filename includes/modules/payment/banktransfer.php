<?php
/* -----------------------------------------------------------------------------------------
   $Id: banktransfer.php 4200 2013-01-10 19:47:11Z Tomcraft1980 $
   modified eCommerce Shopsoftware
   http://www.modified-shop.org
   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(banktransfer.php,v 1.16 2003/03/02 22:01:50); www.oscommerce.com
   (c) 2003 nextcommerce (banktransfer.php,v 1.9 2003/08/24); www.nextcommerce.org
   (c) 2006 XT-Commerce (banktransfer.php 1122 2005-07-26)
   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   OSC German Banktransfer v0.85a         Autor:  Dominik Guder <osc@guder.org>
	
	Änderungen und Erweiterungen dmun
	Jan 2014:
	Erweiterung um SEPA Eingabe und Prüfung
	20140306:
	Erweiterung um zusätzliche Fehlercodes welche von der Prüf-Lib für IBAN/BIC erzeugt werden
 
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  class banktransfer {

    var $code, $title, $description, $enabled;

    function __construct() {
      global $order;

      $this->code = 'banktransfer';
      $this->title = MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_BANKTRANSFER_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER;
      $this->min_order = MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER;
      $this->enabled = ((MODULE_PAYMENT_BANKTRANSFER_STATUS == 'True') ? true : false);
      $this->info = MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO;
	  //BOF - dmun - 20140122 SEPA Creditor ID
	  $this->cid = MODULE_PAYMENT_BANKTRANSFER_CID;
	  //EOF - dmun - 20140122 SEPA
      if ((int)MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID;
      }
      if (is_object($order)) {
        $this->update_status();
      }
      if (isset($_POST['banktransfer_fax']) && $_POST['banktransfer_fax'] == "on") {
        $this->email_footer = MODULE_PAYMENT_BANKTRANSFER_TEXT_EMAIL_FOOTER;
      }
    }

    function update_status() {
      global $order;

      if( MODULE_PAYMENT_BANKTRANSFER_NEG_SHIPPING != '' ) {
        $neg_shpmod_arr = explode(',',MODULE_PAYMENT_BANKTRANSFER_NEG_SHIPPING);
        foreach( $neg_shpmod_arr as $neg_shpmod ) {
          $nd=$neg_shpmod.'_'.$neg_shpmod;
          if( $_SESSION['shipping']['id']==$nd || $_SESSION['shipping']['id']==$neg_shpmod ) {
            $this->enabled = false;
            break;
          }
        }
      }

      $check_order_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
      $order_check = xtc_db_fetch_array($check_order_query);

      if ($order_check['count'] < MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER) {
        $check_flag = false;
        $this->enabled = false;
      } else {
        $check_flag = true;
        if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_BANKTRANSFER_ZONE > 0) ) {
          $check_flag = false;
          $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_BANKTRANSFER_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
          while ($check = xtc_db_fetch_array($check_query)) {
            if ($check['zone_id'] < 1) {
              $check_flag = true;
              break;
            } elseif ($check['zone_id'] == $order->billing['zone_id']) {
              $check_flag = true;
              break;
            }
          }
        }
        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }
	
    function javascript_validation() {
      $js = 'if (payment_value == "' . $this->code . '") {' . "\n" .
            '  var banktransfer_bic = document.getElementById("checkout_payment").banktransfer_bic.value;' . "\n" .
            '  var banktransfer_iban = document.getElementById("checkout_payment").banktransfer_iban.value;' . "\n" .
            '  var banktransfer_blz = document.getElementById("checkout_payment").banktransfer_blz.value;' . "\n" .
            '  var banktransfer_number = document.getElementById("checkout_payment").banktransfer_number.value;' . "\n" .
            '  var banktransfer_owner = document.getElementById("checkout_payment").banktransfer_owner.value;' . "\n" .
            '  if (document.getElementById("checkout_payment").banktransfer_fax) { ' . "\n" .
            '    var banktransfer_fax = document.getElementById("checkout_payment").banktransfer_fax.checked;' . "\n" .
            '  } else { var banktransfer_fax = false; } ' . "\n" .            
            '  if (banktransfer_fax == false) {' . "\n" . 
            '    if (banktransfer_bic != "" || banktransfer_iban != "") {' . "\n" .
            '      if (banktransfer_bic == "") {' . "\n" .
            '        error_message = error_message + "' . JS_BANK_BIC . '";' . "\n" .
            '        error = 1;' . "\n" .
            '      }' . "\n" .
            '      if (banktransfer_iban == "") {' . "\n" .
            '        error_message = error_message + "' . JS_BANK_IBAN . '";' . "\n" .
            '        error = 1;' . "\n" .
            '      }' . "\n" .
            '    }' . "\n" .
            '    else {' . "\n" .
            '      if (banktransfer_blz == "") {' . "\n" .
            '        error_message = error_message + "' . JS_BANK_BLZ . '";' . "\n" .
            '        error = 1;' . "\n" .
            '      }' . "\n" .
            '      if (banktransfer_number == "") {' . "\n" .
            '        error_message = error_message + "' . JS_BANK_NUMBER . '";' . "\n" .
            '        error = 1;' . "\n" .
            '      }' . "\n" .
            '    }' . "\n" .          
            '    if (banktransfer_owner == "") {' . "\n" .
            '      error_message = error_message + "' . JS_BANK_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n" .
            '}' . "\n";
      return $js;
    }

    function selection() {
      global $order;

      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'description'=>$this->info,
                         'fields' => array(array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE,
                                                 'field' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_INFO),
                                           array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER,
                                                 'field' => isset($_GET['banktransfer_owner'])?xtc_draw_input_field('banktransfer_owner', $_GET['banktransfer_owner']):xtc_draw_input_field('banktransfer_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])), //DokuMan - 2012-08-29 - preset banktransfer_owner with customer only if no value was entered
                                           array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ,
                                                 'field' => xtc_draw_input_field('banktransfer_blz', (isset($_GET['banktransfer_blz'])) ? $_GET['banktransfer_blz'] : '', 'size="8" maxlength="8"')),
                                           array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER,
                                                 'field' => xtc_draw_input_field('banktransfer_number', (isset($_GET['banktransfer_number'])) ? $_GET['banktransfer_number'] : '', 'size="16" maxlength="32"')),
                                           array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME,
                                                 'field' => xtc_draw_input_field('banktransfer_bankname', (isset($_GET['banktransfer_bankname'])) ? $_GET['banktransfer_bankname'] : '')), //DokuMan - 2011-12-07 - preset banktransfer_bankname when user already entered a bank name
                                           // BOF - dmun 2014-01-21
										   // Eingabebereich zwischen Konto / BLZ und IBAN trennen sonst meinen manche Kunden noch, dass sie beide Daten
										   // angeben müssen
										   array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE_SEPA,
                                                 'field' => MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO_SEPA),
                                           // EOF - dmun 2014-01-21
										   array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_IBAN,
                                                 'field' => xtc_draw_input_field('banktransfer_iban', (isset($_GET['banktransfer_iban'])) ? $_GET['banktransfer_iban'] : '', 'size="40" maxlength="50"')), //DokuMan - 2012-11-26 - added IBAN and BIC
                                           array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BIC,
                                                 'field' => xtc_draw_input_field('banktransfer_bic', (isset($_GET['banktransfer_bic'])) ? $_GET['banktransfer_bic'] : '', 'size="40" maxlength="11"' )), //DokuMan - 2012-11-26 - added IBAN and BIC
                                           array('title' => '',
                                                 'field' => isset($_POST['recheckok']) ? xtc_draw_hidden_field('recheckok', $_POST['recheckok']) : ''),
										   // BOF - dmun 2014-01-22 SEPA
										   // Mandat erteilen, ob das rechtlich sicher ist ist nicht geklärt
										   //array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_MANDAT_TITLE,
                                           //      'field' => MODULE_PAYMENT_BANKTRANSFER_TEXT_MANDAT_INFO),
										   //array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_CID,
                                           //      'field' => MODULE_PAYMENT_BANKTRANSFER_CID),
										   // EOF - dmun 2014-01-22 SEPA
                                           ));

      if (MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION =='true'){
        $selection['fields'][] = array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE,
                                       'field' => MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE2 . '<a href="' . MODULE_PAYMENT_BANKTRANSFER_URL_NOTE . '" target="_blank"><b>' . MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE3 . '</b></a>' . MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE4);
        $selection['fields'][] = array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX,
                                       'field' => xtc_draw_checkbox_field('banktransfer_fax', 'on'));
      }
      return $selection;
    }

    function pre_confirmation_check(){
      if (@$_POST['banktransfer_fax'] == false && @$_POST['recheckok'] != 'true') {
        include(DIR_WS_CLASSES . 'banktransfer_validation.php');
        include(DIR_WS_CLASSES . 'php-iban.php');

		//Debug-Ausgabe
		//Bei Bedarf auskommentieren
		//var_dump($_POST);
			
        // check iban and bic
        if (!empty($_POST['banktransfer_iban']) || !empty($_POST['banktransfer_bic'])) {
          // bic o.k.?
          if (empty($_POST['banktransfer_bic'])) {
            $banktransfer_result = 11; // no bic
          }  
          // iban o.k.?
          else if (verify_iban($_POST['banktransfer_iban'])) {
            $banktransfer_iban_parts = iban_get_parts($_POST['banktransfer_iban']);
            // normalize iban
            $_POST['banktransfer_iban'] = iban_to_machine_format($_POST['banktransfer_iban']);
            $banktransfer_result = 0; // o.k.
          }
          else {
            $banktransfer_result = 12; // not o.k.
          }
        }

        if ($banktransfer_validation->Bankname != '') {
			$this->banktransfer_bankname =  $banktransfer_validation->Bankname;
        } else {
			$this->banktransfer_bankname = xtc_db_prepare_input($_POST['banktransfer_bankname']);
        }
        if (isset($_POST['banktransfer_owner']) && $_POST['banktransfer_owner'] == '') {
			$banktransfer_result = 10;
        }

        // dmun 2014-01-18 unterschiedliche Prüfmethoden je nachem ob IBAN oder Konto/BLZ gesetzt ist
		// Es muss die aktuelle Version der banktransfer_validation.php installiert sein
		if (!empty($_POST['banktransfer_iban']) || !empty($_POST['banktransfer_bic'])) {
			$banktransfer_validation = new IbanAccountCheck;
			$banktransfer_result = $banktransfer_validation->IbanCheckAccount(xtc_db_prepare_input($_POST['banktransfer_iban']), xtc_db_prepare_input($_POST['banktransfer_bic']));        
		}
		else if (!empty($_POST['banktransfer_number']) || !empty($_POST['banktransfer_blz'])) {
			$banktransfer_validation = new AccountCheck;
			$banktransfer_result = $banktransfer_validation->CheckAccount($_POST['banktransfer_number'], $_POST['banktransfer_blz']);
		}
		else {
			$banktransfer_result = 12;
	    }
		// Ende Modifikation IBAN

        switch ($banktransfer_result) {
          case 0: // payment o.k.
            $error = 'O.K.';
            $recheckok = 'false';
			// dmun 2014-01-21 Wenn Prüfung OK und keine IBAN angegeben wurde, dann IBAN errechnen
			// funktioniert so aber nur mit deutschen Bankkonten
			if (!empty($_POST['banktransfer_number']) || !empty($_POST['banktransfer_blz'])) {
					$make_iban = new IbanAccountCheck;
					$_POST['banktransfer_iban'] = $make_iban->make_iban($_POST['banktransfer_number'],$_POST['banktransfer_blz'], $ctry_iso2 = 'DE');
			} 
            break;
          case 1: // number & blz not ok
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1;
            $recheckok = 'false';
            break;
          case 2: // account number has no calculation method
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2;
            $recheckok = 'true';
            break;
          case 3: // No calculation method implemented
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_3;
            $recheckok = 'true';
            break;
          case 4: // Number cannot be checked
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4;
            $recheckok = 'true';
            break;
          case 5: // BLZ not found
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_5;
            $recheckok = 'false'; // Set "true" if you have not the latest BLZ table!
            break;
          case 8: // no BLZ entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_8;
            $recheckok = 'false';
            break;
          case 9: // no number entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_9;
            $recheckok = 'false';
            break;
          case 10: // no account holder entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_10;
            $recheckok = 'false';
            break;
          case 11: // no bic entered
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_11;
            $recheckok = 'false';
            break;
          case 12: // iban not o.k.
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_12;
            $recheckok = 'false';
            break;            
          case 128: // Internal error
            $error = 'Internal error, please check again to process your payment';
            $recheckok = 'false';
            break;
          case 1000: // Länderkennung ist unbekannt
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1000;
            $recheckok = 'false';
            break;
		  case 1010: // Länge der IBAN ist falsch: Zu viele Stellen
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1010;
            $recheckok = 'false';
            break;
		  case 1020: // Länge der IBAN ist falsch: Zu wenige Stellen
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1020;
            $recheckok = 'false';
            break;
		  case 1030: // IBAN entspricht nicht dem für das Land festgelegten Format
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1030;
            $recheckok = 'false';
            break;
		  case 1040: // Prüfsumme der IBAN ist nicht korrekt -> Tippfehler 
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1040;
            $recheckok = 'false';
            break;
		  case 1050: // BIC ist ungültig 
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1050;
            $recheckok = 'false';
            break;
			/* Zusätzlich werden für Deutsche Konten folgende Returncodes übergeben          */
		  case 2001: // Deutsche Kontonummer & deutsche BLZ passen nicht 
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2001;
            $recheckok = 'false';
            break;		
		  case 2002: // Kein deutsches Prüfziffernverfahren definiert
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2002;
            $recheckok = 'false';
            break;		
		  case 2003: // Prüfziffernverfahren ist noch nicht implementiert
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2003;
            $recheckok = 'false';
            break;	
		  case 2004: // Kontonummer im Detail nicht prüfbar
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2004;
            $recheckok = 'false';
            break;
		  case 2005: // Deutsche BLZ nicht gefunden
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2005;
            $recheckok = 'false';
            break;
		  case 2008: // Keine deutsche BLZ übergeben
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2008;
            $recheckok = 'false';
            break;
		  case 2009: // Keine deutsche Kontonummer übergeben
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2009;
            $recheckok = 'false';
            break;
		  case 2010: // Kein Kontoinhaber übergeben
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2010;
            $recheckok = 'false';
            break;
		  case 2128: // interner Fehler, der zeigt, dass eine Methode nicht implementiert ist
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2128;
            $recheckok = 'false';
            break;				  
		  default:
            $error = MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4;
            $recheckok = 'true';
            break;
        }

        if ($banktransfer_result > 0 && $_POST['recheckok'] != 'true') {
          $payment_error_return = 	'payment_error=' . 
									$this->code . 
									'&error=' . urlencode($error) . 
									'&banktransfer_owner=' . urlencode($_POST['banktransfer_owner']) . 
									'&banktransfer_number=' . urlencode($_POST['banktransfer_number']) . 
									'&banktransfer_blz=' . urlencode($_POST['banktransfer_blz']) . 
									'&banktransfer_bankname=' . urlencode($_POST['banktransfer_bankname']) . 
									'&banktransfer_iban=' . urlencode($_POST['banktransfer_iban']) . 
									'&banktransfer_bic=' . urlencode($_POST['banktransfer_bic']) . 
									'&recheckok=' . $recheckok;
          xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }
        $this->banktransfer_owner = xtc_db_prepare_input($_POST['banktransfer_owner']);
        $this->banktransfer_blz = xtc_db_prepare_input($_POST['banktransfer_blz']);
        $this->banktransfer_number = xtc_db_prepare_input($_POST['banktransfer_number']);
        $this->banktransfer_iban = xtc_db_prepare_input(strtoupper($_POST['banktransfer_iban'])); //DokuMan - 2012-11-26 - added IBAN and BIC
        $this->banktransfer_bic = xtc_db_prepare_input(strtoupper($_POST['banktransfer_bic'])); //DokuMan - 2012-11-26 - added IBAN and BIC
        $this->banktransfer_prz = $banktransfer_validation->PRZ;
        $this->banktransfer_status = $banktransfer_result;
      }
    }

    function confirmation() {
      global $banktransfer_val, $banktransfer_owner, $banktransfer_bankname, $banktransfer_blz, $banktransfer_number, $checkout_form_action, $checkout_form_submit;

      if (!$_POST['banktransfer_owner'] == '') {
        $confirmation = array('title' => $this->title,
                              'fields' => array(array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER.'<br>'.
                                                                  MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ.'<br>'.
                                                                  MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER.'<br>'.
                                                                  MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME.'<br>'.
                                                                  MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_IBAN.'<br>'.
                                                                  MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BIC.'<br>',
                                                       'field' => $this->banktransfer_owner.'<br>'.
                                                                  $this->banktransfer_blz.'<br>'. //dmun 2014.01.18 Feld muss BLZ und nicht BIC sein
                                                                  $this->banktransfer_number.'<br>'.
                                                                  $this->banktransfer_bankname.'<br>'.
                                                                  $this->banktransfer_iban.'<br>'. //DokuMan - 2012-11-26 - added IBAN and BIC
                                                                  $this->banktransfer_bic.'<br>') //DokuMan - 2012-11-26 - added IBAN and BIC
                                                ));
      }
      if (isset($_POST['banktransfer_fax']) && $_POST['banktransfer_fax'] == "on") {
        $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX)));
        $this->banktransfer_fax = "on";
      }
      return $confirmation;
    }

    function process_button() {
      global $_POST;

      $process_button_string = xtc_draw_hidden_field('banktransfer_blz', $this->banktransfer_blz) .
                               xtc_draw_hidden_field('banktransfer_bankname', $this->banktransfer_bankname).
                               xtc_draw_hidden_field('banktransfer_bic', $this->banktransfer_bic) .
                               xtc_draw_hidden_field('banktransfer_iban', $this->banktransfer_iban) .                               
                               xtc_draw_hidden_field('banktransfer_number', $this->banktransfer_number) .
                               xtc_draw_hidden_field('banktransfer_iban', $this->banktransfer_iban) . //DokuMan - 2012-11-26 - added IBAN and BIC
                               xtc_draw_hidden_field('banktransfer_bic', $this->banktransfer_bic) . //DokuMan - 2012-11-26 - added IBAN and BIC
                               xtc_draw_hidden_field('banktransfer_owner', $this->banktransfer_owner) .
                               xtc_draw_hidden_field('banktransfer_status', $this->banktransfer_status) .
                               xtc_draw_hidden_field('banktransfer_prz', $this->banktransfer_prz) .
                               (isset($_POST['banktransfer_fax'])? xtc_draw_hidden_field('banktransfer_fax', $this->banktransfer_fax):'');

      return $process_button_string;
    }

    function before_process() {
      //fp implement checking the post vars
      $this->pre_confirmation_check();
      $this->banktransfer_bankname = xtc_db_prepare_input($_POST['banktransfer_bankname']);
      $this->banktransfer_fax = xtc_db_prepare_input($_POST['banktransfer_fax']);
      return false;
    }

    function after_process() {
      global $insert_id, $_POST, $banktransfer_val, $banktransfer_owner, $banktransfer_bankname, $banktransfer_blz, $banktransfer_number, $banktransfer_status, $banktransfer_prz, $banktransfer_fax, $checkout_form_action, $checkout_form_submit;
      xtc_db_query("INSERT INTO ".TABLE_BANKTRANSFER."
                   ( orders_id,
                     banktransfer_owner,
                     banktransfer_number,
                     banktransfer_bankname,
                     banktransfer_blz,
                     banktransfer_status,
                     banktransfer_prz,
                     banktransfer_iban,
                     banktransfer_bic
                     )
                   VALUES ('" . $insert_id . "',
                           '" . $this->banktransfer_owner ."',
                           '" . $this->banktransfer_number . "',
                           '" . $this->banktransfer_bankname . "',
                           '" . $this->banktransfer_blz . "',
                           '" . $this->banktransfer_status ."',
                           '" . $this->banktransfer_prz ."',
                           '" . $this->banktransfer_iban . "',
                           '" . $this->banktransfer_bic . "')"
                  );
      
	  if (isset($_POST['banktransfer_fax'])) {
        xtc_db_query("UPDATE banktransfer SET banktransfer_fax = '" . $this->banktransfer_fax ."' WHERE orders_id = '" . $insert_id . "'");
      }
      if (isset($this->order_status) && $this->order_status) {
        xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
        xtc_db_query("UPDATE ".TABLE_ORDERS_STATUS_HISTORY." SET orders_status_id='".$this->order_status."' WHERE orders_id='".$insert_id."'");
      }
    }

    function get_error() {
      if (isset($_GET['error'])) {
        $error = array('title' => MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR,
                       'error' => stripslashes(urldecode($_GET['error'])));
        return $error;
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_BANKTRANSFER_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION', 'false',  '6', '2', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ', 'false', '6', '0', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_URL_NOTE', 'fax.html', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER', '0', '6', '0', now())");
	  // BOF - dmun - 20140122 Gläubiger-ID
	  xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_CID', '', '6', '0', now())");
	  // EOF - dmun - 20140122 Gläubiger-ID
      xtc_db_query("CREATE TABLE IF NOT EXISTS banktransfer (orders_id int(11) NOT NULL default '0', banktransfer_owner varchar(64) default NULL, banktransfer_number varchar(24) default NULL, banktransfer_bankname varchar(255) default NULL, banktransfer_blz varchar(8) default NULL, banktransfer_status int(11) default NULL, banktransfer_prz char(2) default NULL, banktransfer_fax char(2) default NULL, banktransfer_bic varchar(11) default NULL, banktransfer_iban varchar(50) default NULL, KEY orders_id(orders_id))");

      // BOF - Hendrik - 2010-08-09 - exlusion config for shipping modules
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_BANKTRANSFER_NEG_SHIPPING', '', '6', '99', now())");
      // EOF - Hendrik - 2010-08-09 - exlusion config for shipping modules
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_BANKTRANSFER_STATUS',
                    'MODULE_PAYMENT_BANKTRANSFER_ALLOWED',
                    'MODULE_PAYMENT_BANKTRANSFER_ZONE',
                    'MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID',
                    'MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER',
                    'MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ',
					// BOF - dmun 20140122 Gläubiger-ID
					'MODULE_PAYMENT_BANKTRANSFER_CID',
					// EOF - dmun 20140122 Gläubiger-ID
                    'MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION',
                    'MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER',
                    'MODULE_PAYMENT_BANKTRANSFER_URL_NOTE',
                    'MODULE_PAYMENT_BANKTRANSFER_NEG_SHIPPING'					
					);
    }
  }
?>
