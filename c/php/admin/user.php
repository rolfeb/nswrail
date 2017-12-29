<?php

require_once "site.inc";

global $BASE_PATH;

$template_dir   = "$BASE_PATH/c/tpl";

$t = new HTML_Template_ITX($template_dir);
if (!auth_priv_admin())
{
    if (!$t->loadTemplateFile("noperm.tpl", true, true))
        return "<!-- ERROR: couldn't open noperm.tpl -->\n";

    $t->setCurrentBlock('CONTENT');
    $t->touchBlock('CONTENT');
    $t->parseCurrentBlock();

    display_page("ERROR", $t->get("CONTENT"));
}

$mode = quote_external(get_post('mode'));

if ($mode == 'add')
{
    if (!isset($_POST['uid']))
    {
        show_user_add_screen($t);
    }
    else
    {
        if ($_POST['action'] == 'Cancel')
        {
            header("Location: user.php");
            exit();
        }
        create_user_details();
    }
}
else if ($mode == 'mod')
{
    if (!isset($_POST['uid']))
    {
        show_user_modify_screen($t);
    }
    else
    {
        if ($_POST['action'] == 'Cancel')
        {
            header("Location: user.php");
            exit();
        }
        update_user_details();
    }
}
else
{
    show_user_listing($t);
}

exit();

function show_user_listing($t)
{
    global $dbi;

    if (!$t->loadTemplateFile("admin-user-listing.tpl", true, true))
        return "<!-- ERROR: couldn't open admin-user-listing.tpl -->\n";

    $title = 'User Listing';

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            *
        from
            r_person
        order by
            uid
    ")
        or dbi_error_trace("prepare failed");

    $stmt->execute();
    $r = dbi_bind_to_array($stmt);

    while ($stmt->fetch())
    {
        $t->setCurrentBlock('USER-ENTRY');
        $t->setVariable('UID', $r['uid']);
        $t->setVariable('EMAIL', $r['email']);
        $t->setVariable('FULLNAME', $r['fullname']);
        $t->setVariable('PASSWORD', $r['password']);
        $t->setVariable('ROLE', $r['role']);
        $t->setVariable('STATUS', $r['status']);
        $t->setVariable('JOINED', $r['joined']);
        $t->parseCurrentBlock();
    }
    $stmt->close();

    $t->setCurrentBlock("CONTENT");
    $t->setVariable('TITLE', $title);
    $t->parseCurrentBlock();

    display_page($title, $t->get("CONTENT"));
}

function show_user_modify_screen($t)
{
    global $dbi;

    if (!$t->loadTemplateFile("admin-user-modify.tpl", true, true))
        return "<!-- ERROR: couldn't open admin-user-modify.tpl -->\n";

    $title = 'User Details';

    $uid = quote_external($_GET['uid']);

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            *
        from
            r_person
        where
            uid = ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("i", $uid); 
    $stmt->execute();
    $r = dbi_bind_to_array($stmt);

    if ($stmt->fetch())
    {
        $t->setCurrentBlock('USER-DETAIL');
        $t->setVariable('UID', $r['uid']);
        $t->setVariable('EMAIL', $r['email']);
        $t->setVariable('FULLNAME', $r['fullname']);

        $t->setVariable('ANONYMOUS', $r['anonymous']);
        foreach (array('N', 'Y') as $v)
        {
            $t->setCurrentBlock('ANONYMOUS-OPTION');
            $t->setVariable('VALUE', $v);
            if ($v == $r['anonymous'])
                $t->setVariable('SELECTED', 'selected');
            $t->parseCurrentBlock();
        }

        $t->setVariable('PASSWORD', $r['password']);

        $t->setVariable('ROLE', $r['role']);
        foreach (array('normal', 'editor', 'admin') as $v)
        {
            $t->setCurrentBlock('ROLE-OPTION');
            $t->setVariable('VALUE', $v);
            if ($v == $r['role'])
                $t->setVariable('SELECTED', 'selected');
            $t->parseCurrentBlock();
        }

        $t->setVariable('STATUS', $r['status']);
        foreach (array('pending', 'proxy', 'active', 'locked') as $v)
        {
            $t->setCurrentBlock('STATUS-OPTION');
            $t->setVariable('VALUE', $v);
            if ($v == $r['status'])
                $t->setVariable('SELECTED', 'selected');
            $t->parseCurrentBlock();
        }

        $t->setCurrentBlock('USER-DETAIL');
        $t->setVariable('JOINED', $r['joined']);
        $t->setVariable('VERSION', $r['version']);
        $t->parseCurrentBlock();
    }
    else
    {
        $t->setCurrentBlock('ERROR');
        $t->setVariable('TEXT', "Invalid user ID: $uid");
        $t->parseCurrentBlock();
    }

    $stmt->close();

    $t->setCurrentBlock("CONTENT");
    $t->setVariable('TITLE', $title);
    $t->parseCurrentBlock();

    display_page($title, $t->get("CONTENT"));
}

