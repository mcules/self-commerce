INSERT INTO `categories` (`categories_image`, `parent_id`, `categories_status`, `categories_template`, `group_permission_0`, `group_permission_1`, `group_permission_2`, `group_permission_3`, `listing_template`, `sort_order`, `products_sorting`, `products_sorting2`, `date_added`, `last_modified`) VALUES
(NULL, 0, 0, 'categorie_listing.html', 0, 0, 0, 0, 'product_listing_v2.html', 0, 'p.products_price', 'ASC', NOW(), NULL);
INSERT INTO `categories_description` (`language_id`, `categories_name`, `categories_heading_title`, `categories_description`, `categories_meta_title`, `categories_meta_description`, `categories_meta_keywords`) VALUES
(1, 'Warenkorb', '', '', '', '', ''),
(2, 'Warenkorb', '', '', '', '', '');
ALTER TABLE products ADD (products_setup int(1) default '0', setup_price decimal(15,4) NOT NULL default '0.0000');