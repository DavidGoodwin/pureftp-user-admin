<?php

require_once(dirname(__FILE__) . '/functions.php');

/**
 * Manage virtual users for PureFTPd.
 *
 * This class provides every function you need to manage virtual users.
 * It can handle users stored in a MySQL or PostgreSQL database.
 * @version 0.2.0
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://pureuseradmin.sourceforge.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2004, Michiel van Baak
 */

class pureuseradmin {

    const DEBUG      = 1;
    /**
     * This variable is filled in the constructor. It can be changed with a public function.
     * @var array all settings needed
     * @access public
     */
    public $settings = Array();
    /**
     * This variable is filled in the constructor.
     * @var array uids on the system.
     * @access public
     */
    public $uids     = Array();
    /**
     * This variable is filled in the constructor.
     * @var array gids on the system.
     * @access public
     */
    public $gids     = Array();
    
    /**
     * The company of the user who is logged-in and using the system.
     * @var string Name of the company to which they belong.
     * @access public
     */
    public $adminCompany = '_unknown_company_';
    
    /**
     * Stores an array of errors encountered/thrown.
     * @var array Associative array of errors.
     * @access public
     */
    public $errors = array();

    /**
     * Generate a password statement for the database query.
     * <code> $pass = self::mkpass("password"); </code>
     * @param string $passwd The password to insert into the database.
     * @return string The string to use in the sql statement.
     * @access private
     */
    private function mkpass ($passwd) {
        if ($this->settings["pwcrypt"] == "password") {
            $ret = "password('".$passwd."')";
        } elseif ($this->settings["pwcrypt"] == "cleartext") {
            $ret = "'".$passwd."'";
        } elseif ($this->settings["pwcrypt"] == "md5") {
            $ret = "'".md5($passwd)."'";
        } else {
            //error
            $this->addError("Update User's Password", "Please provide a valid password encryption method in the configuration section");
        }
        return $ret;
    }

    /**
     * Database connection
     *
     * Make connection to the database server, select the right database and include the file
     * with database specific functions.
     * <code> self::dbinit(); </code>
     * @access private
     */
    private function db_init () {
        if ($this->settings["sql_type"] == "mysql") {
            $db = mysql_pconnect($this->settings["sql_server"], $this->settings["sql_user"], $this->settings["sql_pass"]) or die("Cannot connect to MySQL Server");
            mysql_select_db($this->settings["sql_dbase"], $db) or die("Database ".$this->settings["sql_dbase"]." cannot be selected");
            include("functions_mysql.php");
        } elseif ($this->settings["sql_type"] == "postgres") {
            $db = pg_pconnect("host=".$this->settings["sql_server"]." dbname=".$this->settings["sql_dbase"]." user=".$this->settings["sql_user"]." password=".$this->settings["sql_pass"]) or die("Cannot connect to PostgreSQL Server or cannot select database ".$this->settings["sql_dbase"]);
            include("functions_postgres.php");
        }
    }
    
    /**
     * PHP database support
     *
     * Check wether the needed database module is loaded as php module.
     * If not, try to load it now.
     * <code> self::load_sql($sql_type); </code>
     * @param string $sql_type database server type. "mysql" or "postgres"
     * @access private
     */
    private function load_sql ($sql_type) {
        if ($sql_type == "mysql") {
            // check for mysql module and try to load it when absent
            if (!extension_loaded("mysql")) {
                @dl("mysql");
            }
            // if by now it still isn't loaded we miss the module.
            if (!extension_loaded("mysql")) {
                $this->addError("Database Error", "MySQL support unavailable");
                exit();
            }
        } elseif($sql_type == "postgres") {
            // check for postgresql module and try to load it when absent
            if (!extension_loaded("pgsql")) {
                @dl("pgsql");
            }
            // if by now it still isn't loaded we miss the module.
            if (!extension_loaded("pgsql")) {
                $this->addError("Database Error", "PostgreSQL support unavailable");
                exit();
            }
        } else {
            // unsupported database type
            $this->addError("Database Error", "We dont support database system $sql_type");
            exit();
        }
    }

    /**
     * Load all the uids and usernames on the system.
     * <code> self::load_uids(); </code>
     * @return array uids as key and usernames as value.
     * @access private
     */
    private function load_uids() {
        $uids = array(65534=> 'nobody');
        return $uids;
    }

    /**
     * Load all the gids and groupnames on the system.
     * <code> self::load_gids(); </code>
     * @return array gids as key and groupnames as value.
     * @access private
     */
    private function load_gids() {
        $gids = array(65534 => 'nobody' );
        return $gids;
    }

    /**
     * Class constructor
     *
     * This function is called as soon as an instance of the class is created.
     * It will init the settings, connect to the database and load the uids and gids on the system.
     * <code> $instance = new pureuseradmin(); </code>
     * @access protected
     */
    function __construct() {
        /* global settings */
        $this->settings = require_once(dirname(__FILE__) . '/config.php');

        /* load uids*/
        $this->uids = self::load_uids();
        /* load gids */
        $this->gids = self::load_gids();
        /* load database library */
        self::load_sql($this->settings["sql_type"]);
        /* connect to database server and select database */
        self::db_init();
    }

