<?php
/**
 * Settings file for PureUserAdmin
 * @see PureUserClass::__construct() which requires this in.
 */

$config = array();
$config["version"]     = "0.3.1";
#$config["homepage"]    = "http://sourceforge.net/projects/PureUserAdmin";
$config["homepage"]    = "http://github.com/palepurple/pureftp-user-admin";
// 0 = disabled, 1 = enabled - check if user has read/write access in homedir
$config["check_access"]= "1"; 
// 0 = disabled, 1 = enabled - email user with password etc. Database needs field "email"
$config["notify_user"] = "1"; 
$config["admin_email"] = "some.one.to.email@example.com";
$config["ftp_hostname"]= "your.host.name";

// database settigs 
// PureFTPd only supports MySQL and PostgreSQL (mysql and postgres)
// Change these to match your local setup - see also schema.sql...
$config["sql_type"]    = "mysql"; 
$config["sql_server"]  = "localhost";
$config["sql_user"]    = "user";
$config["sql_pass"]    = "pass";
$config["sql_dbase"]   = "pureftp";
$config["sql_table"]   = "logins";
$config["field_id"]    = "id";
$config["field_uid"]   = "uid";
$config["field_gid"]   = "gid";
$config["field_pass"]  = "password";
$config["field_user"]  = "username";
$config["field_dir"]   = "dir";
$config["field_email"] = "email";
$config["field_company"] = "company";

// How we encrypt user's passwords.
// md5 vs cleartext vs password (for mysql's password() function)
$config["pwcrypt"]     = "md5"; 

// we use nobody (on OpenBSD)
$config["default_uid"] = "65534";		 
// we use nobody	(on OpenBSD)
$config["default_gid"] = "65534";    
// number of records on 1 page in userlist
$config["page_size"]   = "40";        

// List of companies.
$config["companies"] = array();
// List of admin users.
$config["super_admins"] = array("admin");

return $config;
