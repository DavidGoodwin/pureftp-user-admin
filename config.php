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
    'database_dsn'  => getenv('DATABASE_DSN') ?: "mysql:host=localhost;dbname=pureftp",
    'database_user' => getenv('DATABASE_USER') ?: 'db_username',
    'database_pass' => getenv('DATABASE_PASS') ?: 'db_password', 
    'sql_table' => 'logins',
    'field_uid' => 'uid',
    'field_gid' => 'gid',
    'field_pass' => 'password',
    'field_user' => 'username',
    'field_dir' => 'dir',
    'field_email' => 'email',
    
    // How we encrypt user's passwords.
    // https://download.pureftpd.org/pub/pure-ftpd/doc/README.MySQL
    // (good) crypt > sha1 > md5 > cleartext (not good)
    'pwcrypt'     => "crypt",
    'default_uid' => "65534", // nobody
    'default_gid'  => "65534", // nogrop
    'page_size' => 40,
];


$optional = dirname(__FILE__) . '/config.local.php';

if(file_exists($optional)) {
    // put your local config changes in this file to overwrite $config['things']
    require_once($optional);
}

return $config;
