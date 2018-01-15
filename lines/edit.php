<?php

require_once "site.inc";
require_once "dbutil.inc";

$name = quote_external(get_post("name"));       /* mandatory */
$state = quote_external(get_post("state"));     /* obsolete */
$line = quote_external(get_post("line"));       /* obsolete */
$mode = quote_external(get_post("mode", ""));   /* optional */

if ($name)
    list($state, $line) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("edit.tpl", true, true);

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
        dbline_get_details($state, $line);

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

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $fullname);
    $t->parseCurrentBlock();

    display_page($fullname, $t->get("CONTENT"),
        array(
            "BODY-EXTRA" => 'class="edit-mode"'
        )
    );
}

/*
 * Add the line URLs to the edit page.
 */
function add_urls($state, $line)
{
    global $t;
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LU.text,
            LU.url
        from
            r_line_url LU
        where
            LU.line_state = ?
            and
            LU.line_name = ?
        order by
            LU.seqno
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($text, $url);

    $n = 1;
    while ($stmt->fetch())
    {
        $t->setCurrentBlock("LINE-URL");
        $t->setVariable("SEQ", $n);
        $t->setVariable("LINK-URL$n", $url);
        $t->setVariable("LINK-TEXT$n", $text);
        $t->parseCurrentBlock();
        $n++;
    }
    $stmt->close();
    while ($n <= 10)
    {
        $t->setCurrentBlock("LINE-URL");
        $t->setVariable("SEQ", $n++);
        $t->parseCurrentBlock();
    }
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
        $stmt = $db->stmt_init();
        $stmt->prepare("
            update
                r_line
            set
                description = ?,
                region = ?,
                traffic = ?,
                version = version + 1
            where
                line_state = ?
                and
                line_name = ?
                and
                version = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("sssssi", $fullname, $region, $traffic, $state,
            $line, $version);

        if (!$stmt->execute())
        {
            $db->rollback();
            error_page(
                "Update failed: record locked by someone else",
                "show.php?" . urlenc("name=$state:$line")
            );
        }
        $stmt->close();

        /*
         * Delete and re-add the URLs
         */
        $stmt = $db->stmt_init();
        $stmt->prepare("
            delete from
                r_line_url
            where
                line_state = ?
                and
                line_name = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ss", $state, $line);

        if (!$stmt->execute())
        {
            $db->rollback();
            error_page(
                "Delete failed: " . $db->error,
                "show.php?" . urlenc("name=$state:$line")
            );
        }
        $stmt->close();

        $stmt = $db->stmt_init();
        $stmt->prepare("
            insert into
                r_line_url
            values(?, ?, ?, ?, ?)
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssiss", $state, $line, $n, $text, $url);

        for ($n = 1; $n <= 10; $n++)
        {
            $text = quote_external(get_post("text$n"));
            $url = quote_external(get_post("url$n"));

            if ($text != "" and $url != "")
            {
                if (!$stmt->execute())
                {
                    $db->rollback();
                    error_page(
                        "Update failed: record locked by someone else",
                        "show.php?" . urlenc("name=$state;$line")
                    );
                }
                $stmt->close();
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

        $stmt = $db->stmt_init();
        $stmt->prepare("
            select
                max(seqno)
            from
                r_line_text
            where
                line_state = ?
                and
                line_name = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ss", $state, $line);
        $stmt->execute();
        $stmt->bind_result($seqno);

        $stmt->fetch();

        $stmt->close();

        $seqno++;

        $stmt = $db->stmt_init();
        $stmt->prepare("
            insert into
                r_line_text
            values(?, ?, 'DESC', ?, ?, now(), ?, 'U')
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssisi", $state, $line, $seqno, $desc, $userid);

        if (!$stmt->execute())
        {
            rollback();
            error_page(
                "Update failed: record locked by someone else",
                "show.php?" . urlenc("name=$state:$line")
            );
        }
        $stmt->close();
    }

    $db->commit();

    header("Location: $return_url");
    return;
}

?>
