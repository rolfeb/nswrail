<?php

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
