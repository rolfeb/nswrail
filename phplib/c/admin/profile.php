<?php
/**
 * Copyright (c) 2018 Rolfe Bozier
 */

require 'site.inc';

/**
 * @param mysqli $db
 * @param $referer
 * @throws InternalError
 */
function show_profile($db, $referer)
{
    global $user;

    $title = 'Update Profile';
    $tp = [
        'title' => $title,
     ];

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            *
        from
            r_user
        where
            uid = ?
    ");

    $stmt->bind_param("i", $user->uid);
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
        $tp['orig_referer'] = $referer;
    } else {
        throw new InternalError("Missing profile data [$user->uid]");
    }

    $stmt->close();

    normal_page('admin-profile.latte', $tp);
}

/**
 * @param mysqli $db
 * @throws InternalError
 * @throws SecurityError
 */
function update_profile($db)
{
    global $user;

    $uid = param_post_integer('uid');
    # $username = param_post_string('username');
    $fullname = param_post_string('fullname');
    $password1 = param_post_string('password1');
    $password2 = param_post_string('password2');
    $orig_referer = param_post_string('orig_referer');

    if ($uid != $user->uid
            # || strlen($username) < 5
            # || strpos($username, "@") == false
            || strlen($fullname) < 5
            || ($password1 != '' && strlen($password1) < 6)
            || ($password2 != '' && strlen($password2) < 6)
            || $password1 != $password2)
            {
        throw new InternalError('Malformed profile update request');
    }

    if ($password1 != '') {
        $enc_password = password_hash($password1, PASSWORD_DEFAULT);
        $extra = ", password = ?";
    }
    else {
        $extra = "";
    }

    $stmt = $db->stmt_init();
    /** @noinspection SyntaxError */
    $stmt->prepare("
        update
            r_user
        set
             fullname = ? $extra
        where
            uid = ?
    ");

    if ($password1 != '') {
        $stmt->bind_param("ssi", $fullname, $enc_password, $uid);
    } else {
        $stmt->bind_param("si",$fullname, $uid);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: $orig_referer");
}

/** @var mysqli $db */
global $db;

/** @var User $user */
global $user;

if ($user->status != 0) {
    noperm_page();
}

try {
    $action = param_post_string_opt('action');

    if ($action == '') {
        $referer = $_SERVER["HTTP_REFERER"];
        if (!isset($referer)) {
            $referer = get_config('website-url');
        }
        show_profile($db, $referer);
    } else {
        if ($action == 'Cancel') {
            $orig_referer = param_post_string_opt('orig_referer');
            if ($referer == '') {
                $referer = get_config('website-url');
            }
            header("Location: $referer");
            exit();
        }
        update_profile($db);
    }

} catch (Exception $e) {
    report_error($e, "/c/admin/profile.php");
}
