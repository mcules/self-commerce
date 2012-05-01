INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `date_added`) VALUES ('SELF_VERSION', '2.0.1', 1, NOW());
ALTER TABLE `address_book` MODIFY `entry_company` VARCHAR( 64 );
ALTER TABLE `address_book` MODIFY `entry_city` VARCHAR( 50 );
