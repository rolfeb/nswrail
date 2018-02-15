<?php

require_once "site.inc";

$value = quote_external(get_post("search"));

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("location.tpl");

$title = "Search Results";

$value .= "%";  /* prefix matching */

$results = array();
$locations_seen = array();

/*
 * Select current location names from r_location
 */
$stmt = $db->stmt_init();
$stmt->prepare("
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
        RL.location_name LIKE ?
        and
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
        and
        RL.mainline = 'Y'
")
    or dbi_error_trace("prepare failed");

$stmt->bind_param("s", $value);
$stmt->execute();
$stmt->bind_result($line_state, $line_name, $description, $location_state,
    $location_name);

while ($stmt->fetch())
{
    if (!array_key_exists($location_name, $locations_seen))
    {
        $results[$location_name] = array($line_state, $line_name, $description, $location_state, $location_name, "");
        $locations_seen[$location_name] = 1;
    }
}
$stmt->close();

/*
 * Select historic location names from r_location_event
 */
$stmt = $db->stmt_init();
$stmt->prepare("
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
        RE.current_name LIKE ?
        and
        RE.current_name != RL.location_name
        and
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
        and
        RL.mainline = 'Y'
")
    or dbi_error_trace("prepare failed");

$stmt->bind_param("s", $value);
$stmt->execute();
$stmt->bind_result($line_state, $line_name, $description, $location_state,
    $location_name, $old_name);

while ($stmt->fetch())
{
    $location = "$location_name (was $old_name)";

    if (!array_key_exists($location_name, $locations_seen))
    {
        $results[$location] = array($line_state, $line_name, $description, $location_state, $location_name, $old_name);

        $locations_seen[$location_name] = 1;
    }
}
$stmt->close();

ksort($results);

/*
 * If there is exactly 1 result, just send the user to that page
 */
if (count($results) == 1)
{
    list($line_state, $line_name, $description, $location_state,
        $location_name, $old_name) = current($results);

    $url = "/locations/show.php?" . urlenc("name=$location_state:$location_name");
    header("Location: $url");
    return;
}

/*
 * Otherwise give a list of links to the locations
 */
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

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