function show_user_add_screen($t)
{
    global $dbi;

    if (!$t->loadTemplateFile("admin-user-add.tpl", true, true))
        return "<!-- ERROR: couldn't open admin-user-add.tpl -->\n";

    $title = 'New User Details';

    $t->setCurrentBlock('USER-DETAIL');

    foreach (array('N', 'Y') as $v)
    {
        $t->setCurrentBlock('ANONYMOUS-OPTION');
        $t->setVariable('VALUE', $v);
        if ($v == 'N')
            $t->setVariable('SELECTED', 'selected');
        $t->parseCurrentBlock();
    }

    foreach (array('normal', 'editor', 'admin') as $v)
    {
        $t->setCurrentBlock('ROLE-OPTION');
        $t->setVariable('VALUE', $v);
        if ($v == 'normal')
            $t->setVariable('SELECTED', 'selected');
        $t->parseCurrentBlock();
    }

    foreach (array('pending', 'proxy', 'active', 'locked') as $v)
    {
        $t->setCurrentBlock('STATUS-OPTION');
        $t->setVariable('VALUE', $v);
        if ($v == 'proxy')
            $t->setVariable('SELECTED', 'selected');
        $t->parseCurrentBlock();
    }

    $t->setCurrentBlock('USER-DETAIL');
    $t->parseCurrentBlock();

    $t->setCurrentBlock("CONTENT");
    $t->setVariable('TITLE', $title);
    $t->parseCurrentBlock();

    display_page($title, $t->get("CONTENT"));
}

function update_user_details()
{
    global $dbi;

    $uid = quote_external($_POST['uid']);
    $email = quote_external($_POST['email']);
    $fullname = quote_external($_POST['fullname']);
    $password = quote_external($_POST['password']);
    $role = quote_external($_POST['role']);
    $status = quote_external($_POST['status']);
    $version = quote_external($_POST['version']);

    if ($password != '')
    {
        $salt = auth_generate_salt();
        $enc_password = auth_encrypt_password($password, $salt);

        $extra = "password = ?, pwdsalt = ?,";
    }
    else
        $extra = "";

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        update
            r_person
        set
            email = ?,
            fullname = ?, $extra
            role = ?,
            status = ?,
            version = version + 1
        where
            uid = ?,
            and
            version = ?,
    ")
        or dbi_error_trace("prepare failed");

    if ($password != '')
    {
        $stmt->bind_param("ssssssii", $email, $fullname, $enc_password, $salt,
            $role, $status, $uid, $version);
    }
    else
    {
        $stmt->bind_param("ssssii", $email, $fullname, $role, $status, $uid,
            $version);
    }

    if (!$stmt->execute())
    {
        $err = $dbi->error;
        $stmt->close();
        $dbi->rollback();
        error_page("Update failed: " . $err);
    }
    $stmt->close();
    $dbi->commit();

    header("Location: user.php");
}

function create_user_details()
{
    global $dbi;

    $email = quote_external($_POST['email']);
    $fullname = quote_external($_POST['fullname']);
    $password = quote_external($_POST['password']);
    $role = quote_external($_POST['role']);
    $status = quote_external($_POST['status']);
    $version = 1;

    $salt = auth_generate_salt();
    $enc_password = auth_encrypt_password($password, $salt);

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        insert into
            r_person
            (
                uid,
                email,
                fullname,
                password,
                pwdsalt,
                role,
                status,
                anonymous,
                joined,
                version
            )
        select
            max(uid) + 1, ?, ?, ?, ?, ?, ?, ?, sysdate(), ?
        from
            r_person
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ssssssii", $email, $fullname, $enc_password, $salt,
        $role, $status, $anonymous, $version);

    if (!$stmt->execute())
    {
        $err = $dbi->error;
        $stmt->close();
        $dbi->rollback();
        error_page("Insert failed: " . $err);
    }
    $stmt->close();
    $dbi->commit();

    header("Location: user.php");
}

?>
