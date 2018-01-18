#!/usr/bin/perl -w
#
# Main director
#

use CGI qw(:all *table *Tr *td *dl);
use CGI::Session;
use Data::Dumper;
use strict;

my $cgi = new CGI;
my $sid = $cgi->{SID} || $cgi->cookie("SESSIONID") || "";
my $session;
if ($sid)
{
    # retrieve session
    $session = new CGI::Session(undef, $sid, { Directory => "/tmp" });
}

my $action = $cgi->param("ACTION") || "";
my $page = $cgi->param("PAGE") || "";

print STDERR "main.cgi: session=$sid, action=[$action], page=[$page]\n";

#
# Perform any special actions
#
if ($action eq "do-login")
{
    ($page, $session) = process_login($cgi, $session);
}
elsif ($action eq "do-logout")
{
    ($page) = process_logout($cgi, $session);
    $session = undef;
}


#
# Print the HTTP header, including cookie if logged in
#
if ($session)
{
    my $cookie = $cgi->cookie(
        -name=>"SESSIONID",
        -value=>$session->id(),
        -expires=>"+1d",
        -domain=>".nswrail.net",
    );

    print header(-type=>"text/html", -cookie=>$cookie);
}
else
{
    print header(-type=>"text/html");
}

#
# Display the appropriate page
#
if ($page eq "login")
{
    run_login_page($cgi, $session);
}
elsif ($page eq "line")
{
    run_line_page($cgi, $session);
}
elsif ($page eq "location")
{
    run_location_page($cgi, $session);
}
else
{
    run_index_page($cgi, $session);
}

exit 0;

sub	process_login
{
    my ($cgi, $session) = @_;

    if ($session)
    {
        $session->delete();
    }

    my $user = $cgi->param("user");
    my $password = $cgi->param("password");

    # XXX: verify password

    # create new session
    $session = new CGI::Session(undef, undef, { Directory => "/tmp" });
    $session->param("user", $user);

    print STDERR "New session " . $session->id() . " for $user\n";

    return ($cgi->param("PAGE") || "", $session);
}

sub	process_logout
{
    my ($cgi, $session) = @_;

    if ($session)
    {
        $session->delete();
    }

    return $cgi->param("PAGE");
}

sub	run_login_page
{
    my ($cgi, $session) = @_;

    print start_html({-title=>"New South Wales Railways",
        -author=>"Rolfe Bozier, nswrail\@pobox.com",
        -meta=>{-keywords=>"NSW rail history photographs maps"},
        -style=>{-src=>["/rail.css","/index.css"]},
        -head=>Link({-rel=>"shortcut icon",-type=>"image/gif",-href=>"/images/railicon.gif"}),
    });

    print div({-align=>"center"},
        img({src=>"/images/title.jpg", -width=>"545", -height=>"49", -alt=>"[title: New South Wales Railways]"})
    );

    print div(
        h2("Login"),
        start_form({-method=>"POST",-action=>"/cgi-bin/main.cgi"}),
        hidden(-name=>"ACTION",-value=>"do-login"),
        hidden(-name=>"PAGE",-value=>"index"),
        start_table(),
        Tr(
            td([
                b("User:"),
                textfield({-name=>"user",-size=>"12",-maxlength=>"20",-class=>"textinput"})
            ])
        ),
        Tr(
            td([
                b("Passwd:"),
                password_field({-name=>"password",-size=>"12",-maxlength=>"20",-class=>"textinput"})
            ])
        ),
        Tr(
            td([
                submit(-name=>"Cancel",-value=>"Cancel"),
                submit(-name=>"Login",-value=>"Login"),
            ])
        ),
        end_form(),
        end_table()
    );

    print end_html();
}

