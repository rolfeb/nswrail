<?php

require "site.inc";

function run_search_bulletin()
{
    global $db;

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
    ");
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

    return $tp;
}

normal_page_wrapper('run_search_bulletin', 'search-bulletin.latte');

?>
