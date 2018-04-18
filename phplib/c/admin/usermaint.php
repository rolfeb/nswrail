<?php

require 'site.inc';

# XXX: move to User class
$map_role_flag_to_string = [
    User::R_EDITOR      => 'editor',
    User::R_MODERATOR   => 'moderator',
    User::R_SUPERUSER   => 'superuser',
];

$map_status_flag_to_string = [
    User::S_UNCONFIRMED => 'unconfirmed',
    User::S_SUSPENDED   => 'suspended',
    User::S_PWDEXPIRED  => 'pwdexpired',
    User::S_PWDLOCKED   => 'pwdlocked',
];

/**
 * @param $role
 * @return array
 */
function role_as_str_array($role)
{
    $roles = [];
    if ($role & User::R_EDITOR) {
        $roles[] = 'editor';
    }
    if ($role & User::R_MODERATOR) {
        $roles[] = 'moderator';
    }
    if ($role & User::R_SUPERUSER) {
        $roles[] = 'superuser';
    }
    return $roles;
}

/**
 * @param $status
 * @return array
 */
function status_as_str_array($status)
{
    $statuses = [];
    if ($status & User::S_UNCONFIRMED) {
        $statuses[] = 'unconfirmed';
    }
    if ($status & User::S_SUSPENDED) {
        $statuses[] = 'suspended';
    }
    if ($status & User::S_PWDEXPIRED) {
        $statuses[] = 'pwdexpired';
    }
    if ($status & User::S_PWDLOCKED) {
        $statuses[] = 'pwdlocked';
    }
    return $statuses;
}

/**
 * @param mysqli $db
 */
function show_user_listing($db)
{
    global $db;

    $title = 'User Listing';

    $tp = [
        'title' => $title,
        'users' => [],
    ];

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            *
        from
            r_user
        order by
            uid
    ");

    $stmt->execute();
    $r = dbi_bind_to_array($stmt);

    while ($stmt->fetch()) {
        $roles = role_as_str_array($r['role']);
        $is_superuser = ($r['role'] & User::R_SUPERUSER) != 0;
        $rolestr = join('<br>', $roles);
        if (!$rolestr) {
            $rolestr = '-';
        }

        $status = status_as_str_array($r['status']);
        $statusstr = join('<br>', $status);
        if (!$statusstr) {
            $statusstr = '-';
        }

        $tp['users'][] = [
            'rowclass' => $is_superuser ? 'table-info' : '',
            'uid' => $r['uid'],
            'username' => $r['username'],
            'fullname' => $r['fullname'],
            'ne_role' => $rolestr,
            'ne_status' => $statusstr,
            'joined' => $r['register_time'],
            'login' => $r['last_login_time'],
        ];
    }
    $stmt->close();

    normal_page('admin-user-listing.latte', $tp);
}

/**
 * @param mysqli $db
 */
function show_user_modify_screen($db)
{
    global $db;

    $title = 'Modify User Details';
    $tp = [
        'title' => $title,
        'roles' => [],
        'statuses' => [],
    ];

    $uid = param_get_integer('uid');

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            *
        from
            r_user
        where
            uid = ?
    ");

    $stmt->bind_param("i", $uid); 
    $stmt->execute();
    $r = dbi_bind_to_array($stmt);

    if ($stmt->fetch()) {
        $tp['uid'] = $r['uid'];
        $tp['username'] = $r['username'];
        $tp['fullname'] = $r['fullname'];
        $tp['register_time'] = $r['register_time'];
        $tp['register_addr'] = $r['register_addr'];
        $tp['last_login_time'] = $r['last_login_time'];
        $tp['last_login_addr'] = $r['last_login_addr'];

        $roles = [
            User::R_EDITOR      => 'editor',
            User::R_MODERATOR   => 'moderator',
            User::R_SUPERUSER   => 'superuser',
        ];
        foreach ($roles as $f => $s) {
            $tp['roles'][] = [
                'name' => "ROLE-$s",
                'value' => $s,
                'checked' => (($r['role'] & $f) != 0) ? 'checked' : '',
            ];
        }

        $statuses = [
            User::S_UNCONFIRMED => 'unconfirmed',
            User::S_SUSPENDED   => 'suspended',
            User::S_PWDEXPIRED  => 'pwdexpired',
            User::S_PWDLOCKED   => 'pwdlocked',
        ];
        foreach ($statuses as $f => $s) {
            $tp['statuses'][] = [
                'name' => "STATUS-$s",
                'value' => $s,
                'checked' => (($r['status'] & $f) != 0) ? 'checked' : '',
            ];
        }

    } else {
        $tp['error_text'] = "Invalid user ID: $uid";
    }

    $stmt->close();

    normal_page('admin-user-modify.latte', $tp);
}

/**
 *
 */
