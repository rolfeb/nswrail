<?php

require_once "../db.php";

class Location
{
    var $_state;
    var $_name;
    var $_version    = -1;
    var $_type0;
    var $_status0;
    var $_distance0;
    var $_geox0;
    var $_geoy0;
    var $_desc_text0;
    var $_curr_text0;

    var $nphotos;
    var $ndiagrams;

    var $type;
    var $status;
    var $distance;
    var $geox;
    var $geoy;
    var $desc_text;
    var $curr_text;

    function __construct()
    {
    }

    function retrieve($db, $state, $name)
    {
        $stmt = $db->prepare("
            select
                L.type,
                L.status,
                L.distance,
                L.geo_x,
                L.geo_y,
                L.version
            from
                r_location L
            where
                L.location_state = ?
                and
                L.location_name = ?
        ");
        $stmt->bind_param("ss", $state, $name);
        if (!$stmt->execute())
            throw new Exception("execute failed [" . $stmt->error() . "]", 2);

        $stmt->bind_result(
            $this->type,
            $this->status,
            $this->distance,
            $this->geox,
            $this->geoy,
            $this->_version
        );
        if (!$stmt->fetch())
            return 1;

        $stmt->close();

        $_type0         = $this->type;
        $_status0       = $this->status;
        $_distance0     = $this->distance;
        $_geox0         = $this->geox;
        $_geoy0         = $this->geoy;
        $_desc_text0    = $this->desc_text;
        $_curr_text0    = $this->curr_text;

        return 0;
    }

    function update()
    {
        /* XXX */
    }

}


?>
