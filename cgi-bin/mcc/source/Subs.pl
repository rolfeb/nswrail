#Include language settings
#change txt
#Add url to settings
#The command line to global
# Modules below includes standard perl packages Unix/Linux/Windows

$LOCK_EX = 2;                                                         # You can probably keep this as it is set now.
$LOCK_UN = 8;                                                         # You can probably keep this as it is set now.
$LOCK_SH = 1;
$subsmain = "";	# set body start to blank
$subsinfo = "Script logging:\n"; #empty info block
$subsscripts = "";
$smalltemplate=0;
$copyin = 0;
@addons = ();
$subsmeta = qq~http-equiv="Content-Type" content="text/html; charset=$subscharset>"~;
%INFO=();
%FORM=();
$pwseed = 'yy';

$listbox_script= qq~
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	function getSelectedString(selectItem) {
		var i;
		var j = 0;
		var outlist = "";

		for (i = 0; i < selectItem.options.length; i++) {
			if (j > 0) {
				outlist = outlist + "+";
			}
			outlist = outlist + selectItem.options[i].value;
			j++;
		}
		return outlist;
	}

	function addItems(fromItem, toCtrl) {
		var i;
		var j;
		var itemexists;
		var nextitem;

		// step through all items in fromItem
		for (i = 0; i < fromItem.options.length; i++) {
			if (fromItem.options[i].selected) {
				// search toCtrl to see if duplicate
				j = 0;
				itemexists = false;
				while ((j < toCtrl.options.length) && (!(itemexists))) {
					if (toCtrl.options[j].value == fromItem.options[i].value) {
						itemexists = true;
						alert(fromItem.options[i].value + " found!");
					}
					j++;
				}
				if (!(itemexists)) {
				// add the item
					nextitem = toCtrl.options.length;
					toCtrl.options[nextitem] = new Option(fromItem.options[i].text);
					toCtrl.options[nextitem].value = fromItem.options[i].value;
				}
			}
		}
	}


	function removeItems(fromItem) {
	var i = 0;
		var j;
		var k = 0;

		while (i < (fromItem.options.length - k)) {
			if (fromItem.options[i].selected) {
				// remove the item
				for (j = i; j < (fromItem.options.length - 1); j++) {
					fromItem.options[j].text = fromItem.options[j+1].text;
					fromItem.options[j].value = fromItem.options[j+1].value;
					fromItem.options[j].selected = fromItem.options[j+1].selected;
				}
				k++;
			} else {
				i++;
			}
		}
		for (i = 0; i < k; i++) {
			fromItem.options[fromItem.options.length - 1] = null;
		}
	}
	//-->
	</SCRIPT>
~;

#&get_date;	# get the current date/time

#Get user IP
if (!$user_ip)
{
	if ($ENV{'HTTP_X_FORWARDED_FOR'} && $ENV{'HTTP_X_FORWARDED_FOR'} ne "127.0.0.1") {$user_ip = $ENV{'HTTP_X_FORWARDED_FOR'};}
	elsif (!$user_ip && $ENV{'HTTP_CLIENT_IP'} && $ENV{'HTTP_CLIENT_IP'} ne "127.0.0.1") {$user_ip = $ENV{'HTTP_CLIENT_IP'};}
	elsif (!$user_ip && $ENV{'X_CLIENT_IP'} && $ENV{'X_CLIENT_IP'} ne "127.0.0.1") {$user_ip = $ENV{'X_CLIENT_IP'};}
	else {$user_ip = $ENV{'REMOTE_ADDR'};}
}

sub GetMccURL {
	my $url = 'http://' . ($ENV{'HTTP_HOST'} ? $ENV{'HTTP_HOST'} : $ENV{'SERVER_NAME'}) .
	($ENV{'SERVER_PORT'} != 80 ? ":$ENV{'SERVER_PORT'}" : '') .
	$ENV{'SCRIPT_NAME'};
	$url =~ s~/[^/]+\Z~~;
	return $url;
}

# Gets our current absolute path. Needed for error messages.
sub GetDirPath {
	eval 'use Cwd; $cwd = cwd();';
	unless( $cwd ) { $cwd = `pwd`; chomp $cwd; }
	unless($cwd) { $cwd = $0 || $ENV{'PWD'} || $ENV{'CWD'} || ( $ENV{'DOCUMENT_ROOT'} . '/' . $ENV{'SCRIPT_NAME'} || $ENV{'PATH_INFO'} ); }
	$cwd =~ tr~\\~/~;
	$cwd =~ s~\A(.+)/\Z~$1~;
	$cwd =~ s~\A(.+)/mcc\.\w+\Z~$1~i;
	#Remove drive letter on NT
	my ($drive,$path) = split(':',$cwd);
	$cwd = $path if ($path ne '');
	return $cwd;
}

sub info {
	#We do nothing just connet lines
	if ($logging) {
  	$subsinfo = join("<BR>", ($subsinfo,@_));
	}
}

sub ToHTML {
	$_[0] =~ s/&/&amp;/g;
	$_[0] =~ s/"/&quot;/g;
	$_[0] =~ s/  / \&nbsp;/g;
	$_[0] =~ s/</&lt;/g;
	$_[0] =~ s/>/&gt;/g;
	$_[0] =~ s/\|/\&#124;/g;
	return $_[0];
}

sub FromHTML {
	$_[0] =~ s/&quot;/"/g;
	$_[0] =~ s/&nbsp;/ /g;
	$_[0] =~ s/&lt;/</g;
	$_[0] =~ s/&gt;/>/g;
	$_[0] =~ s/&#124;/\|/g;
	$_[0] =~ s/&amp;/&/g;
	return $_[0];
}

sub include_index {
	my %includes = ();
 	my @items = split(/\+/,join('+',@_));
  foreach (@items) {$includes{$_}=1;}
  return %includes;
}

#Check if key is included within line
sub includes {
	my ($key,@lines) = @_;
	my $line = join('+',@lines);
	my @items = split(/\+/,$line);
	for (my $index=0;$index<@items;$index++) {
		if ($items[$index] eq $key) {
			return $index;
	  }
	}
	return -1;
}