    /**
     * Overwrite a predefined setting,
     * <code> $instance->changeSetting("setting", "value"); </code>
     * @param string $setting The setting to overwrite.
     * @param string $value The new value.
     * @access public
     */
    public function changeSetting ($setting, $value) {
        $this->settings[$setting] = $value;
    }
    
    /**
     * Retrieves a setting's value. From the config file.
     * <code> $instance->getSetting("version"); </code>
     * @param string $setting Name of the setting to return.
     * @return mixed Value found, or null if no such setting.
     * @access public
     */
    public function getSetting ($setting) {
        if(isset($this->settings[$setting])) {
            return $this->settings[$setting];
        }
        return null;
    }

    /**
     * Save a user in the database.
     * <code> $result = $instance->save_user($userinfo); </code>
     * @param array $userinfo
     * @return boolean true when success, false on error.
     * @access public
     */
    public function save_user ($userinfo) {
        if (!count($userinfo)) {
            return false;
            //error, $userinfo is an array with fields from edit form
        }
        
        // If $this->adminCompany is empty, then they're an admin and can add users to any company they like. If not,
        // they can only add users to the company to which they themselves belong.
        if (!empty($this->adminCompany)) {
            $userinfo["company"] = $this->adminCompany;
        }
        
        // update or insert ?
        if ($userinfo["update"]) {
            $sql = "UPDATE ".$this->settings["sql_table"]." SET ";
            $sql .= $this->settings["field_uid"]."=". (int) $userinfo["uid"];
            $sql .= ", ".$this->settings["field_gid"]."=". (int) $userinfo["gid"];
            $sql .= ", ".$this->settings["field_dir"]."='".$userinfo["dir"]."'";
            $sql .= ", ".$this->settings["field_email"]."='".$userinfo["email"]."'";
            $sql .= ", ".$this->settings["field_company"]."='".$userinfo["company"]."'";
            // are we going to reset the password ?
            if ($userinfo["password"]) {
                if ($userinfo["password"] == $userinfo["password1"]) {
                    $sql .= ", ".$this->settings["field_pass"]."=".self::mkpass($userinfo["password"]);
                }
            }
            $sql .= " WHERE ".$this->settings["field_id"]."='".$userinfo["id"]."'";
            if (!empty($this->adminCompany)) {
                $sql .= " AND ".$this->settings["field_company"]."='".$this->adminCompany."'";
            }
        } else {
            // check if name is already in DB.
            $sql  = "SELECT COUNT(*) FROM ".$this->settings["sql_table"]." WHERE ".$this->settings["field_user"]."='".$userinfo["username"]."'";
            $res = sql_query($sql);
            $aantal = sql_result($res,0);
            if ($aantal) {
                $this->addError("User Name In Use", "There is already a user with that name. Please choose another user name.");
                return false;
            } else {
                $sql = "INSERT INTO ".$this->settings["sql_table"]." (".$this->settings["field_user"].",".$this->settings["field_pass"].",".$this->settings["field_uid"].",".$this->settings["field_gid"].",".$this->settings["field_dir"]."," . $this->settings['field_email'] . "," . $this->settings['field_company'] . ") VALUES (";
                $sql .= "'".$userinfo["username"]."', ";
                $sql .= self::mkpass($userinfo["password"]).", ";
                $sql .= $userinfo["uid"].", ".$userinfo["gid"].", '".$userinfo["dir"]."', '";
                $sql .= $userinfo['email'] . "', '" . $userinfo['company'] . "'";
                $sql .= ")";
            }
        }
        //echo $sql;
        $res = sql_query($sql);
        if ($this->settings["notify_user"] && strlen($userinfo["email"])) {
            // send email
            $subject = $this->settings["ftp_hostname"]." FTP information";
            $body = "Hi ".$userinfo["username"].",\n\n";
            $body .= "Here is some information you will need to login with FTP:\n";
            $body .= "hostname: ".$this->settings["ftp_hostname"]."\n";
            $body .= "username: ".$userinfo["username"]."\n";
            $body .= "password: ".$userinfo["password"]."\n\n";
            $body .= "Please download and use an FTP client application (such as Filezilla) rather than using a browser to upload and download files\n Thanks\n";
            mail($userinfo["email"], $subject, $body, "From: ".$this->settings["admin_email"]."\r\n", "-f".$this->settings["admin_email"]);
        }
        umask(0);
        foreach(array('', '/in', '/out') as $dir) {
            if(!is_dir($userinfo['dir'] . $dir)) {
                if(!@mkdir($userinfo['dir'] . $dir)) {
                    $this->addError("File-System Error", "Could not create directory: '{$userinfo['dir']}$dir'");
                }
            }
        }
        return true;
    }

