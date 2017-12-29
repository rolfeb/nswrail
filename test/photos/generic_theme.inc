<?php

require_once "../init.inc";
require_once "../util.inc";

function display_theme($db, $theme, $template, $title)
{
    $order = quote_external(get_post("order", "name"));
    $mode = quote_external(get_post("mode", ""));

    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("$template");
    $t->setCurrentBlock("CONTROLS");
    $t->setVariable("TOP", top());
    $t->setVariable("MENU", menu());
    $t->parseCurrentBlock();

    if ($order == "year")
        $sort = "RP.year, RL.location_name, RP.seqno";
    else
        $sort = "RL.location_name, RP.seqno";

    $stmt = mysql_query("
        select
            RL.line_state,
            RL.line_name,
            RL.location_state,
            RL.location_name,
            RP.file,
            RP.seqno,
            RP.year,
            RP.owner,
            RP.caption
        from
            r_line_location RL,
            r_location_photo RP
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            RP.status = 'Y'
            and
            FIND_IN_SET('$theme', RP.themes)
        order by
            $sort
    ", $db)
        or dbi_error_trace("prepare failed");

    $COLUMNS = 4;

    $n = 0;
    while ($row = mysql_fetch_array($stmt))
    {
        list($state, $line, $location_state, $location, $file, $seqno, $year,
            $owner, $caption) = $row;

        $href = "/locations/photo.php?"
            . urlenc("name=$location_state:$location:$seqno");

        if ($mode == "thumbnail")
        {
            $col = $n % $COLUMNS;

            $thumb = "/locations/photos/small/$file";

            $t->setCurrentBlock("CELL");
            $t->setVariable("THUMB-IMG", $thumb);
            $t->setVariable("PHOTO-URL", $href);
            $t->setVariable("LOCATION", $location);
            $t->parseCurrentBlock();

            if ($col == $COLUMNS - 1)
            {
                $t->setCurrentBlock("THUMBNAIL-ROW");
                $t->parseCurrentBlock();
            }
        }
        else
        {
            empty($owner) && $owner = "Rolfe Bozier";

            $t->setCurrentBlock("LISTING");
            $t->setVariable("HREF", $href);
            $t->setVariable("NAME", $location);
            $t->setVariable("DATE", $year);
            $t->setVariable("TEXT", $caption);
            $t->parseCurrentBlock();
        }

        $n++;

    }
    mysql_free_result($stmt);

    $t->setCurrentBlock("MAIN");
    $t->setVariable("TITLE", $title);
    $t->parseCurrentBlock();

    $t->show();
}
?>
