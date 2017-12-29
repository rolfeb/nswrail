<?php

$secret = "H8f_h2x7:l";

function auth_level()
{
    if (isset($_SESSION['role']))
    {
        if ($_SESSION['role'] == 'admin')
            return 3;
        else
        if ($_SESSION['role'] == 'editor')
            return 2;
        else
        if ($_SESSION['role'] == 'normal')
            return 1;
    }

    return 0;
}

function auth_priv_none()
{
    return
        !isset($_SESSION['username'])
        ||
        auth_level() == 0;
}

function auth_priv_normal()
{
    return
        isset($_SESSION['username'])
        &&
        auth_level() >= 1;
}

function auth_priv_editor()
{
    return
        isset($_SESSION['username'])
        &&
        auth_level() >= 2;
}

function auth_priv_admin()
{
    return
        isset($_SESSION['username'])
        &&
        auth_level() >= 3;
}

function auth_loggedin()
{
    return isset($_SESSION['username']);
}

function auth_username()
{
    return $_SESSION['fullname'];
}

function auth_userid()
{
    return $_SESSION['uid'];
}

function auth_uid()
{
    return auth_userid();
}

function auth_generate_salt()
{
    return md5(sprintf("%ld", time()));
}

function auth_encrypt_password($plaintext, $salt)
{
    return md5($plaintext . $salt);
}

function auth_check_password($username, $password)
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
            and
            status = 'active'
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $row = dbi_bind_to_array($stmt);

    if ($stmt->fetch())
    {
        $enc_password = auth_encrypt_password($password, $row['pwdsalt']);
        if ($enc_password == $row['password'])
        {
            $stmt->close();
            return $row;
        }
    }
    $stmt->close();

    return null;
}

?>