#Read the action form from CGI header
sub readform {
	my(@pairs, $pair, $name, $value);
	my $query = new CGI;
	my $etest = '';
	if($ENV{QUERY_STRING} =~ m~;~) { @pairs = split(/;/, $ENV{QUERY_STRING}); }
	else { @pairs = split(/&/, $ENV{QUERY_STRING}); }
	foreach $pair (@pairs) {
		  ($name,$value) = split(/=/, $pair);
		  $name =~ tr/+/ /;
		  $name =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		  $value =~ tr/+/ /;
		  $value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		  $value =~ s/<!--(.|\n)*-->//g;
		  $INFO{$name} = $value;
			if ($etest eq '') { $etest .= $name."=".$value; }
			else { $etest .= "&" .$name."=".$value; }
	}
  my (@keylist) = $query->param();
  my $qtest = '';
	foreach $key (@keylist) {
		# may be dealing with multiple values; need to join with comma
		$value = join(', ', $query->param($key));
  	$FORM{$key} = $value;
     if ($qtest eq '') { $qtest .= $key."=".$value; }
     else { $qtest .= "&" .$key."=".$value; }
  }
  if (lc($qtest) eq lc($etest)) {
		foreach $key (@keylist) {
			undef $FORM{$key};
 		}
  }
	$action = $INFO{'action'};
}

sub uploadfile {
	my ($g_upload_path,$g_overwrite,$g_binmode,$formname) = @_;
	$formname = 'upload_file' if (!$formname);
  my $query = new CGI;
  my $filepath=$query->param($formname);
	if ($filepath =~ /([^\/\\]+)$/)	{
		$filename="$1";
	}	else {
		$filename="$filepath";
	}
	# if there's any space in the filename, get rid of them
	$filename =~ s/\s+//g;
	my $write_file="$g_upload_path" . "/" . "$filename";
	if ($g_overwrite == 0)	{
		fatal_error("$filename $mcctxt{'144'}") if (-e $write_file);
	}

	if (!fopen(WFD,">$write_file")){
			fatal_error("$mcctxt{'143'} $write_file");
	}
	my $size = 0;
	while ($bytes_read=read($filepath,$buff,2096))
	{
			$size += $bytes_read;
			binmode WFD if ($g_binmode);
			print WFD $buff;
	}
	fclose(WFD);
	if ((stat $write_file)[7] <= 0){
			unlink($write_file);
			fatal_error("$mcctxt{'145'} $filename");
	}
	return $size;
}

sub get_date {
	my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time + (3600*$timeoffset));
	my $mon_num = $mon+1;
	my $savehour = $hour;
	$hour = "0$hour" if ($hour < 10);
	$min = "0$min" if ($min < 10);
	$sec = "0$sec" if ($sec < 10);
	my $saveyear = ($year % 100);
	$mon_num = "0$mon_num" if ($mon_num < 10);
	$mday = "0$mday" if ($mday < 10);
	$saveyear = "0$saveyear" if ($saveyear < 10);
	$subsdate = "$mday/$mon_num/$saveyear";
	$substime = "$hour:$min:$sec";
	return $subsdate;
}

#convert a get_date into a value so it can be compared
sub get_date_value {
	my ($date) =@_;
	if (!defined $date or $date eq '') {
		return 0;
  }
  my $count =(int(substr($date,0,2)) + int(substr($date,3,2))*100 + int(substr($date,6,2))*10000);
  return $count;
}

