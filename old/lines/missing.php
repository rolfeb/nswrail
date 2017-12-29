<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "icon.inc";    /* for get_location_icon() */
require_once "dbutil.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$line = quote_external(get_post("line"));           /* obsolete */

if ($name)
    list($state, $line) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("missing.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

if (auth_priv_none())
{
    error_page(
        "Error: you do not have access to this operation\n",
        ""
    );
}

run_default_mode($state, $line);

function run_default_mode($line_state, $line)
{
    global $db;
    global $t;

    $stmt = mysql_query("
        select distinct
            RL.location_state,
            RL.location_name,
            L.type,
            L.status,
            L.distance,
            L.geo_x,
            L.geo_y
        from
            r_line_location RL,
            r_location L
        where
            RL.line_state = '$line_state'
            and
            RL.line_name = '$line'
            and
            L.location_state = RL.location_state
            and
            L.location_name = RL.location_name
        order by
            RL.segment,
            RL.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    while ($row = mysql_fetch_array($stmt))
    {
        list ($state, $location, $type, $status, $distance, $geo_x, $geo_y)
            = $row;

        if ($type == "")
            $type = "unknown";
        if ($status == "")
            $status = "unknown";

        $got_geoxy = ($geo_x && $geo_y ? "Y" : "N");

        list($n_desc, $n_curr, $n_photo, $n_diag, $n_url, $got_open,
            $got_close) = get_location_details($state, $location);

        if
        (
            $type != "unknown"
            &&
            $status != "unknown"
            &&
            ($distance != "" && $distance != 0)
            &&
            $got_geoxy == "Y"
            &&
            ($n_desc > 0 || $type == "junction")
            &&
            ($n_curr > 0 || $type == "junction" || $type == "tunnel")
            &&
            ($status != 'unopened' || $got_open == 'Y')
            &&
            ($status != 'closed' || $got_close == 'Y')
        )
            continue;
        
        $close = " ";
        if ($got_close == "Y")
            $close = "Y";
        else
        {
            if ($status == "not opened" || $status == "in use")
                $close = "-";
        }

        $desc = ($n_desc > 0 ? "Y" : " ");
        $curr = ($n_curr > 0 ? "Y" : " ");
        if ($type == "junction")
        {
            $desc = "-";
            $curr = "-";
        }

        $t->setVariable("OPEN", $got_open == "Y" ? "Y" : " ");

        $t->setCurrentBlock("LOCATION");
        $t->setVariable("URL", "/locations/show.php?"
            . urlenc("name=$state:$location"));
        $t->setVariable("NAME", $location);
        $t->setVariable("TYPE", $type != "unknown" ? "Y" : " ");
        $t->setVariable("STATUS", $status != "unknown" ? "Y" : " ");
        $t->setVariable("DISTANCE", $distance != 0 ? "Y" : " ");
        $t->setVariable("GEOXY", $got_geoxy == "Y" ? "Y" : " ");
        $t->setVariable("DESC", $desc);
        $t->setVariable("CURR", $curr);
        $t->setVariable("OPEN", $got_open == "Y" ? "Y" : " ");
        $t->setVariable("CLOSE", $close);
        $t->setVariable("NPHOTOS", $n_photo == 0 ? "" : $n_photo);
        $t->setVariable("NDIAGRAMS", $n_diag == 0 ? "" : $n_diag);
        $t->setVariable("NURLS", $n_url == 0 ? "" : $n_url);
        $t->parseCurrentBlock();
    }
    mysql_free_result($stmt);

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) =
        get_line_details($line_state, $line);

    $t->setCurrentBlock("MAIN");
    $t->setVariable("LINE", "$fullname - Missing Data");
    $t->parseCurrentBlock();

    $t->show();
}

function get_location_details($state, $location)
{
    global $db;

    $n_desc = 0;
    $n_curr = 0;
    $n_photo = 0;
    $n_diag = 0;
    $n_url = 0;
    $got_open = "";
    $got_close = "";

    /* look for location text */
    $stmt = mysql_query("
        select
            LT.type,
            count(*)
        from
            r_location_text LT
        where
            LT.location_state = '$state'
            and
            LT.location_name = '$location'
        group by
            LT.type
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    while ($row = mysql_fetch_array($stmt))
    {
        list ($type, $count) = $row;

        if ($type == "DESC")
            $n_desc = $count;
        else
        if ($type == "CURR")
            $n_curr = $count;
    }
    mysql_free_result($stmt);

    /* look for location photos */
    $stmt = mysql_query("
        select
            count(*)
        from
            r_location_photo LP
        where
            LP.location_state = '$state'
            and
            LP.location_name = '$location'
            and
            LP.status != 'N'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    while ($row = mysql_fetch_array($stmt))
    {
        list ($count) = $row;

        $n_photo = $count;
    }
    mysql_free_result($stmt);

    /* look for location diagrams */
    $stmt = mysql_query("
        select
            count(*)
        from
            r_location_diagram LD
        where
            LD.location_state = '$state'
            and
            LD.location_name = '$location'
            and
            LD.status = 'N'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    while ($row = mysql_fetch_array($stmt))
    {
        list ($count) = $row;

        $n_diag = $count;
    }
    mysql_free_result($stmt);

    /* look for location URLs */
    $stmt = mysql_query("
        select
            count(*)
        from
            r_location_url LU
        where
            LU.location_state = '$state'
            and
            LU.location_name = '$location'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    while ($row = mysql_fetch_array($stmt))
    {
        list ($count) = $row;

        $n_url = $count;
    }
    mysql_free_result($stmt);

    /* look for location events */
    $stmt = mysql_query("
        select
            LEV.type,
            LEV.year
        from
            r_location_event LEV
        where
            LEV.location_state = '$state'
            and
            LEV.location_name = '$location'
            and
            LEV.type in ( 'ON', 'CN' )
        order by
            LEV.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $got_open = "";
    $got_close = "";
    while ($row = mysql_fetch_array($stmt))
    {
        list ($type, $year) = $row;

        if ($type == "ON")
        {
            if (!$got_open)
                $got_open = ($year ? "Y" : "U");
        }
        else
        if ($type == "CN")
            $got_close = ($year ? "Y" : "U");
    }
    mysql_free_result($stmt);

    if ($got_open != "Y")
        $got_open = "N";
    if ($got_close != "Y")
        $got_close = "N";

    return array($n_desc, $n_curr, $n_photo, $n_diag, $n_url, $got_open,
        $got_close);
}

?>
