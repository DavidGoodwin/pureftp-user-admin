<?php
/**
 * Settings file for PureUserAdmin
 */

$config = [
    'version' => '0.4.0',
    'homepage' => "https://github.com/DavidGoodwin/pureftp-user-admin",
    'check_access' => true , // boolean - check if user has read/write access in homedir
    'notify_user' => false, // if enabled, email new user with password. Database needs field "email"
    'admin_email' => 'admin+pureftp@example.com',
    'ftp_hostname' => php_uname('n'),

// database settigs 
// We require a PDO DSN.
    'database_dsn' => "mysql:host=localhost;dbname=pureftp",
    'database_user' => 'db_username',
    'database_pass' => 'db_password', 
    'sql_table' => 'logins',
    'field_uid' => 'uid',
    'field_gid' => 'gid',
    'field_pass' => 'password',
    'field_user' => 'username',
    'field_dir' => 'dir',
    'field_email' => 'email',
    
// How we encrypt user's passwords.
// crypt vs md5 vs cleartext 
    'pwcrypt'     => "md5",
    'default_uid' => "65534", // nobody
    'default_gid'  => "65534", // nogrop
    'page_size' => 40,
];

if(file_exists(dirname(__FILE__) . '/config.local.php')) {
    // put your local config changes in this file to overwrite $config['things']
    require_once(dirname(__FILE__) . '/config.local.php');
}

return $config;
