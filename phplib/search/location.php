<?php

require "site.inc";

function run_location_search()
{
    global $db;

    $tp = [
        'title' => "Location Search Results",
    ];

    $text = param_get_string_opt("location");

    $text .= "%";  /* prefix matching */

    $results = [];
    $locations_seen = [];

    /*
     * Select current location names from r_location
     */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            RL.line_state,
            RL.line_name,
            R.description,
            RL.location_state,
            RL.location_name
        from
            r_line R,
            r_line_location RL
        where
            RL.location_name LIKE ?
            and
            R.line_state = RL.line_state
            and
            R.line_name = RL.line_name
            and
            RL.mainline = 'Y'
    ");
    $stmt->bind_param("s", $text);
    $stmt->execute();
    $stmt->bind_result($line_state, $line_name, $description, $location_state, $location_name);

    while ($stmt->fetch()) {
        if (!array_key_exists($location_name, $locations_seen)) {
            $results[$location_name] = [$line_state, $line_name, $description, $location_state, $location_name, ""];
            $locations_seen[$location_name] = True;
        }
    }
    $stmt->close();

    /*
     * Select historic location names from r_location_event
     */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            RL.line_state,
            RL.line_name,
            R.description,
            RL.location_state,
            RL.location_name,
            RE.current_name
        from
            r_line R,
            r_line_location RL,
            r_location_event RE
        where
            RE.location_state = RL.location_state
            and
            RE.location_name = RL.location_name
            and
            RE.current_name LIKE ?
            and
            RE.current_name != RL.location_name
            and
            R.line_state = RL.line_state
            and
            R.line_name = RL.line_name
            and
            RL.mainline = 'Y'
    ");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->bind_result($line_state, $line_name, $description, $location_state, $location_name, $old_name);

    while ($stmt->fetch()) {
        $location = "$location_name (was $old_name)";

        if (!array_key_exists($location_name, $locations_seen)) {
            $results[$location] = [$line_state, $line_name, $description, $location_state, $location_name, $old_name];
            $locations_seen[$location_name] = 1;
        }
    }
    $stmt->close();

    ksort($results);

    /*
     * If there is exactly 1 result, just send the user to that page
     */
    if (count($results) == 1) {
        list($line_state, $line_name, $description, $location_state,
            $location_name, $old_name) = current($results);

        $url = "/locations/details.php?" .
            http_build_query([
                'name' => "$location_state:$location_name",
            ]);
        header("Location: $url");
        return NULL;
    }

    /*
     * Otherwise give a list of links to the locations
     */
    $rows = [];
    foreach ($results as $text => $r) {
        list($line_state, $line_name, $description, $location_state,
            $location_name, $old_name) = $r;

        $url = '/locations/details.php?' .
            http_build_query([
                'name' => "$location_state:$location_name",
            ]);

        $line_url = '/lines/details.php?' .
            http_build_query([
                'name' => "$line_state:$line_name",
            ]);

        $rows[] = [
            'ne_url' => $url,
            'location' => $text,
            'ne_line_url' => $line_url,
            'line' => $description,
        ];
    }

    $nrows = count($rows);
    if ($nrows > 0) {
        if ($nrows > 200) {
            $tp['opt_warning'] = "Too many matches; results have been truncated";
            array_splice($rows, 200);
        }
        $tp['opt_results'] = $rows;
    } else {
        $tp['opt_warning'] = "No results matched the search criteria.";
    }

    return $tp;
}

normal_page_wrapper('run_location_search', 'search-location.latte');

?>
