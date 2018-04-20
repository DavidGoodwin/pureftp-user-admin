<?php
/**
 * Settings file for PureUserAdmin
 * @see PureUserClass::__construct() which require's this in.
 */

$config = array();
$config["version"]     = "0.4.0";
$config["homepage"]    = "https://github.com/DavidGoodwin/pureftp-user-admin";
// 0 = disabled, 1 = enabled - check if user has read/write access in homedir
$config["check_access"]= "1"; 
// 0 = disabled, 1 = enabled - email user with password etc. Database needs field "email"
$config["notify_user"] = "1"; 
$config["admin_email"] = "some.one.to.email@example.com";
$config["ftp_hostname"]= "your.host.name";

// database settigs 
// We require a PDO DSN.

$config['database_dsn'] = "mysql:host=localhost;dbname=pureftp";
$config['database_user'] = 'db_username';
$config['database_pass'] = 'db_password';

$config["sql_table"]   = "logins";

$config["field_uid"]   = "uid";
$config["field_gid"]   = "gid";
$config["field_pass"]  = "password";
$config["field_user"]  = "username";
$config["field_dir"]   = "dir";
$config["field_email"]   = "email";

// How we encrypt user's passwords.
// md5 vs cleartext vs password (for mysql's password() function)
$config["pwcrypt"]     = "md5"; 

// we use nobody (on OpenBSD)
$config["default_uid"] = "65534";		 
// we use nobody	(on OpenBSD)
$config["default_gid"] = "65534";

// number of records on 1 page in userlist
$config["page_size"]   = "40";        

return $config;
