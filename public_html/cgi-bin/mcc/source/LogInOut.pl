
sub LoginSmall {
    $subsmain .= qq~
        <form action="$cgi;action=login2;" method="post" name="form">
        <table border="0" width="100%" cellspacing="1" cellpadding="0" bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
          <tr>
            <td class="windowbg" bgcolor="$color{'windowbg'}" width="100%">
            <table width="100%" cellspacing="0" cellpadding="3">
              <tr>
                <td class="titlebg" bgcolor="$color{'titlebg'}" colspan="2">
                <img src="$imagesdir/login.gif">
                <font size=2 class="text1" color="$color{'titletext'}" colspan="2"><b>$txt{'34'}</b></font></td>
              </tr><tr>
                <td class="windowbg" bgcolor="$color{'windowbg'}"colspan="2" ><font size=2><b>$txt{'35'}:</b></font></td>
              </tr><tr>
                <td class="windowbg" bgcolor="$color{'windowbg'}" colspan="2"><font size=2><input type=text name="username" size="15" tabindex="1"></font></td>
              </tr><tr>
                <td class="windowbg" bgcolor="$color{'windowbg'}" colspan="2"><font size=2><b>$txt{'36'}:</b></font></td>
              </tr><tr>
                <td class="windowbg" bgcolor="$color{'windowbg'}" colspan="2"><font size=2><input type=password name="passwrd" size="15" tabindex="2"></font></td>
              </tr><tr>
                <td align=center colspan="2" class="windowbg" bgcolor="$color{'windowbg'}"><input type=submit value="$txt{'34'}" tabindex="3" accesskey="l"></td>
              </tr>
            </table>
            </td>
          </tr>
        </table>
        <input type=hidden name="cookielength" value="$Cookie_Length" >
        <input type=hidden name="cookieneverexp" value="1" >
        </form>
        <script language="JavaScript"> <!--
                document.form.username.focus();
        //--> </script>
        ~;
	template();
}

sub Login {
	  if ($smalltemplate) {LoginSmall()};
    $subsmain .= qq~
        <BR><BR>
        <form action="$cgi;action=login2;" method="post" name="form">
        <table border="0" width="60%" cellspacing="1" cellpadding="0" bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
          <tr>
            <td class="windowbg" bgcolor="$color{'windowbg'}" width="100%">
            <table width="100%" cellspacing="0" cellpadding="3">
              <tr>
                <td class="titlebg" bgcolor="$color{'titlebg'}" colspan="3">
                <img src="$imagesdir/login.gif">
                <font size=2 class="text1" color="$color{'titletext'}"><b>$txt{'34'}</b></font></td>
              </tr><tr>
                <td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><b>$txt{'35'}:</b></font></td>
                <td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><input type=text name="username" size=20 tabindex="1"></font></td>
                <td class="windowbg" bgcolor="$color{'windowbg'}"><a href="$cgi;action=register"><font size="1">$txt{'753'}</font></a></td>
              </tr><tr>
                <td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><b>$txt{'36'}:</b></font></td>
                <td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><input type=password name="passwrd" size=20 tabindex="2"></font></td>
                <td class="windowbg" bgcolor="$color{'windowbg'}"><a href="$cgi;action=reminder"><font size="1">$txt{'315'}</font></a></td>
              </tr><tr>
                <td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><b>$txt{'497'}:</b></font></td>
                <td class="windowbg" bgcolor="$color{'windowbg'}" colspan="2"><font size=2><input type=text name="cookielength" size=4 maxlength="4" value="$Cookie_Length" tabindex="3"></font></td>
              </tr><tr>
                <td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><b>$txt{'508'}:</b></font></td>
                <td class="windowbg" bgcolor="$color{'windowbg'}" colspan="2"><font size=2><input type=checkbox name="cookieneverexp" tabindex="4" value="1" checked></font></td>
              </tr><tr>
                <td align=center colspan="3" class="windowbg" bgcolor="$color{'windowbg'}"><BR><input type=submit value="$txt{'34'}" tabindex="5" accesskey="l"></td>
              </tr><tr>
                <td align=center colspan="3" class="windowbg" bgcolor="$color{'windowbg'}"><BR></td>
              </tr>
            </table>
            </td>
          </tr>
        </table>
        </form>
        <script language="JavaScript"> <!--
                document.form.username.focus();
        //--> </script>
        ~;
	template();
}

