<?php
/* -----------------------------------------------------------------------------------------
$Id: xtc_checkout_site.inc.php 1000 2006-12-18 17:06:00Z Estelco $

Estelco - Programming for xt:Commerce - community made shopping
http://www.estelco.de

Copyright (c) 2006 Estelco
-----------------------------------------------------------------------------------------
Released under the GNU General Public License
---------------------------------------------------------------------------------------*/

function xtc_checkout_site($site) {
    if (!$_SESSION['customer_id'] || ($site != 'cart' && $site != 'shipping' && $site != 'payment' && $site != 'confirm')) {
        return false;
    }
    $checkQuery = xtc_db_query("SELECT checkout_site FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id=" . $_SESSION['customer_id']);
    $checkResult = xtc_db_fetch_array($checkQuery);
    compareSite($site, $checkResult['checkout_site']);
}

function compareSite($currentSite, $oldSite) {
    $cart = 1;
    $shipping = 2;
    $payment = 3;
    $confirm = 4;
    if ($$currentSite > $$oldSite) {
        xtc_db_query("UPDATE " . TABLE_CUSTOMERS_BASKET . " SET checkout_site='" . xtc_db_input($currentSite) . "', language='" . xtc_db_input($_SESSION['language']) . "' WHERE customers_id=" . (int)$_SESSION['customer_id']);
    }
}
?>