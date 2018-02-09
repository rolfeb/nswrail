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

function show_user_listing()
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
    ")
        or dbi_error_trace("prepare failed");

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
            'role' => $rolestr,
            'status' => $statusstr,
            'joined' => $r['register_time'],
            'login' => $r['last_login_time'],
        ];
    }
    $stmt->close();

    $latte = new Latte\Engine;
    display_page($title, $latte->renderToString('admin-user-listing.latte', $tp));
}

function show_user_modify_screen()
{
    global $db;

    $title = 'Modify User Details';
    $tp = [
        'title' => $title,
        'roles' => [],
        'statuses' => [],
    ];

    $uid = quote_external($_REQUEST['uid']);

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            *
        from
            r_user
        where
            uid = ?
    ")
        or dbi_error_trace("prepare failed");

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

    $latte = new Latte\Engine;
    display_page($title, $latte->renderToString('admin-user-modify.latte', $tp));
}

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

    $latte = new Latte\Engine;
    display_page($title, $latte->renderToString('admin-user-add.latte', $tp));
}

function update_user_details()
{
    global $db;
    global $map_role_flag_to_string;
    global $map_status_flag_to_string;

    $uid = $_REQUEST['uid'];
    $username = $_REQUEST['username'];
    $fullname = $_REQUEST['fullname'];
    $password1 = $_REQUEST['password1'];
    $password2 = $_REQUEST['password2'];

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
        if (isset($_REQUEST["ROLE-$s"])) {
            $role |= $f;
        }
    }
    $status = 0;
    foreach ($map_status_flag_to_string as $f => $s) {
        if (isset($_REQUEST["STATUS-$s"])) {
            $status |= $f;
        }
    }

    if ($password != '') {
        $enc_password = password_hash($password, PASSWORD_DEFAULT);
        $extra = "password = ?, ";
    }
    else {
        $extra = "";
    }

    $stmt = $db->stmt_init();
    if (!$stmt->prepare("
        update
            r_user
        set
            username = ?,
            fullname = ?, $extra
            role = ?,
            status = ?
        where
            uid = ?
    ")) {
        throw new InternalError('prepare failed: ' . $stmt->error);
    }

    if ($password1 != '') {
        $stmt->bind_param("sssiii", $username, $fullname, $enc_password, $role, $status, $uid);
    } else {
        $stmt->bind_param("ssiii", $username, $fullname, $role, $status, $uid);
    }

    if (!$stmt->execute()) {
        throw new InternalError('execute failed: ' . $stmt->error);
    }
    $stmt->close();

    header("Location: usermaint.php");
}

function create_user_details()
{
    global $db;
    global $map_role_flag_to_string;
    global $map_status_flag_to_string;

    $username = $_REQUEST['username'];
    $fullname = $_REQUEST['fullname'];
    $password1 = $_REQUEST['password1'];
    $password2 = $_REQUEST['password2'];

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
        if (isset($_REQUEST["ROLE-$s"])) {
            $role |= $f;
        }
    }
    $status = 0;
    foreach ($map_status_flag_to_string as $f => $s) {
        if (isset($_REQUEST["STATUS-$s"])) {
            $status |= $f;
        }
    }

    $enc_password = password_hash($password1, PASSWORD_DEFAULT);

    $stmt = $db->stmt_init();
    if (!$stmt->prepare('
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
    ')) {
        throw new InternalError('prepare failed: ' . $stmt->error);
    }

    $addr = "127.0.0.1";
    $stmt->bind_param("sssiis", $username, $fullname, $enc_password, $role, $status, $addr);

    if (!$stmt->execute()) {
        throw new InternalError('execute failed: ' . $stmt->error);
    }
    $stmt->close();

    header("Location: usermaint.php");
}

if (!$user->is_superuser()) {
    noperm_page();
}

try {
    if (isset($_REQUEST['mode'])) {
        $mode = $_REQUEST['mode'];

        if ($mode == 'add') {
            if (!isset($_REQUEST['action'])) {
                show_user_add_screen();
            } else {
                if ($_REQUEST['action'] == 'Cancel') {
                    header("Location: usermaint.php");
                    exit();
                }
                create_user_details();
            }
        } elseif ($mode == 'modify') {
            if (!isset($_REQUEST['action'])) {
                show_user_modify_screen();
            } else {
                if ($_REQUEST['action'] == 'Cancel') {
                    header("Location: usermaint.php");
                    exit();
                }
                update_user_details();
            }
        }
    } else {
        show_user_listing();
    }
} catch (Exception $e) {
    report_error($e, "/c/admin/usermaint.php");
}

?>
