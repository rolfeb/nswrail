<?php

require_once "../init.inc";
require_once "../util.inc";

$value = quote_external(get_post("search"));

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("search.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$value .= "%";  /* prefix matching */

$results = array();
$locations_seen = array();

$stmt = mysql_query("
    select
        RL.line_state,
        RL.line_name,
        R.description,
        RL.location_state,
        RL.location_name
    from
        r_line R,
        r_line_location RL
    where
        RL.location_name LIKE '$value'
        and
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
        and
        RL.mainline = 'Y'
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

while ($row = mysql_fetch_array($stmt))
{
    list ($line_state, $line_name, $description, $location_state,
        $location_name) = $row;

    if (!array_key_exists($location_name, $locations_seen))
    {
        $results[$location_name] = array($line_state, $line_name, $description, $location_state, $location_name, "");
        $locations_seen[$location_name] = 1;
    }
}
mysql_free_result($stmt);

$stmt = mysql_query("
    select
        RL.line_state,
        RL.line_name,
        R.description,
        RL.location_state,
        RL.location_name,
        RE.current_name
    from
        r_line R,
        r_line_location RL,
        r_location_event RE
    where
        RE.location_state = RL.location_state
        and
        RE.location_name = RL.location_name
        and
        RE.current_name LIKE '$value'
        and
        RE.current_name != RL.location_name
        and
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
        and
        RL.mainline = 'Y'
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

while ($row = mysql_fetch_array($stmt))
{
    list ($line_state, $line_name, $description, $location_state,
        $location_name, $old_name) = $row;

    $location = "$location_name (was $old_name)";

    if (!array_key_exists($location_name, $locations_seen))
    {
        $results[$location] = array($line_state, $line_name, $description, $location_state, $location_name, $old_name);

        $locations_seen[$location_name] = 1;
    }
}
mysql_free_result($stmt);

ksort($results);

$count = 1;

foreach ($results as $text => $r)
{
    list($line_state, $line_name, $description, $location_state,
        $location_name, $old_name) = $r;

    if ($count++ > 200)
    {
        $t->touchBlock("TRUNCATED-WARNING");
        break;
    }

    $t->setCurrentBlock("RESULT");
    $t->setVariable("LINE-URL", "/lines/show.php?"
        . urlenc("name=$line_state:$line_name"));
    $t->setVariable("LINE-TEXT", $description);
    $t->setVariable("LOCATION-URL", "/locations/show.php?"
        . urlenc("name=$location_state:$location_name"));
    $t->setVariable("LOCATION-TEXT", $text);

    $t->parseCurrentBlock();
}

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Search Results");
$t->parseCurrentBlock();

$t->show();


?>
