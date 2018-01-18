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

my $NAME = $q->param("name")
	or error("missing parameter: name");

my $PHOTO = $q->param("photo");

my $l;
eval {
	$l = Location->retrieve($STATE, $NAME)
};
$@ and error($@);

if (defined $PHOTO)
{
	show_location_photo($l, $PHOTO);
}
else
{
	show_location($l);
}

exit 0;

#
# Show all the details about a location
#
sub	show_location
{
	my ($l) = @_;

	print start_html(-title=>"$NAME",
		-style=>{"src"=>"/rail.css"},
		-head=>Link({-rel=>"shortcut icon", -type=>"image/gif", -href=>"/images/railicon.gif"}));

	print h1($NAME);

	print start_table({-border=>"0"});

	print start_Tr({-valign=>"top"});
	print td(b("Distance:"));
	print td(show_distance($l));
	print end_Tr();

	print start_Tr({-valign=>"top"});
	print td("");
	print td(show_great_circle_distance($l));
	print end_Tr();

	print start_Tr({-valign=>"top"});
	print td(b("Position:"));
	print td(show_position($l));
	print end_Tr();

	my @lines = Line->retrieve_by_location(
		$l->get(Location::STATE),
		$l->get(Location::NAME));

	my $title = @lines > 1 ? b("Lines:") : b("Line:");

	foreach my $line (@lines)
	{
		print start_Tr({-valign=>"top"});
		print td($title);
		print td(show_line($line->{line}));
		print end_Tr();

		$title = "";
	}

	print end_table();

	print start_dl();

	print dt(b("Description:"));
	print dd();
	print td(show_description($l));
	print p();

	print dt(b("Current Status:"));
	print dd();
	print td(show_current($l));
	print p();

	my $ndiagrams = $l->get(Location::NDIAGRAMS);
	if ($ndiagrams > 0)
	{
		print show_diagrams($l);
		print p();
	}

	my $nphotos = $l->get(Location::NPHOTOS);
	if ($nphotos > 0)
	{
		print show_photos($l);
	}

	print end_dl();


	print end_html();
}

#
# Show a specific photo for a location
#
sub	show_location_photo
{
	my ($l, $photo) = @_;

	print start_html(-title=>"$NAME Photograph",
		-style=>{"src"=>"/rail.css"},
		-head=>Link({-rel=>"shortcut icon", -type=>"image/gif", -href=>"/images/railicon.gif"}));

	print h1($NAME);

	my $d = $l->get(Location::PHOTOS, $photo);

	my $owner = $d->{owner} || "Rolfe Bozier";
	my $date = pretty_date($d->{day}, $d->{month}, $d->{year});

	my $mainline = $l->get(Location::MAINLINE);
	my $linename = $mainline->get(Line::SHORTNAME);

	print $d->{caption};
	print br();
	print br();
	print "$owner ($date)";
	print br();
	print br();
	print img({-src=>"/lines/$linename/photos/$d->{file}"});

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

sub	show_distance
{
	my ($l) = @_;

	return sprintf("%.2f", $l->get(Location::DISTANCE)) . " km from Sydney (by rail)";
}

sub	show_great_circle_distance
{
	my ($l) = @_;

	my ($lon1, $lat1) = to_rad(151.205704, -33.882999);	# Central
	my ($lon2, $lat2)
		= to_rad($l->get(Location::GEO_X), $l->get(Location::GEO_Y));

	my $dlon = $lon2 - $lon1;
	my $dlat = $lat2 - $lat1;
	my $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
	my $c = 2 * asin(sqrt($a));

	my $distance = $c * 6378.0;

	return sprintf("%.0f", $distance) . " km from Sydney (straight line)";
}

sub	to_rad
{
	return map { $_ / 180 * 3.14159265358979 } @_;
}

sub	show_position
{
	my ($l) = @_;

	return sprintf("Lat/long: (%.4f&deg;, %.4f&deg;)",
		$l->get(Location::GEO_X), $l->get(Location::GEO_Y));
}

sub	show_description
{
	my ($l) = @_;

	if ($l->get(Location::NDESCTEXT))
	{
		return $l->get(Location::DESCTEXT, 0)->{text};
	}
	return i("no information");
}

sub	show_current
{
	my ($l) = @_;

	if ($l->get(Location::NCURRTEXT))
	{
		return $l->get(Location::CURRTEXT, 0)->{text};
	}
	return i("no information");
}

sub	show_diagrams
{
	my ($l) = @_;

	my $mainline = $l->get(Location::MAINLINE);
	my $linename = $mainline->get(Line::SHORTNAME);

	my $html;

	$html .= dt(b("Diagrams:"));
	$html .= dd();

	my $ndiagrams = $l->get(Location::NDIAGRAMS);
	for (my $i = 0; $i < $ndiagrams; $i++)
	{
		my $d = $l->get(Location::DIAGRAMS, $i);

		$html .= b("$d->{year} layout:");
		$html .= img({-src=>"/lines/$linename/diagrams/$d->{file}.gif", -border=>"1"});
	}


	return $html;
}

sub	show_photos
{
	my ($l) = @_;

	my $mainline = $l->get(Location::MAINLINE);
	my $linename = $mainline->get(Line::SHORTNAME);

	my $html;

	$html .= dt(b("Photographs:"));
	$html .= dd();
	
	$html .= start_table({-border=>"0"});

	my $nphotos = $l->get(Location::NPHOTOS);
	for (my $i = 0; $i < $nphotos; $i++)
	{
		my $d = $l->get(Location::PHOTOS, $i);

		my $owner = $d->{owner} || "Rolfe Bozier";
		my $date = pretty_date($d->{day}, $d->{month}, $d->{year});

		my $jpg = $d->{file};
		my $gif = $jpg;
		$gif =~ s/\.jpg/.gif/;
		my $link = $jpg;
		$link =~ s/\.jpg/.html/;

		$html .= start_Tr({-valign=>"top"});

		$html .= td({-align=>"center"},a({-href=>"?state=$STATE&name=$NAME&photo=$i"},img({-src=>"/lines/$linename/photos/$gif"})));

		$html .= td(br() . $d->{caption} . br() . br(). "$owner ($date)");
		$html .= end_Tr();
	}
	$html .= end_table();

	return $html;
}

sub	show_line
{
	my ($line) = @_;

	my $html = "";

	my $state = $line->get(Line::STATE);
	my $name = $line->get(Line::NAME);
	my $id = $line->get(Line::ID);

	$html .= a({-href=>"/cgi-bin/line.cgi?state=$state&id=$id"},$name);

	return $html;
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
