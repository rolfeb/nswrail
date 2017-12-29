package Line;
# vi: sw=4 ts=4 expandtab

use Carp;
use Database;
use Data::Dumper;

=head1 NAME

Line - class to manipulate Lines

=head1 SYNOPSIS

use Line;

$location = Line->retrieve($state, $id);

$value = $line->get($field);

$line->set($field, $value);

@lines = Line->retrieve_by_location($state, $name);

=head1 DESCRIPTION

Use this class to query, update, or create Lines and their associated
data.

=cut

use constant	STATE		=>	"state";
use constant	ID		    =>	"id";
use constant	SHORTNAME	=>	"shortname";
use constant	NAME		=>	"name";
use constant	REGION		=>	"region";
use constant	DESCTEXT	=>	"desctext";
use constant	LOCATIONS	=>	"locations";

my %table_info = (  # RO
    STATE		=>	[ 1 ],
    ID		    =>	[ 1 ],
    SHORTNAME	=>	[ 0 ],
    NAME	    =>	[ 0 ],
    REGION		=>	[ 0 ],
    DESCTEXT	=>	[ 0 ],
    LOCATIONS	=>	[ 1 ],
);

#
# retrieve($state, $id)
#
sub BEGIN 
{
    my $db = Database::handle();

    my $sql = qq(
        select
            shortname,
            name,
            region,
            version
        from
            r_line
        where
            state = ?
            and
            id = ?
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
            r_line_text
        where
            state = ?
            and
            id = ?
        order by
            seqno
    );

    my $sel2 = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    $sql = qq(
        select
            location_state,
            location_name
        from
            r_line_location
        where
            line_state = ?
            and
            line_id = ?
        order by
            seqno
    );

    my $sel3 = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    sub	retrieve
    {
        my ($pkg, $state, $id) = @_;

        $sel1->execute($state, $id)
            or confess("SQL execute failed: $DBI::errstr");
    
        my ($shortname, $name, $region, $version)
            = $sel1->fetchrow_array()
                or carp("unknown location: \"$state\", \"$id\"");
        
        my $data = {
            state       => $state,
            id          => $id,
            shortname   => $shortname,
            name        => $name,
            region      => $region,
            locations   => [],
            version     => $version,
        };

        #
        # Load text information
        #
        $sel2->execute($state, $id)
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
        }

        #
        # Load location information
        #
        $sel3->execute($state, $id)
            or confess("SQL execute failed: $DBI::errstr");
        
        while (my ($lstate, $lname) = $sel3->fetchrow_array())
        {
            my $d = {
                    state       => $lstate,
                    name        => $lname,
            };

            push(@{$data->{locations}}, $d);
        }

        bless($data, $pkg);
        return $data;
    }
}

#
# retrieve_by_location($state, $name)
#
sub BEGIN 
{
    my $db = Database::handle();

    my $sql = qq(
        select
            line_state,
            line_id,
            mainline
        from
            r_line_location
        where
            location_state = ?
            and
            location_name = ?
        );

    my $sel1 = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    my @lines;

    sub	retrieve_by_location
    {
        my ($pkg, $state, $name) = @_;

        $sel1->execute($state, $name)
            or confess("SQL execute failed: $DBI::errstr");
    
        while (($line_state, $line_id, $mainline)
            = $sel1->fetchrow_array())
        {
            my $data = {
                line        => $pkg->retrieve($line_state, $line_id),
                mainline    => $mainline,
            };

            push(@lines, ($data));
        }

        #
        # Sort lines my mainline first, then alphabetic order
        #
        @lines = 
            map { $_->[2] }
            sort { $b->[0] cmp $a->[0] || $a->[1] cmp $b->[1] }
            map { [ $_->{mainline}, $_->{line}->get(Line::NAME), $_ ] }
            @lines;

        return @lines;
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

    if ($field eq DESCTEXT)
    {
        return $l->{description}->[$specifier];
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
            id = "$l->{id}"
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
