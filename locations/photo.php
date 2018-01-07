<?php

require_once "site.inc";

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

$seqno_list = get_available_photos($state, $location);

global $dbi;

$stmt = $dbi->stmt_init();
$stmt->prepare("
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
        L.location_state = ?
        and
        L.location_name = ?
        and
        LP.location_state = L.location_state
        and
        LP.location_name = L.location_name
        and
        LP.seqno = ?
        and
        LP.status = 'Y'
")
    or dbi_error_trace("prepare failed");

$stmt->bind_param("ssi", $state, $location, $seqno);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($type, $file, $owner, $day, $month, $year, $year_error,
    $caption);

while ($stmt->fetch())
{
    $date = date_cpts2text($day, $month, $year, $year_error);

    if ($owner == "")
        $owner = "Rolfe Bozier";

    $back_url = "show.php?"
        . urlenc("name=$state:$location");
    if ($line)
        $back_url .= urlenc("&line=$line");

    $title = locn_fulltitle($location, $type);

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", locn_fulltitle($location, $type));
    $t->setVariable("IMAGE", "/locations/photos/$file");
    $t->setVariable("TEXT", $caption);
    $t->setVariable("IMG-ALT-TEXT", htmlentities($caption));
    $t->setVariable("DATE", $date);
    $t->setVariable("OWNER", $owner);
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

$stmt->close();

display_page($title, $t->get("CONTENT"));

exit();

function get_available_photos($state, $location)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            LP.seqno
        from
            r_location_photo LP
        where
            LP.location_state = ?
            and
            LP.location_name = ?
            and
            LP.status = 'Y'
        order by
            LP.seqno
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($seqno);

    $arr_seqno = array();

    while ($stmt->fetch())
        array_push($arr_seqno, $seqno);

    $stmt->close();

    return $arr_seqno;
}

?>
