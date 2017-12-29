package Location;
# vi: sw=4 ts=4 expandtab

use Carp;
use Database;
use Data::Dumper;

=head1 NAME

Location - class to manipulate Locations

=head1 SYNOPSIS

use Location;

$location = Location->retrieve($state, $name);

$value = $location->get($field);

$location->set($field, $value);

=head1 DESCRIPTION

Use this class to query, update, or create Locations and their associated
data.

=cut

use constant	STATE		=>	"state";
use constant	NAME		=>	"name";
use constant	DISTANCE	=>	"distance";
use constant	GEO_X		=>	"geo_x";
use constant	GEO_Y		=>	"geo_y";
use constant	UTM_ZONE	=>	"utm_zone";
use constant	UTM_X		=>	"utm_x";
use constant	UTM_Y		=>	"utm_y";
use constant	MAINLINE	=>	"mainline";
use constant	NDESCTEXT	=>	"ndesctext";
use constant	DESCTEXT	=>	"desctext";
use constant	NCURRTEXT	=>	"ncurrtext";
use constant	CURRTEXT	=>	"currtext";
use constant	PHOTOS	    =>	"photos";
use constant	NPHOTOS	    =>	"nphotos";
use constant	DIAGRAMS   =>	"diagrams";
use constant	NDIAGRAMS   =>	"ndiagrams";


my %table_info = (  # RO
    STATE		=>	[ 1 ],
    NAME		=>	[ 1 ],
    DISTANCE	=>	[ 0 ],
    GEO_X		=>	[ 0 ],
    GEO_Y		=>	[ 0 ],
    UTM_ZONE	=>	[ 0 ],
    UTM_X		=>	[ 0 ],
    UTM_Y		=>	[ 0 ],
    MAIN_LINE	=>	[ 1 ],
    NDESCTEXT	=>	[ 1 ],
    NCURRTEXT	=>	[ 1 ],
    NPHOTOS	    =>	[ 1 ],
    NDIAGRAMS	=>	[ 1 ],
);

#
# retrieve($state, $name)
#
sub BEGIN 
{
    my $db = Database::handle();

    my $sql = qq(
        select
            a.distance,
            a.geo_x,
            a.geo_y,
            a.utm_zone,
            a.utm_x,
            a.utm_y,
            b.line_state,
            b.line_id,
            a.version
        from
            r_location a,
            r_line_location b
        where
            a.state = ?
            and
            a.name = ?
            and
            b.location_state = a.state
            and
            b.location_name = a.name
            and
            b.mainline = 'Y'
        );

    my $sel1 = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    $sql = qq(
        select
            type,
            text,
            submit_date,
            submit_by,
            status
        from
            r_location_text
        where
            state = ?
            and
            name = ?
        order by
            seqno
    );

    my $sel2 = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    $sql = qq(
        select
            file,
            owner,
            day,
            month,
            year,
            caption,
            themes,
            submit_date,
            submit_by,
            status
        from
            r_location_photo
        where
            state = ?
            and
            name = ?
        order by
            year,
            month,
            day
    );

    my $sel3 = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    $sql = qq(
        select
            file,
            year,
            submit_date,
            submit_by,
            status
        from
            r_location_diagram
        where
            state = ?
            and
            name = ?
        order by
            year
    );

    my $sel4 = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    sub	retrieve
    {
        my ($pkg, $state, $name) = @_;

        $sel1->execute($state, $name)
            or confess("SQL execute failed: $DBI::errstr");
    
        my ($distance, $geo_x, $geo_y, $utm_zone, $utm_x, $utm_y, $line_state, $line_id, $version)
            = $sel1->fetchrow_array()
                or carp("unknown location: \"$state\", \"$name\"");
        
        my $data = {
            state       => $state,
            name        => $name,
            distance    => $distance,
            geo_x       => $geo_x,
            geo_y       => $geo_y,
            utm_zone    => $utm_zone,
            utm_x       => $utm_x,
            utm_y       => $utm_y,
            description => [],
            current     => [],
            photos      => [],
            diagrams    => [],
            mainline    => Line->retrieve($line_state, $line_id),
            version     => $version,
        };

        #
        # Load text information
        #
        $sel2->execute($state, $name)
            or confess("SQL execute failed: $DBI::errstr");
        
        while (my ($type, $text, $submit_date, $submit_by, $status)
            = $sel2->fetchrow_array())
        {
            my $d = {
                    text        => $text,
                    submit_date => $submit_date,
                    submit_by   => $submit_by,
                    status      => $status,
            };

            if ($type eq "DESC")
            {
                unshift(@{$data->{description}}, $d);
            }
            else
            {
                unshift(@{$data->{current}}, $d);
            }
        }
        
        #
        # Load photo information
        #
        $sel3->execute($state, $name)
            or confess("SQL execute failed: $DBI::errstr");
        
        while (@row = $sel3->fetchrow_array())
        {
            my ($file, $owner, $day, $month, $year, $caption, $themes,
                $submit_date, $submit_by, $status) = @row;

            push(@{$data->{photos}}, {
                file        => $file,
                owner       => $owner,
                day         => $day,
                month       => $month,
                year        => $year,
                caption     => $caption,
                themes      => $themes,
                submit_date => $submit_date,
                submit_by   => $submit_by,
                status      => $status,
            });
        }
        
        #
        # Load diagram information
        #
        $sel4->execute($state, $name)
            or confess("SQL execute failed: $DBI::errstr");

        while (@row = $sel4->fetchrow_array())
        {
            my ($file, $year, $submit_date, $submit_by, $status) = @row;

            push(@{$data->{diagrams}}, {
                file        => $file,
                year        => $year,
                submit_date => $submit_date,
                submit_by   => $submit_by,
                status      => $status,
            });
        }


        bless($data, $pkg);
        return $data;
    }
}

