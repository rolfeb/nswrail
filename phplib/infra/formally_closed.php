<?php

require "site.inc";

function run_infra_formally_closed_lines()
{
    $tp = [
        'title' => "Formally Closed NSW Railway Lines",
        'lines' => [],
    ];

    $closed = [
        [
            "NSW", "ballina", "Ballina Branch",
            1, 6, 1948,
            "Ballina to Booyong Railway (Cessation of Operation) Act 1953 No 13",
            "http://www.austlii.edu.au/au/legis/nsw/num_act/btbrooa1953n13512.txt"
        ],
        [
            "NSW", "westby", "Westby Branch",
            24, 1, 1952,
            "", ""
        ],
        [
            "NSW", "richmond", "Richmond to Kurrajong Line",
            26, 7, 1952,
            "Richmond to Kurrajong Railway (Cessation of Operation) Act 1954 No 9",
            "http://www.austlii.edu.au/au/legis/nsw/num_act/rtkrooa1954n9564.txt"
        ],
        [
            "NSW", "morpeth", "Morpeth Branch",
            31, 8, 1953,
            "Maitland to Morpeth Railway (Cessation of Operation) Act 1953 No 38",
            "http://www.austlii.edu.au/au/legis/nsw/num_act/mtmrooa1953n38536.txt"
        ],
        [
            "NSW", "kunama", "Kunama Branch",
            1, 2, 1957,
            "", ""
        ],
        [
            "NSW", "taralga", "Taralga Branch",
            1, 5, 1957,
            "", ""
        ],
        [
            "NSW", "camden", "Camden Branch",
            1, 1, 1963,
            "Campbelltown to Camden Tramway and Jerilderie towards Deniliquin Railway Act 1963 No 8",
            "http://www.austlii.edu.au/au/legis/nsw/num_act/ctctajtdra1963n8713.txt",
        ],
        [
            "NSW", "dorrigo", "Dorrigo Branch",
            9, 11, 1993,
            "GLENREAGH TO DORRIGO RAILWAY (CLOSURE) ACT 1993",
            "http://www.austlii.edu.au/au/legis/nsw/consol_act/gtdra1993369.txt",
        ],
    ];

    foreach ($closed as $l)
    {
        list($line_state, $line_name, $line_desc, $d, $m, $y, $act, $act_url) = $l;

        $url = '/lines/details.php?' .
            http_build_query([
                'name' => "$line_state:$line_name",
            ]);

        $tp['lines'][] = [
            'ne_url' => $url,
            'text' => $line_desc,
            'closed' => date_cpts2text($d, $m, $y, 0),
            'nc_act_url' => $act_url,
            'act' => $act,
        ];
    }
    return $tp;
}

normal_page_wrapper('run_infra_formally_closed_lines', 'infra-formally-closed-lines.latte');
