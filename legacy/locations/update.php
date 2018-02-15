<?php

require_once "site.inc";

header("Content-Type: text/xml");

if (!$user->is_editor())
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
 *  location-x, location-y, location-exact [admin]
 *  distance [admin]
 *  htype<n> etc [admin]
 *  desc [editor]
 *  curr [editor]
 */
if ($user->is_editor())
{
    $type = quote_external(post_only("type", "undef"));
    $status = quote_external(post_only("status", "undef"));
    $geo_x = quote_external(post_only("location-x", "undef"));
    $geo_y = quote_external(post_only("location-y", "undef"));
    $geo_exact = quote_external(post_only("location-exact", "undef"));
    $distance = quote_external(post_only("distance", "undef"));

    $stmt = Null;

    if ($type != "undef" and $status != "undef")
    {
        $stmt = $db->stmt_init();
        $stmt->prepare("
            update
                r_location
            set
                type = ?,
                status = ?,
                version = version + 1
            where
                location_state = ?
                and
                location_name = ?
                and
                version = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ssssi", $type, $status, $state, $location, $version);
    }
    else
    if ($geo_x != "undef" and $geo_y != "undef" and $geo_exact != "undef")
    {
        $stmt = $db->stmt_init();
        $stmt->prepare("
            update
                r_location
            set
                geo_x = ?,
                geo_y = ?,
                geo_exact = ?,
                version = version + 1
            where
                location_state = ?
                and
                location_name = ?
                and
                version = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ddsssi", $geo_x, $geo_y, $geo_exact, $state, $location, $version);
    }
    else
    if ($distance != "undef")
    {
        $stmt = $db->stmt_init();
        $stmt->prepare("
            update
                r_location
            set
                distance = ?,
                version = version + 1
            where
                location_state = ?
                and
                location_name = ?
                and
                version = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("dssi", $distance, $state, $location, $version);
    }

    if ($stmt)
    {
        if (!$stmt->execute())
        {
            $db->rollback();
            reply(3, "Update failed: record locked by someone else");
            return;
        }
        $db->commit();
        $stmt->close();
        reply(0, 'OK');
        return;
    }

    $htype1 = quote_external(post_only("htype1", "undef"));
    if ($htype1 != "undef")
    {
        /*
         * Delete and re-add the events
         */
        $stmt = $db->stmt_init();
        $stmt->prepare("
            delete from
                r_location_event
            where
                location_state = ?
                and
                location_name = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("ss", $state, $location);

        if (!$stmt->execute())
        {
            $db->rollback();
            reply(4, "History delete failed: " . mysql_error());
            return;
        }
        $stmt->close();

        $stmt = $db->stmt_init();
        $stmt->prepare("
            insert into
                r_location_event
            values(?, ?, ?, ?, ?, ?, ?, ?, ?, null)
        ")
            or dbi_error_trace("prepare failed");

        for ($n = 1; $n <= 10; $n++)
        {
            $type = quote_external(get_post("htype$n"));
            $day = quote_external(get_post("hday$n"));
            $month = quote_external(get_post("hmonth$n"));
            $year = quote_external(get_post("hyear$n"));
            $year_error = quote_external(get_post("herror$n"));
            $name = quote_external(get_post("hname$n"));

            if ($type != "")
            {
                $stmt->bind_param("ssisiiiss", $state, $location, $n, $type,
                    $day, $month, $year, $year_error, $name);

                if (!$stmt->execute())
                {
                    $db->rollback();
                    reply(3, "Update failed: record locked by someone else");
                    return;
                }
            }
        }
        $stmt->close();
    }
}

if ($user->is_editor())
{
    $desc = quote_external(post_only("desc", "undef"));
    if ($desc != "undef")
    {
        if ($err = add_location_text($state, $location, $version, 'DESC', $desc))
        {
            $db->rollback();
            reply(3, $err);
            return;
        }
    }

    $curr = quote_external(post_only("curr", "undef"));
    if ($curr != "undef")
    {
        if (add_location_text($state, $location, $version, 'CURR', $curr) != 0)
        {
            $db->rollback();
            reply(3, $err);
            return;
        }
    }
}

$db->commit();
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
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LT.seqno,
            LT.text
        from
            r_location_text LT
        where
            LT.location_state = ?
            and
            LT.location_name = ?
            and
            LT.type = ?
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("sss", $state, $location, $type);
    $stmt->bind_result($seqno, $curr_text);
    $stmt->execute();

    $stmt->fetch();
    $stmt->close();

    /*
     * Add a new entry, but only if it differs from the current value
     */
    if ($text != $curr_text)
    {
        $seqno++;

        $stmt = $db->stmt_init();
        $stmt->prepare("
            insert into
                r_location_text
            values(?, ?, ?, ?, ?, now(), ?, 'U')
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("sssisi", $state, $location, $type, $seqno, $text,
            $userid);
        if (!$stmt->execute())
        {
            $stmt->close();
            return "Update failed: record locked by someone else";
        }
        $stmt->close();
    }

    return 0;
}

?>
