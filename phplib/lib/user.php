<?php

class User
{
    const R_EDITOR      = 0x01;
    const R_MODERATOR   = 0x02;
    const R_SUPERUSER   = 0x04;

    const S_UNCONFIRMED = 0x01; // registration has not yet been confirmed
    const S_SUSPENDED   = 0x02; // account has been suspended
    const S_PWDEXPIRED  = 0x04;
    const S_PWDLOCKED   = 0x08;

    /** @var mysqli $_db */
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

    public function login($username, $password_in, $ip_addr)
    {
        # XXX: parameter validation?

        # check password and status
        $stmt = $this->_db->stmt_init();

        $stmt->prepare('
            select
                U.uid,
                U.password,
                U.status
            from
                r_user U
            where
                U.username = ?
        ');

        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($uid, $enc_password, $status);
        $user_exists = $stmt->fetch();
        $stmt->close();

        if (!$user_exists || !password_verify($password_in, $enc_password)) {
            throw new UserError('Incorrect username or password');
        }

        if ($status != 0) {
            if (($status & User::S_UNCONFIRMED) != 0) {
                throw new UserError('Account registration has not been confirmed; check your email.');
            } else if (($status & User::S_SUSPENDED) != 0) {
                throw new UserError('Account has been suspended.');
            } else {
                throw new UserError('Login denied: ' . $status);
            }
        }

        # Update last-login details
        $stmt = $this->_db->stmt_init();

        $stmt->prepare('
            update r_user
            set
                last_login_addr = ?,
                last_login_time = ?
            where
                uid = ?
        ');

        $now_dt = date('Y-m-d H:i:s');
        $stmt->bind_param('ssi', $ip_addr, $now_dt, $uid);
        $stmt->execute();
        $stmt->close();

        # Load this user and set their session
        $this->load_user_from_db(NULL, $username);
        session_regenerate_id();
        $_SESSION['uid'] = $this->uid;
	}

	public function logout()
    {
		session_unset();
		session_destroy();
        session_regenerate_id();

        $this->load_guest_user();
	}

    public function is_guest()
    {
        return $this->uid == -1;
    }

    public function is_loggedin()
    {
        return $this->uid != -1;
    }

    public function is_editor()
    {
        return !$this->is_guest() && ($this->role & (User::R_EDITOR|User::R_SUPERUSER)) != 0;
    }

    public function is_moderator()
    {
        return !$this->is_guest() && ($this->role & (User::R_MODERATOR|User::R_SUPERUSER)) != 0;
    }

    public function is_superuser()
    {
        return !$this->is_guest() && ($this->role & User::R_SUPERUSER) != 0;
    }

    # get user details from database, given uid or username
    private function load_user_from_db($uid, $username)
    {
        $stmt = $this->_db->stmt_init();

        if (!is_null($uid)) {
            $stmt->prepare('
                select
                    uid,
                    username,
                    fullname,
                    role
                from
                    r_user
                where
                    uid = ?
            ');
            $stmt->bind_param('s', $uid);
        }
        else {
            $stmt->prepare('
                select
                    uid,
                    username,
                    fullname,
                    role
                from
                    r_user
                where
                    username = ?
            ');
            $stmt->bind_param('s', $username);
        }

        $stmt->execute();
        $stmt->bind_result($uid, $username, $fullname, $role);
        $stmt->fetch();

        $this->uid = $uid;
        $this->username = $username;
        $this->fullname = $fullname;
        $this->role = $role;

        $stmt->close();
    }

    private function load_guest_user()
    {
        $this->uid = -1;
        $this->username = 'guest';
        $this->fullname = 'guest';
        $this->role = 0;
    }

    #
    # Class/static functions
    #

    public static function email_address_in_use($addr)
    {
        /** @var mysqli $db */
        global $db;

        $stmt = $db->stmt_init();

        $stmt->prepare('
            select
                1
            from
                r_user U
            where
                U.username = ?
        ');
        $stmt->bind_param('s', $addr);
        $stmt->execute();
        $exists = $stmt->fetch();
        $stmt->close();

        return $exists;
    }

    public static function register_new_user($addr, $fullname, $enc_password, $activate_id, $register_addr)
    {
        /** @var mysqli $db */
        global $db;

        $stmt = $db->stmt_init();

        $stmt->prepare('
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
        ');

        $status = User::S_UNCONFIRMED;
        $now_dt = date('Y-m-d H:i:s');
        $stmt->bind_param('ssssssi', $addr, $fullname, $enc_password, $register_addr, $now_dt, $activate_id, $status);
        $stmt->execute();
        $stmt->close();
    }

    public static function activate_user_via_code($activate_code)
    {
        /** @var mysqli $db */
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
        ");
        $stmt->bind_param('s', $activate_code);
        $stmt->execute();
        $stmt->close();
    }
}
