#!/usr/bin/perl -w

use strict;
use CGI qw(:all *table *Tr *td *dl);
use Cwd;

print header(-type=>"text/html");

print start_html({-title=>"ARHS Bulletin Search Form",
	-style=>{-src=>["/rail.css"]},
	-head=>Link({-rel=>"shortcut icon",-type=>"image/gif",-href=>"/images/railicon.gif"}),
});

print
    h1("ARHS(NSW) Bulletin Search Form");

my $cgi = new CGI;
my $searchmode = $cgi->param("searchmode");

my $MAXRESULTS = 200;

if ($searchmode)
{
    process_search($cgi);
}
else
{
    show_search_form($cgi);
}

print end_html();

exit 0;

sub find_data_file
{
    my ($file) = @_;

    my $path = "../data/$file";
    -f $path or $path = "/home/rolfeb/data/$file";

    return $path;
}

sub show_search_form
{
    my ($cgi) = @_;

    print
        p(
        "The following search form can be used to search for articles from the",
        "ARHS Bulletin magazine (note that the articles themselves are",
        "<em>not</em> on line).  The index covers the years from 1937 to 2002",
        "(some entries are missing for the early 1990s).",
        ),
        p(
        "The data is almost completely the responsibility of the following",
        "people:",
        ul(
            li("Howard Quinlan (compiled original data from 1937 to 1987)"),
            li("David Virgo (converted to electronic form)"),
            li("Geoff Lambert (corrected data and added data up to Dec 2000)"),
            li("Rolfe Bozier (corrected data and added data up the latest date)"),
        ),
        );

    my $url = $cgi->url(-absolute=>1);

    print
        start_form(-method=>"POST", -action=>$url),
        start_table(),
        Tr(
            td(b("Title:")),
            td(
                textfield("titlekeywords", "", 60, 60),
                popup_menu(
                    -name=>"titlejoin",
                    -values=>[ "all", "any" ],
                    -labels=>{"all"=>"all of", "any"=>"any of" },
                    -default=> "all",
                )
            )
        ),
        Tr(
            td(b("Author:"),),
            td(
                textfield("authorkeywords", "", 40, 40),
            )
        ),
        Tr(
            td(b("Description:")),
            td(
                textfield("synopsiskeywords", "", 60, 60),
                popup_menu(
                    -name=>"synopsisjoin",
                    -values=>[ "all", "any" ],
                    -labels=>{"all"=>"all of", "any"=>"any of" },
                    -default=> "all",
                )
            )
        ),
        Tr(
            td(b("Volume:")),
            td(
                textfield("volume", "", 4, 4),
                popup_menu(
                    -name=>"volumetype",
                    -values=>[ "new", "old" ],
                    -labels=>{ "new"=>"New series", "old"=>"Old series" },
                    -default=> "new",
                )
            )
        ),
        Tr(
            td(b("Number:")),
            td(
                textfield("issue", "", 4, 4),
            )
        ),
        Tr(
            td(b("Month:")),
            td(
                popup_menu(
                    -name=>"month",
                    -values=>[ "", qw(Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec) ],
                    -labels=>{ "Jan"=>"January", "Feb"=>"February", "Mar"=>"March", "Apr"=>"April", "May"=>"May", "Jun"=>"June", "Jul"=>"July", "Aug"=>"August", "Sep"=>"September", "Oct"=>"October", "Nov"=>"November", "Dec"=>"December", },
                    -default=> "",
                )
            )
        ),
        Tr(
            td(b("Year:"),),
            td(
                textfield("year", "", 4, 4),
            )
        ),
        end_table();

    print
        br(),
        reset("Reset Form"),
        submit("Perform Search"),
        hidden("searchmode", "1"),
        end_form();
}

sub process_search
{
    my ($cgi) = @_;

    my $datfile = find_data_file("arhsbull.dat");

    if (!open(DAT, "< $datfile"))
    {
        print "<b>ERROR:</b> failed to open database file: arhsbull.dat\n";
        return;
    }

    my $count = 0;
    my $curr_issue = -1;

    while (my $line = <DAT>)
    {
        chomp($line);

        my $match = 0;
        my $words;

        my ($title, $authors, $synopsis, $volume, $issue, $month, $year,
            $pages) = split(/\|/, $line);

        if ($words = $cgi->param("titlekeywords"))
        {
            my @keywords = split(/\s+/, $words);

            if ($cgi->param("titlejoin") eq "all")
            {
                $match = match_all($title, \@keywords);
            }
            else
            {
                $match = match_any($title, \@keywords);
            }
            !$match and next;
        }

        if ($words = $cgi->param("authorkeywords"))
        {
            my @keywords = split(/\s+/, $words);

            $match = match_any($authors, \@keywords);

            !$match and next;
        }

        if ($words = $cgi->param("synopsiskeywords"))
        {
            my @keywords = split(/\s+/, $words);

            if ($cgi->param("synopsisjoin") eq "all")
            {
                $match = match_all($synopsis, \@keywords);
            }
            else
            {
                $match = match_any($synopsis, \@keywords);
            }
            !$match and next;
        }

        if (my $vol = $cgi->param("volume"))
        {
            if ($cgi->param("volumetype") eq "old")
            {
                $vol .= " (Old)";
            }

            if (lc($vol) ne lc($volume))
            {
                next;
            }
        }

        if (my $val = $cgi->param("issue"))
        {
            if ($val != $issue)
            {
                next;
            }
        }
        if (my $val = $cgi->param("month"))
        {
            if ($val != $month)
            {
                next;
            }
        }
        if (my $val = $cgi->param("year"))
        {
            if ($val != $year)
            {
                next;
            }
        }

        #
        # If we reach this point, we've matched an article
        #
        if (++$count > $MAXRESULTS)
        {
            print
                end_table(),
                hr(),
                p(b("Results truncated - a maximum of $MAXRESULTS references will be displayed")),
                hr();
            last;
        }

        if ($count == 1)
        {
            print start_table({-cellspacing=>"0",-cellpadding=>"0",-border=>"0"});
        }

        if ($issue != $curr_issue)
        {
            $issue ||= "-";

            print
                Tr(
                    td({-colspan=>"2"},
                        br(),
                        b("$month $year"),
                        " (Vol $volume No. $issue)"
                    )
                );
        }

        $authors ||= "unknown";

        print
            Tr(
                td({-width=>"10%"}, ""),
                td(b("$title ($authors)"))
            );

        if ($synopsis)
        {
            print
                Tr({-valign=>"top"},
                    td({-width=>"10%"}, ""),
                    td("$synopsis")
                );
        }

        print
            Tr(
                td({-width=>"10%"}, ""),
                td("page[s]: $pages")
            );
    }
    close(DAT);

    if ($count <= $MAXRESULTS)
    {
        print
            end_table(),
            hr();
    }

    my $url = $cgi->url(-absolute=>1);

    print
        p("Perform another " . a({-href=>"$url"},"search") . ".");
}

sub match_all
{
    my ($string, $keywords) = @_;

    $string = lc $string;

    foreach my $word (@$keywords)
    {
        $word = lc $word;
        my $re = qr($word);

        if ($string !~ /$re/)
        {
            return 0;
        }
    }
    return 1;
}

sub match_any
{
    my ($string, $keywords) = @_;

    $string = lc $string;

    foreach my $word (@$keywords)
    {
        $word = lc $word;
        my $re = qr($word);

        if ($string =~ /.*$re.*/)
        {
            return 1;
        }
    }
    return 0;
}
