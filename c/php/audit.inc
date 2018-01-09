<?php

class Audit
{
    const A_REGISTER    = 1;
    const A_ACTIVATE    = 2;
    const A_LOGIN       = 3;
    const A_LOGOUT      = 4;

    public function __construct() { }

    public static function addentry($type, $text=NULL)
    {
        global $db;
        global $user;

        $stmt = $db->stmt_init();
        $stmt->prepare("
            insert into
            r_audit (
                event_time,
                event_type,
                uid,
                ip_addr,
                text
            )
            values (?, ?, ?, ?, ?)
        ")
            or dbi_error_trace("prepare failed");


        $now_dt = date("Y-m-d H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("ssiss", $now_dt, $type, $user->uid, $ip, $text);
        $status = $stmt->execute();
        $stmt->close();

        return $status;
    }
}

?>
