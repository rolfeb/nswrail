<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

function get_available_photo_tags()
{
    /** @var mysqli $db */
    global $db;

    $tag_list = [];
    $stmt = $db->stmt_init();

    $stmt->prepare('
        select
            name, text, description
        from
            r_tag
        order by
            text
    ');
    $stmt->execute();
    $stmt->bind_result($name, $text, $description);
    while ($stmt->fetch()) {
        $tag_list[] = [
            'name' => $name,
            'text' => $text,
            'description' => $description,
        ];
    }
    $stmt->close();

    return $tag_list;
}

/**
 * @return array
 */
function get_locations()
{
    /** @var mysqli $db */
    global $db;

    $location_list = [];
    $stmt = $db->stmt_init();

    $stmt->prepare('
        select
            location_name
        from
            r_location
        order by
            location_name
    ');
    $stmt->execute();
    $stmt->bind_result($location);
    while ($stmt->fetch()) {
        $location_list[] = $location;
    }
    $stmt->close();

    return $location_list;
}

/**
 *
 */
function commit()
{
    /** @var mysqli $db */
    global $db;

    mysqli_commit($db) or die("commit failed");
}

/**
 *
 */
function rollback()
{
    /** @var mysqli $db */
    global $db;

    mysqli_rollback($db) or die("rollback failed");
}

/**
 * @param mysqli_stmt $stmt
 * @return array
 */
function dbi_bind_to_array($stmt)
{
    $params = array();
    $row = array();

    $meta = $stmt->result_metadata();

    while ($field = $meta->fetch_field()) {
        $params[] = &$row[$field->name];
    }

    call_user_func_array(array($stmt, 'bind_result'), $params);

    return $row;
}
