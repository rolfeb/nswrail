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

print_region(1, "NSW", "T", "Trunk Lines");
print_region(1, "NSW", "N", "Northern Region");

print_region(2, "NSW", "S", "Southern Region");

print_region(3, "NSW", "W", "Western Region");
print_region(3, "VIC", "W", "Victorian");
print_region(3, "SA", "T", "South Australian");
print_region(3, "QLD", "S", "Queensland");

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Lines");
$t->parseCurrentBlock();

$t->show();

exit;

function print_region($column, $state, $region, $title)
{
    global $db, $t;

    $stmt = mysql_query("
        select
            R.line_name,
            R.description
        from
            r_line R
        where
            R.line_state = '$state'
            and
            R.region = '$region'
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
    mysql_free_result($stmt);

    $t->parse("GROUP_OR_LINK$column");

}

?>