sub	run_index_page
{
    my ($cgi, $session) = @_;

    print start_html({-title=>"New South Wales Railways",
        -author=>"Rolfe Bozier, nswrail\@pobox.com",
        -meta=>{-keywords=>"NSW rail history photographs maps"},
        -style=>{-src=>["/rail.css","/index.css"]},
        -head=>Link({-rel=>"shortcut icon",-type=>"image/gif",-href=>"/images/railicon.gif"}),
    });

    print div({-align=>"center"},
        img({src=>"/images/title.jpg", -width=>"545", -height=>"49", -alt=>"[title: New South Wales Railways]"})
    );

    if ($session)
    {
        my $user = $session->param("user");

        print div({-align=>"right"},
            start_form({-method=>"POST",-action=>"/cgi-bin/main.cgi"}),
            "Logged in as $user ",
            hidden(-name=>"PAGE",-value=>"index"),
            hidden(-name=>"ACTION",-value=>"do-logout"),
            submit(-name=>"Logout",-value=>"Logout"),
            end_form(),
        );
    }
    else
    {
        print div({-align=>"right"},
            start_form({-method=>"POST",-action=>"/cgi-bin/main.cgi"}),
            hidden(-name=>"PAGE",-value=>"login"),
            hidden(-name=>"ACTION",-value=>"run-page"),
            submit(-name=>"Go",-value=>"Login"),
            end_form(),
        );
    }

    print start_table({-width=>"100%",-cellspacing=>"0",-cellpadding=>"0",-border=>"0"});

    print start_Tr({-valign=>"top"});
    print start_td();
    print div(
        h2("Network"),
        ul({-class=>"simple"},
            li(a({-href=>"/lines/nsw-lines.html"}, "All lines")),
            li(a({-href=>"/maps/nsw-index.html"}, "NSW map")),
            li(a({-href=>"/maps/sydney-index.html"}, "Sydney map")),
            li(a({-href=>"/maps/newcastle-index.html"}, "Newcastle map")),
        ),
        h2("Photos"),
        ul({-class=>"simple"},
            li(a({-href=>"/photos/recent.html"}, "Recent photos")),
            li(a({-href=>"/photos/index.html"}, "Photo categories")),
            li(a({-href=>"/submissions.html"}, "Submissions")),
        ),
        h2("Maps"),
        ul({-class=>"simple"},
            li(a({-href=>"/network-maps/index.html"}, "Network maps")),
            li(a({-href=>"/historic-maps/index.html"}, "Historic maps")),
        ),
        h2("Infrastructure"),
        ul({-class=>"simple"},
            li(a({-href=>"/timeline.html"}, "Network history")),
            li(a({-href=>"/unstarted.html"}, "Planned lines")),
            li(a({-href=>"/research/index.html"}, "Research")),
            li(a({-href=>"/database/locations.html"}, "Location DB")),
            li(a({-href=>"/tunnels.html"}, "Tunnels")),
            li(a({-href=>"/database/turntables.html"}, "Turntables")),
            li(a({-href=>"/trivia.html"}, "Trivia")),
        ),
        h2("Current"),
        ul({-class=>"simple"},
            li(a({-href=>"/signals.html"}, "Signals")),
            li(a({-href=>"/operators.html"}, "Operators")),
        ),
        h2("This site"),
        ul({-class=>"simple"},
            li(a({-href=>"/updates.html"}, "Change log")),
            li(a({-href=>"/about.html"}, "About...")),
            li(a({-href=>"/credits.html"}, "Credits")),
            li(a({-href=>"/links.html"}, "Links")),
        ),
    );

    print div({-id=>"search"},
        h2("Search"),
        start_form({-method=>"GET",-action=>"/lsearch.php"}),
        textfield({-name=>"location",-size=>"20",-class=>"textinput"}),
        end_form()
    );

###    print div({-id=>"login"},
###        h2("Login"),
###        start_form({-method=>"POST",-action=>"/cgi-bin/main.cgi"}),
###        start_table(),
###        Tr(
###            td([
###                b("User:"),
###                textfield({-name=>"login",-size=>"12",-maxlength=>"20",-class=>"textinput"})
###            ])
###        ),
###        Tr(
###            td([
###                b("Passwd:"),
###                password_field({-name=>"password",-size=>"12",-maxlength=>"20",-class=>"textinput"})
###            ])
###        ),
###        hidden(-name=>"PAGE",-value=>"index"),
###        hidden(-name=>"ACTION",-value=>"do-login"),
###        submit(-name=>"Go",-value=>"Go"),
###        end_form(),
###        end_table()
###    );

    ### PAGE VISITS ###
    my ($count, $date) = get_visits();
    if ($count)
    {
        print div(
            h2("Visitors"),
            p("<b>$count</b> since $date"),
            p()
        );
    }

    print end_td();

    print start_td();

    ### MAIN TEXT ###
    print div({-id=>"content"},
    p("These pages contain a variety of information about the NSW railway network,
    both historical and current.  The data can be broken down into four
    categories: non-spatial (plain data), spatial (map data), photographic
    and hypertext (links to related data sources).
    The intention of these pages to make as much information as possible available
    on the net.  Although you will find pictures of trains within these pages,
    I have chosen to concentrate on recording the infrastructure of the state,
    especially on the abandoned branch lines."),
    p("Much as I would like to, I probably won't be able to visit every location
    in the state, taking notes and photographs!  If you want to contribute
    information or photos, then please contact me at " . a({-href=>"mailto:nswrail\@pobox.com"}, "nswrail\@pobox.com")),
    p("Much of the information and photos in these pages has been generously
    provided by a lot of people.  Their work has contributed immeasureably to
    the success of these pages."),
    p(),
    p("Rolfe Bozier"),
    p(),
    p("P.S.  Please don't ask me about travel arrangements, as I can't provide any
    advice.  You might like to try " .
    a({-href=>"http://www.railpage.org.au/railmaps/"},"Australian Rail Maps") .
    " as a starting point for planning a trip."),

    hr(),
    );

    ### RECENT CHANGES ###
    my $changes_html = get_changes_html();
    if ($changes_html)
    {
        print $changes_html;
    }

    print end_td();

    print start_td();

    ### RANDOM PICTURES ###
    my $randpics_html = get_randpix_html();
    if ($randpics_html)
    {
        print div({-id=>"photos"},
            h2("Random photos"),
            $randpics_html
        );
    }

    print end_td();

    print end_Tr();
    print end_table();

    ### FOOTER ###
    print div({-id=>"footer"},
    p("All text, maps and photographs are &copy; 2000-2005 Rolfe Bozier except
    where otherwise noted.  Please contact me if you would like to use any of
    them (I will almost certainly say yes!)."),
    );

    print end_html();
}

sub    run_line_page
{
    my ($cgi, $session) = @_;

}

sub    run_location_page
{
    my ($cgi, $session) = @_;

}



exit 0;

sub    get_visits
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
sub    get_randpix_html
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
sub    get_changes_html
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
