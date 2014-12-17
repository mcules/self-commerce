ALTER TABLE products ADD products_han VARCHAR( 30 ) NOT NULL; ALTER TABLE products ADD suppliers_id VARCHAR( 30 ) NOT NULL;
CREATE TABLE IF NOT EXISTS suppliers_info ( suppliers_id int(11) NOT NULL, suppliers_url varchar(255) NOT NULL, suppliers_customer_number varchar(30) NOT NULL, PRIMARY KEY (suppliers_id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS suppliers ( suppliers_id int(11) NOT NULL AUTO_INCREMENT, suppliers_name varchar(32) NOT NULL, suppliers_image varchar(64) DEFAULT NULL, date_added datetime DEFAULT NULL, last_modified datetime DEFAULT NULL, PRIMARY KEY (suppliers_id), KEY IDX_SUPPLIERS_NAME (suppliers_name) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;
ALTER TABLE admin_access ADD suppliers INT( 1 ) NOT NULL DEFAULT '1';
INSERT INTO products_details (products_details_id, products_details_name, products_details_title) VALUES
(1, 'Details', '{"2":"","1":""}');