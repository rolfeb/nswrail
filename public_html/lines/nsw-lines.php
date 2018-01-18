<?php

require_once "site.inc";
require_once "dbutil.inc";

$title = "NSW Lines";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("lines.tpl", true, true);

print_region(1, "NSW", "T", "Trunk Lines");
print_region(1, "NSW", "N", "Northern Region");

print_region(2, "NSW", "S", "Southern Region");

print_region(3, "NSW", "W", "Western Region");
print_region(3, "VIC", "W", "Victorian");
print_region(3, "SA", "T", "South Australian");
print_region(3, "QLD", "S", "Queensland");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

exit;

function print_region($column, $state, $region, $title)
{
    global $db, $t;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            R.line_name,
            R.description
        from
            r_line R
        where
            R.line_state = ?
            and
            R.region = ?
        order by
            R.description
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $region);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($line, $description);

    $t->setCurrentBlock("GROUP$column");
    $t->setVariable("REGION$column", $title);
    $t->parseCurrentBlock();

    while ($stmt->fetch())
    {
        $line_class = get_line_status_class($state, $line);

        $t->setCurrentBlock("LINK$column");
        $t->setVariable("URL$column", "show.php?"
            . urlenc("name=$state:$line"));
        $t->setVariable("NAME$column", $description);
        $t->setVariable("LINE-CLASS", $line_class);
        $t->parseCurrentBlock();
    }
    $stmt->close();

    $t->parse("GROUP_OR_LINK$column");

}

?>
