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
$t->loadTemplateFile("gphoto.tpl", true, true);

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

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("IMAGE", "/locations/photos/$file");
    $t->setVariable("TEXT", $caption);
    $t->setVariable("IMG-ALT-TEXT", htmlentities($caption));
    $t->setVariable("DATE", $date);
    $t->setVariable("OWNER", $owner);
    $t->setVariable("LINK-URL", "/locations/photo.php?name=$state:$location:$seqno");
    $t->parseCurrentBlock();
}

$stmt->close();

display_page($location, $t->get("CONTENT"),
    array(
        'BASIC-PAGE'    => 1,
    )
);

exit();

?>
