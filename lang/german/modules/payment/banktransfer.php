<?php

/* -----------------------------------------------------------------------------------------
   $Id: banktransfer.php 4200 2013-01-10 19:47:11Z Tomcraft1980 $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(banktransfer.php,v 1.9 2003/02/18 19:22:15); www.oscommerce.com
   (c) 2003	 nextcommerce (banktransfer.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2006 xt:Commerce; www.xt-commerce.com

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   OSC German Banktransfer v0.85a       	Autor:	Dominik Guder <osc@guder.org>

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

define('MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE', 'Lastschriftverfahren');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_DESCRIPTION', 'Lastschriftverfahren');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO', '');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK', 'Bankeinzug');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_EMAIL_FOOTER', 'Hinweis: Sie k&ouml;nnen sich unser Faxformular unter ' . HTTP_SERVER . DIR_WS_CATALOG . MODULE_PAYMENT_BANKTRANSFER_URL_NOTE . ' herunterladen und es ausgef&uuml;llt an uns zur&uuml;cksenden.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_INFO', 'Bitte beachten Sie, dass das Lastschriftverfahren ohne Angabe von BIC/IBAN <b>nur</b> von einem <b>deutschen Girokonto</b> aus m&ouml;glich ist.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_IBAN', 'IBAN:');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BIC', 'BIC:');

define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER', 'Kontoinhaber :');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER', 'Kontonummer :');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ', 'Bankleitzahl :');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME', 'Name der Bank:');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_IBAN', 'IBAN :');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BIC', 'BIC-Code der Bank :');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE_SEPA', '');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO_SEPA', '<b>SEPA-Eingabe</b></br>Statt der Kontonummer und BLZ bitte hier als Alternative die IBAN und BIC eintragen');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_MANDAT_TITLE', '');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_MANDAT_INFO', '<b>Lastschriftmandat</b><p>Hiermit erteile ich zugleich ein einmaliges SEPA-Mandat den Rechungsbetrag von meinem Konto einzuziehen</p>');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_CID', 'Unsere Gl&auml;ubiger-ID:');

define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX', 'Einzugserm&auml;chtigung wird per Fax best&auml;tigt');

// Note these MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_X texts appear also in the URL, so no html-entities are allowed here
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR', 'FEHLER: ');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1', 'Kontonummer und Bankleitzahl stimmen nicht überein, bitte korrigieren Sie Ihre Angabe.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2', 'Diese Kontonummer ist nicht prüfbar, bitte kontrollieren Sie Ihre Eingabe nochmals.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_3', 'Diese Kontonummer ist nicht prüfbar, bitte kontrollieren Sie Ihre Eingabe nochmals.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4', 'Diese Kontonummer ist nicht prüfbar, bitte kontrollieren Sie Ihre Eingabe nochmals.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_5', 'Diese Bankleitzahl existiert nicht, bitte korrigieren Sie Ihre Angabe.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_8', 'Sie haben keine korrekte Bankleitzahl eingegeben.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_9', 'Sie haben keine korrekte Kontonummer eingegeben.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_10', 'Sie haben keinen Kontoinhaber angegeben.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_11', 'Sie haben keinen BIC angegeben.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_12', 'Sie haben keine korrekte IBAN eingegeben.');

// BOF dmun 20140306 Erweiterte Fehlermeldungen IBAN
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1000', 'Länderkennung ist unbekannt.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1010', 'Länge der IBAN ist falsch: Zu viele Stellen.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1020', 'Länge der IBAN ist falsch: Zu wenige Stellen.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1030', 'IBAN entspricht nicht dem für das Land festgelegten Format.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1040', 'Prüfsumme der IBAN ist nicht korrekt -> Tippfehler.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1050', 'BIC ist ungültig.');

define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2001', 'Deutsche Kontonummer und deutsche BLZ passen nicht.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2002', 'Kein deutsches Prüfziffernverfahren definiert.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2003', 'Prüfziffernverfahren ist noch nicht implementiert.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2004', 'Kontonummer im Detail nicht prüfbar.');

define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2005', 'Deutsche BLZ nicht gefunden.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2008', 'Keine deutsche BLZ übergeben.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2009', 'Keine deutsche Kontonummer übergeben.');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2128', 'Interner Fehler, der zeigt, dass eine Methode nicht implementiert ist.');
// EOF dmun 20140306 Erweiterte Fehlermeldungen IBAN

define('MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE', 'Hinweis:');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE2', 'Wenn Sie aus Sicherheitsbedenken keine Bankdaten &uuml;ber das Internet<br />&uuml;bertragen wollen, k&ouml;nnen Sie sich unser ');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE3', 'Faxformular');
define('MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE4', ' herunterladen und uns ausgef&uuml;llt zusenden.');

define('JS_BANK_BLZ', '* Bitte geben Sie die BLZ Ihrer Bank ein!\n\n');
define('JS_BANK_NAME', '* Bitte geben Sie den Namen Ihrer Bank ein!\n\n');
define('JS_BANK_NUMBER', '* Bitte geben Sie Ihre Kontonummer ein!\n\n');
define('JS_BANK_OWNER', '* Bitte geben Sie den Namen des Kontobesitzers ein!\n\n');
define('JS_BANK_BIC', '* Bitte geben Sie die BIC Ihrer Bank ein!\n\n');
define('JS_BANK_IBAN', '* Bitte geben Sie Ihre IBAN ein!\n\n');

define('MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ_TITLE', 'Datenbanksuche f&uuml;r die Bankleitzahlen-Pr&uuml;fung verwenden?');
define('MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ_DESC', 'M&ouml;chten Sie die Datenbank f&uuml;r die Bankleitzahlen-Plausibilit&auml;tspr&uuml;fung verwenden ("true")?<br/>Vergewissern Sie sich, dass die Bankleitzahlen in der Datenbank auf dem aktuellen Stand sind!<br/><a href="'.FILENAME_BLZ_UPDATE.'" target="_blank"><strong>Link: --> BLZ UPDATE <-- </strong></a><br/><br/>Bei "false" (standard) wird die mitgelieferte blz.csv Datei verwendet, die m&ouml;glicherweise veraltete Eintr&auml;ge enth&auml;lt!');
define('MODULE_PAYMENT_BANKTRANSFER_URL_NOTE_TITLE', 'Fax-URL');
define('MODULE_PAYMENT_BANKTRANSFER_URL_NOTE_DESC', 'Die Fax-Best&auml;tigungsdatei. Diese muss im Catalog-Verzeichnis liegen');
define('MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION_TITLE', 'Fax Best&auml;tigung erlauben');
define('MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION_DESC', 'M&ouml;chten Sie die Fax Best&auml;tigung erlauben?');
define('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER_TITLE', 'Anzeigereihenfolge');
define('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER_DESC', 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.');
define('MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID_TITLE', 'Bestellstatus festlegen');
define('MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID_DESC', 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen');
define('MODULE_PAYMENT_BANKTRANSFER_ZONE_TITLE', 'Zahlungszone');
define('MODULE_PAYMENT_BANKTRANSFER_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist, gilt die Zahlungsmethode nur f&uuml;r diese Zone.');
define('MODULE_PAYMENT_BANKTRANSFER_ALLOWED_TITLE', 'Erlaubte Zonen');
define('MODULE_PAYMENT_BANKTRANSFER_ALLOWED_DESC', 'Geben Sie <b>einzeln</b> die Zonen an, welche f&uuml;r dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))');
define('MODULE_PAYMENT_BANKTRANSFER_STATUS_TITLE', 'Banktranfer Zahlungen erlauben');
define('MODULE_PAYMENT_BANKTRANSFER_STATUS_DESC', 'M&ouml;chten Banktranfer Zahlungen erlauben?');
define('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_TITLE', 'Notwendige Bestellungen');
define('MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_DESC', 'Die Mindestanzahl an Bestellungen die ein Kunden haben muss damit die Option zur Verf&uuml;gung steht.');
define('MODULE_PAYMENT_BANKTRANSFER_NEG_SHIPPING_TITLE', 'Ausschlu&szlig; bei Versandmodulen');
define('MODULE_PAYMENT_BANKTRANSFER_NEG_SHIPPING_DESC', 'Dieses Zahlungsmodul deaktivieren wenn Versandmodul gew&auml;hlt (Komma separierte Liste)');
define('MODULE_PAYMENT_BANKTRANSFER_CID_TITLE', 'Gl&auml;ubiger-ID:');
define('MODULE_PAYMENT_BANKTRANSFER_CID_DESC', 'G&uuml;ltige Gl&auml;ubigr ID');
?>