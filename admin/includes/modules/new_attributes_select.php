<?php
/* --------------------------------------------------------------
   $Id: new_attributes_select.php 17 2012-06-04 20:33:29Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_attributes_select.php); www.oscommerce.com 
   (c) 2003	 nextcommerce (new_attributes_select.php,v 1.9 2003/08/21); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contributions:
   New Attribute Manager v4b				Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   copy attributes                          Autor: Hubi | http://www.netz-designer.de

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
echo xtc_image(DIR_WS_ICONS.'heading_categories.gif');
echo $pageTitle; 
?>
  
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="SELECT_PRODUCT" method="post">
<input type="hidden" name="action" value="edit">
<?php
echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
  echo "<TR>";
  echo "<TD class=\"main\">".SELECT_PRODUCT."</TD>";
  echo "</TR>";
  echo "<TR>";
  echo "<TD class=\"main\"><SELECT NAME=\"current_product_id\">";

//  $query = "SELECT * FROM products_description where products_id LIKE '%' AND language_id = '" . $_SESSION['languages_id'] . "' ORDER BY products_name ASC";
  $query = "SELECT pd.products_id, pd.products_name, p.products_model 
            FROM products_description pd, products p 
            WHERE p.products_id=pd.products_id AND
                  p.products_id LIKE '%' AND 
                  pd.language_id = '" . $_SESSION['languages_id'] . "' 
            ORDER BY pd.products_name ASC";
            
  $result = xtc_db_query($query);

  $matches = xtc_db_num_rows($result);

  if ($matches) {
    while ($line = xtc_db_fetch_array($result)) {
      $title = $line['products_name'];
      $current_product_id = $line['products_id'];

      echo "<OPTION VALUE=\"" . $current_product_id . "\">" . '('.$line['products_model'].') '.$title;
    }
  } else {
    echo "You have no products at this time.";
  }

  echo "</SELECT>";
  echo "</TD></TR>";

  echo "<TR>";
  echo "<TD class=\"main\">";
  echo xtc_button(BUTTON_EDIT);

  echo "</TD>";
  echo "</TR>";
  // start change for Attribute Copy
?>
<br /><br />
<?php
  echo "<TR>";
  echo "<TD class=\"main\"><br /><B>".SELECT_COPY."<br /></TD>";
  echo "</TR>";
  echo "<TR>";
  echo "<TD class=\"main\"><SELECT NAME=\"copy_product_id\">";

//  $copy_query = xtc_db_query("SELECT pd.products_name, pd.products_id FROM products_description pd, products_attributes pa where pa.products_id = pd.products_id AND pd.products_id LIKE '%' AND pd.language_id = '" . $_SESSION['languages_id'] . "' GROUP BY pd.products_id ORDER BY pd.products_name ASC");
  $query = "SELECT pd.products_name, pd.products_id , p.products_model
            FROM products_description pd, products_attributes pa, products p 
            WHERE pa.products_id = pd.products_id AND 
                  p.products_id = pd.products_id AND
                  pd.products_id LIKE '%' AND 
                  pd.language_id = '" . $_SESSION['languages_id'] . "' 
            GROUP BY pd.products_id ORDER BY pd.products_name ASC";

  $copy_query = xtc_db_query($query);
  $copy_count = xtc_db_num_rows($copy_query);

  if ($copy_count) {
      echo '<option value="0">no copy</option>';
      while ($copy_res = xtc_db_fetch_array($copy_query)) {
          echo '<option value="' . $copy_res['products_id'] . '">' . '('.$copy_res['products_model'].') '.$copy_res['products_name'] . '</option>';
      }
  }
  else {
      echo 'No products to copy attributes from';
  }
  echo '</select></td></tr>';
  echo "<TR>";
  echo "<TD class=\"main\">".xtc_button(BUTTON_EDIT)."</TD>";
  echo "</TR>";
?>
</form>
