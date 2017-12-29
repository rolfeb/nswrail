$Magic = '$1$';			# Magic string
$itoa64 = "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

use Digest::MD5;

sub to64 {
    my ($v, $n) = @_;
    my $ret = '';
    while (--$n >= 0) {
	$ret .= substr($itoa64, $v & 0x3f, 1);
	$v >>= 6;
    }
    $ret;
}

sub apache_md5_crypt {
	# change the Magic string to match the one used by Apache
	local $Magic = '$apr1$';
	
	unix_md5_crypt(@_);
}

sub unix_md5_crypt {
    my($pw, $salt) = @_;
    my $passwd;

    $salt =~ s/^\Q$Magic//;	# Take care of the magic string if
				# if present.

    $salt =~ s/^(.*)\$.*$/$1/;	# Salt can have up to 8 chars...
    $salt = substr($salt, 0, 8);

    $ctx = new Digest::MD5;		# Here we start the calculation
    $ctx->add($pw);		# Original password...
    $ctx->add($Magic);		# ...our magic string...
    $ctx->add($salt);		# ...the salt...

    my ($final) = new Digest::MD5;
    $final->add($pw);
    $final->add($salt);
    $final->add($pw);
    $final = $final->digest;

    for ($pl = length($pw); $pl > 0; $pl -= 16) {
	$ctx->add(substr($final, 0, $pl > 16 ? 16 : $pl));
    }

				# Now the 'weird' xform

    for ($i = length($pw); $i; $i >>= 1) {
	if ($i & 1) { $ctx->add(pack("C", 0)); }
				# This comes from the original version,
				# where a memset() is done to $final
				# before this loop.
	else { $ctx->add(substr($pw, 0, 1)); }
    }

    $final = $ctx->digest;
				# The following is supposed to make
				# things run slower. In perl, perhaps
				# it'll be *really* slow!

    for ($i = 0; $i < 1000; $i++) {
	$ctx1 = new Digest::MD5;
	if ($i & 1) { $ctx1->add($pw); }
	else { $ctx1->add(substr($final, 0, 16)); }
	if ($i % 3) { $ctx1->add($salt); }
	if ($i % 7) { $ctx1->add($pw); }
	if ($i & 1) { $ctx1->add(substr($final, 0, 16)); }
	else { $ctx1->add($pw); }
	$final = $ctx1->digest;
    }
    
				# Final xform

    $passwd = '';
    $passwd .= to64(int(unpack("C", (substr($final, 0, 1))) << 16)
		    | int(unpack("C", (substr($final, 6, 1))) << 8)
		    | int(unpack("C", (substr($final, 12, 1)))), 4);
    $passwd .= to64(int(unpack("C", (substr($final, 1, 1))) << 16)
		    | int(unpack("C", (substr($final, 7, 1))) << 8)
		    | int(unpack("C", (substr($final, 13, 1)))), 4);
    $passwd .= to64(int(unpack("C", (substr($final, 2, 1))) << 16)
		    | int(unpack("C", (substr($final, 8, 1))) << 8)
		    | int(unpack("C", (substr($final, 14, 1)))), 4);
    $passwd .= to64(int(unpack("C", (substr($final, 3, 1))) << 16)
		    | int(unpack("C", (substr($final, 9, 1))) << 8)
		    | int(unpack("C", (substr($final, 15, 1)))), 4);
    $passwd .= to64(int(unpack("C", (substr($final, 4, 1))) << 16)
		    | int(unpack("C", (substr($final, 10, 1))) << 8)
		    | int(unpack("C", (substr($final, 5, 1)))), 4);
    $passwd .= to64(int(unpack("C", substr($final, 11, 1))), 2);

    $final = '';
    $Magic . $salt . '$' . $passwd;
}

1;
