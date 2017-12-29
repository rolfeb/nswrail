<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "dbutil.inc";

$name = quote_external(get_post("name"));           /* mandatory */

list($state, $line, $seqno) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("linemap.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$count = get_linemap_count($state, $line);

list($fullname, $region, $traffic, $maxsegment, $desc, $version)
    = get_line_details($state, $line);

$back_url = "show.php?"
    . urlenc("name=$state:$line");

$suffix = "";
if ($state != "NSW")
    $suffix = "_" . strtolower($state);

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", $fullname);
$base = sprintf("%s%s%02d", $line, $suffix, $seqno);
$t->setVariable("SHEET-URL", "/lines/linemaps/$base.png");
if (file_exists("imaps/$base.map"))
    $t->setVariable("IMAGEMAP", implode("", file("imaps/$base.map")));

/* Navigation DIV */

if ($seqno > 1)
{
    $first_seq = 1;
    $prev_seq = $seqno - 1;

    $first_url = urlenc("?name=$state:$line:$first_seq");
    $prev_url = urlenc("?name=$state:$line:$prev_seq");

    $t->setVariable("FIRST-URL", $first_url);
    $t->setVariable("PREV-URL", $prev_url);
}
else
    $t->touchBlock("NAV-PREV-DISABLED");

if ($seqno < $count)
{
    $next_seq = $seqno + 1;
    $last_seq = $count;

    $next_url = urlenc("?name=$state:$line:$next_seq");
    $last_url = urlenc("?name=$state:$line:$last_seq");

    $t->setVariable("NEXT-URL", $next_url);
    $t->setVariable("LAST-URL", $last_url);
}
else
    $t->touchBlock("NAV-NEXT-DISABLED");

$t->setVariable("SHEET-SEQ", $seqno);
$t->setVariable("SHEET-COUNT", $count);
$t->parseCurrentBlock();

$t->show();

function get_available_maps($state, $line)
{
    global $db;

    $stmt = mysql_query("
        select
            RM.seqno
        from
            r_line_map RM
        where
            RM.line_state = '$state'
            and
            RM.line_name = '$line'
        order by
            RM.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $arr_seqno = array();

    while ($row = mysql_fetch_array($stmt))
    {
        array_push($arr_seqno, $row[0]);
    }
    mysql_free_result($stmt);

    return $arr_seqno;
}

?>
