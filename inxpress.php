<?php
require('includes/application_top.php');
$log = fopen("inxpress.log","a");
fwrite($log,'Created At: '.date("Y-m-d H:i:s").PHP_EOL);
try 
{
	if(isset($_POST['key']))
	{
		global $db;
		$table=TABLE_CONFIGURATION;
		$account=mysql_real_escape_string($_POST['account_no']);
		$inxpress_account_no=mysql_real_escape_string($_POST['inxpress_account_no']);
		
		$db->Execute("UPDATE {$table} SET configuration_value='{$inxpress_account_no}' WHERE configuration_key='INXPRESS_ACCOUNT_NUMBER'");
		$db->Execute("UPDATE {$table} SET configuration_value='{$account}' WHERE configuration_key='INXPRESS_ACCOUNT'");
		
		fwrite($log,'Configuration Data Has Been Saved Successfully!!!!'.PHP_EOL);
		
		
	}else{
	
		fwrite($log,'Exception: Key Is Not Provided'.PHP_EOL);
		
	}
}
catch(Exception $e)
{


	fwrite($log,'Exception: '.$e->getMessage().PHP_EOL);
	
}