<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_infra_tunnels()
{
    /** @var mysqli $db */
    global $db;

    $tp = [
        'title' => "NSW Railway Tunnels",
        'rows' => [],
    ];

    $STATE = "NSW";

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            R.line_state,
            R.line_name,
            R.description,
            L.location_name,
            L.status,
            L.distance,
            LTU.lengths,
            LTU.type,
            RL1.location_name,
            RL2.location_name
        from
            r_line R,
            r_line_location RL,
            r_line_location RL1,
            r_line_location RL2,
            r_location L left join r_location_tunnel LTU on
                L.location_state = LTU.location_state
                and
                L.location_name = LTU.location_name
        where
            R.line_state = ?
            and
            RL.line_state = R.line_state
            and
            RL.line_name = R.line_name
            and
            L.location_state = RL.location_state
            and
            L.location_name = RL.location_name
            and
            L.type = 'tunnel'
            and
            RL1.line_state = RL.line_state
            and
            RL1.line_name = RL.line_name
            and
            RL1.segment = RL.segment
            and
            RL1.seqno = RL.seqno - 1
            and
            RL2.line_state = R.line_state
            and
            RL2.line_name = R.line_name
            and
            RL2.segment = RL.segment
            and
            RL2.seqno = RL.seqno + 1
        order by
            R.description,
            RL.segment,
            RL.seqno
    ");

    $stmt->bind_param("s", $STATE);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($line_state, $line_name, $description, $location_name,
        $status, $distance, $lengths, $type, $prev_location, $next_location);

    $prev_line_name = "";
    while ($stmt->fetch()) {
        if ($line_name != $prev_line_name)
        {
            $tp['rows'][] = [
                'u_line' => [
                        'text' => $description,
                    ],
            ];
        }

        if ($distance != "")
            $distance = sprintf("%.1f km", $distance);
        else
            $distance = "???";

        $photos = count_photos($STATE, $location_name);
        if ($photos == 0) {
            $photos = "";
        }

        $len_html = join('<br>', explode(',', tunnel_lengths2text($lengths)));
        $url = "/locations/details.php?" .
            http_build_query([
                'name' => "$STATE:$location_name",
            ]);

        $tp['rows'][] = [
            'u_tunnel' => [
                    'ne_url' => $url,
                    'text' => $location_name,
                    'type' => tunnel_type2text($type),
                    'status' => locn_status2text($status),
                    'ne_length' => $len_html,
                    'nphotos' => $photos,
                    'distance' => $distance,
                    'between' => "$prev_location and $next_location",
                ],
        ];

        $prev_line_name = $line_name;
    }
    $stmt->close();

    return $tp;
}

normal_page_wrapper('run_infra_tunnels', 'infra-tunnels.latte');
