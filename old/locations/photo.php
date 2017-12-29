<?php

require_once "../init.inc";
require_once "../util.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$seqno = quote_external(get_post("seqno"));         /* obsolete */
$line = quote_external(get_post("line"));           /* optional */

if ($name)
    list($state, $location, $seqno) = explode(":", $name);

$location = make_canonical_name($location);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("photo.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$seqno_list = get_available_photos($state, $location);

$stmt = mysql_query("
    select
        L.type,
        LP.file,
        LP.owner,
        LP.day,
        LP.month,
        LP.year,
        LP.year_error,
        LP.caption
    from
        r_location L,
        r_location_photo LP
    where
        L.location_state = '$state'
        and
        L.location_name = '$location'
        and
        LP.location_state = L.location_state
        and
        LP.location_name = L.location_name
        and
        LP.seqno = $seqno
        and
        LP.status = 'Y'
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

while ($row = mysql_fetch_array($stmt))
{
    list($type, $file, $owner, $day, $month, $year, $year_error,
        $caption) = $row;

    $date = date_cpts2text($day, $month, $year, $year_error);

    if ($owner == "")
        $owner = "Rolfe Bozier";

    $owner_url = get_user_url($owner);

    $back_url = "show.php?"
        . urlenc("name=$state:$location");
    if ($line)
        $back_url .= urlenc("&line=$line");

    $t->setCurrentBlock("MAIN");
    $t->setVariable("TITLE", locn_fulltitle($location, $type));
    $t->setVariable("IMAGE", "/locations/photos/$file");
    $t->setVariable("TEXT", $caption);
    $t->setVariable("IMG-ALT-TEXT", htmlentities($caption));
    $t->setVariable("DATE", $date);
    $t->setVariable("OWNER", $owner);
    if ($owner_url)
        $t->setVariable("OWNER-URL", $owner_url);
    $t->setVariable("LOCATION-URL", $back_url);
    $t->setVariable("LOCATION-TEXT", locn_fulltitle($location, $type));

    /* Navigation DIV */

    $count = count($seqno_list);
    $index = array_search($seqno, $seqno_list);

    if ($index > 0)
    {
        $first_seq = $seqno_list[0];
        $prev_seq = $seqno_list[$index - 1];

        $first_url = urlenc("?name=$state:$location:$first_seq");
        $prev_url = urlenc("?name=$state:$location:$prev_seq");

        if ($line)
        {
            $first_url .= urlenc("&line=$line");
            $prev_url .= urlenc("&line=$line");
        }

        $t->setVariable("FIRST-URL", $first_url);
        $t->setVariable("PREV-URL", $prev_url);
    }
    else
        $t->touchBlock("NAV-PREV-DISABLED");

    if ($index < $count - 1)
    {
        $next_seq = $seqno_list[$index + 1];
        $next_url = urlenc("?name=$state:$location:$next_seq");
        if ($line)
            $next_url .= urlenc("&line=$line");

        $t->setVariable("NEXT-URL", $next_url);
    }
    else
        $t->touchBlock("NAV-NEXT-DISABLED");

    $t->setVariable("PHOTO-SEQ", $index + 1);
    $t->setVariable("PHOTO-COUNT", $count);

    $t->parseCurrentBlock();
}
$t->show();

mysql_free_result($stmt);

function get_available_photos($state, $location)
{
    global $db;

    $stmt = mysql_query("
        select
            LP.seqno
        from
            r_location_photo LP
        where
            LP.location_state = '$state'
            and
            LP.location_name = '$location'
            and
            LP.status = 'Y'
        order by
            LP.seqno
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

function get_user_url($owner)
{
    global $db;

    $stmt = mysql_query("
        select
            U.url
        from
            r_user U
        where
            U.fullname = '$owner'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $url = "";

    if ($row = mysql_fetch_array($stmt))
    {
        $url = $row[0];
    }
    mysql_free_result($stmt);

    return $url;
}

?>
