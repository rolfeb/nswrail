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
$t->loadTemplateFile("edit-links.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->parseCurrentBlock();

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

    $l = get_location_details($state, $location);
    
    $t->setCurrentBlock("MAIN");
    add_urls($state, $location);

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
 * Add the location URLs to the edit page.
 */
function add_urls($state, $location)
{
    global $db;
    global $t;

    $stmt = mysql_query("
        select
            LU.text,
            LU.url
        from
            r_location_url LU
        where
            LU.location_state = '$state'
            and
            LU.location_name = '$location'
        order by
            LU.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $n = 1;
    while ($row = mysql_fetch_array($stmt))
    {
        list ($text, $url) = $row;

        $t->setCurrentBlock("LOCATION-URL");
        $t->setVariable("SEQ", $n);
        $t->setVariable("LINK-URL", $url);
        $t->setVariable("LINK-TEXT", $text);
        $t->parseCurrentBlock();
        $n++;
    }
    while ($n <= 10)
    {
        $t->setCurrentBlock("LOCATION-URL");
        $t->setVariable("SEQ", $n++);
        $t->parseCurrentBlock();
    }
    mysql_free_result($stmt);
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
        /*
         * Delete and re-add the URLs
         */
        if (!mysql_query("
            delete from
                r_location_url
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
            $text = quote_external(get_post("text$n"));
            $url = quote_external(get_post("url$n"));

            if ($text != "" and $url != "")
            {
                if (!mysql_query("
                    insert into
                        r_location_url
                    values('$state', '$location', $n, '$text', '$url')
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
