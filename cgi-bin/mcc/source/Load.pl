#Load.pl

sub loadCookies {
	foreach (split(/; /,$ENV{'HTTP_COOKIE'})) {
		$_ =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		($cookie,$value) = split(/=/);
		$Cookies{$cookie} = $value;
	}
	if($Cookies{$cookiepassword}) {
		$password = $Cookies{$cookiepassword};
		$username = $Cookies{$cookieusername} || 'Guest';
	} else {
		$password = '';
		$username = 'Guest';
	}
}

sub clearUserSettings {
  addOnExecute("Logout",$memberData[0]);
	@memberData=();
	$subsSetCookies1 = cookie(-name    =>   "$cookieusername",
				-value   =>   "",
				-path    =>   "/",
				-expires =>   "Thursday, 01-Jan-1970 00:00:00 GMT");
	$subsSetCookies2 = cookie(-name    =>   "$cookiepassword",
				-value   =>   "",
				-path    =>   "/",
				-expires =>   "Thursday, 01-Jan-1970 00:00:00 GMT");
	$username = 'Guest';
	$password = '';
	$realname = '';
	$realemail = '';
	$headerAuthorization='';
	$ENV{'HTTP_COOKIE'} = '';
}

sub loadUserSettings {
	if($username ne 'Guest') {
		@memberData = validate_member($username,$password,1);
		if (defined @memberData) {
			$realname =$memberData[8];
			$realemail = $memberData[9];
			my ($date,$count,$lastdate) = split(/ - /,$memberData[5]);
			if ($date ne get_date()) {
				my $line = get_date();
				$line .= " - ";
				$count++;
				$line .= "$count - $date";
				set_member_last_visit($memberData[0],$line);
				commit_database();
				addOnExecute("NewDateLogin",$memberData[0],$count-1,$lastdate);
			}
			addOnExecute("Login",$memberData[0]);
		}
		else { $username = ''; }
	}
	unless($username) {
		clearUserSettings();
	}
	info("Username=$username and password=$password")if ($logging);

}

1;