<?php

class Audit
{
    const A_REGISTER    = 'register';
    const A_ACTIVATE    = 'activate';
    const A_LOGIN       = 'login';
    const A_LOGOUT      = 'logout';
    const A_UPLOAD      = 'upload';
    const A_PUBLISH     = 'publish';
    const A_SECURITY    = 'security';

    public function __construct() { }

    public static function addentry($type, $text=NULL)
    {
        global $db;
        global $user;

        $stmt = $db->stmt_init();
        $stmt->prepare('
            insert into
            r_audit (
                event_time,
                event_type,
                uid,
                ip_addr,
                text
            )
            values (?, ?, ?, ?, ?)
        ');

        $now_dt = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param('ssiss', $now_dt, $type, $user->uid, $ip, $text);
        $stmt->execute();
        $stmt->close();
    }
}

?>