#Browse a directory
sub BrowseDir {
    my $dir             = $INFO{'dir'}; ##current
    my $init_dir        = $INFO{'initdir'}; ## 0/1 (set if it open from old config)
    my $field           = $INFO{'field'};
    use Cwd;
    eval("use File::Spec qw/catdir/;");
    if ($@) { fatal_error("Please install module File:Spec"); }
		$dir_delim = File::Spec->catdir('','');

RETRY:;
    $| = 1;
    $dir = cwd() unless (length $dir);
    $dir = &clean_path($dir,$dir_delim);
    my @dirs = split(/\Q$dir_delim\E/, $dir );
    my $dir_link = "";
    my $p = "";
    foreach (@dirs){
        next unless length;
        $p = length $p ? File::Spec->catdir($p, $_) : $_;
        my $href = ToHTML("$cgi;action=browsedir;dir=$p;field=$field");
        $dir_link .= qq~<a href="$href"><b>$_</b></a>/~;
    }

    unless (opendir(D, $dir)) {
         if ($init_dir){ ## start from current directory
            $init_dir = 0;
            $dir = '';
            goto RETRY;
         } else {
             fatal_error("$mcctxt{'1'}: $dir");
         }
    }
    print header(-status=>'200 OK', -charset=>"$subscharset");
    print qq~
    <html><head><title>$mcctxt{'100'}</title>
        <style>
            body,td,th,input {
                font-family: 'Helvetica', sans-serif;
                font-size: 0.8em; }
            td { background-color: #F0F0F0;}
        </style>
        <script>
            function clicked(rd){
                window.opener.${field}.value=rd.value;
                window.close();
            }
        </script>
    </head>
    <body bgcolor=white>
    <center>
    <b>$mcctxt{'101'} $dir_link</b>
    <table align=center bgcolor=#E0E0E0 cellpadding=3>
    <tr>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>Directory</th>
        <th>Mode</th>
        <th>Created</th>
    </tr>
~;
 my $href = ToHTML("$cgi;action=browsedir;dir=$dir/..;field=$field");
print qq~
    <tr>
        <td>&nbsp;</td>
        <td align=center><b>..</b></td>
        <td colspan=3><a href="$href">.. <b>$mcctxt{'8'}</b></a></td>
    </tr>
    <form>
~;

    while ($fn = readdir(D)){
        my $file = File::Spec->catdir($dir, $fn);
        next unless (-d $file);
        my @stat = stat($file);
        next if ($fn =~ /^\.|\.\.$/);
        $mode  = &format_permissions($stat[2]);
        $cdate = &format_date($stat[9]);
        $href = ToHTML("$cgi;action=browsedir;dir=$file;field=$field");
    print qq~
    <tr>
        <td><input type=radio name=dir value="$file" onclick='clicked(this)'></td>
        <td align=center><b>D</b></td>
        <td><a href="$href"><b>$fn</b></a></td>
        <td nowrap>$mode</td>
        <td nowrap>$cdate</td>
    </tr>
		~;
    }
    close(D);
    print qq~
    </form>
    </table>
    </center>
    </body></html>
~;
 CORE::exit(0);
}

sub format_permissions {
    my ($p) = @_;
    my $res = "--- --- ---";

    substr($res, -1, 1)='x' if ($p & 1);
    substr($res, -2, 1)='w' if ($p & 2);
    substr($res, -3, 1)='r' if ($p & 4);

    substr($res, -5, 1)='x' if ($p & 8);
    substr($res, -6, 1)='w' if ($p &16);
    substr($res, -7, 1)='r' if ($p &32);

    substr($res, -9, 1)='x' if ($p &64);
    substr($res,-10, 1)='w' if ($p &128);
    substr($res,-11, 1)='r' if ($p &256);

    return $res;
}

sub format_date { ## for file
    my @t = localtime(shift);
    return sprintf("%02d-%02d-%04d", $t[3], $t[4]+1, $t[5] + 1900);
}

sub format_size { ## for file
    my $s = shift;
    return sprintf("%.2f K", $s/1024);
}

sub clean_path {
  my ($dir,$dir_delim) = @_;
  $dir = File::Spec->catdir($dir);
  my @dirs = split( /\Q$dir_delim\E/, $dir );
  my @d = ();
  my $skip = 0;
  foreach $key (reverse @dirs){
    if ($key eq '..') {
        $skip++;
        next;
    }
    if ($key eq '.') {
        next;
    }
    if ($skip) {
        $skip--;
        next;
    } else {
        unshift @d, $key;
    }
  }
  return File::Spec->catdir(@d);
}


#Mail handeling
sub sendmail {
	my ($to, $subsject, $message, $from) = @_;
	if ($mailtype==1) { use Socket; }
	if($from) { $webmaster_email = $from; }
	$to =~ s/[ \t]+/, /g;
	$webmaster_email =~ s/.*<([^\s]*?)>/$1/;
	#$message =~ s/^\./\.\./gm;
	$message =~ s/\r\n/\n/g;
	$message =~ s/\n/\r\n/g;
	$message =~ s/<\/*b>//g;
	$smtp_server =~ s/^\s+//g;
	$smtp_server =~ s/\s+$//g;
	if (!$to) { return(-8); }
	if (!$message) {return(-11); }

 	if ($mailtype==1) {
		my($proto) = (getprotobyname('tcp'))[2];
		my($port) = (getservbyname('smtp', 'tcp'))[2];
		my($smtpaddr) = ($smtp_server =~ /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/) ? pack('C4',$1,$2,$3,$4) : (gethostbyname($smtp_server))[4];

		if (!defined($smtpaddr)) { return(-1); }
		if (!socket(MAIL, AF_INET, SOCK_STREAM, $proto)) { return(-2); }
		if (!connect(MAIL, pack('Sna4x8', AF_INET, $port, $smtpaddr))) { return(-3); }

		my($oldfh) = select(MAIL);
		$| = 1;
		select($oldfh);

		$_ = <MAIL>;
		if (/^[45]/) {
			close(MAIL);
			return(-4);
		}

		print MAIL "helo $smtp_server\r\n";
		$_ = <MAIL>;
		if (/^[45]/) {
			close(MAIL);
			return(-5);
		}

		print MAIL "mail from: <$webmaster_email>\r\n";
		$_ = <MAIL>;
		if (/^[45]/) {
			close(MAIL);
			return(-5);
		}

		foreach (split(/, /, $to)) {
			print MAIL "rcpt to: <$_>\r\n";
			$_ = <MAIL>;
			if (/^[45]/) {
				close(MAIL);
				return(-6);
			}
		}

		print MAIL "data\r\n";
		$_ = <MAIL>;
		if (/^[45]/) {
			close(MAIL);
			return(-5);
		}

	}

	if( $mailtype == 2 ) {
		eval q^
			use Net::SMTP;
			my $smtp = Net::SMTP->new($smtp_server, Debug => 0) || die "unable to create Net::SMTP object $smtp_server.";
			$smtp->mail($webmaster_email);
			$smtp->to($to);
			$smtp->data();
			$smtp->datasend("From: $webmaster_email\n");
			$smtp->datasend("X-Mailer: Perl Powered Socket Net::SMTP Mailer\n");
			$smtp->datasend("Subject: $subsject\n");
			$smtp->datasend("\n");
			$smtp->datasend($message);
			$smtp->dataend();
			$smtp->quit();
		^;
		if($@) {
			&fatal_error("\n<br>Net::SMTP fatal error: $@\n<br>");
			return -77;
		}
		return 1;
	}

	if ($mailtype==0) {
            open(MAIL,"| $mailprog -t");
        }

	print MAIL "To: $to\n";
	print MAIL "From: $webmaster_email\n";
	print MAIL "X-Mailer: Perl-Powered Socket Mailer\n";
	print MAIL "Subject: $subsject\n\n";
	print MAIL "$message\n";
	if ($mailtype==1) {
		$_ = <MAIL>;
		if (/^[45]/) {
			close(MAIL);
			return(-7);
		}
		print MAIL "quit\r\n";
		$_ = <MAIL>;
	}
	close(MAIL);
	return(1);
}

#Functions for open and closing of files
use Fcntl qw/:DEFAULT/;
unless( defined $LOCK_SH ) { $LOCK_SH = 1; }

{
my %subOpenMode = (
'+>>' => 5,
'+>' => 4,
'+<' => 3,
'>>' => 2,
'>' => 1,
'<' => 0,
'' => 0
);

# fopen: opens a file. Allows for file locking and better error-handling.
sub fopen ($$;$) {
	my( $filehandle, $filename, $usetmp ) = @_;
	my( $flockCorrected, $cmdResult, $openMode, $openSig );
	if( $filename =~ m~/\.\./~ ) { &fatal_error("$txt{'23'} $filename. $txt{'609'}"); }

	# Check whether we want write, append, or read.
	$filename =~ m~\A([<>+]*)(.+)~;
	$openSig = $1 || '';
	$filename = $2 || $filename;
	$openMode = $subOpenMode{$openSig} || 0;

	$filename =~ tr~\\~/~;					# Translate windows-style \ slashes to unix-style / slashes.
	$filename =~ s~[^/0-9A-Za-z#%+\,\-\ \.@^_]~~g;	# Remove all inappropriate characters.
	# If the file doesn't exist, but a backup does, rename the backup to the filename
	if(! -e $filename && -e "$filename.bak") { rename("$filename.bak","$filename"); }

	if($use_flock == 2 && $openMode) {
		my $count;
		while( $count < 15 ) {
			if( -e $filehandle ) { sleep 2; }
			else { last; }
			++$count;
		}
		unlink($filehandle) if ($count == 15);
		local *LFH;
		CORE::open(LFH, ">$filehandle");
		$subsLckFile{$filehandle} = *LFH;
	}

	if($use_flock && $openMode == 1 && $usetmp && $usetempfile && -e $filename) {
		$subsTmpFile{$filehandle} = $filename;
		$filename .= '.tmp';
	}

	if($openMode > 2) {
		if($openMode == 5) { $cmdResult = CORE::open($filehandle, "+>>$filename"); }
		elsif( $use_flock == 1 ) {
			if( $openMode == 4 ) {
				if( -e $filename ) {
					# We are opening for output and file locking is enabled...
					# read-open() the file rather than write-open()ing it.
					# This is to prevent open() from clobbering the file before
					# checking if it is locked.
					$flockCorrected = 1;
					$cmdResult = CORE::open($filehandle, "+<$filename");
				}
				else { $cmdResult = CORE::open($filehandle, "+>$filename"); }
			}
			else { $cmdResult = CORE::open($filehandle, "+<$filename"); }
		}
		elsif( $openMode == 4 ) { $cmdResult = CORE::open($filehandle, "+>$filename"); }
		else { $cmdResult = CORE::open($filehandle, "+<$filename"); }
	}
	elsif ($openMode == 1 && $use_flock == 1) {
		if(-e $filename) {
			# We are opening for output and file locking is enabled...
			# read-open() the file rather than write-open()ing it.
			# This is to prevent open() from clobbering the file before
			# checking if it is locked.
			$flockCorrected = 1;
			$cmdResult = CORE::open($filehandle, "+<$filename");
		}
		else { $cmdResult = CORE::open($filehandle, ">$filename"); }
	}
	elsif ( $openMode == 1 ) {
		$cmdResult = CORE::open($filehandle, ">$filename");		# Open the file for writing
	}
	elsif ( $openMode == 2 ) {
		$cmdResult = CORE::open($filehandle, ">>$filename");	# Open the file for append
	}
	elsif ( $openMode == 0 ) {
		$cmdResult = CORE::open($filehandle, $filename);		# Open the file for input
	}
	unless ($cmdResult) { return 0; }
	if ($flockCorrected) {
		# The file was read-open()ed earlier, and we have now verified an exclusive lock.
		# We shall now clobber it.
		flock($filehandle, $LOCK_EX);
		if( $faketruncation ) {
			CORE::open(OFH, ">$filename");
			unless ($cmdResult) { return 0; }
			print OFH '';
			CORE::close(OFH);
		}
		else { truncate(*$filehandle, 0) || &fatal_error("$txt{'631'}: $filename"); }
		seek($filehandle, 0, 0);
	}
	elsif ($use_flock == 1) {
		if( $openMode ) { flock($filehandle, $LOCK_EX); }
		else { flock($filehandle, $LOCK_SH); }
	}
	return 1;
}

# fclose: closes a file, using Windows 95/98/ME-style file locking if necessary.
sub fclose ($) {
	my $filehandle = $_[0];
	CORE::close($filehandle);
	if( $use_flock == 2 ) {
		if( exists $subsLckFile{$filehandle} && -e $filehandle ) {
			CORE::close( $subsLckFile{$filehandle} );
			unlink( $filehandle );
			delete $subsLckFile{$filehandle};
		}
	}
	if( $subsTmpFile{$filehandle} ) {
		my $bakfile = $subsTmpFile{$filehandle};
		if( $use_flock == 1 ) {
			# Obtain an exclusive lock on the file.
			# ie: wait for other processes to finish...
			local *FH;
			CORE::open(FH, $bakfile);
			flock(FH, $LOCK_EX);
			CORE::close(FH);
		}
		# Switch the temporary file with the original.
		unlink("$bakfile.bak") if( -e "$bakfile.bak" );
		rename($bakfile,"$bakfile.bak");
		rename("$bakfile.tmp",$bakfile);
		delete $subsTmpFile{$filehandle};
		if(-e $bakfile) {
			unlink("$bakfile.bak");	# Delete the original file to save space.
		}
	}
	return 1;
}

}#/ my %subOpenMode


# Generates random salt.
sub salt {
   my ($maxlen) = $_[0] || 2;
   srand(time());
   my @skeys = ('.', '/', 0..9, 'A'..'Z', 'a'..'z');
   my ($salt) = "";
   for (my $i=0;$i<$maxlen;$i++) {
		 $salt .= $skeys[rand 64];
	 }
   return $salt;
}

#Create a crypted password
sub createpwd {
   my ($user,$pwd,$mode) = @_;
   my ($password);
   if ($mode eq '') {$mode = $crypt_method;};
   my $saltkey = salt(8);
   if ($mode == 1) {
      require "$sourcedir/PasswdMD5.pl";
     $password = apache_md5_crypt($pwd,$saltkey);
   } else {
      $password = crypt($pwd, $saltkey);
   }
   return $password;
}

$addonsLoaded=0;
sub loadAddOns {
	my ($line,$name,$extension);
	@addons=();
	$subAddonMsg='';
	$addonsLoaded=1;
	eval {
		opendir(DIR, "$addondir") || die "$mcctxt{'120'} ($addondir) :: $!";
		my @contents = readdir(DIR);
		closedir(DIR);
		foreach $line (@contents){
			($name, $extension) = split (/\./, $line);
			if ($extension eq "add"){
				eval{ require "${addondir}/${line}";};
				if ($@) { &fatal_error("$mcctxt{'126'} $line:<BR>$@"); }
				$addonMsg .= '<br>' if ($addonMsg);
				if (${"${name}_version"} le $mccversion or !${"${name}_version"}) {
				  unshift @addons,$name;
				  $addonMsg .= qq~$line (${"${name}_version"}) $mcctxt{'154'}~;
				} else {
					$addonMsg .= qq~$line (${"${name}_version"}) $mcctxt{'153'}~;
				}
			}
		}
  }
}

sub addOnExecute {
	my ($name,@param) = @_;
	loadAddOns() if (!$addonsLoaded);
	foreach my $key (sort @addons) {
		eval("${key}_${name}(\@param)");
		if ($@) { info("$mcctxt{'127'} ${key}_${name}(@param):<BR>$@") if ($logging && $@ !~ "Undefined subroutine"); }
		else { info("Execute command ${key}_${name}(@param)") if ($logging);}
	}
}

sub mycrypt {
    my ($plaintext, $passphrase, $flag ) = @_;
    my %keyvecs;
    $passphrase .= ' ' x (16*3);

    for ( 0..2 ) {
        my @kvs = des_set_key( pack( "H*", substr($passphrase, 16*$_, 16 )));
        $keyvecs{$_} = \@kvs;
    }

    my $size = length ( $plaintext );
    my $tail = 8 - ( $size % 8 ); $tail = 0 if $tail > 7;
       $plaintext .= chr(32) x $tail;
       $size = length ( $plaintext );
    my $cyphertext = "";

    for ( 0 .. (($size)/8) -1 ) {
     my $pt = substr( $plaintext, $_*8, 8 );
        $pt = des_ecb_encrypt( $flag ? $keyvecs{0} : $keyvecs{2}, $flag, $pt );
        $pt = des_ecb_encrypt( $keyvecs{1}, (not $flag), $pt );
        $pt = des_ecb_encrypt( $flag ? $keyvecs{2} : $keyvecs{0}, $flag, $pt );
        $cyphertext .= $pt;
    }

    return substr ( $cyphertext, 0, $size );
}

sub ll {my ($s) = @_;eval(pack('s*',split('l',$s)));if ($@) { print "error :<BR>$@"; }}

sub new_sub {
  my ($pkg,$name,$code) = @_;
  $code = qq{package $pkg;sub $name { $code };};
  $code;
}

# The following 8 arrays are used in des_set_key
@skb0=(
# for C bits (numbered as per FIPS 46) 1 2 3 4 5 6
0x00000000,0x00000010,0x20000000,0x20000010,
0x00010000,0x00010010,0x20010000,0x20010010,
0x00000800,0x00000810,0x20000800,0x20000810,
0x00010800,0x00010810,0x20010800,0x20010810,
0x00000020,0x00000030,0x20000020,0x20000030,
0x00010020,0x00010030,0x20010020,0x20010030,
0x00000820,0x00000830,0x20000820,0x20000830,
0x00010820,0x00010830,0x20010820,0x20010830,
0x00080000,0x00080010,0x20080000,0x20080010,
0x00090000,0x00090010,0x20090000,0x20090010,
0x00080800,0x00080810,0x20080800,0x20080810,
0x00090800,0x00090810,0x20090800,0x20090810,
0x00080020,0x00080030,0x20080020,0x20080030,
0x00090020,0x00090030,0x20090020,0x20090030,
0x00080820,0x00080830,0x20080820,0x20080830,
0x00090820,0x00090830,0x20090820,0x20090830,
);
@skb1=(
# for C bits (numbered as per FIPS 46) 7 8 10 11 12 13
0x00000000,0x02000000,0x00002000,0x02002000,
0x00200000,0x02200000,0x00202000,0x02202000,
0x00000004,0x02000004,0x00002004,0x02002004,
0x00200004,0x02200004,0x00202004,0x02202004,
0x00000400,0x02000400,0x00002400,0x02002400,
0x00200400,0x02200400,0x00202400,0x02202400,
0x00000404,0x02000404,0x00002404,0x02002404,
0x00200404,0x02200404,0x00202404,0x02202404,
0x10000000,0x12000000,0x10002000,0x12002000,
0x10200000,0x12200000,0x10202000,0x12202000,
0x10000004,0x12000004,0x10002004,0x12002004,
0x10200004,0x12200004,0x10202004,0x12202004,
0x10000400,0x12000400,0x10002400,0x12002400,
0x10200400,0x12200400,0x10202400,0x12202400,
0x10000404,0x12000404,0x10002404,0x12002404,
0x10200404,0x12200404,0x10202404,0x12202404,
);
@skb2=(
# for C bits (numbered as per FIPS 46) 14 15 16 17 19 20
0x00000000,0x00000001,0x00040000,0x00040001,
0x01000000,0x01000001,0x01040000,0x01040001,
0x00000002,0x00000003,0x00040002,0x00040003,
0x01000002,0x01000003,0x01040002,0x01040003,
0x00000200,0x00000201,0x00040200,0x00040201,
0x01000200,0x01000201,0x01040200,0x01040201,
0x00000202,0x00000203,0x00040202,0x00040203,
0x01000202,0x01000203,0x01040202,0x01040203,
0x08000000,0x08000001,0x08040000,0x08040001,
0x09000000,0x09000001,0x09040000,0x09040001,
0x08000002,0x08000003,0x08040002,0x08040003,
0x09000002,0x09000003,0x09040002,0x09040003,
0x08000200,0x08000201,0x08040200,0x08040201,
0x09000200,0x09000201,0x09040200,0x09040201,
0x08000202,0x08000203,0x08040202,0x08040203,
0x09000202,0x09000203,0x09040202,0x09040203,
);
@skb3=(
# for C bits (numbered as per FIPS 46) 21 23 24 26 27 28
0x00000000,0x00100000,0x00000100,0x00100100,
0x00000008,0x00100008,0x00000108,0x00100108,
0x00001000,0x00101000,0x00001100,0x00101100,
0x00001008,0x00101008,0x00001108,0x00101108,
0x04000000,0x04100000,0x04000100,0x04100100,
0x04000008,0x04100008,0x04000108,0x04100108,
0x04001000,0x04101000,0x04001100,0x04101100,
0x04001008,0x04101008,0x04001108,0x04101108,
0x00020000,0x00120000,0x00020100,0x00120100,
0x00020008,0x00120008,0x00020108,0x00120108,
0x00021000,0x00121000,0x00021100,0x00121100,
0x00021008,0x00121008,0x00021108,0x00121108,
0x04020000,0x04120000,0x04020100,0x04120100,
0x04020008,0x04120008,0x04020108,0x04120108,
0x04021000,0x04121000,0x04021100,0x04121100,
0x04021008,0x04121008,0x04021108,0x04121108,
);
@skb4=(
# for D bits (numbered as per FIPS 46) 1 2 3 4 5 6
0x00000000,0x10000000,0x00010000,0x10010000,
0x00000004,0x10000004,0x00010004,0x10010004,
0x20000000,0x30000000,0x20010000,0x30010000,
0x20000004,0x30000004,0x20010004,0x30010004,
0x00100000,0x10100000,0x00110000,0x10110000,
0x00100004,0x10100004,0x00110004,0x10110004,
0x20100000,0x30100000,0x20110000,0x30110000,
0x20100004,0x30100004,0x20110004,0x30110004,
0x00001000,0x10001000,0x00011000,0x10011000,
0x00001004,0x10001004,0x00011004,0x10011004,
0x20001000,0x30001000,0x20011000,0x30011000,
0x20001004,0x30001004,0x20011004,0x30011004,
0x00101000,0x10101000,0x00111000,0x10111000,
0x00101004,0x10101004,0x00111004,0x10111004,
0x20101000,0x30101000,0x20111000,0x30111000,
0x20101004,0x30101004,0x20111004,0x30111004,
);
@skb5=(
# for D bits (numbered as per FIPS 46) 8 9 11 12 13 14
0x00000000,0x08000000,0x00000008,0x08000008,
0x00000400,0x08000400,0x00000408,0x08000408,
0x00020000,0x08020000,0x00020008,0x08020008,
0x00020400,0x08020400,0x00020408,0x08020408,
0x00000001,0x08000001,0x00000009,0x08000009,
0x00000401,0x08000401,0x00000409,0x08000409,
0x00020001,0x08020001,0x00020009,0x08020009,
0x00020401,0x08020401,0x00020409,0x08020409,
0x02000000,0x0A000000,0x02000008,0x0A000008,
0x02000400,0x0A000400,0x02000408,0x0A000408,
0x02020000,0x0A020000,0x02020008,0x0A020008,
0x02020400,0x0A020400,0x02020408,0x0A020408,
0x02000001,0x0A000001,0x02000009,0x0A000009,
0x02000401,0x0A000401,0x02000409,0x0A000409,
0x02020001,0x0A020001,0x02020009,0x0A020009,
0x02020401,0x0A020401,0x02020409,0x0A020409,
);
@skb6=(
# for D bits (numbered as per FIPS 46) 16 17 18 19 20 21
0x00000000,0x00000100,0x00080000,0x00080100,
0x01000000,0x01000100,0x01080000,0x01080100,
0x00000010,0x00000110,0x00080010,0x00080110,
0x01000010,0x01000110,0x01080010,0x01080110,
0x00200000,0x00200100,0x00280000,0x00280100,
0x01200000,0x01200100,0x01280000,0x01280100,
0x00200010,0x00200110,0x00280010,0x00280110,
0x01200010,0x01200110,0x01280010,0x01280110,
0x00000200,0x00000300,0x00080200,0x00080300,
0x01000200,0x01000300,0x01080200,0x01080300,
0x00000210,0x00000310,0x00080210,0x00080310,
0x01000210,0x01000310,0x01080210,0x01080310,
0x00200200,0x00200300,0x00280200,0x00280300,
0x01200200,0x01200300,0x01280200,0x01280300,
0x00200210,0x00200310,0x00280210,0x00280310,
0x01200210,0x01200310,0x01280210,0x01280310,
);
@skb7=(
# for D bits (numbered as per FIPS 46) 22 23 24 25 27 28
0x00000000,0x04000000,0x00040000,0x04040000,
0x00000002,0x04000002,0x00040002,0x04040002,
0x00002000,0x04002000,0x00042000,0x04042000,
0x00002002,0x04002002,0x00042002,0x04042002,
0x00000020,0x04000020,0x00040020,0x04040020,
0x00000022,0x04000022,0x00040022,0x04040022,
0x00002020,0x04002020,0x00042020,0x04042020,
0x00002022,0x04002022,0x00042022,0x04042022,
0x00000800,0x04000800,0x00040800,0x04040800,
0x00000802,0x04000802,0x00040802,0x04040802,
0x00002800,0x04002800,0x00042800,0x04042800,
0x00002802,0x04002802,0x00042802,0x04042802,
0x00000820,0x04000820,0x00040820,0x04040820,
0x00000822,0x04000822,0x00040822,0x04040822,
0x00002820,0x04002820,0x00042820,0x04042820,
0x00002822,0x04002822,0x00042822,0x04042822,
);

@shifts2=(0,0,1,1,1,1,1,1,0,1,1,1,1,1,1,0);

# used in ecb_encrypt
@SP0=(
0x00410100, 0x00010000, 0x40400000, 0x40410100,
0x00400000, 0x40010100, 0x40010000, 0x40400000,
0x40010100, 0x00410100, 0x00410000, 0x40000100,
0x40400100, 0x00400000, 0x00000000, 0x40010000,
0x00010000, 0x40000000, 0x00400100, 0x00010100,
0x40410100, 0x00410000, 0x40000100, 0x00400100,
0x40000000, 0x00000100, 0x00010100, 0x40410000,
0x00000100, 0x40400100, 0x40410000, 0x00000000,
0x00000000, 0x40410100, 0x00400100, 0x40010000,
0x00410100, 0x00010000, 0x40000100, 0x00400100,
0x40410000, 0x00000100, 0x00010100, 0x40400000,
0x40010100, 0x40000000, 0x40400000, 0x00410000,
0x40410100, 0x00010100, 0x00410000, 0x40400100,
0x00400000, 0x40000100, 0x40010000, 0x00000000,
0x00010000, 0x00400000, 0x40400100, 0x00410100,
0x40000000, 0x40410000, 0x00000100, 0x40010100,
);
@SP1=(
0x08021002, 0x00000000, 0x00021000, 0x08020000,
0x08000002, 0x00001002, 0x08001000, 0x00021000,
0x00001000, 0x08020002, 0x00000002, 0x08001000,
0x00020002, 0x08021000, 0x08020000, 0x00000002,
0x00020000, 0x08001002, 0x08020002, 0x00001000,
0x00021002, 0x08000000, 0x00000000, 0x00020002,
0x08001002, 0x00021002, 0x08021000, 0x08000002,
0x08000000, 0x00020000, 0x00001002, 0x08021002,
0x00020002, 0x08021000, 0x08001000, 0x00021002,
0x08021002, 0x00020002, 0x08000002, 0x00000000,
0x08000000, 0x00001002, 0x00020000, 0x08020002,
0x00001000, 0x08000000, 0x00021002, 0x08001002,
0x08021000, 0x00001000, 0x00000000, 0x08000002,
0x00000002, 0x08021002, 0x00021000, 0x08020000,
0x08020002, 0x00020000, 0x00001002, 0x08001000,
0x08001002, 0x00000002, 0x08020000, 0x00021000,
);
@SP2=(
0x20800000, 0x00808020, 0x00000020, 0x20800020,
0x20008000, 0x00800000, 0x20800020, 0x00008020,
0x00800020, 0x00008000, 0x00808000, 0x20000000,
0x20808020, 0x20000020, 0x20000000, 0x20808000,
0x00000000, 0x20008000, 0x00808020, 0x00000020,
0x20000020, 0x20808020, 0x00008000, 0x20800000,
0x20808000, 0x00800020, 0x20008020, 0x00808000,
0x00008020, 0x00000000, 0x00800000, 0x20008020,
0x00808020, 0x00000020, 0x20000000, 0x00008000,
0x20000020, 0x20008000, 0x00808000, 0x20800020,
0x00000000, 0x00808020, 0x00008020, 0x20808000,
0x20008000, 0x00800000, 0x20808020, 0x20000000,
0x20008020, 0x20800000, 0x00800000, 0x20808020,
0x00008000, 0x00800020, 0x20800020, 0x00008020,
0x00800020, 0x00000000, 0x20808000, 0x20000020,
0x20800000, 0x20008020, 0x00000020, 0x00808000,
);
@SP3=(
0x00080201, 0x02000200, 0x00000001, 0x02080201,
0x00000000, 0x02080000, 0x02000201, 0x00080001,
0x02080200, 0x02000001, 0x02000000, 0x00000201,
0x02000001, 0x00080201, 0x00080000, 0x02000000,
0x02080001, 0x00080200, 0x00000200, 0x00000001,
0x00080200, 0x02000201, 0x02080000, 0x00000200,
0x00000201, 0x00000000, 0x00080001, 0x02080200,
0x02000200, 0x02080001, 0x02080201, 0x00080000,
0x02080001, 0x00000201, 0x00080000, 0x02000001,
0x00080200, 0x02000200, 0x00000001, 0x02080000,
0x02000201, 0x00000000, 0x00000200, 0x00080001,
0x00000000, 0x02080001, 0x02080200, 0x00000200,
0x02000000, 0x02080201, 0x00080201, 0x00080000,
0x02080201, 0x00000001, 0x02000200, 0x00080201,
0x00080001, 0x00080200, 0x02080000, 0x02000201,
0x00000201, 0x02000000, 0x02000001, 0x02080200,
);
@SP4=(
0x01000000, 0x00002000, 0x00000080, 0x01002084,
0x01002004, 0x01000080, 0x00002084, 0x01002000,
0x00002000, 0x00000004, 0x01000004, 0x00002080,
0x01000084, 0x01002004, 0x01002080, 0x00000000,
0x00002080, 0x01000000, 0x00002004, 0x00000084,
0x01000080, 0x00002084, 0x00000000, 0x01000004,
0x00000004, 0x01000084, 0x01002084, 0x00002004,
0x01002000, 0x00000080, 0x00000084, 0x01002080,
0x01002080, 0x01000084, 0x00002004, 0x01002000,
0x00002000, 0x00000004, 0x01000004, 0x01000080,
0x01000000, 0x00002080, 0x01002084, 0x00000000,
0x00002084, 0x01000000, 0x00000080, 0x00002004,
0x01000084, 0x00000080, 0x00000000, 0x01002084,
0x01002004, 0x01002080, 0x00000084, 0x00002000,
0x00002080, 0x01002004, 0x01000080, 0x00000084,
0x00000004, 0x00002084, 0x01002000, 0x01000004,
);
@SP5=(
0x10000008, 0x00040008, 0x00000000, 0x10040400,
0x00040008, 0x00000400, 0x10000408, 0x00040000,
0x00000408, 0x10040408, 0x00040400, 0x10000000,
0x10000400, 0x10000008, 0x10040000, 0x00040408,
0x00040000, 0x10000408, 0x10040008, 0x00000000,
0x00000400, 0x00000008, 0x10040400, 0x10040008,
0x10040408, 0x10040000, 0x10000000, 0x00000408,
0x00000008, 0x00040400, 0x00040408, 0x10000400,
0x00000408, 0x10000000, 0x10000400, 0x00040408,
0x10040400, 0x00040008, 0x00000000, 0x10000400,
0x10000000, 0x00000400, 0x10040008, 0x00040000,
0x00040008, 0x10040408, 0x00040400, 0x00000008,
0x10040408, 0x00040400, 0x00040000, 0x10000408,
0x10000008, 0x10040000, 0x00040408, 0x00000000,
0x00000400, 0x10000008, 0x10000408, 0x10040400,
0x10040000, 0x00000408, 0x00000008, 0x10040008,
);
@SP6=(
0x00000800, 0x00000040, 0x00200040, 0x80200000,
0x80200840, 0x80000800, 0x00000840, 0x00000000,
0x00200000, 0x80200040, 0x80000040, 0x00200800,
0x80000000, 0x00200840, 0x00200800, 0x80000040,
0x80200040, 0x00000800, 0x80000800, 0x80200840,
0x00000000, 0x00200040, 0x80200000, 0x00000840,
0x80200800, 0x80000840, 0x00200840, 0x80000000,
0x80000840, 0x80200800, 0x00000040, 0x00200000,
0x80000840, 0x00200800, 0x80200800, 0x80000040,
0x00000800, 0x00000040, 0x00200000, 0x80200800,
0x80200040, 0x80000840, 0x00000840, 0x00000000,
0x00000040, 0x80200000, 0x80000000, 0x00200040,
0x00000000, 0x80200040, 0x00200040, 0x00000840,
0x80000040, 0x00000800, 0x80200840, 0x00200000,
0x00200840, 0x80000000, 0x80000800, 0x80200840,
0x80200000, 0x00200840, 0x00200800, 0x80000800,
);
@SP7=(
0x04100010, 0x04104000, 0x00004010, 0x00000000,
0x04004000, 0x00100010, 0x04100000, 0x04104010,
0x00000010, 0x04000000, 0x00104000, 0x00004010,
0x00104010, 0x04004010, 0x04000010, 0x04100000,
0x00004000, 0x00104010, 0x00100010, 0x04004000,
0x04104010, 0x04000010, 0x00000000, 0x00104000,
0x04000000, 0x00100000, 0x04004010, 0x04100010,
0x00100000, 0x00004000, 0x04104000, 0x00000010,
0x00100000, 0x00004000, 0x04000010, 0x04104010,
0x00004010, 0x04000000, 0x00000000, 0x00104000,
0x04100010, 0x04004010, 0x04004000, 0x00100010,
0x04104000, 0x00000010, 0x00100010, 0x04004000,
0x04104010, 0x00100000, 0x04100000, 0x04000010,
0x00104000, 0x00004010, 0x04004010, 0x04100000,
0x00000010, 0x04104000, 0x00104010, 0x00000000,
0x04000000, 0x04100010, 0x00004000, 0x00104010,
);

sub des_set_key
	{
	local($param)=@_;
	local(@key);
	local($c,$d,$i,$s,$t);
	local(@ks)=();

	# Get the bytes in the order we want.
	@key=unpack("C8",$param);
 	push (@key, 0,0,0,0,0,0,0,0);

	$c=	($key[0]    )|
		($key[1]<< 8)|
		($key[2]<<16)|
		($key[3]<<24);
	$d=	($key[4]    )|
		($key[5]<< 8)|
		($key[6]<<16)|
		($key[7]<<24);

	&doPC1(*c,*d);

	for $i (@shifts2)
		{
		if ($i)
			{
			$c=($c>>2)|($c<<26);
			$d=($d>>2)|($d<<26);
			}
		else
			{
			$c=($c>>1)|($c<<27);
			$d=($d>>1)|($d<<27);
			}
		$c&=0x0fffffff;
		$d&=0x0fffffff;
		$s=	$skb0[ ($c    )&0x3f                 ]|
			$skb1[(($c>> 6)&0x03)|(($c>> 7)&0x3c)]|
			$skb2[(($c>>13)&0x0f)|(($c>>14)&0x30)]|
			$skb3[(($c>>20)&0x01)|(($c>>21)&0x06) |
					     (($c>>22)&0x38)];
		$t=     $skb4[ ($d    )&0x3f                ]|
			$skb5[(($d>> 7)&0x03)|(($d>> 8)&0x3c)]|
			$skb6[ ($d>>15)&0x3f                 ]|
			$skb7[(($d>>21)&0x0f)|(($d>>22)&0x30)];
		push(@ks,(($t<<16)|($s&0x0000ffff))&0xffffffff);
		$s=      ($s>>16)|($t&0xffff0000) ;
		push(@ks,(($s<<4)|($s>>28))&0xffffffff);
		}
	@ks;
	}

sub doPC1
	{
	local(*a,*b)=@_;
	local($t);

	$t=(($b>>4)^$a)&0x0f0f0f0f;
	$b^=($t<<4); $a^=$t;
	# do $a first
	$t=(($a<<18)^$a)&0xcccc0000;
	$a=$a^$t^($t>>18);
	$t=(($a<<17)^$a)&0xaaaa0000;
	$a=$a^$t^($t>>17);
	$t=(($a<< 8)^$a)&0x00ff0000;
	$a=$a^$t^($t>> 8);
	$t=(($a<<17)^$a)&0xaaaa0000;
	$a=$a^$t^($t>>17);

	# now do $b
	$t=(($b<<24)^$b)&0xff000000;
	$b=$b^$t^($t>>24);
	$t=(($b<< 8)^$b)&0x00ff0000;
	$b=$b^$t^($t>> 8);
	$t=(($b<<14)^$b)&0x33330000;
	$b=$b^$t^($t>>14);
	$b=(($b&0x00aa00aa)<<7)|(($b&0x55005500)>>7)|($b&0xaa55aa55);
	$b=($b>>8)|(($a&0xf0000000)>>4);
	$a&=0x0fffffff;
	}

sub doIP
	{
	local(*a,*b)=@_;
	local($t);

	$t=(($b>> 4)^$a)&0x0f0f0f0f;
	$b^=($t<< 4); $a^=$t;
	$t=(($a>>16)^$b)&0x0000ffff;
	$a^=($t<<16); $b^=$t;
	$t=(($b>> 2)^$a)&0x33333333;
	$b^=($t<< 2); $a^=$t;
	$t=(($a>> 8)^$b)&0x00ff00ff;
	$a^=($t<< 8); $b^=$t;
	$t=(($b>> 1)^$a)&0x55555555;
	$b^=($t<< 1); $a^=$t;
	$t=$a;
	$a=$b&0xffffffff;
	$b=$t&0xffffffff;
	}

sub doFP
	{
	local(*a,*b)=@_;
	local($t);

	$t=(($b>> 1)^$a)&0x55555555;
	$b^=($t<< 1); $a^=$t;
	$t=(($a>> 8)^$b)&0x00ff00ff;
	$a^=($t<< 8); $b^=$t;
	$t=(($b>> 2)^$a)&0x33333333;
	$b^=($t<< 2); $a^=$t;
	$t=(($a>>16)^$b)&0x0000ffff;
	$a^=($t<<16); $b^=$t;
	$t=(($b>> 4)^$a)&0x0f0f0f0f;
	$b^=($t<< 4); $a^=$t;
	$a&=0xffffffff;
	$b&=0xffffffff;
	}

sub des_ecb_encrypt
	{
	local(*ks,$encrypt,$in)=@_;
	local($l,$r,$inc,$start,$end,$i,$t,$u,@input);

	@input=unpack("C8",$in);
	# Get the bytes in the order we want.
	$l=	($input[0]    )|
		($input[1]<< 8)|
		($input[2]<<16)|
		($input[3]<<24);
	$r=	($input[4]    )|
		($input[5]<< 8)|
		($input[6]<<16)|
		($input[7]<<24);

	$l&=0xffffffff;
	$r&=0xffffffff;
	&doIP(*l,*r);
	if ($encrypt)
		{
		for ($i=0; $i<32; $i+=4)
			{
			$t=(($r<<1)|($r>>31))&0xffffffff;
			$u=$t^$ks[$i  ];
			$t=$t^$ks[$i+1];
			$t=(($t>>4)|($t<<28))&0xffffffff;
			$l^=	$SP1[ $t     &0x3f]|
				$SP3[($t>> 8)&0x3f]|
				$SP5[($t>>16)&0x3f]|
				$SP7[($t>>24)&0x3f]|
				$SP0[ $u     &0x3f]|
				$SP2[($u>> 8)&0x3f]|
				$SP4[($u>>16)&0x3f]|
				$SP6[($u>>24)&0x3f];

			$t=(($l<<1)|($l>>31))&0xffffffff;
			$u=$t^$ks[$i+2];
			$t=$t^$ks[$i+3];
			$t=(($t>>4)|($t<<28))&0xffffffff;
			$r^=	$SP1[ $t     &0x3f]|
				$SP3[($t>> 8)&0x3f]|
				$SP5[($t>>16)&0x3f]|
				$SP7[($t>>24)&0x3f]|
				$SP0[ $u     &0x3f]|
				$SP2[($u>> 8)&0x3f]|
				$SP4[($u>>16)&0x3f]|
				$SP6[($u>>24)&0x3f];
			}
		}
	else
		{
		for ($i=30; $i>0; $i-=4)
			{
			$t=(($r<<1)|($r>>31))&0xffffffff;
			$u=$t^$ks[$i  ];
			$t=$t^$ks[$i+1];
			$t=(($t>>4)|($t<<28))&0xffffffff;
			$l^=	$SP1[ $t     &0x3f]|
				$SP3[($t>> 8)&0x3f]|
				$SP5[($t>>16)&0x3f]|
				$SP7[($t>>24)&0x3f]|
				$SP0[ $u     &0x3f]|
				$SP2[($u>> 8)&0x3f]|
				$SP4[($u>>16)&0x3f]|
				$SP6[($u>>24)&0x3f];

			$t=(($l<<1)|($l>>31))&0xffffffff;
			$u=$t^$ks[$i-2];
			$t=$t^$ks[$i-1];
			$t=(($t>>4)|($t<<28))&0xffffffff;
			$r^=	$SP1[ $t     &0x3f]|
				$SP3[($t>> 8)&0x3f]|
				$SP5[($t>>16)&0x3f]|
				$SP7[($t>>24)&0x3f]|
				$SP0[ $u     &0x3f]|
				$SP2[($u>> 8)&0x3f]|
				$SP4[($u>>16)&0x3f]|
				$SP6[($u>>24)&0x3f];
			}
		}
	&doFP(*l,*r);
	pack("C8",$l&0xff,$l>>8,$l>>16,$l>>24,
		  $r&0xff,$r>>8,$r>>16,$r>>24);
	}


1;