sub Login2 {
    &fatal_error("$txt{'37'}") if($FORM{'username'} eq "");
    &fatal_error("$txt{'38'}") if($FORM{'passwrd'} eq "");
    $FORM{'username'} =~ s/\s/_/g;
    $username = $FORM{'username'};
    &fatal_error("$txt{'240'} $txt{'35'} $txt{'241'}") if($username !~ /^[\s0-9A-Za-z#%+,-\.:=?@^_]+$/);
    &fatal_error("$txt{'337'}") if($FORM{'cookielength'} !~ /^[0-9]+$/);

    @memberData =validate_member($FORM{'username'},$FORM{'passwrd'},0);
    if (defined @memberData) {
        info("Member logged in $username @memberData\n") if ($logging);
        addOnExecute("Login",$username);
    }  else {
        $username = "Guest"; &fatal_error("$txt{'40'}");
    }

    if($FORM{'cookielength'} < 1 || $FORM{'cookielength'} > 9999) { $FORM{'cookielength'} = $Cookie_Length; }
    if(!$FORM{'cookieneverexp'}) { $ck{'len'} = "\+$FORM{'cookielength'}m"; }
    else { $ck{'len'} = 'Sunday, 17-Jan-2038 00:00:00 GMT'; }
    $password = crypt("$FORM{'passwrd'}",$pwseed);

    $subsSetCookies1 = cookie(-name    =>   "$cookieusername",
                            -value   =>   "$username",
                            -path    =>   "/",
                            -expires =>   "$ck{'len'}");
    $subsSetCookies2 = cookie(-name    =>   "$cookiepassword",
                            -value   =>   "$password",
                            -path    =>   "/",
                            -expires =>   "$ck{'len'}");
    loadUserSettings();
    $action=''; #Clear action
}

sub Logout {
    clearUserSettings();
    Login();
}

sub Reminder {
	$subsmain .= qq~
<BR><BR><table border=0 width=400 cellspacing=1 bgcolor="$color{'bordercolor'}" align="center" class="bordercolor">
  <tr>
    <td class="titlebg" bgcolor="$color{'titlebg'}">
    <font size=2 class="text1" color="$color{'titletext'}"><b>$mbname $txt{'36'} $txt{'194'}</b></b></font></td>
  </tr><tr>
    <td class="windowbg" bgcolor="$color{'windowbg'}">
    <form action="$cgi;action=reminder2" method="post">
    <table border=0 align="center">
      <tr>
        <td><font size="2">$txt{'35'}: <input type="text" name="user">
        <input type="submit" value="$txt{'339'}"></font></td>
      </tr>
    </table>
    </form>
    </td>
  </tr>
</table>
~;
$substitle = "$txt{'669'}";
&template;
}

sub Reminder2 {
$user = $FORM{'user'};

my @member = get_email_member_info($FORM{'user'},1);
$password = $member[1];
$name = $member[8];
$email = $member[9];

chomp($name);
chomp($email);
chomp($password);

my $line = qq~$txt{'711'} $name,\n\n$mbname ==>\n\n$txt{'35'}: $user\n$txt{'36'}: $password\n\n$txt{'130'}~;

if (fopen(FILE,"$datadir/reminder.txt")) {
	my @lines = <FILE>;
	fclose(FILE);
	$line = join('',@lines);
}
$line =~ s~<field\s+(\w+)>~${"$1"}~g;

$subject = "$txt{'36'} $mbname : $name";
&sendmail($email, $subject, $line);

$subsmain .= qq~
<BR><BR><table border=0 width=400 cellspacing=1 bgcolor="$color{'bgcolor'}" align="center">
  <tr>
    <td class="titlebg" bgcolor="$color{'titlebg'}">
    <font size=2 class="text1" color="$color{'titletext'}"><b>$mbname $txt{'36'} $txt{'194'}</b></b></font></td>
  </tr><tr>
    <td class="windowbg" bgcolor="$color{'windowbg'}">
    <table border=0 align="center">
      <tr>
        <td align="center"><font size="2"><b>$txt{'192'}: $user</b></font></td>
      </tr>
    </table>
    </td>
  </tr>
</table>
<br><center><a href="javascript:history.back(-2)">$txt{'193'}</a></center><br>
~;
$substitle = "$txt{'669'}";
&template;
}


1;