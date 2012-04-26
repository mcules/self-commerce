<?php
/* --------------------------------------------------------------
   $Id ip_block.php v 1.0 kunigunde

   Self-Commerce - Fresh up You're Ecommerce
   http://www.self-commerce.de

   Copyright (c) 2007 Self-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com 
   (c) 2003-?	 nextcommerce www.nextcommerce.org
   (c) ? xt:commerce www.xtcommerce.com
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/

define('HEADING_TITLE', 'IP Adressen blockieren');

define('TABLE_HEADING_BANNED_IP', 'Blockierte IP Adressen');
define('TABLE_HEADING_ACTION', 'Aktion');

define('TEXT_HEADING_NEW_MANUFACTURER', 'Neuer Hersteller');
define('TEXT_HEADING_EDIT_MANUFACTURER', 'Hersteller bearbeiten');
define('TEXT_HEADING_DELETE_MANUFACTURER', 'Hersteller l&ouml;schen');

define('TEXT_MANUFACTURERS', 'Hersteller:');
define('TEXT_DATE_ADDED', 'hinzugef&uuml;gt am:');
define('TEXT_LAST_MODIFIED', 'letzte &Auml;nderung am:');
define('TEXT_PRODUCTS', 'Artikel:');
define('TEXT_IMAGE_NONEXISTENT', 'BILD NICHT VORHANDEN');

define('TEXT_NEW_INTRO', 'Bitte geben Sie den neuen Hersteller mit allen relevanten Daten ein.');
define('TEXT_EDIT_INTRO', 'Bitte f&uuml;hren Sie alle notwendigen &Auml;nderungen durch');

define('TEXT_MANUFACTURERS_NAME', 'Herstellername:');
define('TEXT_MANUFACTURERS_IMAGE', 'Herstellerbild:');
define('TEXT_MANUFACTURERS_URL', 'Hersteller URL:');

define('TEXT_DELETE_INTRO', 'Sind Sie sicher, dass Sie diesen Hersteller l&ouml;schen m&ouml;chten?');
define('TEXT_DELETE_IMAGE', 'Hersteller Image l&ouml;schen?');
define('TEXT_DELETE_PRODUCTS', 'Alle Artikel von diesem Hersteller l&ouml;schen? (inkl. Bewertungen, Angebote und Neuerscheinungen)');
define('TEXT_DELETE_WARNING_PRODUCTS', '<b>WARNUNG:</b> Es existieren noch %s Artikel, welche mit diesem Hersteller verbunden sind!');

define('ERROR_DIRECTORY_NOT_WRITEABLE', 'Fehler: Das Verzeichnis %s ist schreibgesch&uuml;tzt. Bitte korrigieren Sie die Zugriffsrechte zu diesem Verzeichnis!');
define('ERROR_DIRECTORY_DOES_NOT_EXIST', 'Fehler: Das Verzeichnis %s existiert nicht!');
?>
