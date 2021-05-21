<?php

namespace PureFTPAdmin;

/**
 * Manage virtual users for PureFTPd.
 * @link  https://github.com/DavidGoodwin/pureftp-user-admin
 */
class UserAdmin
{

    const DEBUG = 1;
    /**
     * This variable is filled in the constructor. It can be changed with a public function.
     * @var array all settings needed
     * @access public
     */
    protected $settings;

    /**
     * @var Database $database
     */
    private $database;

    /**
     * Class constructor
     *
     * This function is called as soon as an instance of the class is created.
     * It will init the settings, connect to the database and load the uids and gids on the system.
     * <code> $instance = new pureuseradmin(); </code>
     * @access protected
     */
    public function __construct(Database $database, array $settings)
    {
        $this->database = $database;
        $this->settings = $settings;

    }

    /**
     * Hash a plaintext password.
     * @param string $passwd The password to insert into the database.
     * @return string The string to use in the sql statement.
     */
    private function mkpass(string $passwd) : string
    {
        $mode = $this->settings["pwcrypt"];

        // https://download.pureftpd.org/pub/pure-ftpd/doc/README.MySQL
        // md5, sha1 shouldn't really be used - use crypt if you have a choice.
        if ($mode == "crypt") {
            $salt = uniqid(); /* not all that good */
            $ret = crypt($passwd, $salt);
        } elseif ($mode == "cleartext") {
            $ret = $passwd;
        } elseif ($mode == "md5") {
            $ret = md5($passwd);
        } elseif ($mode == "sha1") {
            $ret = sha1($passwd);
        } else {
            //error
            throw new \Exception("Please provide a valid password encryption (pwcrypt) method in the configuration section (crypt, sha1, md5, cleartext)");
        }
        return $ret;
    }


    /**
     * Load all the uids and usernames on the system.
     * <code> self::load_uids(); </code>
     * @return array uids as key and usernames as value.
     */
    public function getUidList() : array
    {
        $uids = [
            $this->settings['default_uid'] => 'default'
        ];
        return $uids;
    }

    /**
     * Load all the gids and groupnames on the system.
     * <code> self::load_gids(); </code>
     * @return array gids as key and groupnames as value.
     */
    public function getGidList() : array
    {
        $gids = [
            $this->settings['default_gid'] => 'default'
        ];

        return $gids;
    }

    /**
     * Save a user in the database.
     * @param array $userinfo
     * @return boolean true when success, false on error.
     */
    public function saveUser(array $userinfo) : bool
    {
        if (!count($userinfo)) {
            return false;
            //error, $userinfo is an array with fields from edit form
        }

        if(!isset($userinfo['username'])) {
            throw new \InvalidArgumentException("username required");
        }
        $uid_field = $this->settings['field_uid'];
        $gid_field = $this->settings['field_gid'];
        $dir_field = $this->settings['field_dir'];
        $email_field = $this->settings['field_email'];
        $username_field = $this->settings['field_user'];
        $password_field = $this->settings['field_pass'];

        $args = [];
        $existing = null;

        if (!empty($userinfo['username'])) {
            $existing = $this->getUserByUsername($userinfo['username']);
        }

        $password_stuff ='';
        if (!empty($userinfo["password"])) {
                $password_stuff = ", {$password_field} = :password ";
                $args['password'] = $this->mkpass($userinfo['password']);
        }

        if(!empty($existing)) {
            $sql = <<<SQL
UPDATE {$this->settings['sql_table']} SET 
    {$uid_field} = :uid,
    {$gid_field} = :gid,
    {$dir_field} = :dir,
    {$email_field} = :email
    $password_stuff WHERE {$username_field} = :username
SQL;
        } else {
            if(!isset($userinfo['password'])) {
                throw new \InvalidArgumentException("password required");
            }
            // no existing record; insert
            $sql = <<<SQL
INSERT INTO {$this->settings['sql_table']} ({$uid_field}, {$gid_field}, {$dir_field}, {$email_field}, {$username_field}, {$password_field} )
VALUES (:uid, :gid, :dir, :email, :username, :password)
SQL;
            $args['password'] = $this->mkpass($userinfo['password']);
        }

        $args['uid'] = $userinfo['uid'];
        $args['gid'] = $userinfo['gid'];
        $args['dir'] = $userinfo['dir'];
        $args['email'] = $userinfo['email'];
        $args['username'] = $userinfo['username'];

        return $this->database->update($sql, $args) == 1;

    }


    /**
     * @return bool
     * @param array $userinfo 
     */
    public function sendPostCreationEmail(array $userinfo) : bool
    {
        if ($this->settings["notify_user"] && strlen($userinfo["email"])) {
            // send email
            $subject = $this->settings["ftp_hostname"] . " FTP information";
            $body = "Hi " . $userinfo["username"] . ",\n\n";
            $body .= "Here is some information you will need to login with FTP:\n";
            $body .= "hostname: " . $this->settings["ftp_hostname"] . "\n";
            $body .= "username: " . $userinfo["username"] . "\n";
            $body .= "password: " . $userinfo["password"] . "\n\n";
            $body .= "Please download and use an FTP client application (such as Filezilla) rather than using a browser to upload and download files\n Thanks\n";
            mail($userinfo["email"], $subject, $body, "From: " . $this->settings["admin_email"] . "\r\n", "-f" . $this->settings["admin_email"]);
        }
        return true;
    }

