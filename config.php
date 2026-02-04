<?php
/**
 * Settings file for PureUserAdmin
 */


$db_dsn = getenv('DATABASE_DSN');
$db_user = getenv('DATABASE_USER');
$db_pass = getenv('DATABASE_PASS');

if(!is_string($db_dsn) || empty($db_dsn)) {
    $db_dsn = 'mysql:host=localhost;dbname=pureftp';
}
if(!is_string($db_user) || empty($db_user)) {
    $db_user = null;
}

if(!is_string($db_pass) || empty($db_pass)) {
    $db_pass = null;
}


$config = [
    'version' => '0.4.0',
    'homepage' => "https://github.com/DavidGoodwin/pureftp-user-admin",
    'check_access' => true , // boolean - check if user has read/write access in homedir
    'notify_user' => false, // if enabled, email new user with password. Database needs field "email"
    'admin_email' => 'admin+pureftp@example.com',
    'ftp_hostname' => php_uname('n'),

// database settigs 
// We require a PDO DSN.
    'database_dsn'  => $db_dsn,
    'database_user' => $db_user,
    'database_pass' => $db_pass,
    'sql_table' => 'logins',
    'field_uid' => 'uid',
    'field_gid' => 'gid',
    'field_pass' => 'password',
    'field_user' => 'username',
    'field_dir' => 'dir',
    'field_email' => 'email',
    
    // How we bash/encrypt user's passwords.
    // https://download.pureftpd.org/pub/pure-ftpd/doc/README.MySQL
    // (best) argon2i > (good) crypt > sha1 > md5 > cleartext (not good)
    'pwcrypt'     => "argon2i",
    'default_uid' => "65534", // nobody
    'default_gid'  => "65534", // nogrop
    'page_size' => 40,
];


$optional = dirname(__FILE__) . '/config.local.php';

if(file_exists($optional)) {
    // put your local config changes in this file to overwrite $config['things']
    require_once($optional);
}

if(empty($db_user) || empty($db_pass)) {
    error_log(__FILE__ . " - please configure me! database settings missing.";
    die(__FILE__ . " - not yet configured");
}

return $config;
