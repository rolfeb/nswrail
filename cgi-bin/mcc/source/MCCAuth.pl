package MCCAuth;
use Apache::Constants qw(:common);
use CGI::Cookie;

$modperl_loaded=0;

#Authentication
sub authen {

	my($r) = @_;
	return OK unless $r->is_initial_req; #only the first internal request
  loadPath($r);
  my ($area) = $r->dir_config("Area");
	my ($MCCAuthRedirect) = $r->dir_config("RedirectOnError");
	$MCCAuthRedirect = 1 if ($MCCAuthRedirect!=0); #Default use MCC to login
	if (!($area)) {
		$r->log_reason("MCCAuth::Auth:authen area not set ", $r->uri);
		return SERVER_ERROR;
	}

	# Get the Cookie header. If there is a session key for this realm, strip
	# off everything but the value of the cookie or passed password
  loadCookies($r);
	my @memberData = validate_member($username,$password,1);
	my ($ok)=0;
	if ($memberData[0]){
	  $ok=1;
	} else {
		($res,$password) = $r->get_basic_auth_pw;
		$password = crypt($password,$pwseed);
		$username = $r->connection->user || $username;
		@memberData = validate_member($username,$password,1);
		$ok=1 if ($memberData[0]);
	}
	$r->connection->user($username);
	if ($ok){
		my $isAuthorized = is_authorized($username,$area);
		$out = ($isAuthorized) ? $r->headers_out : $r->err_headers_out;
		#Password would be nice
		$cookies = CGI::Cookie->new(-name    =>   "$cookieusername",
												-value   =>   "$username",
												-path    =>   "/",
												-expires =>   '+360m');
		$out->add("Set-Cookie" => $cookies);
		$cookies = CGI::Cookie->new(-name    =>   "$cookiepassword",
												-value   =>   "$password",
												-path    =>   "/",
												-expires =>   '+360m');
 	  $out->add("Set-Cookie" => $cookies);
 	  if ($isAuthorized) {
			%ModPerl=( 'ModPerlObject' => $r,
			           'User' => $username,
	               'Result' => OK);
      addOnExecute("ModPerl",\%ModPerl) if ($useaddon);
		  return $ModPerl{'Result'};
		}
		return FORBIDDEN;
	}
	return FORBIDDEN if ($MCCAuthRedirect==1);
  #Popup the login screen
	$r->log_error("MCCAuth: Declined Authen $username");
	$r->log_reason("Password or Cookies incorrect");
	$r->connection->auth_type("Basic");
	$r->note_basic_auth_failure;
	return AUTH_REQUIRED;
}

sub loadCookies {
	my ($r) = @_;
	%Cookies=();
	foreach (split(/; /,$r->header_in("Cookie"))) {
			$_ =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
			my ($cookie,$value) = split(/=/);
			$Cookies{$cookie} = $value;
	}
	$password = $Cookies{$cookiepassword};
	$username = $Cookies{$cookieusername};
}


sub loadPath {
	my($r) = @_;
	if (!$modperl_loaded) {
		$modperl_loaded=1;
		my $path = $r->dir_config("MCCPath");
		#use lib "$path";
		chdir("$path");
		#Load MCC Libraries
		require "$path/Settings.pl";
		require "$path/$language";
		require "$sourcedir/Subs.pl";
		$databasedriver = "TextFiles.dbd" if ($databasedriver eq '');
		require "$sourcedir/$databasedriver";
		require "$sourcedir/Template.pl";
		require "$sourcedir/AccessControl.pl";
	} else {
		#Reload the database
		refresh_database();
	}
}

1;