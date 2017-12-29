#!/usr/bin/perl -w

use strict;
use Data::Dumper;
use CGI qw(:all *table *Tr *td *dl);
use CGI::Session;
use FindBin;

my $cgi = new CGI;
my $session = new CGI::Session(undef, $cgi, { Directory=>"/tmp" });

my $cookie = $cgi->cookie(
	-name=>"CGISESSID",
	-value=>$session->id,
	-expires=>"+6M",
	-domain=>".nswrail.net",
);

print header(-type=>"text/html",
	-cookie=>$cookie);

if (!$session->param("logged-in"))
{
	#
	# Run the login form
	#
	if (!run_login_form($cgi, $session))
	{
		exit 0;
	}
}


my $STATE = $cgi->param("state")
	or error("missing parameter: state");

my $NAME = $cgi->param("name")
	or error("missing parameter: name");


if ($cgi->param("photo"))
{
	handle_submission($cgi);
}
else
{
	show_form($cgi);
}

exit 0;

#
# Display the submittal form
#
sub	show_form
{
	my ($cgi) = @_;

	my %cookie = $cgi->cookie("common");
	my $fullname = $cookie{fullname} || "";

	print start_html(-title=>"Photograph submission",
		-style=>{"src"=>"/rail.css"},
		-head=>Link({-rel=>"shortcut icon", -type=>"image/gif", -href=>"/images/railicon.gif"}));

	print h1("Photograph submission");

	print start_form(-method=>"POST", -action=>$cgi->url);

	print start_table({-border=>"0"});

	print start_Tr({-valign=>"top"});
	print td(b("State:"));
	print td($STATE);
	print end_Tr();

	print hidden(-name=>"state", -default=>[$STATE]);

	print start_Tr({-valign=>"top"});
	print td(b("Location:"));
	print td($NAME);
	print end_Tr();

	print hidden(-name=>"name", -default=>[$NAME]);

	print start_Tr({-valign=>"middle"});
	print td(b("Photograph:"));
	print td(filefield(-name=>"photo",-size=>"64",-maxlength=>"128"));
	print end_Tr();

	print start_Tr({-valign=>"middle"});
	print td(b("Date of photo:"));
	print td(
		popup_menu("day", [ "", 1..31 ]),
		"&nbsp;/&nbsp;",
		popup_menu("month",
			[ "", qw(Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec) ]),
		"&nbsp;/&nbsp;",
		popup_menu("year",
			[ "", sort { $b <=> $a } 1930..2005 ]),
		"&nbsp&nbsp;&nbsp;&nbsp;or&nbsp&nbsp;&nbsp;&nbsp;",
		popup_menu("decade",
			[ "", map { "19${_}0s" } 0..9 ]),
	);
	print end_Tr();

	print start_Tr({-valign=>"middle"});
	print td(b("Direction of shot:"));
	print td(
		popup_menu("direction",
			[ "", qw(Up Down North South East West) ])
	);
	print end_Tr();

	print start_Tr({-valign=>"middle"});
	print td(b("Photographer:"));
	print td(
		textfield(-name=>"owner",
			-default=>$fullname,
			-size=>"50", -maxlength=>"80")
	);
	print end_Tr();

	print start_Tr({-valign=>"middle"});
	print td(b("Description:"));
	print td(
		textarea(-name=>"text",
			-columns=>"80", -rows=>"10")
	);
	print end_Tr();

	print start_Tr({-valign=>"middle"});
	print td("&nbsp;");
	print td(
		submit(-name=>"Submit")
	);
	print end_Tr();

	print end_table();

	print end_form();

	print end_html();
}

sub	handle_submission
{
	my ($cgi) = @_;

	my $photo = $cgi->param("photo");
	my $direction = $cgi->param("direction");
	my $year = $cgi->param("year");
	my $month = $cgi->param("month");
	my $day = $cgi->param("day");
	my $owner = $cgi->param("owner");
	my $text = $cgi->param("text");

	print start_html(-title=>"Not yet",
		-style=>{"src"=>"/rail.css"},
		-head=>Link({-rel=>"shortcut icon", -type=>"image/gif", -href=>"/images/railicon.gif"}));

	print h1("Not yet");
	print "owner = [$owner]<br>";

	print end_html();
}

sub	 error
{
	my ($text) = @_;

	print start_html(-title=>"Error",
		-style=>{"src"=>"/rail.css"},
		-head=>Link({-rel=>"shortcut icon", -type=>"image/gif", -href=>"/images/railicon.gif"}));

	print hr() . p(b("Error:") . $text) . hr();
	print end_html();
	exit 1;
}

sub	pretty_date
{
	my ($d, $m, $y) = @_;

	my @mon = qw(Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec);

	if ($d)
	{
		return "$d " . $mon[$m] . " $y";
	}
	elsif ($m)
	{
		return $mon[$m] . " $y";
	}
	elsif ($y)
	{
		return "$y";
	}
	else
	{
		return "date unknown";
	}
}

sub	run_login_form
{
	my ($cgi, $session) = @_;

	print start_html(-title=>"Login",
		-style=>{"src"=>"/rail.css"},
		-head=>Link({-rel=>"shortcut icon", -type=>"image/gif", -href=>"/images/railicon.gif"}));

	print h1("Login");
	print start_div(-padding=>"20px");
	print start_table({-border=>"0"});

	print start_Tr();
	print td(b("Login:"))
	print end_Tr();

	print start_Tr();
	print end_Tr();

	print end_table();
	print end_div();

	print end_html();
}
