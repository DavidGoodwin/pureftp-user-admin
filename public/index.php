<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

$settings = require_once(dirname(__FILE__) . '/../config.php');


$pdo = new \PDO($settings['database_dsn'], $settings['database_user'], $settings['database_pass']);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


$database = new \PureFTPAdmin\Database($pdo);
$model = new \PureFTPAdmin\UserAdmin($database, $settings);
$flash = new \PureFTPAdmin\Flash();

$action = isset($_REQUEST['action']) && is_string($_REQUEST['action']) ? $_REQUEST['action'] : 'welcome';

$allowable_actions = ['welcome', 'delete_user', 'edit_user', 'new_user'];

if (!in_array($action, $allowable_actions)) {
    $action = 'welcome';
}

if (in_array($action, ['edit_user', 'new_user'])) {

    $what = 'New User';
    $user = [];
    $is_new = true;

    $username = $_REQUEST['username'] ?? null;
    if ($username !== null && is_string($username)) {
        $what = 'Edit User';
        $user = $model->getUserByUsername($username);
        if (!empty($user)) {
            $is_new = false;
        }
    }

    $template = new \PureFTPAdmin\Template($what);

    $form = new PureFTPAdmin\Form\User([], $is_new);

    $form->setGidList($model->getGidList());
    $form->setUidList($model->getUidList());
    $form->isValid($user);

    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($form->isValid($_POST)) {
            //error_log("Valid form");
            $values = $form->getValues();
            if ($model->saveUser($values)) {
                //error_log("saved user" . json_encode($values));
                $flash->info("User saved");
                if ($what == 'New User' && $settings['notify_user']) {
                    $flash->info("User emailed");
                    $with_password = $values;
                    $with_password['password'] = $_POST['password'];
                    $success = $model->sendPostCreationEmail($with_password);
                    if(!$success) {
                        $flash->error("Email may not have been sent.");
                    }
                }
                $form = new PureFTPAdmin\Form\User();
            }
        }
    }

    $template->assign('form', $form);
    $template->assign('messages', $flash->getMessages());

    echo $template->display('user.twig');
    exit(0);
}

if ($action == 'delete_user' && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && is_string($_POST['username'])) {

    if ($model->deleteUser($_POST['username'])) {
        $flash->info('Deleted user');
    }
}

if ($action == 'welcome') {
    $template = new \PureFTPAdmin\Template('Welcome');

    foreach ($settings as $key => $value) {
        $template->assign("settings_" . $key, $value);
    }

    $template->assign('settings_check_access', 'No');
    $template->assign('settings_email_user', 'No');
    if ($settings['check_access']) {
        $template->assign('settings_check_access', 'Yes');
    }
    if ($settings['notify_user']) {
        $template->assign('settings_notify_user', 'Yes');
    }
    $template->assign('messages', $flash->getMessages());

    echo $template->display('welcome.twig');
    exit(0);
}


// fall through to a list-users.

$start = 0;
if (isset($_GET['start']) && is_numeric($_GET['start'])) {
    $start = (int)$_GET['start'];
}

$search = '';
if (isset($_GET['q']) && is_string($_GET['q'])) {
    $search = $_GET['q'];
}

$list = $model->getAllUsers($search, $start, 500);

if ($settings['check_access']) {
    foreach ($list as $k => $row) {
        $list[$k]['rights'] = $model->check_access($row['dir'], $row['uid'], $row['gid']);
    }
}

$template = new \PureFTPAdmin\Template('User List');

if (empty($list)) {
    header("Location: ?action=welcome");
    exit(0);
}

$template->assign('users', $list);
$template->assign('start', $start);
$template->assign('page_size', 50);
$template->assign('totalResults', $model->get_nr_users($search));

$template->assign('messages', $flash->getMessages());

echo $template->display('user_list.twig');

exit(0);


/*

if ($a->settings["check_access"]) {
    $user_rights = $a->check_access($user["dir"], $user["uid"], $user["gid"]);
    if ($user_rights["error"]) {
        $right = $user_rights["error"];
    } else {
        if ($user_rights["write"]) {
            $right = "user can read and write files in homedir";
        } elseif ($user_rights["read"]) {
            $right = "user can only read files in homedir";
        } else {
            $right = "<font color=\"red\">user has no access to homedir</font>";
        }
    }
}
     */
