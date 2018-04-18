<?php

require "site.inc";

function run_infra_turntables()
{
    /** @var mysqli $db */
    global $db;

    $tp = [
        'title' => "Railway Turntables",
        'regions' => [],
    ];

    $STATE = 'NSW';

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            R.line_state,
            R.line_name,
            R.description,
            if(
                R.region = 'N' or R.region = 'NC' or R.line_name = 'north_coast' or (R.line_name = 'main_north' and RL.seqno > 20),
                'Northern',
                if(
                    R.region = 'S' or (R.line_name = 'main_south' and RL.seqno > 43) or
                        (R.line_state = 'NSW' and R.line_name = 'south_coast' and RL.seqno > 38),
                    'Southern',
                    if(
                        R.region = 'W' or R.line_name = 'broken_hill' or (R.line_name = 'main_west' and RL.seqno > 22),
                        'Western',
                        'Metropolitan'
                    )
                )
            ) as region,
            RL.seqno,
            LTT.location_state,
            LTT.location_name,
            LTT.type,
            LTT.size_ft,
            LTT.status,
            LTT.text,
            count(LP.caption)
        from
            r_line_location RL,
            r_line R,
            r_location_turntable LTT
                left outer join r_location_photo LP
                on
                    LP.location_state = LTT.location_state
                    and
                    LP.location_name = LTT.location_name
                    and
                    LP.hold is null
                    and
                    FIND_IN_SET('turntable', LP.tags)
        where
            LTT.location_state = ?
            and
            RL.location_state = LTT.location_state
            and
            RL.location_name = LTT.location_name
            and
            RL.mainline = 'Y'
            and
            R.line_state = RL.line_state
            and
            R.line_name = RL.line_name
        group by
            R.line_state,
            R.line_name,
            R.description,
            region,
            RL.seqno,
            LTT.location_state,
            LTT.location_name,
            LTT.type,
            LTT.size_ft,
            LTT.status,
            LTT.text
        order by
            region,
            R.description,
            RL.seqno,
            LTT.seqno
    ");

    $stmt->bind_param("s", $STATE);
    $stmt->execute();
    $stmt->bind_result($line_state, $line_name, $description, $region, $seqno,
        $location_state, $location_name, $type, $size, $status, $notes, $photos);

    $type_lookup = [
        "electric"  => "E",
        "manual"    => "M",
        "unknown"   => "?",
    ];

    $status_lookup = [
        "in use"        => "In use",
        "out of use"    => "Out of use",
        "closed"        => "Closed",
        "derelict"      => "Derelict",
        "ruins"         => "Ruins",
        "no trace"      => "No trace",
        "unknown"       => "?",
    ];

    $nr = -1;
    $curr_region = "";
    $curr_line_name = "";
    while ($stmt->fetch()) {
        if ($curr_region != $region) {
            $tp['regions'][] = [
                'text' => "$region Region",
                'rows' => [],
            ];
            $nr++;
            $curr_region = $region;
        }

        if ($curr_line_name != $line_name) {
            $tp['regions'][$nr]['rows'][] = [
                'u_line' => [
                        'text' => $description,
                    ],
            ];
            $curr_line_name = $line_name;
        }

        $url = "/locations/details.php?" .
            http_build_query([
                'name' => "$location_state:$location_name",
            ]);

        if (!$size) {
            $size = "?";
        } else if (floor($size) != $size) {
            $size = sprintf("%d'%.0f\"",
                floor($size),
                ($size - floor($size)) * 12);
        } else {
            $size = "$size'";
        }

        $size_type = $size . '-' . $type_lookup[$type];

        $tp['regions'][$nr]['rows'][] = [
            'u_turntable' => [
                    'ne_url' => $url,
                    'text' => $location_name,
                    'size_type' => $size_type,
                    'status' => $status_lookup[$status],
                    'notes' => $notes,
                    'nphotos' => $photos,
                ],
        ];
    }
    $stmt->close();

    return $tp;
}

normal_page_wrapper('run_infra_turntables', 'infra-turntables.latte');
