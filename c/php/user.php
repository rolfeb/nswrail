<?php

class User
{
    const S_UNCONFIRMED = 1;    // registration has not yet been confirmed
    const S_SUSPENDED   = 2;    // account has been suspended
    const S_PWDEXPIRED  = 4;
    const S_PWDLOCKED   = 8;

    private $_db;

    public $uid;
    public $username;
    public $fullname;

    public function __construct($db)
    {
        $this->_db = $db;

        if (isset($_SESSION['uid']) && $_SESSION['uid'] >= 0) {
            $this->load_user_from_db($_SESSION['uid'], NULL);
        } else {
            $this->load_guest_user();
        }
    }

    // XXX: login() should return error, not redirect to error_page
    public function login($username, $password_in)
    {
        # XXX: parameter validation?

        # check password and status
        $stmt = $this->_db->stmt_init();

        $stmt->prepare("
            select
                U.password,
                U.status
            from
                r_user U
            where
                U.username = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($enc_password, $status);
        $user_exists = $stmt->fetch();
        $stmt->close();

        if (!$user_exists || !password_verify($password_in, $enc_password)) {
            error_page("Incorrect username or password");
        }

        if ($status != 0) {
            if (($status & User::S_UNCONFIRMED) != 0) {
                error_page('Account registration has not been confirmed; check your email.');
            } else if (($status & User::S_SUSPENDED) != 0) {
                error_page('Account has been suspended.');
            } else {
                error_page("Login denied: $status");
            }
        }

        $this->load_user_from_db(NULL, $username);
        $_SESSION['uid'] = $this->uid;

        return true;
	}

	public function logout()
    {
		session_destroy();

        $this->load_guest_user();
	}

    public function is_guest()
    {
        return $this->uid == -1;
    }

    # get user details from database, given uid or username
    private function load_user_from_db($uid, $username)
    {
        $stmt = $this->_db->stmt_init();

        if (!is_null($uid)) {
            $stmt->prepare("
                select
                    U.uid,
                    U.username,
                    U.fullname
                from
                    r_user U
                where
                    U.uid = ?
            ")
                or dbi_error_trace("prepare failed");

            $stmt->bind_param("s", $uid);
        }
        else {
            $stmt->prepare("
                select
                    U.uid,
                    U.username,
                    U.fullname
                from
                    r_user U
                where
                    U.username = ?
            ")
                or dbi_error_trace("prepare failed");

            $stmt->bind_param("s", $username);
        }

        $stmt->execute();
        $stmt->bind_result($uid, $username, $fullname);
        $stmt->fetch();

        $this->uid = $uid;
        $this->username = $username;
        $this->fullname = $fullname;

        $stmt->close();
    }

    private function load_guest_user()
    {
        $this->uid = -1;
        $this->username = "guest";
        $this->fullname = "guest";
    }

    public static function email_address_in_use($addr)
    {
        global $db;

        $stmt = $db->stmt_init();

        $stmt->prepare("
            select
                1
            from
                r_user U
            where
                U.username = ?
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("s", $addr);
        $stmt->execute();
        $exists = $stmt->fetch();
        $stmt->close();

        return $exists;
    }

    public static function register_new_user($addr, $fullname, $enc_password, $activate_id, $register_addr)
    {
        global $db;

        $stmt = $db->stmt_init();

        $stmt->prepare("
            insert into
            r_user (
                username,
                fullname,
                password,
                register_addr,
                register_time,
                activate_code,
                status
            )
            values (?, ?, ?, ?, ?, ?, ?)
        ")
            or dbi_error_trace("prepare failed");

        $status = User::S_UNCONFIRMED;
        $now_dt = date("Y-m-d H:i:s");
        $stmt->bind_param("ssssssi", $addr, $fullname, $enc_password, $register_addr, $now_dt, $activate_id, $status);

        $status = $stmt->execute();
        $stmt->close();

        return $status;
    }

    public static function activate_user_via_code($activate_code)
    {
        global $db;

        $stmt = $db->stmt_init();

        $flag = User::S_UNCONFIRMED;

        $stmt->prepare("
            update r_user
            set
                status = (status & ~($flag)),
                activate_code = NULL
            where
                activate_code = ?
                and
                (status & ($flag)) != 0
        ")
            or dbi_error_trace("prepare failed");

        $stmt->bind_param("s", $activate_code);
        $update_ok = $stmt->execute();
        $stmt->close();

        if (!$update_ok) {
            return 'Cannot activate account';
        }
    }
}

?>