#
# create(...)
#
sub BEGIN
{
    sub create
    {
        XXX;
    }
}

############################################################################

#
# get($field, $specifier) -> $value
#
sub	get
{
    my ($l, $field, $specifier) = @_;

    if ($field eq NDESCTEXT)
    {
        return scalar @{$l->{description}};
    }
    elsif ($field eq NCURRTEXT)
    {
        return scalar @{$l->{current}};
    }
    elsif ($field eq NPHOTOS)
    {
        return scalar @{$l->{photos}};
    }
    elsif ($field eq NDIAGRAMS)
    {
        return scalar @{$l->{diagrams}};
    }
    elsif ($field eq DESCTEXT)
    {
        return $l->{description}->[$specifier];
    }
    elsif ($field eq CURRTEXT)
    {
        return $l->{current}->[$specifier];
    }
    elsif ($field eq PHOTOS)
    {
        return $l->{photos}->[$specifier];
    }
    elsif ($field eq DIAGRAMS)
    {
        return $l->{diagrams}->[$specifier];
    }
    else
    {
        return $l->{$field};
    }
}

#
# set($field, $value)
#
sub	set
{
    my ($l, $field, $value) = @_;

    #
    # Disable update of read-only fields
    #
    if ($table_info{$field}->[0])
    {
        croak("attempted update of read-only field");
    }

    my $sql = qq(
        update
            r_location
        set
            $field = ?,
            version = version + 1
        where
            state = "$l->{state}"
            and
            name = "$l->{name}"
            and
            version = $l->{version}
    );

    my $stmt = Database::handle->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    my $n = $stmt->execute($value)
        or confess("SQL execute failed: $DBI::errstr");

    if ($n == 0)
    {
        #
        # The update is likely to have failed due to a version mismatch,
        # where someone else has updated the row first...
        #
        croak("update failed");
    }
    
    $l->{$field} = $value;
    $l->{version}++;
}

sub add_text
{
    my ($l, $type, $text) = @_;

    # XXX: my $user = User->current();
    # XXX
}

sub add_diagram
{
    my ($l, $file, $year) = @_;

    # XXX: my $user = User->current();
    # XXX
}

sub add_photo
{
    my ($l, $file, $owner, $day, $month, $year, $caption, $themes) = @_;

    # XXX: my $user = User->current();
    # XXX
}

############################################################################

sub _strip_whitespace
{
    my ($text) = @_;

    $text =~ s/^\s+//;
    $text =~ s/\s+$//;
    $text =~ s/\s\s+/ /g;

    return $text;
}

1;
