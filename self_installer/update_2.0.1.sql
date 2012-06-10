INSERT INTO `categories` (`categories_id`, `categories_image`, `parent_id`, `categories_status`, `categories_template`, `group_permission_0`, `group_permission_1`, `group_permission_2`, `group_permission_3`, `listing_template`, `sort_order`, `products_sorting`, `products_sorting2`, `date_added`, `last_modified`) VALUES
(1, NULL, 0, 0, 'categorie_listing.html', 0, 0, 0, 0, 'product_listing_v2.html', 0, 'p.products_price', 'ASC', NOW(), NULL);
INSERT INTO `categories_description` (`categories_id`, `language_id`, `categories_name`, `categories_heading_title`, `categories_description`, `categories_meta_title`, `categories_meta_description`, `categories_meta_keywords`) VALUES
(1, 1, 'Warenkorb', '', '', '', '', ''),
(1, 2, 'Warenkorb', '', '', '', '', '');