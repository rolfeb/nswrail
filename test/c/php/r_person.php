<?php

require_once 'dbutil.inc';

function get_person_by_email($email)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            *
        from
            r_person
        where
            email = ?
    ")
        or die("prepare failed: " . $dbi->error . "\n");

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $row = dbi_bind_to_array($stmt);

    if (!$stmt->fetch())
        $row = null;

    $stmt->close();
    return $row;
}

function get_person_by_username($email)
{
    return get_person_by_email($email);
}

function get_person_by_uid($uid)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            *
        from
            r_person
        where
            uid = ?
    ")
        or die("prepare failed: " . $dbi->error . "\n");

    $stmt->bind_param("d", $uid);
    $stmt->execute();
    $row = dbi_bind_to_array($stmt);

    if (!$stmt->fetch())
        $row = null;

    $stmt->close();
    return $row;
}

?>
