<?php

$secret = "H8f_h2x7:l";

function auth_level()
{
    $server = $_SERVER["SERVER_NAME"];

    if ($server == "nswrail-devel")
        return 2;
    else
        return 0;
}

function auth_priv_none()
{
    return (auth_level() == 0);
}

function auth_priv_editor()
{
    return (auth_level() >= 1);
}

function auth_priv_admin()
{
    return (auth_level() >= 2);
}

function auth_userid()
{
    return 0;
}

function auth_username()
{
    return "rolfe";
}


function auth_loggedin()
{
    /* XXX: if _SERVER[username] and haah valid -> true */
    return 0;
}

?>
