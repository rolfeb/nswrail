<?php

require_once "site.inc";
require_once "dbutil.inc";

$name = quote_external(get_post("name"));           /* mandatory */

list($state, $line, $seqno) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("linemap.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("PAGE-HEADER", page_header());
$t->setVariable("PAGE-MENU", display_menu());
$t->parseCurrentBlock();

$count = get_linemap_count($state, $line);

list($fullname, $region, $traffic, $maxsegment, $desc, $version)
    = dbline_get_details($state, $line);

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
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
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
    ")
        or dbi_error_trace("prepare failed");

    $arr_seqno = array();

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($seqno);

    while ($stmt->fetch())
        array_push($arr_seqno, $seqno);

    $stmt->close();

    return $arr_seqno;
}

?>