    /**
     * Delete a user from the database.
     * <code> $result = $instance->delete_user($userinfo); </code>
     * @param string $username
     * @return boolean true when success, false on error.
     */
    public function deleteUser($username) : bool
    {
        $sql = "DELETE FROM {$this->settings['sql_table']} WHERE {$this->settings['field_user']} = :username";
        return (bool) $this->database->update($sql, ['username' => $username]);
    }

    /**
     * Get a user from the database.
     * <code> $user = $instance->getUserByUsername($username); </code>
     * @param string $username
     * @return array A user with all info that is in the database; empty if user does not exist.
     */
    public function getUserByUsername(string $username) : array
    {
        $sql = "SELECT * FROM {$this->settings['sql_table']} WHERE {$this->settings['field_user']} = :username";

        $row = $this->database->selectOne($sql, ['username' => $username]);

        if(empty($row) || !is_array($row)) {
            return [];
        }
        return $this->remapFromDb($row);
    }

    /**
     * @return array
     */
    private function remapFromDb(array $row) : array {

        if(empty($row)) {
            return [];
        }
        $field_username = $this->settings['field_user'];
        $field_uid = $this->settings['field_uid'];
        $field_gid = $this->settings['field_gid'];
        $field_email = $this->settings['field_email'];
        $field_dir = $this->settings['field_dir'];

        return [
            'username' => $row[$field_username],
            'uid' => $row[$field_uid],
            'gid' => $row[$field_gid],
            'dir' => $row[$field_dir],
            'email' => $row[$field_email],
        ];

    }

    /**
     * Get all users from the database, in alphabetic order.
     * <code> $userlist = $instance->getAllUsers(); </code>
     * @param string $search Searchstring to limit results.
     * @param integer $start Record in database to start output.
     * @param integer $pagesize Number of users to show on a page.
     * @return array All users with all info that is in the database.
     */
    public function getAllUsers(string $search = "", int $start = 0, int $pagesize = 0) : array
    {
        if (!$pagesize) {
            $pagesize = $this->settings["page_size"];
        }

        if ($search) {
            $q = " WHERE {$this->settings["field_user"]} LIKE :search OR {$this->settings["field_dir"]} LIKE :search";
        } else {
            $q = "";
        }
        $sql = "SELECT * FROM {$this->settings["sql_table"]} $q ORDER BY {$this->settings["field_user"]} LIMIT $start, $pagesize";

        $search = "$search%";

        $rows = $this->database->select($sql, ['search' => $search]);
        $users = [];
        foreach($rows as $row) {
            $users[] = $this->remapFromDb($row);
        }
        return $users;
    }


    /**
     * Get number of users in the database.
     * <code> $nr_users = $instance->get_nr_users(); </code>
     * @param string $search Searchstring to limit results.
     * @return integer Number of users in the database.
     */
    public function get_nr_users(string $search = "") : int
    {
        if ($search) {
            $q = " WHERE {$this->settings["field_user"]} LIKE :search OR {$this->settings["field_dir"]} LIKE :search";
        } else {
            $q = "";
        }
        $sql = "SELECT COUNT(*) as count FROM {$this->settings["sql_table"]} $q";

        $search = "%$search%";

        $count = $this->database->selectOne($sql, ['search' => $search]);
        if(is_array($count) && isset($count['count'])) {
            return $count['count'];
        }
        return 0;
    }


    /**
     * Check what type of access the user has.
     * @param string $homedir The home directory of the user processed.
     * @param int $uid The main userid of the user.
     * @param int $gid The main groupid of the user.
     * @return string read/write/none
     */
    public function check_access(string $homedir, int $uid, int $gid) : string
    {
        $rights = ['error' => false, 'write' => false, 'read' => false];

        if (file_exists($homedir)) {
            $fuid = fileowner($homedir);
            $fgid = filegroup($homedir);
            $fperms = fileperms($homedir);
            $fperm = substr(sprintf("%o", $fperms), 2);
            $rights["owner"] = substr($fperm, 0, 1);
            $rights["group"] = substr($fperm, 1, 1);
            $rights["world"] = substr($fperm, 2, 1);
            $rights["read"] = 0;
            $rights["write"] = 0;
            if ($rights["world"] > 6) {
                $rights["write"] = 1;
            }
            if ($rights["world"] > 4) {
                $rights["read"] = 1;
            }
            if ($uid == $fuid) {
                if ($rights["owner"] > 6) {
                    $rights["write"] = 1;
                }
                if ($rights["owner"] > 4) {
                    $rights["read"] = 1;
                }
            }
            if ($gid == $fgid) {
                if ($rights["group"] > 6) {
                    $rights["write"] = 1;
                }
                if ($rights["group"] > 4) {
                    $rights["read"] = 1;
                }
            }
        } else {
            $rights["error"] = "Error: Dir Path not found";
        }

        if($rights['write']) {
            return 'write';
        }
        if($rights['read']) {
            return 'read';
        }
        if($rights['error']) {
            return $rights['error'];
        }
        return 'none';
    }
}
