<?php
/* -----------------------------------------------------------------------------------------
   $Id$   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(validations.php,v 1.11 2003/02/11); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_validate_email.inc.php,v 1.5 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_validate_email($email) {

    if (strpos($email,"\0")!== false) {return false;}
    if (strpos($email,"\x00")!== false) {return false;}
    if (strpos($email,"\u0000")!== false) {return false;}
    if (strpos($email,"\000")!== false) {return false;}

    $email = trim($email);
    $valid_address = false;
    if (strlen($email) > 255) {
        $valid_address = false;
    } else {
        if ( substr_count( $email, '@' ) > 1 ) {
            $valid_address = false;
        }

        $regex = "/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)$/i";
        $valid_address = preg_match($regex, $email);
    }

    if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
        $domain = explode('@', $email);
        if (!checkdnsrr($domain[1], "MX") && !checkdnsrr($domain[1], "A")) {
            $valid_address = false;
        }
    }
    return $valid_address;
}
?>