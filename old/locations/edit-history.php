<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "dbutil.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$line = quote_external(get_post("line"));           /* optional */
$mode = quote_external(get_post("mode", ""));       /* optional */
$action = quote_external(get_post("action", ""));   /* optional */
$redirect = quote_external(get_post("redirect", "")); /* optional */

if ($name)
    list($state, $location) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("edit-history.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->parseCurrentBlock();

if (!$redirect)
{
    $redirect = "show.php?name=$state:$location";
    if ($line)
        $redirect = $redirect . "&line=$line";
}

if (!auth_priv_admin())
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

    $error_lookup = array(
        0   =>  "-",
        1   =>  "circa",
        2   =>  "decade",
        -1  =>  "before",
        -2  =>  "after",
    );

    $l = get_location_details($state, $location);
    $events = get_location_history($state, $location);

    for ($n = 1; $n <= 10; $n++)
    {
        $t->setCurrentBlock("LOCATION-HISTORY");
        $t->setVariable("SEQ", $n);


        $type = "-";
        $day = "";
        $month = "";
        $year = "";
        $error = 0;
        $name = "";
        $text = "";
        if ($n <= count($events))
        {
            $param = $events[$n-1];
            $type = $param["type"];
            $day = $param["day"];
            $month = $param["month"];
            $year = $param["year"];
            $error = $param["year_error"];
            if ($error == "")
                $error = 0;
            $name = $param["name"];
            $text = $param["text"];
        }

        foreach (locn_eventtypelist() as $v)
        {
            $t->setCurrentBlock("TYPE-OPTION");

            if ($v == $type)
                $t->setVariable("SELECTED", "selected");

            $label = $v;
            if ($label == "")
                $lable = "-";

            $t->setVariable("VALUE", $v);
            $t->setVariable("LABEL", $label);
            $t->parseCurrentBlock();
        }
        $t->setVariable("TYPE", $type);

        $t->setVariable("DAY", $day);
        $t->setVariable("MONTH", $month);
        $t->setVariable("YEAR", $year);

        foreach (date_errorlist() as $v)
        {
            $t->setCurrentBlock("ERROR-OPTION");

            if ($v == $error)
                $t->setVariable("SELECTED", "selected");

            $label = $error_lookup[$v];

            $t->setVariable("VALUE", $v);
            $t->setVariable("LABEL", $label);
            $t->parseCurrentBlock();
        }
        $t->setVariable("ERROR", $type);

        $t->setVariable("NAME", $name);
        $t->setVariable("TEXT", $text);

        $t->parse("LOCATION-HISTORY");
    }

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

        /*
         * Delete and re-add the events
         */
        if (!mysql_query("
            delete from
                r_location_event
            where
                location_state = '$state'
                and
                location_name = '$location'
        ", $db))
        {
            rollback();
            error_page("Delete failed: " . mysql_error(), $return_url);
        }

        for ($n = 1; $n <= 10; $n++)
        {
            $type = quote_external(get_post("type$n"));
            $day = quote_external(get_post("day$n"));
            $month = quote_external(get_post("month$n"));
            $year = quote_external(get_post("year$n"));
            $year_error = quote_external(get_post("error$n"));
            $name = quote_external(get_post("name$n"));
            $text = quote_external(get_post("text$n"));

            $day_str = $day ? "'$day'" : "NULL";
            $month_str = $month ? "'$month'" : "NULL";
            $year_str = $year ? "'$year'" : "NULL";
            $name_str = $name ? "'$name'" : "NULL";
            $text_str = $text ? "'$text'" : "NULL";

            if ($type != "")
            {
                if (!mysql_query("
                    insert into
                        r_location_event
                    values('$state', '$location', $n, '$type', $day_str, $month_str, $year_str, '$year_error', $name_str, $text_str)
                ", $db))
                {
                    rollback();
                    error_page("Update failed: record locked by someone else",
                        $return_url);
                }
            }
        }
    }

    commit();

    header("Location: $return_url");
    return;
}

?>