    /**
     * Delete a user from the database.
     * <code> $result = $instance->delete_user($userinfo); </code>
     * @param array $userinfo
     * @return boolean true when success, false on error.
     * @access public
     */
    public function delete_user($userinfo) {
        $sql = "DELETE FROM ".$this->settings["sql_table"]." WHERE ".$this->settings["field_id"]."='".$userinfo["id"]."'";
        // They can only delete users from their own company. Unless they're an admin.
        if (!empty($this->adminCompany)) {
            $sql .= " AND ".$this->settings["field_company"]."='".$this->adminCompany."'";
        }
        $res = sql_query($sql);
        @rmdir($userinfo['dir']); // will probably fail due to access issues.
        return true;
    }

    /**
     * Get a user from the database.
     * <code> $userlist = $instance->get_user($id); </code>
     * @param integer $id ID of the user.
     * @return array A user with all info that is in the database.
     * @access public
     */
    public function get_user($id) {
        $sql = "SELECT * FROM ".$this->settings["sql_table"]." WHERE ".$this->settings["field_id"]."='".intval($id)."'";
        // They can only see users from their own company. Unless they're an admin.
        if (!empty($this->adminCompany)) {
            $sql .= " AND ".$this->settings["field_company"]."='".$this->adminCompany."'";
        }
        $res = sql_query($sql);
        $userinfo = sql_fetch_assoc($res);
        return $userinfo;
    }

    /**
     * Get all users from the database, in alphabetic order.
     * <code> $userlist = $instance->get_all_users(); </code>
     * @param string $search Searchstring to limit results.
     * @param integer $start Record in database to start output.
     * @param integer $pagesize Number of users to show on a page.
     * @return array All users with all info that is in the database.
     * @access public
     */
    public function get_all_users($search = "", $start = 0, $pagesize = 0) {
        if (!$pagesize) { $pagesize = $this->settings["page_size"]; }
        if ($search) {
            $q = " WHERE (".$this->settings["field_user"]." LIKE '%$search%' OR ".$this->settings["field_dir"]." LIKE '%$search%')";
        } else {
            $q = "";
        }
        if (!empty($this->adminCompany)) {
            if (empty($q)) {
                $q .= " WHERE";
            }
            else {
                $q .= " AND";
            }
            $q .= " ".$this->settings["field_company"]." = '{$this->adminCompany}'";
        }
        $sql = "SELECT * FROM ".$this->settings["sql_table"]."$q ORDER BY ".$this->settings["field_company"].", ".$this->settings["field_user"]." LIMIT $start, $pagesize";
        $res = sql_query($sql);
        $users = Array();
        while ($row = sql_fetch_assoc($res)) {
            $users[] = $row;
        }
        return $users;
    }
    

    /**
     * Get number of users in the database.
     * <code> $nr_users = $instance->get_nr_users(); </code>
     * @param string $search Searchstring to limit results.
     * @return integer Number of users in the database.
     * @access public
     */
    public function get_nr_users($search = "") {
        if ($search) {
            $q = " WHERE (".$this->settings["field_user"]." LIKE '%$search%' OR ".$this->settings["field_dir"]." LIKE '%$search%')";
        } else {
            $q = "";
        }
        if (!empty($this->adminCompany)) {
            if (empty($q)) {
                $q .= " WHERE";
            }
            else {
                $q .= " AND";
            }
            $q .= " ".$this->settings["field_company"]." = '{$this->adminCompany}'";
        }
        $sql = "SELECT COUNT(*) FROM ".$this->settings["sql_table"]."$q";
        $res = sql_query($sql);
        $count = sql_result($res,0);
        return $count;
    }

    /**
     * Check what type of access the user has.
     * <code> $permission = $instance->check_access("/home/test",1001,1001); </code>
     * @param string $homedir The home directory of the user processed.
     * @param int $uid The main userid of the user.
     * @param int $gid The main groupid of the user.
     * @return array owner,group,world octal permission and read and write flag.
     * @access public
     */
    public function check_access ($homedir, $uid, $gid) {
        if (file_exists($homedir)) {
            $fuid = fileowner($homedir);
            $fgid = filegroup($homedir);
            $fperms = fileperms($homedir);
            $fperm = substr(sprintf("%o",$fperms),2);
            $rights["owner"] = substr($fperm,0,1);
            $rights["group"] = substr($fperm,1,1);
            $rights["world"] = substr($fperm,2,1);
            $rights["read"] = 0;
            $rights["write"] = 0;
            if ($rights["world"] > 6) { $rights["write"] = 1; }
            if ($rights["world"] > 4) { $rights["read"] = 1; }
            if ($uid == $fuid) {
                if ($rights["owner"] > 6) { $rights["write"] = 1; }
                if ($rights["owner"] > 4) { $rights["read"] = 1; }
            }
            if ($gid == $fgid) {
                if ($rights["group"] > 6) { $rights["write"] = 1; }
                if ($rights["group"] > 4) { $rights["read"] = 1; }
            }
        } else {
            $rights["error"] = "No such directory";
        }
        return $rights;
    }
    
    /**
     * Adds error messages to the instance for later retrieval.
     * <code> $instance->addError("File System Error", "Cannot create directory."); </code>
     * @param string $title Title of error message.
     * @param string $body Main error message text.
     */
    public function addError ($title, $body) {
        if (empty($this->errors[$title])) {
            $this->errors[$title] = array();
        }
        $this->errors[$title][] = $body;
    }
}

?>
