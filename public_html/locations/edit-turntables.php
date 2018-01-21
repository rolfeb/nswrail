<?php

require_once "site.inc";

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
$t->loadTemplateFile("edit-turntables.tpl", true, true);

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

    $size_list = array("", "50'", "56'6\"", "60'", "75'", "90'", "105'");
    $type_list = array("", "manual", "electric", "unknown");
    $status_list = array("", "in use", "out of use", "closed", "derelict",
        "ruins", "no trace", "unknown");
    $sellers_list = array("N", "Y");

    $l = get_location_details($state, $location);
    $list = get_location_turntables($state, $location);

    for ($n = 1; $n <= 5; $n++)
    {
        $t->setCurrentBlock("LOCATION-TURNTABLE");
        $t->setVariable("SEQ", $n);


        $size = "";
        $type = "";
        $status = "";
        $sellers = "N";
        $text = "";
        if ($n <= count($list))
        {
            $param = $list[$n-1];

            $size       = $param["size"];
            $type       = $param["type"];
            $status     = $param["status"];
            $sellers    = $param["sellers"];
            $text       = $param["text"];
        }
        if (floor($size) != $size)
        {
            $size = sprintf("%d'%.0f\"",
                floor($size), ($size - floor($size)) * 12);
        }
        else
            $size .= "'";

        foreach ($size_list as $v)
        {
            $t->setCurrentBlock("SIZE-OPTION");

            if ($v == $size)
                $t->setVariable("SELECTED", "selected");

            $label = $v;
            if ($label == "")
                $lable = "-";

            $t->setVariable("VALUE", $v);
            $t->setVariable("LABEL", $label);
            $t->parseCurrentBlock();
        }
        $t->setVariable("SIZE", $size);

        foreach ($type_list as $v)
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

        foreach ($status_list as $v)
        {
            $t->setCurrentBlock("STATUS-OPTION");

            if ($v == $status)
                $t->setVariable("SELECTED", "selected");

            $label = $v;
            if ($label == "")
                $lable = "-";

            $t->setVariable("VALUE", $v);
            $t->setVariable("LABEL", $label);
            $t->parseCurrentBlock();
        }
        $t->setVariable("STATUS", $status);

        foreach ($sellers_list as $v)
        {
            $t->setCurrentBlock("SELLERS-OPTION");

            if ($v == $sellers)
                $t->setVariable("SELECTED", "selected");

            $label = $v;

            $t->setVariable("VALUE", $v);
            $t->setVariable("LABEL", $label);
            $t->parseCurrentBlock();
        }
        $t->setVariable("SELLERS", $sellers);

        $t->setVariable("TEXT", $text);

        $t->parse("LOCATION-TURNTABLE");
    }

    $t->setCurrentBlock("CONTENT");

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
         * Delete and re-add the turntables
         */
        $stmt = $db->stmt_init();
        $stmt->prepare("
            delete from
                r_location_turntable
            where
                location_state = ?
                and
                location_name = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ss", $state, $location);

        if (!$stmt->execute())
        {
            $db->rollback();
            error_page("Delete failed: " . $db->error, $return_url);
        }

        $stmt->prepare("
            insert into
                r_location_turntable
            values('$state', '$location', $n, '$type', '$size_ft', '$status', '$sellers', $text_str)
        ")
            or dbi_error_trace("prepare failed");

        for ($n = 1; $n <= 10; $n++)
        {
            $size = quote_external(get_post("size$n"));
            $type = quote_external(get_post("type$n"));
            $status = quote_external(get_post("status$n"));
            $sellers = quote_external(get_post("sellers$n"));
            $text = quote_external(get_post("text$n"));

            if (preg_match("/(\d+)..$/", $size, $matches))
                $size_ft = $matches[0];
            else
            if (preg_match("/(\d+)\D\D(?:(\d+))?/", $size, $matches))
                $size_ft = sprintf("%.1f", $matches[0] + $matches[1] / 12);
            else
                $size_ft = '';

            if ($size != "" || $type != "")
            {
                $stmt2->bind_param("ssisdsss", $state, $location, $n, $type,
                    $size_ft, $status, $sellers, $text);

                if (!$stmt2->execute())
                {
                    $db->rollback();
                    error_page("Update failed: record locked by someone else",
                        $return_url);
                }
            }
        }
        $stmt2->close();
        $stmt1->close();

        $db->commit();
    }

    header("Location: $return_url");
    return;
}

?>
