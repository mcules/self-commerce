<?php
/* --------------------------------------------------------------
   $Id: orders.php 17 2012-06-04 20:33:29Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(orders.php,v 1.27 2003/02/16); www.oscommerce.com
   (c) 2003	 nextcommerce (orders.php,v 1.7 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
define('TEXT_BANK', 'Bank Collection');
define('TEXT_BANK_OWNER', 'Account Holder:');
define('TEXT_BANK_NUMBER', 'Account Number:');
define('TEXT_BANK_BLZ', 'Bank Code:');
define('TEXT_BANK_NAME', 'Bank:');
define('TEXT_BANK_FAX', 'Collect Authorization will be approved via Fax');
define('TEXT_BANK_STATUS', 'Verify Status:');
define('TEXT_BANK_PRZ', 'Method of Verify:');

define('TEXT_BANK_ERROR_1', 'Accountnumber and Bank Code are not compatible!<br />Please try again!');
define('TEXT_BANK_ERROR_2', 'Sorry, we are unable to proof this account number!');
define('TEXT_BANK_ERROR_3', 'Account number not proofable! Method of Verify not implemented');
define('TEXT_BANK_ERROR_4', 'Account number technically not proofable!<br />Please try again!');
define('TEXT_BANK_ERROR_5', 'Bank Code not found!<br />Please try again.!');
define('TEXT_BANK_ERROR_8', 'No match for your Bank Code or Bank Code not given!');
define('TEXT_BANK_ERROR_9', 'No account number given!');
define('TEXT_BANK_ERRORCODE', 'Errorcode:');

define('HEADING_TITLE', 'Orders');
define('HEADING_TITLE_SEARCH', 'Order ID:');
define('HEADING_TITLE_STATUS', 'Status:');

define('TABLE_HEADING_COMMENTS', 'Comments');
define('TABLE_HEADING_CUSTOMERS', 'Customers');
define('TABLE_HEADING_ORDER_TOTAL', 'Order Total');
define('TABLE_HEADING_DATE_PURCHASED', 'Date Purchased');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Action');
define('TABLE_HEADING_QUANTITY', 'Qty.');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Model');
define('TABLE_HEADING_PRODUCTS', 'Products');
define('TABLE_HEADING_TAX', 'Tax');
define('TABLE_HEADING_TOTAL', 'Total');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Price (ex)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Price (inc)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Total (ex)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Total');
define('TABLE_HEADING_AFTERBUY','Afterbuy');

define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Customer Notified');
define('TABLE_HEADING_DATE_ADDED', 'Date Added');

define('ENTRY_CUSTOMER', 'Customer:');
define('ENTRY_SOLD_TO', 'SOLD TO:');
define('ENTRY_STREET_ADDRESS', 'Street Address:');
define('ENTRY_SUBURB', 'Suburb:');
define('ENTRY_CITY', 'City:');
define('ENTRY_POST_CODE', 'Post Code:');
define('ENTRY_STATE', 'State:');
define('ENTRY_COUNTRY', 'Country:');
define('ENTRY_TELEPHONE', 'Telephone:');
define('ENTRY_EMAIL_ADDRESS', 'eMail Address:');
define('ENTRY_DELIVERY_TO', 'Delivery To:');
define('ENTRY_SHIP_TO', 'SHIP TO:');
define('ENTRY_SHIPPING_ADDRESS', 'Shipping Address:');
define('ENTRY_BILLING_ADDRESS', 'Billing Address:');
define('ENTRY_PAYMENT_METHOD', 'Payment Method:');
define('ENTRY_CREDIT_CARD_TYPE', 'Credit Card Type:');
define('ENTRY_CREDIT_CARD_OWNER', 'Credit Card Owner:');
define('ENTRY_CREDIT_CARD_NUMBER', 'Credit Card Number:');
define('ENTRY_CREDIT_CARD_CVV', 'Security Code (CVV)):');
define('ENTRY_CREDIT_CARD_EXPIRES', 'Credit Card Expires:');
define('ENTRY_SUB_TOTAL', 'Sub-Total:');
define('ENTRY_TAX', 'Tax:');
define('ENTRY_SHIPPING', 'Shipping:');
define('ENTRY_TOTAL', 'Total:');
define('ENTRY_DATE_PURCHASED', 'Date Purchased:');
define('ENTRY_STATUS', 'Status:');
define('ENTRY_DATE_LAST_UPDATED', 'Date Last Updated:');
define('ENTRY_NOTIFY_CUSTOMER', 'Notify Customer:');
define('ENTRY_NOTIFY_COMMENTS', 'Append Comments:');
define('ENTRY_PRINTABLE', 'Print Invoice');

define('TEXT_INFO_HEADING_DELETE_ORDER', 'Delete Order');
define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this order?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', 'Restock product quantity');
define('TEXT_DATE_ORDER_CREATED', 'Date Created:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', 'Last Modified:');
define('TEXT_INFO_PAYMENT_METHOD', 'Payment Method:');

define('TEXT_ALL_ORDERS', 'All Orders');
define('TEXT_NO_ORDER_HISTORY', 'No Order History Available');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Order Update');
define('EMAIL_TEXT_ORDER_NUMBER', 'Order Number:');
define('EMAIL_TEXT_INVOICE_URL', 'Detailed Invoice:');
define('EMAIL_TEXT_DATE_ORDERED', 'Date Ordered:');
define('EMAIL_TEXT_STATUS_UPDATE', 'Your order has been updated to the following status.' . "\n\n" . 'New status: %s' . "\n\n" . 'Please reply to this eMail if you have any questions.' . "\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', 'The comments for your order are' . "\n\n%s\n\n");

define('ERROR_ORDER_DOES_NOT_EXIST', 'Error: Order does not exist.');
define('SUCCESS_ORDER_UPDATED', 'Success: Order has been successfully updated.');
define('WARNING_ORDER_NOT_UPDATED', 'Warning: Nothing to change. The order was not updated.');

define('TABLE_HEADING_DISCOUNT','Discount');
define('ENTRY_CUSTOMERS_GROUP','Customers Group:');
define('ENTRY_CUSTOMERS_VAT_ID','VAT-ID:');
define('TEXT_VALIDATING','Not validated');

define('SUCCESS_ORDER_SEND', 'Success: Order allready send!');

define('TABLE_HEADING_EDIT','all');
define('TEXT_DO_STATUS_CHANGE','Status really change?');
define('HEADING_MULTI_ORDER_STATUS','Order Fast Progress');
define('WARNING_ORDER_NOT_UPDATED_ALL','Order not updated all pleas check your entry');

//BEGIN NEXT AND PREVIOUS ORDERS DISPLAY IN ADMIN
define('PREV_ORDER', '<b><<Previous Order</b>');
define('NEXT_ORDER', '<b>Next Order>></b>');
//END NEXT AND PREVIOUS ORDERS DISPLAY IN ADMIN

// PayPal Express
define('TEXT_INFO_PAYPAL_DELETE', 'Delete PayPal Transaktion data also.');

// Banktransfer IBAN
define('TEXT_BANK_IBAN', 'IBAN:');
define('TEXT_BANK_BIC', 'BIC:');