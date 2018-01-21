<?php

require_once "site.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$seqno = quote_external(get_post("seqno"));         /* obsolete */
$line = quote_external(get_post("line"));           /* optional */
$action = quote_external(get_post("action"));       /* optional */
$redirect = quote_external(get_post("redirect"));   /* optional */

if ($name)
    list($state, $location, $seqno) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("edit-photo-detail.tpl", true, true);

if (!auth_priv_admin())
    error_page("Error: you do not have access to this operation\n", $redirect);

$redirect = "edit-photos.php?name=$state:$location";
if ($line)
    $redirect = $redirect . "&line=$line";

if ($action == "")
    run_edit_mode($state, $location, $seqno, $line, $redirect);
else
if ($action == "Cancel")
{
    header("Location: $redirect");
    exit;
}
else
if ($action == "Save")
    save_changes($state, $location, $seqno, $redirect);
else
    error_page("Unknown action [$action]", $redirect);

/*
 * Display the edit page, allowing the user to edit details
 */
function run_edit_mode($state, $location, $seqno, $line, $redirect)
{
    global $db, $t;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
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
            and
            LP.seqno = ?
        order by
            LP.seqno
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ssi", $state, $location, $seqno);
    $stmt->execute();
    $stmt->bind_result($file, $owner, $day, $month, $year, $year_error,
            $caption, $themes, $submit_date, $submit_by, $status);

    $stmt->store_result();  /* for num rows */

    $i = 0;
    $num_images = $stmt->num_rows;
    $num_rows = floor($num_images / 4);

    if ($stmt->fetch())
    {
        $caption = preg_replace("/^[ \t\n\r]*/", "", $caption);
        $caption = preg_replace("/[\n\r]/", " ", $caption);

        $date = date_cpts2text($day, $month, $year, $year_error);
        if (!$owner)
            $owner = "Rolfe Bozier";

        $t->setCurrentBlock("MAIN");
        $t->setVariable("DATE", $date);
        $t->setVariable("OWNER", $owner);
        $t->setVariable("CAPTION", $caption);

        foreach (explode(",", $themes) as $theme)
            $t->setVariable("CHECKED_$theme", "checked");
            
        $t->setVariable("STATE", $state);
        $t->setVariable("LOCATION", $location);
        $t->setVariable("SEQNO", $seqno);
        $t->setVariable("LINE", $line);
        $t->setVariable("IMAGE-URL", "photos/$file");
    }
    $stmt->close();

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $location);
    $t->parseCurrentBlock();

    display_page($location, $t->get("CONTENT"),
        array(
            'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>',
            'BODY-EXTRA' => 'class="edit-mode"'
        )
    );
}

/*
 * Update the details
 */
function save_changes($state, $location, $seqno, $redirect)
{
    global $db;

    $caption = quote_external(get_post("caption"));
    $theme_box = quote_external(get_post("theme_box"));
    $theme_diagram = quote_external(get_post("theme_diagram"));
    $theme_safeworking = quote_external(get_post("theme_safeworking"));
    $theme_night = quote_external(get_post("theme_night"));
    $theme_turntable = quote_external(get_post("theme_turntable"));

    var_dump($theme_night);
    $themes = "";
    if ($theme_box)
        $themes .= "box,";
    if ($theme_diagram)
        $themes .= "diagram,";
    if ($theme_safeworking)
        $themes .= "safeworking,";
    if ($theme_night)
        $themes .= "night,";
    if ($theme_turntable)
        $themes .= "turntable,";
    $themes = preg_replace("/,$/", "", $themes);

    $stmt = $db->stmt_init();
    $stmt->prepare("
        update
            r_location_photo
        set
            caption = '$caption',
            themes = '$themes'
        where
            location_state = '$state'
            and
            location_name = '$location'
            and
            seqno = $seqno
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ssssi", $caption, $themes, $state, $location, $seqno);

    if (!$stmt->execute())
    {
        $db->rollback();
        $stmt->close();
        error_page("Update failed: record locked by someone else", $redirect);
    }
    $db->commit();
    $stmt->close();

    header("Location: $redirect");

    return;
}

?>
