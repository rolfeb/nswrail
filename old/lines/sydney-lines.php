<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "dbutil.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("lines.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

print_region(1, "NSW", "PASS", "Passenger Lines");
print_region(2, "NSW", "GOODS", "Freight Lines");
print_region(3, "NSW", "COAL", "Coal Lines");

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Sydney Lines");
$t->parseCurrentBlock();

$t->show();

exit;

function print_region($column, $state, $traffic, $title)
{
    global $db, $t;

    $stmt = mysql_query("
        select
            R.line_name,
            R.description
        from
            r_line R
        where
            R.line_state = 'NSW'
            and
            R.region = 'SY'
            and
            R.traffic = '$traffic'
        order by
            R.description
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $t->setCurrentBlock("GROUP$column");
    $t->setVariable("REGION$column", $title);
    $t->parseCurrentBlock();

    while ($row = mysql_fetch_array($stmt))
    {
        list($line, $description) = $row;

        $line_class = get_line_status_class($state, $line);

        $t->setCurrentBlock("LINK$column");
        $t->setVariable("URL$column", "show.php?"
            . urlenc("name=$state:$line"));
        $t->setVariable("NAME$column", $description);
        $t->setVariable("LINE-CLASS", $line_class);
        $t->parseCurrentBlock();
    }
    $t->parse("GROUP_OR_LINK$column");

    mysql_free_result($stmt);
}

?>
