#!/usr/bin/perl -w

use strict;
use Data::Dumper;
use CGI qw(:all *table *Tr *td *dl);
use Math::Trig qw(asin);
use FindBin;

use lib "$FindBin::Bin";
use Line;
use Location;

my $q = new CGI;

print header(-type => "text/html");

my $STATE = $q->param("state")
	or error("missing parameter: state");

my $ID = $q->param("id")
	or error("missing parameter: id");

my $l;
eval {
	$l = Line->retrieve($STATE, $ID)
};
$@ and error($@);

show_line($l);

exit 0;

#
# Show all the details about a line
#
sub	show_line
{
	my ($l) = @_;

	my $desc = $l->get(Line::NAME);

	print start_html(-title=>"$desc",
		-style=>{"src"=>"/rail.css"},
		-head=>Link({-rel=>"shortcut icon", -type=>"image/gif", -href=>"/images/railicon.gif"}));

	print h1($desc);

	#
	# Quick menu
	#
	print div({-class=>"menubox"},ul({-class=>"simple"}, li(a({-href=>"XXX"},"History"))));

	print show_description($l);

	print start_table({-border=>"0",-cellpadding=>"0",-cellspacing=>"0"});

	print start_Tr();
	print th("");
	print th({-width=>"8px"},"");
	print th("");
	print th("");
	print th({-width=>"8px"},"");
	print th({-colspan=>"3",-align=>"center"},"Passenger");
	print th({-width=>"8px"},"");
	print th("");
	print th({-width=>"8px"},"");
	print th("");
	print th({-width=>"8px"},"");
	print th("");
	print th({-width=>"8px"},"");
	print th("");
	print end_Tr();

	print start_Tr();
	print th({-align=>"right"},"km");
	print th({-width=>"8px"},"");
	print th("");
	print th("Name");
	print th({-width=>"8px"},"");
	print th("Facility");
	print th({-width=>"8px"},"");
	print th("Status");
	print th({-width=>"8px"},"");
	print th({-align=>"right"},"Location");
	print th({-width=>"8px"},"");
	print th({-align=>"center"},"Photos");
	print th({-width=>"8px"},"");
	print th({-align=>"center"},"Diagrams");
	print end_Tr();

	foreach my $data (@{$l->get(Line::LOCATIONS)})
	{
		my $lstate = $data->{state};
		my $lname = $data->{name};

		my $locn = Location->retrieve($lstate, $lname);

		print start_Tr();
		print td({-align=>"right"},"XXX");
		print td({-width=>"8px"},"");
		print td(img({-src=>"/lines/images/stn_ii.gif"})); # XXX
		print td(a({-href=>"/cgi-bin/location.cgi?state=$lstate&name=$lname"},"$lname"));
		print td({-width=>"8px"},"");
		print td("XXX");
		print td({-width=>"8px"},"");
		print td("XXX");
		print td({-width=>"8px"},"");
		print td({-align=>"right"},show_distance($locn));
		print td({-width=>"8px"},"");
		print td({-align=>"center"},show_nphotos($locn));
		print td({-width=>"8px"},"");
		print td({-align=>"center"},show_ndiagrams($locn));
		print end_Tr();
	}

	print end_table();

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

sub	show_description
{
	my ($l) = @_;

	if ($l->get(Line::DESCTEXT))
	{
		my $text = $l->get(Location::DESCTEXT, 0)->{text};
		my $html = "";

		$text =~ s/^\s+//;
		$text =~ s/\s+$//;

		# split into paragraphs
		foreach (split(/^$/m, $text))
		{
			$html .= p($_);
		}

		return $html;
	}
	return i("no information");
}

sub	show_distance
{
	my ($l) = @_;

	my $d = $l->get(Location::DISTANCE);

	return $d && sprintf("%.2f", $l->get(Location::DISTANCE));
}

sub	show_nphotos
{
	my ($l) = @_;

	my $n = $l->get(Location::NPHOTOS);

	return $n || "-";
}

sub	show_ndiagrams
{
	my ($l) = @_;

	my $n = $l->get(Location::NDIAGRAMS);

	return $n || "-";
}
