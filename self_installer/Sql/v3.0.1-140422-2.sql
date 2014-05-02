ALTER TABLE products_options ADD  option_active TINYINT( 1 ) NOT NULL AFTER  products_options_sortorder;
ALTER TABLE products_options ADD  option_type TINYINT( 1 ) NOT NULL AFTER  option_active;
ALTER TABLE products_options ADD  option_image VARCHAR( 64 ) NOT NULL AFTER  option_type;

ALTER TABLE products_options_values ADD  value_image VARCHAR( 255 ) NOT NULL AFTER  products_options_values_name;

CREATE TABLE IF NOT EXISTS products_filter (
  products_filter_id int(11) NOT NULL,
  products_id int(11) NOT NULL,
  products_options_id int(11) NOT NULL,
  products_options_values_id int(11) NOT NULL,
  PRIMARY KEY (products_filter_id),
  KEY products_id (products_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;