<?php

require_once "site.inc";

$title = "Locations with incoming photos";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("incoming.tpl");

if (!auth_priv_admin())
    error($t, $title, "Error: you do not have \"admin\" privilege");

$stmt = $db->stmt_init();
$stmt->prepare("
    select distinct
        R.description,
        L.location_state,
        L.location_name,
        count(*)
    from
        r_line R,
        r_line_location RL,
        r_location L,
        r_location_photo LP
    where
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
        and
        RL.mainline = 'Y'
        and
        RL.location_state = L.location_state
        and
        RL.location_name = L.location_name
        and 
        L.location_state = LP.location_state
        and
        L.location_name = LP.location_name
        and
        LP.status = 'U'
    group by
        L.location_state,
        L.location_name
    order by
        RL.line_name,
        RL.seqno
")
    or dbi_error_trace("prepare failed");

$stmt->bind_result($line, $state, $location, $count);
$stmt->execute();

$curr_line = "";
while ($stmt->fetch())
{
    $href = "/locations/edit-photos.php?"
        . urlenc("name=$state:$location");
    $href .= urlenc("&redirect=" . $_SERVER["PHP_SELF"]);

    if ($curr_line != $line)
    {
        if ($curr_line != "")
            $t->parse("LINE");
        $t->setCurrentBlock("LINE");
        $t->setVariable("LINE", $line);
    }

    $t->setCurrentBlock("LOCATION");
    $t->setVariable("URL", $href);
    $t->setVariable("NAME", $location);
    $t->setVariable("COUNT", $count);
    $t->parseCurrentBlock();

    $curr_line = $line;
}
if ($curr_line != "")
    $t->parse("LINE");

$stmt->close();

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

exit;

function error($t, $title, $message)
{
    $t->setCurrentBlock("ERROR");
    $t->setVariable("MESSAGE", $message);
    $t->parseCurrentBlock();

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $title);
    $t->parseCurrentBlock();

    display_page($title, $t->get("CONTENT"));

    exit;
}

?>
