<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "dbutil.inc";

header("Content-Type: text/xml");

if (auth_priv_none())
{
    reply(1, 'You do not have access to this operation');
    return;
}

/*
 * Get mandatory parameters
 */
if (!array_key_exists('state', $_POST))
{
    reply(2, 'Missing parameter: state');
    return;
}
$state = quote_external(post_only('state'));

if (!array_key_exists('location', $_POST))
{
    reply(2, 'Missing parameter: location');
    return;
}
$location = quote_external(post_only('location'));

if (!array_key_exists('version', $_POST))
{
    reply(2, 'Missing parameter: version');
    return;
}
$version = quote_external(post_only('version'));

/*
 * Possible parameters:
 *  type, status [admin]
 *  location-x, location-y [admin]
 *  distance [admin]
 *  desc [editor]
 *  curr [editor]
 */
if (auth_priv_admin())
{
    $updates = "";

    $type = quote_external(post_only("type", "undef"));
    if ($type != "undef")
        $updates .= "type = '$type',";

    $status = quote_external(post_only("status", "undef"));
    if ($status != "undef")
        $updates .= "status = '$status',";

    $location_x = quote_external(post_only("location-x", "undef"));
    if ($location_x != "undef")
    {
        if ($location_x != "")
            $updates .= "geo_x = '$location_x',";
        else
            $updates .= "geo_x = null,";
    }

    $location_y = quote_external(post_only("location-y", "undef"));
    if ($location_y != "undef")
    {
        if ($location_y != "")
            $updates .= "geo_y = '$location_y',";
        else
            $updates .= "geo_y = null,";
    }

    $distance = quote_external(post_only("distance", "undef"));
    if ($distance != "undef")
    {
        if ($distance != "")
            $updates .= "distance = '$distance',";
        else
            $updates .= "distance = null,";
    }

    if ($updates)
    {
        /*
         * Update the database
         */
        if (!mysql_query("
            update
                r_location
            set
                $updates
                version = $version + 1
            where
                location_state = '$state'
                and
                location_name = '$location'
                and
                version = $version
        ", $db))
        {
            rollback();
            reply(3, "Update failed: record locked by someone else");
            return;
        }
    }

    $htype1 = quote_external(post_only("htype1", "undef"));
    if ($htype1 != "undef")
    {
        /*
         * Delete and re-add the events
         */
        if (!mysql_query("
            delete from
                r_location_event
            where
                location_state = '$state'
                and
                location_name = '$location'
        ", $db))
        {
            rollback();
            reply(4, "History delete failed: " . mysql_error());
            return;
        }

        for ($n = 1; $n <= 10; $n++)
        {
            $type = quote_external(get_post("htype$n"));
            $day = quote_external(get_post("hday$n"));
            $month = quote_external(get_post("hmonth$n"));
            $year = quote_external(get_post("hyear$n"));
            $year_error = quote_external(get_post("herror$n"));
            $name = quote_external(get_post("hname$n"));

            $day_str = $day ? "'$day'" : "NULL";
            $month_str = $month ? "'$month'" : "NULL";
            $year_str = $year ? "'$year'" : "NULL";
            $name_str = $name ? "'$name'" : "NULL";

            if ($type != "")
            {
                if (!mysql_query("
                    insert into
                        r_location_event
                    values('$state', '$location', $n, '$type', $day_str, $month_str, $year_str, '$year_error', $name_str, null)
                ", $db))
                {
                    rollback();
                    reply(3, "Update failed: record locked by someone else");
                    return;
                }
            }
        }
    }
}

if (auth_priv_editor())
{
    $desc = quote_external(post_only("desc", "undef"));
    if ($desc != "undef")
    {
        if (add_location_text($state, $location, $version, 'DESC', $desc) != 0)
            return;
    }

    $curr = quote_external(post_only("curr", "undef"));
    if ($curr != "undef")
    {
        if (add_location_text($state, $location, $version, 'CURR', $curr) != 0)
            return;
    }
}

// sleep(3);
// reply(99, "Not yet implemented [$state, $location]");

commit();
reply(0, 'OK');

return;

function reply($value, $text)
{
    print "<reply><status>$value</status><text>$text</text></reply>\n";
}

function add_location_text($state, $location, $version, $type, $text)
{
    global $db;
    $userid = auth_userid();

    /*
     * Get the seqno and text from the latest text entry for this location
     */
    if (!$stmt = mysql_query("
        select
            LT.seqno,
            LT.text
        from
            r_location_text LT
        where
            LT.location_state = '$state'
            and
            LT.location_name = '$location'
            and
            LT.type = '$type'
            and
            LT.seqno = (
                select max(MAX_LT.seqno)
                from
                    r_location_text MAX_LT
                where
                    MAX_LT.location_state = LT.location_state
                    and
                    MAX_LT.location_name = LT.location_name
                    and
                    MAX_LT.type = LT.type
            )
        ;
    ", $db))
    {
        rollback();
        reply(4, "Database error: " . mysql_error());
        return 1;
    }

    list($seqno, $curr_text) = mysql_fetch_array($stmt);
    mysql_free_result($stmt);

    /*
     * Add a new entry, but only if it differs from the current value
     */
    if ($text != $curr_text)
    {
        $seqno++;

        if (!mysql_query("
            insert into
                r_location_text
            values('$state', '$location', '$type', $seqno, '$text', now(),
                $userid, 'U')
        ", $db))
        {
            rollback();
            reply(3, "Update failed: record locked by someone else");
            return 1;
        }
    }

    return 0;
}

?>