function show_user_add_screen()
{
    $title = 'New User Details';

    $tp = [
        'title' => $title,
        'roles' => [],
        'statuses' => [],
    ];

    $roles = [
        User::R_EDITOR      => 'editor',
        User::R_MODERATOR   => 'moderator',
        User::R_SUPERUSER   => 'superuser',
    ];
    foreach ($roles as $f => $s) {
        $tp['roles'][] = [
            'name' => "ROLE-$s",
            'value' => $s,
        ];
    }

    $statuses = [
        User::S_UNCONFIRMED => 'unconfirmed',
        User::S_SUSPENDED   => 'suspended',
        User::S_PWDEXPIRED  => 'pwdexpired',
        User::S_PWDLOCKED   => 'pwdlocked',
    ];
    foreach ($statuses as $f => $s) {
        $tp['statuses'][] = [
            'name' => "STATUS-$s",
            'value' => $s,
        ];
    }

    normal_page('admin-user-add.latte', $tp);
}

/**
 * @param mysqli $db
 * @throws InternalError
 */
function update_user_details($db)
{
    global $db;
    global $map_role_flag_to_string;
    global $map_status_flag_to_string;

    $uid = param_post_integer('uid');
    $username = param_post_string('username');
    $fullname = param_post_string('fullname');
    $password1 = param_post_string('password1');
    $password2 = param_post_string('password2');

    if (strlen($username) < 5
            || strpos($username, "@") == false
            || strlen($fullname) < 5
            || ($password1 != '' && strlen($password1) < 6)
            || ($password2 != '' && strlen($password2) < 6)
            || $password1 != $password2)
            {
        throw new InternalError('Malformed user creation request');
    }

    $role = 0;
    foreach ($map_role_flag_to_string as $f => $s) {
        if (param_post_string_opt("ROLE-$s") != '') {
            $role |= $f;
        }
    }
    $status = 0;
    foreach ($map_status_flag_to_string as $f => $s) {
        if (param_post_string_opt("STATUS-$s") != '') {
            $status |= $f;
        }
    }

    if ($password1 != '') {
        $enc_password = password_hash($password1, PASSWORD_DEFAULT);
        $extra = "password = ?, ";
    }
    else {
        $extra = "";
    }

    $stmt = $db->stmt_init();
    $stmt->prepare("
        update
            r_user
        set
            username = ?,
            fullname = ?, $extra
            role = ?,
            status = ?
        where
            uid = ?
    ");

    if ($password1 != '') {
        $stmt->bind_param("sssiii", $username, $fullname, $enc_password, $role, $status, $uid);
    } else {
        $stmt->bind_param("ssiii", $username, $fullname, $role, $status, $uid);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: usermaint.php");
}

/**
 * @param mysqli $db
 * @throws InternalError
 * @throws UserError
 */
function create_user_details($db)
{
    global $db;
    global $map_role_flag_to_string;
    global $map_status_flag_to_string;

    $username = param_post_string_opt('username');
    $fullname = param_post_string_opt('fullname');
    $password1 = param_post_string_opt('password1');
    $password2 = param_post_string_opt('password2');

    if (strlen($username) < 5
            || strpos($username, "@") == false
            || strlen($fullname) < 5
            || strlen($password1) < 6
            || strlen($password2) < 6
            || $password1 != $password2) {
        throw new InternalError('Malformed user creation request');
    }

    // check for an existing entry
    if (User::email_address_in_use($username)) {
        throw new UserError('Email address is already in use');
    }

    $role = 0;
    foreach ($map_role_flag_to_string as $f => $s) {
        if (param_post_string_opt("ROLE-$s") != '') {
            $role |= $f;
        }
    }
    $status = 0;
    foreach ($map_status_flag_to_string as $f => $s) {
        if (param_post_string_opt("STATUS-$s") != '') {
            $status |= $f;
        }
    }

    $enc_password = password_hash($password1, PASSWORD_DEFAULT);

    $stmt = $db->stmt_init();
    $stmt->prepare('
        insert into
            r_user
            (
                username,
                fullname,
                password,
                role,
                status,
                register_time,
                register_addr
            )
        values(?, ?, ?, ?, ?, sysdate(), ?)
    ');

    $addr = "127.0.0.1";
    $stmt->bind_param("sssiis", $username, $fullname, $enc_password, $role, $status, $addr);

    $stmt->execute();
    $stmt->close();

    header("Location: usermaint.php");
}

/** @var mysqli $db */
global $db;

/** @var User $user */
global $user;

if (!$user->is_superuser()) {
    noperm_page();
}

try {
    $mode = param_get_string_opt('mode');
    if ($mode == '') {
        $mode = param_post_string_opt('mode');
    }
    if ($mode != '') {
        if ($mode == 'add') {
            $action = param_post_string_opt('action');
            if ($action == '') {
                show_user_add_screen();
            } else {
                if ($action == 'Cancel') {
                    header("Location: usermaint.php");
                    exit();
                }
                create_user_details($db);
            }
        } elseif ($mode == 'modify') {
            $action = param_post_string_opt('action');
            if ($action == '') {
                show_user_modify_screen($db);
            } else {
                if ($action == 'Cancel') {
                    header("Location: usermaint.php");
                    exit();
                }
                update_user_details($db);
            }
        }
    } else {
        show_user_listing($db);
    }
} catch (Exception $e) {
    report_error($e, "/c/admin/usermaint.php");
}
