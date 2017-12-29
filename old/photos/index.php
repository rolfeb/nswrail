<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$stmt = mysql_query("
    select
        IFNULL(owner, 'Rolfe Bozier'),
        count(*)
    from
        r_location_photo
    where
        status = 'Y'
    group by
        IFNULL(owner, 'Rolfe Bozier')
    order by
        IFNULL(owner, 'Rolfe Bozier')
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

$n = 0;
$rows = array();
while ($row = mysql_fetch_array($stmt))
    $rows[$n++] = array($row[0], $row[1]);

mysql_free_result($stmt);

$nrows = floor(($n + 2) / 3);

for ($i = 0; $i < $nrows; $i++)
{
    $t->setCurrentBlock("COL1");
    $t->setVariable("NAME1", $rows[$i][0]);
    $t->setVariable("COUNT1", $rows[$i][1]);
    $t->setVariable("PHOTOS-URL1",
        urlenc("/photos/owner.php?owner=" . $rows[$i][0]));
    $t->parseCurrentBlock();

    if ($i + $nrows < $n)
    {
        $t->setCurrentBlock("COL2");
        $t->setVariable("NAME2", $rows[$i + $nrows][0]);
        $t->setVariable("COUNT2", $rows[$i + $nrows][1]);
        $t->setVariable("PHOTOS-URL2",
            urlenc("/photos/owner.php?owner=" . $rows[$i + $nrows][0]));
        $t->parseCurrentBlock();
    }

    if ($i + $nrows*2 < $n)
    {
        $t->setCurrentBlock("COL3");
        $t->setVariable("NAME3", $rows[$i + $nrows*2][0]);
        $t->setVariable("COUNT3", $rows[$i + $nrows*2][1]);
        $t->setVariable("PHOTOS-URL3",
            urlenc("/photos/owner.php?owner=" . $rows[$i + $nrows*2][0]));
        $t->parseCurrentBlock();
    }
}

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Railway Photo Contributors");
$t->parseCurrentBlock();

$t->show();

?>
