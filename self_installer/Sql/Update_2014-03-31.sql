ALTER TABLE products_to_categories ADD INDEX (categories_id);
ALTER TABLE products ADD INDEX (products_model);

ALTER TABLE banktransfer ADD banktransfer_iban VARCHAR(50) DEFAULT NULL;
ALTER TABLE banktransfer ADD banktransfer_bic VARCHAR(11) DEFAULT NULL;
ALTER TABLE admin_access ADD blz_update INT(1) DEFAULT 0 NOT NULL;
UPDATE admin_access SET blz_update = 1 WHERE customers_id = '1';
UPDATE admin_access SET blz_update = 1 WHERE customers_id = 'groups';
DROP TABLE IF EXISTS banktransfer_blz;
CREATE TABLE IF NOT EXISTS banktransfer_blz (
  blz int(10) NOT NULL DEFAULT 0,
  bankname varchar(255) NOT NULL DEFAULT '',
  prz char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (blz)
) ENGINE=MyISAM;