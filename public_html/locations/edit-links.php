<?php

require_once "site.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$line = quote_external(get_post("line"));           /* optional */
$mode = quote_external(get_post("mode", ""));       /* optional */

if ($name)
    list($state, $location) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("edit-links.tpl", true, true);

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
    
    $t->setCurrentBlock("CONTENT");
    add_urls($state, $location);

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
            'BODY-EXTRA' => 'class="edit-mode"'
        )
    );
}

/*
 * Add the location URLs to the edit page.
 */
function add_urls($state, $location)
{
    global $db;
    global $t;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LU.text,
            LU.url
        from
            r_location_url LU
        where
            LU.location_state = ?
            and
            LU.location_name = ?
        order by
            LU.seqno
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($text, $url);

    $n = 1;
    while ($stmt->fetch())
    {
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
    $stmt->close();
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
        $stmt1 = $db->stmt_init();
        $stmt1->prepare("
            delete from
                r_location_url
            where
                location_state = ?
                and
                location_name = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt1->bind_param("ss", $state, $location);

        $stmt2 = $db->stmt_init();
        $stmt2->prepare("
            insert into
                r_location_url
            values(?, ?, ?, ?, ?)
        ")
            or dbi_error_trace("prepare failed");

        $stmt1->execute();

        for ($n = 1; $n <= 10; $n++)
        {
            $text = quote_external(get_post("text$n"));
            $url = quote_external(get_post("url$n"));

            if ($text != "" and $url != "")
            {
                $stmt2->bind_param("ssiss", $state, $location, $n, $text, $url);

                if (!$stmt1->execute())
                {
                    rollback();
                    error_page("Update failed: record locked by someone else",
                        $return_url);
                }
            }
        }

        $stmt2->close();
        $stmt1->close();
    }

    $db->commit();

    header("Location: $return_url");
    return;
}

?>
