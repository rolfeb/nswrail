#!/usr/bin/perl -w

use strict;
use CGI qw(:all *table *Tr *td *dl *div);
use Cwd;

my $q = new CGI;
my $url = $q->url({-full=>1});

my @modes = qw(home browse photos maps infrastructure library current about);
my %modes = (
    home            => [ {}, \&show_home, ],
    browse          => [ {}, \&show_browse, ],
    photos          => [ {}, \&show_photos, ],
    maps            => [ {}, \&show_maps, ],
    infrastructure  => [ {}, \&show_infrastructure, ],
    library         => [ {}, \&show_library, ],
    current         => [ {}, \&show_current, ],
    about           => [ {}, \&show_about, ],
);

my $mode = $q->param("mode") || "home";

$modes{$mode}->[0]->{-class} = "selected";
my $show_fn = $modes{$mode}->[1] || \&show_home;

my $menubar = "";
foreach (@modes)
{
    (my $text = $_) =~ s/^(.)/\U$1/;
    $menubar .= td($modes{$_}->[0], a({-href=>"$url?mode=$_"}, $text));
}
(my $title = $mode) =~ s/^(.)/\U$1/;

print
    header(-type=>"text/html"),
    start_html({
        -title=>"New South Wales Railways",
	-author=>"Rolfe Bozier, nswrail\@pobox.com",
	-meta=>{-keywords=>"NSW rail history photographs maps"},
	-style=>{-src=>["/nswrail.css"]},
	-head=>Link({-rel=>"shortcut icon",-type=>"image/gif",-href=>"/images/railicon.gif"}),
    });

print
    img({-src=>"/images/nswrail-logo.png", -alt=>"[NSWrail.net logo]",-align=>"left"}),
    h1($title),
    div({-class=>"menubar"},
        table(
            Tr(
                $menubar
            ),
        ),
    ),
    div({-class=>"mainbody"},
        &$show_fn(),
    );

print end_html();

exit 0;

sub show_home
{
    my $html = "";
    my $visits_html = "";

    my ($count, $date) = get_visits();
    if ($count)
    {
        $visits_html = qq(
            h2("Visitors"),
            p("<b>$count</b> since $date"),
            p()
        );
    }

    $html .= start_table({-width=>"100%",-cellspacing=>"0",-cellpadding=>"0",-border=>"0"});


    $html .=
        Tr(
            td(
                div({-id=>"content"},
                    p(
"These pages contain a variety of information about the NSW railway network,
both historical and current.  The data can be broken down into four
categories: non-spatial (plain data), spatial (map data), photographic
and hypertext (links to related data sources).
The intention of these pages to make as much information as possible available
on the net.  Although you will find pictures of trains within these pages,
I have chosen to concentrate on recording the infrastructure of the state,
especially on the abandoned branch lines."
                    ),
                    p(
"Much as I would like to, I probably won't be able to visit every location
in the state, taking notes and photographs!  If you want to contribute
information or photos, then please contact me at " . a({-href=>"mailto:nswrail\@pobox.com"}, "nswrail\@pobox.com")
                    ),
                    p(
"Most of the information and photos in these pages has been generously
provided by a lot of people.  Their work has contributed immeasureably to
the success of these pages."
                    ),
                    p(),
                    p("Rolfe Bozier"),
                    p(),
                    p(
"P.S.  Please don't ask me about travel arrangements, as I can't provide any
advice.  You might like to try " .
a({-href=>"http://www.railpage.org.au/railmaps/"},"Australian Rail Maps") .
" as a starting point for planning a trip."
                    ),
                    hr(),
                    get_changes_html(),
                ),
            ),
            td(
                div({-id=>"photos"},
                    h1("Random photos"),
                    get_randpix_html(),
                ),
            ),
        ),
        Tr(
            td({-colspan=>"2"},
                $visits_html,
                br(),
                div({-id=>"footer"},
                    p(
"All text, maps and photographs are &copy; 2000-2005 Rolfe Bozier except
where otherwise noted.  Please contact me if you would like to use any of
them (I will almost certainly say yes!)."
                    ),
                ),
            ),
        );

    $html .= end_table();

    return $html;
}

sub show_browse
{
    my $html = "";

    $html .=
        ul(
            li("Select lines from a map:"),
            ul(
                li(a({-href=>"/maps/nsw-index.html"}, "New South Wales")),
                li(a({-href=>"/maps/sydney-index.html"}, "Sydney")),
                li(a({-href=>"/maps/newcastle-index.html"}, "Newcastle")),
            ),
            li("Select lines by name:"),
            ul(
                li(a({-href=>"/lines/nsw-lines.html"}, "New South Wales")),
                li(a({-href=>"/lines/sydney-lines.html"}, "Sydney")),
                li(a({-href=>"/lines/newcastle-lines.html"}, "Newcastle")),
            ),
        );

    return $html;
}

