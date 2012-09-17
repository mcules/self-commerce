#!/usr/bin/php
<?php
define('DIR_FS_DOCUMENT_ROOT', '/var/www/dev.self-commerce.de/web/');
$LogFile = fopen(DIR_FS_DOCUMENT_ROOT . "Log_Auth_Admin.log", "a+");
fputs($LogFile, date("d.m.Y, H:i:s", time()) . " ---------------------------------- " . "\n");

$fp = fopen("php://stdin", "r");
require_once DIR_FS_DOCUMENT_ROOT . 'includes/configure.php';
require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/radius.class.php';

$connection = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
mysql_select_db(DB_DATABASE, $connection);

$configuration_query = mysql_query('SELECT configuration_key AS cfgKey, configuration_value AS cfgValue FROM configuration;');
while ($configuration = mysql_fetch_array($configuration_query))
{
	if ($configuration['cfgKey']=="CURRENT_TEMPLATE")
	{
		$template = $configuration['cfgValue'];
	}
	else
	{
		define($configuration['cfgKey'], $configuration['cfgValue']);
	}
}

$strUsername    = stream_get_line($fp, 1024, "\n");
$strUsername    = str_replace('"', '', $strUsername);
$strPassword    = stream_get_line($fp, 1024, "\n");
$strPassword    = str_replace('"', '', $strPassword);

fputs($LogFile, date("d.m.Y, H:i:s", time()) . " User:" . $strUsername . ", Pass:" . $strPassword . "\n");
fputs($LogFile, date("d.m.Y, H:i:s", time()) . " Secure Admin: " . TOKEN_SECURE_ADMIN . "\n");

if (TOKEN_SECURE_ADMIN)
{
	$Sql = "SELECT * FROM token_admins WHERE username='$strUsername' LIMIT 1;";
	fputs($LogFile, date("d.m.Y, H:i:s", time()) . " SQL: " . $Sql . "\n");
	$check_admin_token = mysql_query($Sql);
	if (mysql_num_rows($check_admin_token))
	{
		$token_temp = mysql_fetch_array($check_admin_token);
		fputs($LogFile, date("d.m.Y, H:i:s", time()) . " Token Server: " . TOKEN_SERVER . "\n");
		fputs($LogFile, date("d.m.Y, H:i:s", time()) . " Token Secret: " . TOKEN_SECRET . "\n");
		fputs($LogFile, date("d.m.Y, H:i:s", time()) . " Token Auth Port: " . TOKEN_SERVER_PORT_AUTH . "\n");
		fputs($LogFile, date("d.m.Y, H:i:s", time()) . " Token Acc Port: " . TOKEN_SERVER_PORT_ACCOUNTING . "\n");
		fputs($LogFile, date("d.m.Y, H:i:s", time()) . " Token NAS IP: " . TOKEN_SERVER_NAS_IP . "\n");
		fputs($LogFile, date("d.m.Y, H:i:s", time()) . " Token Debug: " . TOKEN_DEBUG . "\n");
		$class_radius = new Radius($ip_radius_server = TOKEN_SERVER, $shared_secret = TOKEN_SECRET, $radius_suffix = '', $udp_timeout = 5, $authentication_port = TOKEN_SERVER_PORT_AUTH, $accounting_port = TOKEN_SERVER_PORT_ACCOUNTING);
		$class_radius->SetNasIpAddress(TOKEN_SERVER_NAS_IP);
		if (TOKEN_DEBUG)
		{
			$class_radius->SetDebugMode(true);
		}
		if ($class_radius->AccessRequest($strUsername, $strPassword))
		{
			$customers_id = (int)$token_temp['customers_id'];
			fputs($LogFile, date("d.m.Y, H:i:s", time()) . " customers_id: " . $customers_id . "\n");
		}
		fputs($LogFile, date("d.m.Y, H:i:s", time()) . " Radius Debug: " . str_replace("<br />", "\n", $class_radius->debug_messages) . "\n");
	}
	else
	{
		fputs($LogFile, date("d.m.Y, H:i:s", time()) . " ERROR: No Result from Database". "\n");
	}
}

fclose($LogFile);

if (!empty($customers_id))
{
	exit(0);    // Login success
}
else
{
	exit(1);    // Login error
}
?>