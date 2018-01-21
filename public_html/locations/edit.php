<?php

require_once "site.inc";

if (!$user->is_editor()) {
    noperm_page();
}

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$line = quote_external(get_post("line"));           /* optional */
$mode = quote_external(get_post("mode", ""));       /* optional */

if ($name)
    list($state, $location) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("edit.tpl", true, true);

if ($mode == "submit")
    run_submit_mode($state, $location, $line);
else
    run_edit_mode($state, $location, $line);

/*
 * Display the edit page, allowing the user to edit details
 */
function run_edit_mode($state, $location, $line)
{
    global $t, $user;

    $l = get_location_details($state, $location);

    if ($user->is_editor())
    {
        $t->setCurrentBlock("ADMIN-BLOCK1");

        foreach (locn_typelist() as $v)
        {
            $t->setCurrentBlock("TYPE-OPTION");
            $t->setVariable("VALUE", $v);
            if ($v == $l["type"])
                $t->setVariable("SELECTED", "selected");
            $t->setVariable("TYPE", $l["type"]);
            $t->parseCurrentBlock();
        }

        foreach (locn_statuslist() as $v)
        {
            $t->setCurrentBlock("STATUS-OPTION");
            $t->setVariable("VALUE", $v);
            if ($v == $l["status"])
                $t->setVariable("SELECTED", "selected");
            $t->setVariable("STATUS", $l["status"]);
            $t->parseCurrentBlock();
        }

        $t->setVariable("DISTANCE", $l["distance"]);
        $t->setVariable("GEOX", $l["geox"]);
        $t->setVariable("GEOY", $l["geoy"]);
        $t->parse("ADMIN-BLOCK1");
    }

    $t->setCurrentBlock("MAIN");
    $t->setVariable("DESC", $l["desc"]);
    $t->setVariable("CURR", $l["curr"]);

    $t->setCurrentBlock("MAIN");

    $url = "show.php?" . urlenc("name=$state:$location");
    if ($line)
        $url = $url . urlenc("&line=$line");

    $t->setVariable("RETURN-URL", $url);
    $t->setVariable("STATE", $state);
    $t->setVariable("LOCATION", $location);
    $t->setVariable("VERSION", $l["version"]);

    $title = locn_fulltitle($location, $l["type"]);

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $title);
    $t->parseCurrentBlock();

    display_page($title, $t->get("CONTENT"),
        array(
            'BODY-EXTRA' => 'class="edit-mode"';
        )
    );
}

/*
 * Commit the changes (if any) to the database
 */
function run_submit_mode($state, $location, $line)
{
    global $db, $user;

    $action = quote_external(get_post("action", ""));
    $return_url = quote_external(get_post("return-url"));

    if ($action == "Cancel")
    {
        header("Location: $return_url");
        return;
    }

    $version = quote_external(get_post("version"));

    /*
     * Save admin-level changes
     */
    if ($user->is_editor())
    {

        $type = quote_external(get_post("type"));
        $otype = quote_external(get_post("o_type"));
        $status = quote_external(get_post("status"));
        $ostatus = quote_external(get_post("o_status"));
        $distance = quote_external(get_post("distance"));
        $odistance = quote_external(get_post("o_distance"));
        $geox = quote_external(get_post("geox"));
        $ogeox = quote_external(get_post("o_geox"));
        $geoy = quote_external(get_post("geoy"));
        $ogeoy = quote_external(get_post("o_geoy"));

        if ($type != $otype or $status != $ostatus or $distance != $odistance
            or $geox != $ogeox or $geoy != $ogeoy)
        {
            $stmt = $db->stmt_init();
            $stmt->prepare("
                update
                    r_location
                set
                    type = $type,
                    status = $status,
                    distance = $distance,
                    geo_x = $geox,
                    geo_y = $geoy,
                    version = version + 1
                where
                    location_state = ?
                    and
                    location_name = ?
                    and
                    version = ?
            ")
                or dbi_error_trace("prepare failed");

            $stmt->bind_param("ssdddssi", $type, $status, $distance, $geox,
                $geoy, $state, $location, $version);

            if (!$stmt->execute())
            {
                $db->rollback();
                error_page("Update failed: record locked by someone else ["
                    . $db->error . "]",
                    $return_url);
            }
            $stmt->close();
        }
    }

    /*
     * Save editor-level changes
     */
    $desc = quote_external(get_post("desc"));
    $o_desc = quote_external(get_post("o_desc"));
    if ($desc != $o_desc)
    {
        $userid = auth_userid();

        $stmt = $db->stmt_init();
        $stmt->prepare("
            select
                max(LT.seqno)
            from
                r_location_text LT
            where
                LT.location_state = ?
                and
                LT.location_name = ?
                and
                LT.type = 'DESC'
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ss", $state, $location);
        $stmt->bind_result($seqno);

        if (!$stmt->execute())
        {
            $db->rollback();
            die("update failed: " . $db->error . "\n");
        }
        $stmt->close();

        $seqno++;

        $stmt = $db->stmt_init();
        $stmt->prepare("
            insert into
                r_location_text
            values(?, ?, 'DESC', ?, ?, now(), ?, 'U')
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssisi", $state, $location, $seqno, $desc, $userid);

        if (!$stmt->execute())
        {
            $db->rollback();
            error_page("Update failed: record locked by someone else",
                $return_url);
        }
    }

    $curr = quote_external(get_post("curr"));
    $o_curr = quote_external(get_post("o_curr"));
    if ($curr != $o_curr)
    {
        $userid = auth_userid();

        $stmt = $db->stmt_init();
        $stmt->prepare("
            select
                max(LT.seqno)
            from
                r_location_text LT
            where
                LT.location_state = ?
                and
                LT.location_name = ?
                and
                LT.type = 'CURR'
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ss", $state, $location);
        $stmt->bind_result($seqno);

        if (!$stmt->execute())
        {
            $db->rollback();
            die("update failed: " . $db->error . "\n");
        }
        $stmt->close();

        $seqno++;

        $stmt = $db->stmt_init();
        $stmt->prepare("
            insert into
                r_location_text
            values(?, ?, 'CURR', ?, ?, now(), ?, 'U')
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssisi", $state, $location, $seqno, $curr, $userid);

        if (!$stmt->execute())
        {
            $db->rollback();
            error_page("Update failed: record locked by someone else",
                $return_url);
        }
    }

    $db->commit();

    header("Location: $return_url");
    return;
}

?>
