<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "dbutil.inc";

$name = quote_external(get_post("name"));       /* mandatory */
$state = quote_external(get_post("state"));     /* obsolete */
$line = quote_external(get_post("line"));       /* obsolete */
$mode = quote_external(get_post("mode", ""));   /* optional */

if ($name)
    list($state, $line) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("edit.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->parseCurrentBlock();

if (auth_priv_none())
{
    error_page(
        "Error: you do not have access to this operation\n",
        "show.php?" . urlenc("name=$state:$line")
    );
}

if ($mode == "submit")
    run_submit_mode($state, $line);
else
    run_edit_mode($state, $line);

/*
 * Display the edit page, allowing the user to edit details
 */
function run_edit_mode($state, $line)
{
    global $t;

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        get_line_details($state, $line);

    if (auth_priv_admin())
    {
        $t->setCurrentBlock("ADMIN-BLOCK1");
        $t->setVariable("NAME", "$fullname");
        $t->setVariable("REGION", "$region");
        $t->setVariable("TRAFFIC", "$traffic");

        foreach (array("N", "S", "W", "SY", "T", "NC") as $r)
        {
            $t->setCurrentBlock("REGION-OPTION");
            $t->setVariable("VALUE", $r);
            if ($r == $region)
                $t->setVariable("SELECTED", "selected");
            $t->parseCurrentBlock();
        }

        foreach (array("", "PASS", "GOODS", "COAL") as $tr)
        {
            $t->setCurrentBlock("TRAFFIC-OPTION");
            $t->setVariable("VALUE", $tr);
            if ($tr == $traffic)
                $t->setVariable("SELECTED", "selected");
            $t->parseCurrentBlock();
        }

        $t->parse("ADMIN-BLOCK1");

        add_urls($state, $line);
        $t->parse("ADMIN-BLOCK2");
    }

    $t->setCurrentBlock("MAIN");
    $t->setVariable("DESC", $desc);

    $referrer = "show.php?" . urlenc("name=$state:$line");

    $t->setCurrentBlock("MAIN");
    $t->setVariable("RETURN-URL", $referrer);
    $t->setVariable("STATE", $state);
    $t->setVariable("LINE", $line);
    $t->setVariable("VERSION", $version);

    $t->setCurrentBlock("MAIN");
    $t->setVariable("TITLE", $fullname);
    $t->parseCurrentBlock();

    $t->show();
}

/*
 * Add the line URLs to the edit page.
 */
function add_urls($state, $line)
{
    global $db;
    global $t;

    $stmt = mysql_query("
        select
            LU.text,
            LU.url
        from
            r_line_url LU
        where
            LU.line_state = '$state'
            and
            LU.line_name = '$line'
        order by
            LU.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $n = 1;
    while ($row = mysql_fetch_array($stmt))
    {
        list ($text, $url) = $row;

        $t->setCurrentBlock("LINE-URL");
        $t->setVariable("SEQ", $n);
        $t->setVariable("LINK-URL$n", $url);
        $t->setVariable("LINK-TEXT$n", $text);
        $t->parseCurrentBlock();
        $n++;
    }
    while ($n <= 10)
    {
        $t->setCurrentBlock("LINE-URL");
        $t->setVariable("SEQ", $n++);
        $t->parseCurrentBlock();
    }
    mysql_free_result($stmt);
}

/*
 * Commit the changes (if any) to the database
 */
function run_submit_mode($state, $line)
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
        $fullname = quote_external(get_post("fullname"));
        $region = quote_external(get_post("region"));
        $traffic = quote_external(get_post("traffic"));

        /*
         * Update core details
         */
        if (!mysql_query("
            update
                r_line
            set
                description = '$fullname',
                region = '$region',
                traffic = '$traffic',
                version = $version + 1
            where
                line_state = '$state'
                and
                line_name = '$line'
                and
                version = $version
        ", $db))
        {
            rollback();
            error_page(
                "Update failed: record locked by someone else",
                "show.php?" . urlenc("name=$state:$line")
            );
        }

        /*
         * Delete and re-add the URLs
         */
        if (!mysql_query("
            delete from
                r_line_url
            where
                line_state = '$state'
                and
                line_name = '$line'
        ", $db))
        {
            rollback();
            error_page(
                "Delete failed: " . mysql_error(),
                "show.php?" . urlenc("name=$state:$line")
            );
        }

        for ($n = 1; $n <= 10; $n++)
        {
            $text = quote_external(get_post("text$n"));
            $url = quote_external(get_post("url$n"));

            if ($text != "" and $url != "")
            {
                if (!mysql_query("
                    insert into
                        r_line_url
                    values('$state', '$line', $n, '$text', '$url')
                ", $db))
                {
                    rollback();
                    error_page(
                        "Update failed: record locked by someone else",
                        "show.php?" . urlenc("name=$state;$line")
                    );
                }
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
                max(seqno)
            from
                r_line_text
            where
                line_state = '$state'
                and
                line_name = '$line'
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
                r_line_text
            values('$state', '$line', 'DESC', $seqno, '$desc', now(),
                $userid, 'U')
        ", $db))
        {
            rollback();
            error_page(
                "Update failed: record locked by someone else",
                "show.php?" . urlenc("name=$state:$line")
            );
        }
    }

    commit();

    header("Location: $return_url");
    return;
}

?>
