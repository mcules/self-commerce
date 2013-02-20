<?php
/* --------------------------------------------------------------
   $Id: server_info.php 17 2012-06-04 20:33:29Z deisold $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(server_info.php,v 1.4 2003/03/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (server_info.php,v 1.7 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
require ('includes/application_top.php');
  $system = xtc_get_system_information();
require ('includes/application_top_1.php');
?>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="80" rowspan="2">
<?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?>
                    </td>
                    <td class="pageHeading">
<?php echo HEADING_TITLE; ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">

                    </td>
                  </tr>
                </table></td>
            </tr>
          </table>
<!-- content -->
<table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td>

<?php
  if (function_exists('ob_start')) {
ob_start();

phpinfo();
$php_info .= ob_get_contents();

ob_end_clean();
$php_info = str_replace("<title>phpinfo()</title>", "", $php_info);
$php_info = str_replace("</head>", "", $php_info);
$php_info = str_replace("<body>", "", $php_info);
$php_info = str_replace(" width=\"600\"", " width=\"786\"", $php_info);
$php_info = str_replace("</body></html>", "", $php_info);

$php_info = str_replace(";", "; ", $php_info);
$php_info = str_replace(",", ", ", $php_info);

$offset = strpos($php_info, "<div");

echo '
<style type="text/css"><!--
pre {margin: 0px;  font-family: monospace; }
a:link {color: #000099;  text-decoration: none;  background-color: #ffffff; }
a:hover {text-decoration: underline; }
table {border-collapse: collapse; }
.center {text-align: center; }
.center table { margin-left: auto;  margin-right: auto;  text-align: left; }
.center th { text-align: center !important;  }

h1 {font-size: 150%; }
h2 {font-size: 125%; }
.p {text-align: left; }
.e {background-color: #ccccff;  font-weight: bold;  color: #000000; }
.h {background-color: #9999cc;  font-weight: bold;  color: #000000; }
.v {background-color: #cccccc;  color: #000000; }
i {color: #666666;  background-color: #cccccc; }


//--></style>
';
print substr($php_info, $offset);
  }
?>
        </td>
      </tr>
    </table>
<!-- end content -->
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>
