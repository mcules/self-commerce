<?php
/* --------------------------------------------------------------
   $Id$   
   XT-Commerce - community made shopping
   http://www.xt-commerce.com
   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com 
   (c) 2003	 nextcommerce ( start.php,v 1.6 2003/08/19); www.nextcommerce.org
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
  require('includes/application_top.php');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
    <title>
      <?php echo TITLE; ?>
    </title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
  </head>
  <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
      <tr>
        <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
          <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
            <!-- left_navigation //-->
            <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
            <!-- left_navigation_eof //-->
          </table></td>
        <!-- body_text //-->
        <td class="boxCenter" width="100%" valign="top">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="80" rowspan="2">
                      <?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?></td>
                    <td class="pageHeading">
                      Credits</td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">
                      Self-Commerce Credits</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td class="main">
                <font color="D68000">
                  <b>
                    <?php echo PROJECT_VERSION; ?></b>
                </font>
                <br />
                  <strong><br /> Release
                    Datum: Jan 2008</strong><br />
                  Released under the GNU General Public License<br /><br /> This program is distributed
                  in the hope that it will be useful, but 
                  <b>WITHOUT ANY WARRANTY</b>
                  ;<br />
                  without even the implied warranty of 
                  <b>MERCHANTABILITY</b>
                  or 
                  <b> FITNESS FOR A PARTICULAR PURPOSE</b>.<br /> See the GNU General Public License for more details. You should have received
                  a copy of the<br />
                  GNU General Public License along with this program; if not, write to the<br />
                  Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.<br />
                  See 
                  <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
                  for details. <br /><br />
                  <b>Das Self-Commerce-Team dankt allen Programmierern und Entwicklern die Ihre Software<br />
                    zur Verf&uuml;gung gestellt haben. Sollten wir jemanden vergessen haben hier zu nennen,<br />
                    bitten wir um Entschuldigung. In diesem Fall bitten wir um einen Hinweis in unter:</b><br />
                  <a href="mailto://admin@ne-xt.de">admin@ne-xt.de</a><br /><br /><br />
                  <font color="D68000">
                    <b> Programmierer (Modifikation & Redesign) von Self-Commerce:</b>
                  </font><br />
                <hr width="100%">
                <table width="100%" border="0" cellpadding="0" cellspacing="4" border-color: #ff0000;" bgcolor="FDAC00">
                  <tr>
                    <td class="main" width="150">
                        <b>Maik Schmidt (kunigunde)</b>
                    </td>
                    <td class="main" width="80%">
                        <a href="mailto:admin@ne-xt.de">admin@ne-xt.de</a>
                    </td>
                  </tr>
                  <tr>
                    <td class="main" width="150">
                        <b>Tina Klassen (Tikla)</b>
                    </td>
                    <td class="main" width="80%">
                        <a href="mailto:template@ne-xt.de">template@ne-xt.de</a>
                    </td>
                  </tr>
                  <tr>
                    <td class="main" colspan="2">
                      <hr>

                        <b>Nat�rlich kommen sehr viele Ideen direkt aus der Community.<br />Auch all denen gilt der Dank f�r die Mitarbeit.</b>
                    </td>
                    <td></td>
                  </tr>                  
                </table>
                <br /><br />
                  <font color="D68000">
                    <b> Die Shopsoftware basiert auf:</b>
                  </font><br />
                <hr align="center" width="100%">
                  <b>� 2000-2001 The Exchange Project</b>
                  � Harald Ponce de Leon | http://www.oscommerce.com
                <br />
                  <b>� 2002-2003 osCommerce (Milestone2)</b>
                  � Harald Ponce de Leon | http://www.oscommerce.com<br />
                  <b>� 2003-2004 neXTCommerce</b> � neXTCommerce
                  (XTC 0.9 RC3 CVS) | http://www.nextcommerce.org ( code-modifications &amp; redesign by Guido Winger/Mario Zanier/Andreas Oberzier)<br />
                  Mario Zanier 
                  <a href="mailto:mzanier@xtcommerce.com">mzanier@xtcommerce.com</a>
                  / Guido Winger 
                  <a href="mailto:gwinger@xtcommerce.com">gwinger@xtcommerce.com</a>
                  / Andreas Oberzier 
                  <a href="mailto:aoberzier@nextcommerce.org">aoberzier@nextcommerce.org</a><br /><br />
                  <b>� 2004- XT:Commerce</b>
                  � XT:Commerce (Xt:C3.04sp1/sp2.1) | http://xtcommerce.de<br /><br />
                <hr align="center" width="100%">
              </td>
            </tr>
          </table></td>
        <!-- body_text_eof //-->
      </tr>
    </table>
    <!-- body_eof //-->
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
