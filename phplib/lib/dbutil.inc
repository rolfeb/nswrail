<?php

function get_available_photo_tags()
{
    global $db;

    $tag_list = [];
    $stmt = $db->stmt_init();

    if (!$stmt->prepare('
        select
            name, text, description
        from
            r_tag
        order by
            text
    ')) {
        throw new InternalError('prepare failed: ' . $stmt->error);
    }

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

function get_locations()
{
    global $db;

    $location_list = [];
    $stmt = $db->stmt_init();

    if (!$stmt->prepare('
        select
            location_name
        from
            r_location
        order by
            location_name
    ')) {
        throw new InternalError('prepare failed: ' . $stmt->error);
    }

    $stmt->execute();
    $stmt->bind_result($location);
    while ($stmt->fetch()) {
        $location_list[] = $location;
    }
    $stmt->close();

    return $location_list;
}

function commit()
{
    global $db;

    mysql_query("commit", $db) or die("commit failed");
}

function rollback()
{
    global $db;

    mysql_query("rollback", $db) or die("rollback failed");
}

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

?>
