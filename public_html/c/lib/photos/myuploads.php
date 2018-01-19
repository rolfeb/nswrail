<?php

require_once "site.inc";

if (!auth_priv_normal())
    noperm_page();

$username = $_SESSION['username'];

global $BASE_PATH, $db;
$template_dir   = "$BASE_PATH/c/tpl";

$t = new HTML_Template_ITX($template_dir);
if (!$t->loadTemplateFile("myuploads.tpl", true, true))
    return "<!-- ERROR: couldn't open myuploads.tpl -->\n";

$title = "Pending Submissions";

$stmt = $db->stmt_init();
$stmt->prepare("
    select
        RP.location_state,
        RP.location_name,
        RP.file,
        RP.seqno,
        RP.day,
        RP.month,
        RP.year,
        RP.year_error,
        RP.submit_date,
        RP.owner,
        RP.width,
        RP.height,
        RP.caption,
        RP.status
    from
        r_location_photo RP,
        r_person P
    where
        RP.status = 'U'
        and
        RP.released = 'N'
        and
        RP.owner_uid = P.uid
        and
        P.email = ?
    order by
        RP.submit_date
")
    or dbi_error_trace("prepare failed");


$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($state, $location, $file, $seqno, $day, $month, $year,
    $year_error, $submit_date, $owner, $width, $height, $description, $status);

while ($stmt->fetch())
{
    $date_str = date_cpts2text($day, $month, $year, $year_error = 0);

    $t->setCurrentBlock("PHOTO");
    $t->setVariable("STATE", $state);
    $t->setVariable("LOCATION", $location);
    $t->setVariable("DATE", $date_str);
    $t->setVariable("SIZE", "$width x $height");
    $t->setVariable("UPLOADED", $submit_date);
    $t->setVariable("STATUS", $status);
    $t->setVariable("DESCRIPTION", $description);
    $t->parseCurrentBlock();
}
$stmt->close();

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"),
    array(
        'HEAD-EXTRA'    => implode(file("$template_dir/myuploads.hdr"), ""),
    )
);

exit;

?>
