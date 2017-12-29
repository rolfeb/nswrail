<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "dbutil.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$seqno = quote_external(get_post("seqno"));         /* obsolete */
$line = quote_external(get_post("line"));           /* optional */
$action = quote_external(get_post("action", ""));   /* optional */
$redirect = quote_external(get_post("redirect", ""));       /* optional */

if ($name)
{
    $f = explode(":", $name);
    $state = $f[0];
    $location = $f[1];
    if (count($f) > 2)
        $seqno = $f[2];
}

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("edit-photos.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top(true));
$t->parseCurrentBlock();

if (!$redirect)
{
    $redirect = "show.php?" . urlenc("name=$state:$location");
    if ($line)
        $redirect = $redirect . urlenc("&line=$line");
}

if (!auth_priv_admin())
    error_page("Error: you do not have access to this operation\n", $redirect);

if ($action == "")
    run_edit_mode($state, $location, $line, $redirect);
else
if ($action == "enable" or $action == "disable")
    set_status($state, $location, $seqno, $line);
else
if ($action == "move_right" or $action == "move_left" or $action == "move_up"
        or $action == "move_down" )
    set_seqno($state, $location, $seqno, $line);
else
    error_page("Unknown action [$action]", "");

/*
 * Display the edit page, allowing the user to edit details
 */
function run_edit_mode($state, $location, $line, $redirect)
{
    global $db, $t;

    $stmt = mysql_query("
        select
            LP.seqno,
            LP.file,
            LP.owner,
            LP.day,
            LP.month,
            LP.year,
            LP.year_error,
            LP.caption,
            LP.themes,
            LP.submit_date,
            LP.submit_by,
            LP.status
        from
            r_location_photo LP
        where
            LP.location_state = '$state'
            and
            LP.location_name = '$location'
        order by
            LP.seqno
    ", $db)
        or error_page("Prepare failed: " . mysql_error($db), $redirect);

    $i = 0;
    $num_images = mysql_num_rows($stmt);
    $num_rows = floor($num_images / 4);

    while ($row = mysql_fetch_array($stmt))
    {
        list($seqno, $file, $owner, $day, $month, $year, $year_error,
            $caption, $themes, $submit_date, $submit_by, $status) = $row;

        $caption = ereg_replace("^[ \t\n\r]*", "", $caption);
        $caption = ereg_replace("[\n\r]", " ", $caption);
        $caption = ereg_replace("'", "\'", $caption);
        $caption = ereg_replace("\"", "\'", $caption);


        $date = date_cpts2text($day, $month, $year, $year_error);
        if (!$owner)
            $owner = "Rolfe Bozier";

        $t->setCurrentBlock("PHOTO");
        $t->setVariable("STATE", $state);
        $t->setVariable("LOCATION", $location);
        $t->setVariable("SEQNO", $seqno);
        $t->setVariable("LINE", $line);
        $t->setVariable("THUMBNAIL", "photos/small/$file");
        $t->setVariable("STATUS", "STATUS_$status");
        $t->setVariable("DATE", $date);
        $t->setVariable("OVERLIB-TEXT", "$caption<br>$date ($owner)");

        $urlbase1 = urlenc("?name=$state:$location:$seqno");
        $urlbase2 = urlenc("edit-photo-detail.php?name=$state:$location:$seqno");

        $t->setVariable("DETAILS-URL",
            $urlbase2 . urlenc("&line=$line"));
        $t->setVariable("ENABLE-URL",
            $urlbase1 . urlenc("&action=enable"));
        $t->setVariable("DISABLE-URL",
            $urlbase1 . urlenc("&action=disable"));
            
        if ($i > 3)
        {
            $t->setVariable("MOVE-UP-URL",
                $urlbase1 . urlenc("&action=move_up"));
        }
        if ($i < $num_images - 4)
        {
            $t->setVariable("MOVE-DOWN-URL",
                $urlbase1 . urlenc("&action=move_down"));
        }
        if ($i % 4 > 0)
        {
            $t->setVariable("MOVE-LEFT-URL",
                $urlbase1 . urlenc("&action=move_left"));
        }
        if ($i % 4 < 3 and $i != $num_images - 1)
        {
            $t->setVariable("MOVE-RIGHT-URL",
                $urlbase1 . urlenc("&action=move_right"));
        }

        $t->parseCurrentBlock();
        
        if ($i % 4 == 3)
            $t->parse("ROW");

        $i++;
    }
    if ($i % 4 != 0)
        $t->parse("ROW");

    $t->setCurrentBlock("MAIN");
    $t->setVariable("TITLE", $location);
    $t->setVariable("RETURN-URL", $redirect);
    $t->parseCurrentBlock();

    $t->show();
}

/*
 * Update the status of the selected image
 */
function set_status($state, $location, $seqno, $line)
{
    global $db;

    $redirect = "edit-photos.php?" . urlenc("name=$state:$location");
    if ($line)
        $redirect = $redirect . urlenc("&line=$line");

    $action = quote_external(get_post("action", ""));

    if ($action == "enable")
        $new_status = "Y";
    else
        $new_status = "N";

    if (!mysql_query("
        update
            r_location_photo
        set
            status = '$new_status'
        where
            location_state = '$state'
            and
            location_name = '$location'
            and
            seqno = $seqno
    ", $db))
    {
        rollback();
        error_page("Update failed: record locked by someone else", $redirect);
    }
    commit();

    header("Location: $redirect");
    return;
}

/*
 * Update the odering of the selected image
 */
function set_seqno($state, $location, $seqno, $line)
{
    global $db;

    $redirect = "edit-photos.php?"
        . urlenc("name=$state:$location");
    if ($line)
        $redirect = $redirect . urlenc("&line=$line");

    $action = quote_external(get_post("action", ""));


    if ($action == "move_left" or $action == "move_right")
    {
        if ($action == "move_left")
            $new_seqno = $seqno - 1;
        else
            $new_seqno = $seqno + 1;

        $total = $seqno + $new_seqno;

        /* swap the two seqnos around */
        if (!mysql_query("
            update
                r_location_photo
            set
                seqno = -($total - seqno)
            where
                location_state = '$state'
                and
                location_name = '$location'
                and
                (
                    seqno = $seqno
                    or
                    seqno = $new_seqno
                )
        ", $db))
        {
            rollback();
            error_page("Update failed [1]: " . mysql_error($db), $redirect);
        }
    }
    else
    if ($action == "move_up")
    {
        if (!mysql_query("
            update
                r_location_photo
            set
                seqno = -(seqno + 1)
            where
                location_state = '$state'
                and
                location_name = '$location'
                and
                seqno >= $seqno - 4
                and
                seqno <= $seqno - 1
        ", $db))
        {
            rollback();
            error_page("Update failed [2]: " . mysql_error($db), $redirect);
        }
        if (!mysql_query("
            update
                r_location_photo
            set
                seqno = -(seqno - 4)
            where
                location_state = '$state'
                and
                location_name = '$location'
                and
                seqno = $seqno
        ", $db))
        {
            rollback();
            error_page("Update failed [3]: " . mysql_error($db), $redirect);
        }
    }
    else
    {
        if (!mysql_query("
            update
                r_location_photo
            set
                seqno = -(seqno - 1)
            where
                location_state = '$state'
                and
                location_name = '$location'
                and
                seqno <= $seqno + 4
                and
                seqno >= $seqno + 1
        ", $db))
        {
            rollback();
            error_page("Update failed [4]: " . mysql_error($db), $redirect);
        }
        if (!mysql_query("
            update
                r_location_photo
            set
                seqno = -(seqno + 4)
            where
                location_state = '$state'
                and
                location_name = '$location'
                and
                seqno = $seqno
        ", $db))
        {
            rollback();
            error_page("Update failed [5]: " . mysql_error($db), $redirect);
        }
    }

    /* restore negative seqnos */
    if (!mysql_query("
        update
            r_location_photo
        set
            seqno = -seqno
        where
            location_state = '$state'
            and
            location_name = '$location'
            and
            seqno < 0
    ", $db))
    {
        rollback();
        error_page("Update failed [6]: " . mysql_error($db), $redirect);
    }
    commit();

    header("Location: $redirect");
    return;
}

?>
