package LocationText;
# vi: sw=4 ts=4 expandtab

use Carp;
use Database;

use constant	STATE		=>	"state";
use constant	NAME		=>	"name";
use constant	SEQNO	    =>	"seqno";
use constant	TEXT		=>	"text";
use constant	TYPE		=>	"type";
use constant	SUBMIT_DATE	=>	"submit_date";
use constant	SUBMIT_BY	=>	"submit_by";
use constant	STATUS		=>	"status";

my %table_info = (  # RO
    STATE		=>	[ 1 ],
    NAME		=>	[ 1 ],
    SEQNO	    =>	[ 1 ],
    TEXT		=>	[ 0 ],
    TYPE		=>	[ 0 ],
    SUBMIT_DATE	=>	[ 0 ],
    SUBMIT_BY	=>	[ 0 ],
    STATUS		=>	[ 0 ],
);

#
# retrieve($state, $name, $seqno)
#
sub BEGIN 
{
    my $db = Database::handle();

    my $sql = qq(
        select
            text,
            type,
            submit_date,
            submit_by,
            status
        from
            r_location_text
        where
            state = ?
            and
            name = ?
            and
            seqno = ?
        );

    my $sel1 = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    sub	retrieve
    {
        my ($pkg, $state, $name, $seqno) = @_;

        $sel1->execute($state, $name, $seqno)
            or confess("SQL execute failed: $DBI::errstr");
        
        my ($text, $type, $submit_date, $submit_by, $status)
            = $sel1->fetchrow_array()
                or carp("unknown location text: \"$state\", \"$name\", \"$seqno\"");
        
        my $data = {
            state       => $state,
            name        => $name,
            seqno       => $seqno,
            text        => $text,
            type        => $type,
            submit_date => $submit_date,
            submit_by   => $submit_by,
            status      => $status,
        };
        
        bless($data, $pkg);
        return $data;
    }
}

#
# create($state, $name, $text, $type, $submit_by, $submit_date, $status)
#
sub BEGIN
{
    my $db = Database::handle();

    my $sql = qq(
        lock tables r_location write
    );

    my $lock = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    $sql = qq(
        select
            count(*)
        from 
            r_location_text
        where
            state = ?
            and
            name = ?
    );

    my $sel = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    $sql = qq(
        insert into
            r_location_text
        values (?, ?, ?, ?, ?, ?, ?, ?)
    );

    my $insert = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    my $sql = qq(
        unlock tables
    );

    my $unlock = $db->prepare($sql)
        or confess("SQL parse failed: $DBI::errstr");

    sub create
    {
        my $pkg = shift;
        my $state = shift or carp("missing argument: state");
        my $name = shift or carp("missing argument: name");
        my $text = shift or carp("missing argument: text");
        my $type = shift or carp("missing argument: type");
        my $submit_by = shift or carp("missing argument: submit_by");
        my $submit_date = shift or carp("missing argument: submit_date");
        my $status = shift or carp("missing argument: status");

        #
        # Validate the parameters
        #
        if ($type ne "DESC" and $type ne "CURR")
        {
            carp("invalid text type: $type");
        }

        $lock->execute()
            or confess("SQL execute failed: $DBI::errstr");

        if (!$sel->execute($state, $name))
        {
            my $err = $DBI::errstr;
            $db->rollback();
            confess("SQL execute failed: $err");
        }
        
        my ($count) = $sel->fetchrow_array();

        if (!$insert->execute($state, $name, $count+1, $text, $type, $submit_by, $sumit_date, $status))
        {
            my $err = $DBI::errstr;
            $db->rollback();
            confess("SQL execute failed: $err");
        }
        
        if (!$unlock->execute())
        {
            my $err = $DBI::errstr;
            $db->rollback();
            confess("SQL execute failed: $err");
        }
    
        $db->commit();
    }
}

############################################################################

#
# get($field) -> $value
#
sub	get
{
    my ($l, $field) = @_;

    return $l->{$field};
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