sub show_photos
{
}

sub show_maps
{
    my $html = "";

    $html .=
        table(
            Tr({-valign=>"top"},
                td(
                    ul(
                        li("Network maps by year",
                            ul(
                                li(a({-href=>"/network-maps/nsw2004.html"}, "New South Wales")),
                                li(a({-href=>"/network-maps/sydney2004.html"}, "Sydney")),
                                li(a({-href=>"/network-maps/newcastle2004.html"}, "Newcastle")),
                            )
                        ),
                        li("Network growth animations",
                            ul(
                                li(a({-href=>"/network-maps/nsw-changes.html"}, "New South Wales")),
                                li(a({-href=>"/network-maps/sydney-changes.html"}, "Sydney")),
                                li(a({-href=>"/network-maps/newcastle-changes.html"}, "Newcastle")),
                            )
                        ),
                    ),
                ),
                td(
                    ul(
                        li("Scanned historic maps",
                            ul(
                                li(a({-href=>"/historic-maps/nsw-network1933.html"}, "NSW Network 1933")),
                                li(a({-href=>"/historic-maps/images/syd-network1969.gif"}, "Sydney Passenger Network 1969")),
                                li(a({-href=>"/historic-maps/sydney-network1974.html"}, "NSW Network 1933")),
                                li(a({-href=>"/historic-maps/images/newcastle1950.gif"}, "Newcastle Network 1950")),
                            )
                        ),
                    ),
                ),
            ),
        );

    $html .=
        h2("Links")
        .
        table({-class=>"clean"},
            Tr({-valign=>"top"},
                td(a({-href=>"http://www.lands.nsw.gov.au/OnlineServices/ParishMaps/default.htm"}, "Parish&nbsp;Maps")),
                td(
"The NSW Department of Lands has made a huge range of old parish maps
available online. If you are trying to track down old branches or stations
in NSW, these maps are invaluable."
                ),
            ),
            Tr({-valign=>"top"},
                td(a({-href=>"http://maps.nsw.gov.au/viewer.htm"}, "GeoSpatial&nbsp;Portal")),
                td(
"Current topographic map data is available from the NSW Department of Lands."
                ),
            ),
            Tr({-valign=>"top"},
                td(a({-href=>"http://iplan.australis.net.au/landview.php"}, "LandView")),
                td(
"The LandView application provided by the NSW Department of Infrastructure,
Planning and Natural Resources delivers satellite and aerial images online."
                ),
            ),
            Tr({-valign=>"top"},
                td(a({-href=>"http://maps.google.com/maps?ll=-33.174342,148.117676&spn=9.410539,11.376343&t=k&hl=en"}, "Google&nbsp;Maps")),
                td(
"NSW satellite and aerial imagery is also available from Google Maps."
                ),
            ),
        );

    return $html;
}

sub show_infrastructure
{
}

sub show_library
{
}

sub show_current
{
}

sub show_about
{
}



sub	get_visits
{
	my $count;
	my $date;
	my $cntfile = "../visitors.dat";

	-f $cntfile or $cntfile = "/home/rolfeb/visitors.dat";

	if (-s $cntfile and open(COUNTER, "+< $cntfile"))
	{
		($date, $count) = split(/,\s*/, <COUNTER>);
		seek(COUNTER, 0, 0);
		printf(COUNTER "$date,%d", ++$count);
		close(COUNTER);
	}
	elsif (open(COUNTER, "> $cntfile"))
	{
		my @mon = qw(Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec);
		my ($d, $m, $y) = (localtime())[3,4,5];

		$date = sprintf("%d %s %d", $d, $mon[$m], $y + 1900);
		$count = 1;

		printf(COUNTER "%s,%d", $date, 1);
		close(COUNTER);
	}

	return ($count, $date);
}

#
# Read in the HTML fragemnt containing random pictures
#
sub	get_randpix_html
{
    my $incfile = "../index-pics.inc";
    -f $incfile or $incfile = "/home/rolfeb/public_html/index-pics.inc";

    if (open(HTML, "< $incfile"))
    {
        my $html = "";
        while (<HTML>)
        {
            $html .= $_;
        }
        close(HTML);

        return $html;
    }
    return "";
}

#
# Read in the HTML fragemnt containing recent changes
#
sub	get_changes_html
{
	my $incfile = "../index-log.inc";
	-f $incfile or $incfile = "/home/rolfeb/public_html/index-log.inc";

	if (open(HTML, "< $incfile"))
	{
		my $html = "";
		while (<HTML>)
		{
			$html .= $_;
		}
		close(HTML);

		return $html;
	}
	return "";
}
