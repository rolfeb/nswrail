<?php

require_once "site.inc";

$title = "NSW Railway Photo Contributors";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");

$stmt = $dbi->stmt_init();
$stmt->prepare("
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
")
    or dbi_error_trace("prepare failed");

$stmt->bind_result($owner, $count);
$stmt->execute();

$n = 0;
$rows = array();
while ($stmt->fetch())
    $rows[$n++] = array($owner, $count);

$stmt->close();

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

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
