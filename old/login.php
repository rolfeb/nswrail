<?php

require_once "init.inc";
require_once "util.inc";

/*
if ($argv)
{
    for ($i=1;$i<count($argv);$i++)
    {
        $it = split("=",$argv[$i]);
        $_GET[$it[0]] = $it[1];
    }
}
*/

$redirect = quote_external(get_post("redirect"));   /* optional */
$action = quote_external(get_post("action"));       /* optional */
$message = quote_external(get_post("message"));     /* optional */

if ($action != "login")
{
    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("login.tpl", true, true);
    $t->setCurrentBlock("CONTROLS");
    $t->setVariable("TOP", top(true));
    $t->parseCurrentBlock();


    if (!$redirect)
        $redirect = "/";

    $t->setCurrentBlock("LOGIN");
    $t->setVariable("REDIRECT", $redirect);

    if ($message)
        $t->setVariable("MESSAGE", $message);

    $t->parseCurrentBlock();

    $t->setCurrentBlock("MAIN");
    $t->setVariable("TITLE", "Login");
    $t->parseCurrentBlock();

    $t->show();
}
else
{
    $button = quote_external(get_post("button"));

    if ($button == "Cancel")
    {
        header("Location: $redirect");
        exit;
    }

    $username = quote_external(get_post("username"));
    $password = quote_external(get_post("password"));

    /*
    if ($username == "rolfe"
        and md5($password) == "96efc2378b2cd7428e0afde5bcf0cbec")
    {
    }
    else
    {
    }
    */

    /*
        if (md5(password) == r_person.password)
            h = md5(secret, username, role)
            session -> { username, role, h }
        else
            session -> { }
    */

    $message = "Login failed";

    $url = $_SERVER["PHP_SELF"] . "?redirect=$redirect";
    $url .= "&message=$message";

    header("Location: $url");
    exit;
}

exit;

?>
