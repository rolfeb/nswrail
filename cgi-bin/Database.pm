package Database;
# vi: sw=4 ts=4 expandtab

use DBI;

my $DB_NAME		= "raildb";
my $DB_USER		= "admin";
my $DB_PASSWORD = "admin";

my $handle;

sub	handle
{
    $handle = DBI->connect("DBI:mysql:$DB_NAME", $DB_USER, $DB_PASSWORD)
        or die "$0: couldn't connect to database $DB_NAME: $!\n";

    return $handle;
}

sub	disconnect
{
    if ($handle)
    {
        $handle->disconnect();
        $handle = undef;
    }
}

1;
