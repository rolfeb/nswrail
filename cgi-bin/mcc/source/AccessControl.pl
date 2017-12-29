#Access Control
%authorized_groups=();
%authorized_areas=();
$authorized_loaded=0;
$authorized_member='';

#Validate if a member is authorized
sub is_authorized {
	my ($member,$area) = @_;
	$authorized_loaded=0 if ($authorized_member ne $member);
	if ($authorized_loaded==0) {
		$authorized_member = $member;
		%authorized_groups=();
    %authorized_areas=();
		my ($found,@memberRecord) = find_member($member);
		if ($found and $memberRecord[3]==1) {
			#Find all groups where user belongs to
			foreach my $key (get_group_keys()) {
				my @record = get_group_info($key); #Allways exits no checking
				#Check if members is in record and record is active
				if ($record[1] and includes($member,$record[4])!=-1) {
					$authorized_groups{$key}=@record;
				}
			}
			foreach my $key (get_area_keys()) {
				my @record = get_area_info($key); #Allways exits no checking
				#Check if members is in record
				if (includes($member,$record[7])!=-1) {
					$authorized_areas{$key}=@record;
				} else {
					my %includes = include_index($record[6]);
					foreach my $gKey (%authorized_groups) {
						if (exists $includes{$gKey}) {
							$authorized_areas{$key}=@record;
							last;
						}
					}
				}
			}
		}
		$authorized_loaded = 1;
	}
	if ($area) {
		return 1 if (exists $authorized_areas{$area});
  }
  return 0;
}

sub update_all_member_password {
	foreach my $member (get_member_keys()){
		regenerate_password($member);
  }
  commit_database();
  create_htpasswd();
}

#Created the password file(s) for all members that are active
sub create_htpasswd {
    # Opens password file, locks it, writes to it, closes it.  Adds user to password file basically.
    $mccdir= GetDirPath() || $rootdir;
    $mccsourcedir = $sourcedir;
    $mccsourcedir =~ s~$mccdir~~;
    $mccsourcedir = $mccsourcedir eq '' ? $mccdir : "$mccdir/$mccsourcedir";
    foreach  my $area (get_area_keys()) {
			my @dir = get_area_info($area);

			if ($dir[13] ne '1') {
				fopen (HTACCESS, ">$dir[2]/.htaccess") || warn "$mcctxt{'9'} ($dir[2]) $!";
				if (fopen(FILE,"$datadir/htatemplate.txt")) {
					my ($msg,$line);
					$areaname = $dir[4];
					$areadir = $dir[2];
					$areaid = $dir[0];
					while($line = <FILE>) {
						$line =~ s~<field\s+(\w+)>~${"$1"}~g;
						$msg .= qq~$line~;
					}
					fclose(FILE);
					print HTACCESS "$msg";
				} else {
					my $line = qq~AuthType ~;
					$line .= qq~Basic ~;
					$line .= qq~\nAuthName "$dir[4]"\nAuthUserFile $dir[2]/.htpasswd\nrequire valid-user\n ~;
					print HTACCESS "$line";
				}
				fclose(HTACCESS);
				if ($crypt_method==2) {
					#Apache Mod_perl w don't do anything but remove old pwd file
					unlink "$dir[2]/.htpasswd";
				} else {
					#Normal methods
					fopen (HTPASSWD, ">$dir[2]/.htpasswd") || warn "$mcctxt{'9'} ($dir[2]) $!";
					if ($dir[1]==1) {
						foreach my $member (get_member_keys()) {
							if (is_authorized($member,$area)==1) {
								info("Adding member $member");
								my @record = get_member_info($member);
								my ($pwd);
								if ($record[3] == 1) {
									print HTPASSWD "$record[0]:$record[2]\n";
								}
							}
						}
					}
					fclose (HTPASSWD);
				}
			}
		}
		$passwordChanged=0;
}

sub load_members_from_file {
	my ($file) = @_;
	my @lines =<$file>;
	my @defgroups = split(/\+/, $defaultgroups);
	#file lookslike
	#Name|PWD|Full Name|E-Mail|Active|Admin|Group|Area|@flexfields
  foreach my $line (@lines) {
		chomp($line);
		if (substr($line,0,1) ne '#') {
			info("Importing line:$line") if ($logging);
			my @record = split(/\|/, $line);
			my @flex = ();
			for ($i=8;$i<@record;$i++) { @flex = (@flex,$record[$i]); }
			set_member_info($record[0],$record[1],"","","","$record[5]",$record[2],$record[3],@flex);
			set_member_state($record[0],$record[4]);
			my @grp = (split(/\+/, $record[6]),@defgroups);
			foreach my $key (@grp) {
			  add_group_members($key,$record[0]);
		  }
		  @grp = split(/\+/, $record[7]);
		  foreach $key (@grp) {
		 	 add_area_members($key,$record[0]);
		  }
 	  }
  }
  commit_database();
	create_htpasswd();
}

sub remove_invalid_data {
	#Remove all invaldidte users and groups form area and groups

}


1;