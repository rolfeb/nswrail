<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");

page_start($t);

if (!auth_priv_admin())
    error($t, "Error: you do not have \"admin\" privilege");

$stmt = mysql_query("
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
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

$curr_line = "";
while ($row = mysql_fetch_array($stmt))
{
    list($line, $state, $location, $count) = $row;

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

mysql_free_result($stmt);

page_end($t);

exit;

function page_start($t)
{
    $t->loadTemplateFile("incoming.tpl");
    $t->setCurrentBlock("CONTROLS");
    $t->setVariable("TOP", top());
    $t->setVariable("MENU", menu());
    $t->parseCurrentBlock();
}

function page_end($t)
{
    $t->setCurrentBlock("MAIN");
    $t->setVariable("TITLE", "Locations with incoming photos");
    $t->parseCurrentBlock();

    $t->show();
}

function error($t, $message)
{
    $t->setCurrentBlock("ERROR");
    $t->setVariable("MESSAGE", $message);
    $t->parseCurrentBlock();

    page_end($t);
    exit;
}

?>
