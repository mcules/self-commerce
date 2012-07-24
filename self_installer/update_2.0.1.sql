INSERT INTO `categories` (`categories_id`, `categories_image`, `parent_id`, `categories_status`, `categories_template`, `group_permission_0`, `group_permission_1`, `group_permission_2`, `group_permission_3`, `listing_template`, `sort_order`, `products_sorting`, `products_sorting2`, `date_added`, `last_modified`) VALUES
(1, NULL, 0, 0, 'categorie_listing.html', 0, 0, 0, 0, 'product_listing_v2.html', 0, 'p.products_price', 'ASC', NOW(), NULL);
INSERT INTO `categories_description` (`categories_id`, `language_id`, `categories_name`, `categories_heading_title`, `categories_description`, `categories_meta_title`, `categories_meta_description`, `categories_meta_keywords`) VALUES
(1, 1, 'Warenkorb', '', '', '', '', ''),
(1, 2, 'Warenkorb', '', '', '', '', '');
ALTER TABLE products ADD (products_setup int(1) default '0', setup_price decimal(15,4) NOT NULL default '0.0000');

# Prüfsummenscanner
CREATE TABLE `self_commerce_filechk_php` (
  `id` int(11) NOT NULL auto_increment,
  `filepath` text,
  `hash` varchar(32) default NULL,
  KEY `id` (`id`)
);
CREATE TABLE `self_commerce_filechk_html` (
  `id` int(11) NOT NULL auto_increment,
  `filepath` text,
  `hash` varchar(32) default NULL,
  KEY `id` (`id`)
);
ALTER TABLE `admin_access` ADD `file_chk` INT( 1 ) NOT NULL DEFAULT '1';