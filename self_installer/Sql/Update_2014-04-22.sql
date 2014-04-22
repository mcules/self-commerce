INSERT INTO configuration_group (
  configuration_group_title,
  configuration_group_description,
  sort_order,
  visible
)
VALUES (
  'Piwik Analytics', 'Piwik Analytics Options', NULL , '1'
);

INSERT INTO configuration (
  configuration_key,
  configuration_value,
  configuration_group_id,
  sort_order,
  set_function
)
SELECT
	'PIWIK_ANAL_ON',
	'false',
	configuration_group_id,
	'1',
	'xtc_cfg_select_option(array(\'true\', \'false\'),'
FROM configuration_group
WHERE configuration_group_title='Piwik Analytics';

INSERT INTO configuration (
  configuration_key,
  configuration_value,
  configuration_group_id,
  sort_order,
  set_function
)
SELECT
	'PIWIK_ANAL_SITE_ID',
	'',
	configuration_group_id,
	'2',
	''
FROM configuration_group
WHERE configuration_group_title='Piwik Analytics';

INSERT INTO configuration (
  configuration_key,
  configuration_value,
  configuration_group_id,
  sort_order,
  set_function
)
SELECT
	'PIWIK_ANAL_URL',
	'',
	configuration_group_id,
	'3',
	''
FROM configuration_group
WHERE configuration_group_title='Piwik Analytics';