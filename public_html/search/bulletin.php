<?php

require "site.inc";

$tp = [
    'title' => "ARHS Bulletin Index Search",
];

$stmt = $db->stmt_init();
$stmt->prepare("
    select
        max(month),
        year
    from
        r_bulletin_index
    where
        year = (select max(year) from r_bulletin_index)
    group by
        year
")
    or dbi_error_trace("prepare failed");

$stmt->execute();
$stmt->bind_result($month, $year);

if ($stmt->fetch()) {
    $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
        "Sep", "Oct", "Nov", "Dec"];

    $last_date = $months[$month-1] . " $year";
} else {
    $last_date = "[unknown]";
}
$stmt->close();

$tp['latest_year'] = $last_date;

normal_page('search-bulletin.latte', $tp);

?>
