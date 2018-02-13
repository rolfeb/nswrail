<?php

require "site.inc";

$lines_nsw = [
    [
        [ 'Trunk Lines',        [ 'NSW', 'T', NULL ] ],
        [ 'Northern Region',    [ 'NSW', 'N', NULL ] ],
    ],
    [
        [ 'Southern Region',    [ 'NSW', 'S', NULL ] ],
    ],
    [
        [ 'Western Region',     [ 'NSW', 'W', NULL ] ],
        [ 'Victorian',          [ 'VIC', 'W', NULL ] ],
        [ 'South Australian',   [ 'SA', 'T', NULL ] ],
        [ 'Queensland',         [ 'QLD', 'S', NULL ] ],
    ],
];

$lines_sydney = [
    [
        [ 'Passenger Lines',    [ 'NSW', 'SY', 'PASS' ] ],
    ],
    [
        [ 'Freight Lines',      [ 'NSW', 'SY', 'GOODS' ] ],
    ],
    [
        [ 'Coal Lines',         [ 'NSW', 'SY', 'COAL' ] ],
    ],
];

$lines_newcastle = [
    [
        [ 'Passenger Lines',    [ 'NSW', 'NC', 'PASS' ] ],
        [ 'Freight Lines',      [ 'NSW', 'NC', 'GOODS' ] ],
    ],
    [
        [ 'South Maitland Railway', [ 'NSW', 'NC', 'SMR' ] ],
    ],
    [
        [ 'Colliery Lines',     [ 'NSW', 'NC', 'COAL' ] ],
    ],
];

function retrieve_category($category, $params)
{
    global $db;

    $category = [
        'name' => $category,
        'lines' => [],
    ];

    list($state, $region, $traffic) = $params;

    if (!is_null($traffic)) {
        $where_extra = ' and R.traffic = ?';
    } else {
        $where_extra = '';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            R.line_name,
            R.description
        from
            r_line R
        where
            R.line_state = ?
            and
            R.region = ? $where_extra
        order by
            R.description
    ")
        or dbi_error_trace("prepare failed");

    if (!is_null($traffic)) {
        $stmt->bind_param("sss", $state, $region, $traffic);
    } else {
        $stmt->bind_param("ss", $state, $region);
    }

    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($line, $description);

    while ($stmt->fetch())
    {
        $line_class = get_line_status_class($state, $line);

        $category['lines'][] = [
            'name' => $description,
            'class' => "line-$line_class",
            'url' => "details.php?" . urlenc("name=$state:$line"),
        ];
    }
    $stmt->close();

    return $category;
}

function create_tab_content($tp, $tab_label, $tab_id, $layout, $is_active)
{
    $tp['tabs'][] = [
        'tab_label' => $tab_label,
        'tab_id' => $tab_id,
        'active' => $is_active ? 'active' : ''
    ];

    $tp_columns = [];
    foreach ($layout as $column) {
        $tp_column = [
            'categories' => []
        ];
        foreach ($column as $category) {
            list($heading, $params) = $category;
            $tp_column['categories'][] = retrieve_category($heading, $params);
        }
        $tp_columns[] = $tp_column;
    }

    $tp['pages'][] = [
        'id' => $tab_id,
        'active' => $is_active ? 'active' : '',
        'columns' => $tp_columns,
    ];

    return $tp;
}

$title = "NSW Railway Network";

$tp = [
    'title' => $title,
    'tabs' => [],
    'pages' => [],
];

# define the tabs
$tp = create_tab_content($tp, 'NSW regional', 'nsw', $lines_nsw, true);
$tp = create_tab_content($tp, 'Sydney network', 'sydney', $lines_sydney, false);
$tp = create_tab_content($tp, 'Newcastle network', 'newcastle', $lines_newcastle, false);

normal_page('line-index.latte', $tp);

?>
