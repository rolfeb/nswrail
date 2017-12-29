<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "dbutil.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$line = quote_external(get_post("line"));           /* optional */
$mode = quote_external(get_post("mode", ""));       /* optional */

if ($name)
    list($state, $location) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("edit.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->parseCurrentBlock();

if (auth_priv_none())
{
    $url = "show.php?" . urlenc("name=$state:$location");
    if ($line)
        $url = $url . "&line=$line";

    error_page("Error: you do not have access to this operation\n", $url);
}

if ($mode == "submit")
    run_submit_mode($state, $location, $line);
else
    run_edit_mode($state, $location, $line);

/*
 * Display the edit page, allowing the user to edit details
 */
function run_edit_mode($state, $location, $line)
{
    global $t;

    $l = get_location_details($state, $location);

    if (auth_priv_admin())
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

    $t->setCurrentBlock("MAIN");
    $t->setVariable("TITLE", locn_fulltitle($location, $l["type"]));
    $t->parseCurrentBlock();

    $t->show();
}

/*
 * Commit the changes (if any) to the database
 */
function run_submit_mode($state, $location, $line)
{
    global $db;

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
    if (auth_priv_admin())
    {
        $updates = "";

        $type = quote_external(get_post("type"));
        $otype = quote_external(get_post("o_type"));
        if ($type != $otype)
        {
            if ($type)
                $updates .= "type = '$type',";
            else
                $updates .= "type = null,";
        }

        $status = quote_external(get_post("status"));
        $ostatus = quote_external(get_post("o_status"));
        if ($status != $ostatus)
        {
            if ($status)
                $updates .= "status = '$status',";
            else
                $updates .= "status = null,";
        }

        $distance = quote_external(get_post("distance"));
        $odistance = quote_external(get_post("o_distance"));
        if ($distance != $odistance)
        {
            if ($distance)
                $updates .= "distance = '$distance',";
            else
                $updates .= "distance = null,";
        }

        $geox = quote_external(get_post("geox"));
        $ogeox = quote_external(get_post("o_geox"));
        if ($geox != $ogeox)
        {
            if ($geox)
                $updates .= "geo_x = '$geox',";
            else
                $updates .= "geo_x = null,";
        }

        $geoy = quote_external(get_post("geoy"));
        $ogeoy = quote_external(get_post("o_geoy"));
        if ($geoy != $ogeoy)
        {
            if ($geoy)
                $updates .= "geo_y = '$geoy',";
            else
                $updates .= "geo_y = null,";
        }

        if ($updates)
        {
            /*
             * Update core details
             */
            if (!mysql_query("
                update
                    r_location
                set
                    $updates
                    version = $version + 1
                where
                    location_state = '$state'
                    and
                    location_name = '$location'
                    and
                    version = $version
            ", $db))
            {
                rollback();
                error_page("Update failed: record locked by someone else ["
                    . mysql_error() . "]",
                    $return_url);
            }
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

        if (!$stmt = mysql_query("
            select
                max(LT.seqno)
            from
                r_location_text LT
            where
                LT.location_state = '$state'
                and
                LT.location_name = '$location'
                and
                LT.type = 'DESC'
        ", $db))
        {
            rollback();
            die("prepare failed: " . mysql_error() . "\n");
        }

        list($seqno) = mysql_fetch_array($stmt);
        mysql_free_result($stmt);

        $seqno++;

        if (!mysql_query("
            insert into
                r_location_text
            values('$state', '$location', 'DESC', $seqno, '$desc', now(),
                $userid, 'U')
        ", $db))
        {
            rollback();
            error_page("Update failed: record locked by someone else",
                $return_url);
        }
    }

    $curr = quote_external(get_post("curr"));
    $o_curr = quote_external(get_post("o_curr"));
    if ($curr != $o_curr)
    {
        $userid = auth_userid();

        if (!$stmt = mysql_query("
            select
                max(LT.seqno)
            from
                r_location_text LT
            where
                LT.location_state = '$state'
                and
                LT.location_name = '$location'
                and
                LT.type = 'CURR'
        ", $db))
        {
            rollback();
            die("prepare failed: " . mysql_error() . "\n");
        }

        list($seqno) = mysql_fetch_array($stmt);
        mysql_free_result($stmt);

        $seqno++;

        if (!mysql_query("
            insert into
                r_location_text
            values('$state', '$location', 'CURR', $seqno, '$curr', now(),
                $userid, 'U')
        ", $db))
        {
            rollback();
            error_page("Update failed: record locked by someone else ["
                . mysql_error() . "]",
                $return_url);
        }
    }

    commit();

    header("Location: $return_url");
    return;
}

?>
