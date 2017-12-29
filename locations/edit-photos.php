<?php

require_once "site.inc";
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
    global $dbi, $t;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
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
            LP.location_state = ?
            and
            LP.location_name = ?
        order by
            LP.seqno
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($seqno, $file, $owner, $day, $month, $year, $year_error,
        $caption, $themes, $submit_date, $submit_by, $status);

    $stmt->store_result();  /* for num rows */

    $i = 0;
    $num_images = $stmt->num_rows;
    $num_rows = floor($num_images / 4);

    while ($stmt->fetch())
    {
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

    $stmt->close();

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $location);
    $t->setVariable("RETURN-URL", $redirect);
    $t->parseCurrentBlock();

    display_page($location, $t->get("CONTENT"),
        array(
            'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>',
            'BODY-EXTRA' => 'class="edit-mode"'
        )
    );
}

/*
 * Update the status of the selected image
 */
function set_status($state, $location, $seqno, $line)
{
    global $dbi;

    $redirect = "edit-photos.php?" . urlenc("name=$state:$location");
    if ($line)
        $redirect = $redirect . urlenc("&line=$line");

    $action = quote_external(get_post("action", ""));

    if ($action == "enable")
        $new_status = "Y";
    else
        $new_status = "N";


    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        update
            r_location_photo
        set
            status = ?
        where
            location_state = ?
            and
            location_name = ?
            and
            seqno = ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("sssi", $new_status, $state, $location, $seqno);

    if (!$stmt->execute())
    {
        $dbi->rollback();
        error_page("Update failed: record locked by someone else", $redirect);
    }
    $dbi->commit();

    header("Location: $redirect");
    return;
}

/*
 * Update the odering of the selected image
 */
function set_seqno($state, $location, $seqno, $line)
{
    global $dbi;

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
        $stmt = $dbi->stmt_init();
        $stmt->prepare("
            update
                r_location_photo
            set
                seqno = -(? - seqno)
            where
                location_state = ?
                and
                location_name = ?
                and
                (
                    seqno = ?
                    or
                    seqno = ?
                )
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("issii", $total, $state, $location, $seqno,
            $new_seqno);

        if (!$stmt->execute())
        {
            $dbi->rollback();
            error_page("Update failed [1]: " . $dbi->error, $redirect);
        }
        $stmt->close();
    }
    else
    if ($action == "move_up")
    {
        $stmt = $dbi->stmt_init();
        $stmt->prepare("
            update
                r_location_photo
            set
                seqno = -(seqno + 1)
            where
                location_state = ?
                and
                location_name = ?
                and
                seqno >= ? - 4
                and
                seqno <= ? - 1
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssii", $state, $location, $seqno, $seqno);

        if (!$stmt->execute())
        {
            $dbi->rollback();
            error_page("Update failed [2]: " . $dbi->error, $redirect);
        }
        $stmt->close();

        $stmt = $dbi->stmt_init();
        $stmt->prepare("
            update
                r_location_photo
            set
                seqno = -(seqno - 4)
            where
                location_state = ?
                and
                location_name = ?
                and
                seqno = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssi", $state, $location, $seqno);

        if (!$stmt->execute())
        {
            $dbi->rollback();
            error_page("Update failed [3]: " . $dbi->error, $redirect);
        }
        $stmt->close();
    }
    else
    {
        $stmt = $dbi->stmt_init();
        $stmt->prepare("
            update
                r_location_photo
            set
                seqno = -(seqno - 1)
            where
                location_state = ?
                and
                location_name = ?
                and
                seqno <= ? + 4
                and
                seqno >= ? + 1
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssii", $state, $location, $seqno, $seqno);

        if (!$stmt->execute())
        {
            $dbi->rollback();
            error_page("Update failed [4]: " . $dbi->error, $redirect);
        }
        $stmt->close();

        $stmt = $dbi->stmt_init();
        $stmt->prepare("
            update
                r_location_photo
            set
                seqno = -(seqno + 4)
            where
                location_state = ?
                and
                location_name = ?
                and
                seqno = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssi", $state, $location, $seqno);

        if (!$stmt->execute())
        {
            $dbi->rollback();
            error_page("Update failed [5]: " . $dbi->error, $redirect);
        }
        $stmt->close();
    }

    /* restore negative seqnos */
    $stmt = $dbi->stmt_init();
    $stmt->prepare("
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);

    if (!$stmt->execute())
    {
        $dbi->rollback();
        error_page("Update failed [6]: " . $dbi->error, $redirect);
    }
    $stmt->close();

    $dbi->commit();

    header("Location: $redirect");
    return;
}

?>
