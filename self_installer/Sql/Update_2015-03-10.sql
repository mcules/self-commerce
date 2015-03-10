CREATE TABLE IF NOT EXISTS `imagesliders` (
  `imagesliders_id` int(11) NOT NULL AUTO_INCREMENT,
  `imagesliders_name` varchar(32) NOT NULL DEFAULT '',
  `date_added` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `sorting` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`imagesliders_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `imagesliders` (`imagesliders_id`, `imagesliders_name`, `date_added`, `last_modified`, `status`, `sorting`) VALUES
(1, 'Bild 1', '2014-01-10 11:31:07', '2014-01-18 17:39:32', 0, 0),
(2, 'Bild 2', '2014-01-10 11:32:40', '2014-01-18 17:39:43', 0, 0),
(3, 'Bild 3', '2014-01-10 11:33:45', NULL, 0, 0),
(4, 'Bild 4', '2014-01-10 11:35:29', NULL, 0, 0);

CREATE TABLE IF NOT EXISTS `imagesliders_info` (
  `imagesliders_id` int(11) NOT NULL,
  `languages_id` int(11) NOT NULL,
  `imagesliders_title` varchar(100) NOT NULL,
  `imagesliders_url` varchar(255) NOT NULL,
  `imagesliders_url_target` tinyint(1) NOT NULL DEFAULT '0',
  `imagesliders_url_typ` tinyint(1) NOT NULL DEFAULT '0',
  `imagesliders_description` text,
  `imagesliders_image` varchar(64) DEFAULT NULL,
  `url_clicked` int(5) NOT NULL DEFAULT '0',
  `date_last_click` datetime DEFAULT NULL,
  `imagesliders_indicator_class` int(1) NOT NULL DEFAULT '0',
  `imagesliders_caption_class` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`imagesliders_id`,`languages_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `imagesliders_info` (`imagesliders_id`, `languages_id`, `imagesliders_title`, `imagesliders_url`, `imagesliders_url_target`, `imagesliders_url_typ`, `imagesliders_description`, `imagesliders_image`, `url_clicked`, `date_last_click`, `imagesliders_indicator_class`, `imagesliders_caption_class`) VALUES
(1, 2, 'Das ist Bild 1', '84', 0, 3, '<h4>Hier der Titel</h4>\r\n<p>Bildbeschreibung in Deutsch</p>', 'imagesliders/german/bild1.jpg', 0, NULL, 0, 0),
(1, 1, 'That is picture 1', '84', 0, 3, '<p>Bildbeschreibung in Englisch</p>', 'imagesliders/english/bild1.jpg', 0, NULL, 0, 0),
(2, 2, 'Titel Bild 2', '86', 0, 3, '<p>Beschreibung Bild 2</p>', 'imagesliders/german/bild2.jpg', 0, NULL, 0, 0),
(2, 1, 'That is picture 2', '86', 0, 3, '<p>Beschreibung Bild 2</p>', 'imagesliders/english/bild2.jpg', 0, NULL, 0, 0),
(3, 2, 'Titel Bild 3', '85', 0, 3, '<p>Beschreibung Bild 3</p>\r\n<p>Zeile 2</p>', 'imagesliders/german/bild3.jpg', 0, NULL, 0, 0),
(3, 1, 'That is picture 3', '85', 0, 3, '<p>Beschreibung Bild 3</p>', 'imagesliders/english/bild3.jpg', 0, NULL, 0, 0),
(4, 2, 'Titel Bild 4', 'http://kultur-forschung.de', 0, 0, '<p>Beschreibung Bild 4</p>', 'imagesliders/german/bild4.jpg', 0, NULL, 0, 0),
(4, 1, 'That is picture 4', 'http://kultur-forschung.de', 0, 0, '<p>Beschreibung Bild 4 Englisch</p>', 'imagesliders/english/bild4.jpg', 0, NULL, 0, 0);

ALTER TABLE `admin_access` ADD `imagesliders` INT( 1 ) NOT NULL DEFAULT '0';
UPDATE `admin_access` SET `imagesliders` = 1 WHERE customers_id = '1';