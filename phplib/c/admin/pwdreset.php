<?php
/**
 * Copyright (c) 2019 Rolfe Bozier
 */

require 'site.inc';

/**
 * Display the form for the person to specify their new password.
 *
 * @param mysqli $db
 * @throws InternalError
 * @throws SecurityError
 * @throws UserError
 */
function show_form($db)
{
    $reset_id = param_get_string('id');

    $tp = [];

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            *,
            if(pwdreset_timestamp < SUBDATE(NOW(), INTERVAL 1 DAY), 0, 1) as valid
        from
            r_user
        where
            pwdreset_code = ?
      ");

    $stmt->bind_param("i", $reset_id);
    $stmt->execute();
    $r = dbi_bind_to_array($stmt);

    if ($stmt->fetch()) {
        if ($r['valid'] == 0) {
            throw new UserError("Password reset request has expired");
        }

        $tp['uid'] = $r['uid'];
        $tp['username'] = $r['username'];
        $tp['fullname'] = $r['fullname'];
        $tp['reset_id'] = $reset_id;
    } else {
        throw new InternalError("Invalid password reset request");
    }

    $stmt->close();

    normal_page('admin-pwdreset.latte', $tp);
}

/**
 * Save the encrypted password in the database.
 *
 * @param $referer
 * @throws InternalError
 * @throws SecurityError
 * @throws UserError
 */
function update_password($referer)
{
    $reset_id = param_post_string('reset_id');
    $password1 = param_post_string('password1');
    $password2 = param_post_string('password2');

    if (strlen($password1) < 6 || strlen($password2) < 6 || $password1 != $password2) {
        throw new InternalError('Malformed password update request');
    }

    $enc_password = password_hash($password1, PASSWORD_DEFAULT);

    $success = User::set_password_via_code($enc_password, $reset_id);
    if (!$success) {
        throw new UserError("Failed to save password");
    }

    header("Location: $referer");
}

/** @var mysqli $db */
global $db;

/** @var User $user */
global $user;

try {
    $homepage = get_config('website-url');

    $action = param_post_string_opt('action');

    if ($action == '') {
        show_form($db);
    } else {
        if ($action == 'Cancel') {
            header("Location: $homepage");
            exit();
        }
        update_password($homepage);
    }

} catch (Exception $e) {
    report_error($e);
}
