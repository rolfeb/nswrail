<?php

require_once "site.inc";

$title = "Sydney Lines";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("lines.tpl", true, true);

print_region(1, "NSW", "PASS", "Passenger Lines");
print_region(2, "NSW", "GOODS", "Freight Lines");
print_region(3, "NSW", "COAL", "Coal Lines");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

exit;

function print_region($column, $state, $traffic, $title)
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
            R.line_state = 'NSW'
            and
            R.region = 'SY'
            and
            R.traffic = ?
        order by
            R.description
   ")
       or dbi_error_trace("prepare failed");

   $stmt->bind_param("s", $traffic);
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
    $t->parse("GROUP_OR_LINK$column");

    $stmt->close();
}

?>
