<?php
/* --------------------------------------------------------------
   $Id: application_bottom.php 17 2012-06-04 20:33:29Z deisold $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_bottom.php,v 1.8 2002/03/15); www.oscommerce.com 
   (c) 2003	 nextcommerce (application_bottom.php,v 1.6 2003/08/1); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

if (STORE_PAGE_PARSE_TIME == 'true') {
	if (!is_object($logger)) $logger = new logger;
	echo $logger->timer_stop(DISPLAY_PAGE_PARSE_TIME);
}

# BOM AMAZON PAYMENTS POWERED BY ALKIM MEDIA   
if(AMZ_CAPTURE_MODE=='after_shipping' && MODULE_PAYMENT_AM_APA_STATUS == 'True'){
    ?>
    <iframe style="width:1px; height:1px; visibility:hidden;" src="<?php echo xtc_href_link('amz_configuration.php', 'ajax=1&action=shippingCapture', ($request_type?$request_type:'NONSSL')); ?>" />
    <?php
}
# EOM AMAZON PAYMENTS POWERED BY ALKIM MEDIA