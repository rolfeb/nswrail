<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */


# third party packages
if (version_compare(PHP_VERSION, "5.6") == -1) {
    require "thirdparty/password_compat/lib/password.php";
}

# require "HTML/Template/ITX.php";
# require "HTML/BBCodeParser.php";

require 'thirdparty/PHPMailer/src/Exception.php';
require 'thirdparty/PHPMailer/src/PHPMailer.php';
require 'thirdparty/PHPMailer/src/SMTP.php';

require 'thirdparty/latte/src/latte.php';

require 'thirdparty/parsedown/Parsedown.php';


# classes
require "user.php";
require "audit.inc";

# other functions
require "config.inc";
require "dbutil.inc";
require "dbutil_line.inc";
require "dbutil_location.inc";
require "error.inc";
require "init.inc";
require "display_page.inc";
require "util.inc";
require "card_page.inc";
require "html_param.inc";
