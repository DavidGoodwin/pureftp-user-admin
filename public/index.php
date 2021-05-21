<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

$settings = require_once(dirname(__FILE__) . '/../config.php');


$pdo = new \PDO($settings['database_dsn'], $settings['database_user'], $settings['database_pass']);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


$database = new \PureFTPAdmin\Database($pdo);
$model = new \PureFTPAdmin\UserAdmin($database, $settings);
$flash = new \PureFTPAdmin\Flash();

$_REQUEST['action'] = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'welcome';


if (in_array($_REQUEST['action'], ['edit_user', 'new_user'])) {

    $what = 'New User';
    $user = [];

    if (isset($_REQUEST['username'])) {
        $what = 'Edit User';
        $user = $model->getUserByUsername($_REQUEST['username']);
    }

    $template = new \PureFTPAdmin\Template($what);

    $form = new PureFTPAdmin\Form\User();

    $form->setGidList($model->getGidList());
    $form->setUidList($model->getUidList());
    $form->isValid($user);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                    $model->sendPostCreationEmail($with_password);
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

if ($_REQUEST['action'] == 'delete_user' &&
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['username'])) {

    if ($model->deleteUser($_POST['username'])) {
        $flash->info('Deleted user');
    }
}

if ($_REQUEST['action'] == 'welcome') {
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

$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

$search = isset($_GET['q']) ? $_GET['q'] : '';

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
