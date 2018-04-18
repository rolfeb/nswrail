<?php

require "site.inc";

function run_infra_short_lived_sections()
{
    /** @var mysqli $db */
    global $db;

    $MAX = 30;

    $tp = [
        'title' => "NSW Railway $MAX Shortest Lived Sections",
        'sections' => [],
    ];

    $STATE = "NSW";

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            R.description,
            R.line_name,
            SEV1.start_state,
            SEV1.start_name,
            SEV1.end_state,
            SEV1.end_name,
            min(SEV1.year * 12 + SEV1.month) ts1,
            SEV1.day,
            SEV1.month,
            SEV1.year,
            SEV1.year_error,
            max(SEV2.year * 12 + SEV2.month) ts2,
            SEV2.day,
            SEV2.month,
            SEV2.year,
            SEV2.year_error,
            max(SEV2.year * 12 + IFNULL(SEV2.month-1, 0)) - min(SEV1.year * 12 + IFNULL(SEV1.month-1, 0)) tdiff,
            abs(L2.distance - L1.distance)
        from
            r_line R,
            r_section_event SEV1,
            r_section_event SEV2,
            r_location L2,
            r_location L1 
        where
            R.line_state = ?
            and
            SEV1.line_state = R.line_state
            and
            SEV1.line_name = R.line_name
            and
            SEV1.type = 'ON'
            and
            SEV2.line_state = SEV1.line_state
            and
            SEV2.line_name = SEV1.line_name
            and
            SEV2.segment = SEV1.segment
            and
            SEV2.start_state = SEV1.start_state
            and
            SEV2.start_name = SEV1.start_name
            and
            SEV2.end_state = SEV1.end_state
            and
            SEV2.end_name = SEV1.end_name
            and
            SEV2.type = 'CN'
            and
            not isnull(SEV2.year)
            and
            L1.location_state = SEV1.start_state
            and
            L1.location_name = SEV1.start_name
            and
            L2.location_state = SEV2.end_state
            and
            L2.location_name = SEV2.end_name
        group by
            R.line_name,
            SEV1.start_state,
            SEV1.start_name,
            SEV1.end_state,
            SEV1.end_name
        having
            tdiff > 0
        order by
            tdiff
        limit
            $MAX
    ");

    $stmt->bind_param("s", $STATE);
    $stmt->execute();
    $stmt->bind_result($line_desc, $line, $start_state, $start_name, $end_state,
        $end_name, $ts1, $day1, $month1, $year1, $year_error1, $ts2, $day2,
        $month2, $year2, $year_error2, $tdiff, $length);

    while ($stmt->fetch()) {
        $opened_html = date_cpts2html($day1, $month1, $year1, $year_error1);
        $closed_html = date_cpts2html($day2, $month2, $year2, $year_error2);

        if ($length) {
            $length = sprintf("%.1f", $length);
        } else {
            $length = "?";
        }
        $url = '/lines/details.php?' .
            http_build_query([
                'name' => "$STATE:$line",
            ]);

        $tp['sections'][] = [
            'ne_url' => $url,
            'text' => $line_desc,
            'start' => $start_name,
            'end' => $end_name,
            'ne_opened' => $opened_html,
            'ne_closed' => $closed_html,
            'years' => floor($tdiff / 12),
            'months' => $tdiff % 12,
            'length' => $length
        ];
    }
    $stmt->close();

    return $tp;
}

normal_page_wrapper('run_infra_short_lived_sections', 'infra-short-lived-sections.latte');
