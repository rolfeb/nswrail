#MemberControl
#A member record contains at least the following information
#0=MemberName,1=dbPassword,2=htPassword,3=Active,4=ActivationCode,5=Last-Visit,
#6=AreaRequest,7=Admin,8=RealName,9=E-Mail

%members=();
$membersChanged=-1;

#Load Members to Hash
sub load_members {
	if ($membersChanged==-1) {
		%members=();
		if (fopen(MEMBERSH,"$membersFile")) {
			my @l_members = <MEMBERSH>;
			fclose(MEMBERSH);
			foreach my $line (@l_members) {
				chomp($line);
				my @record = split(/\|/, $line);
				$members{$record[0]}=$line;
			}
		} else {
			info("$mcctxt{'1'} $membersFile $mcctxt{'4'} $!")if ($logging);
			#Add default admin
			set_member_info("admin","j2use","","","",1,"Administrator", '');
		}
		$membersChanged=0;
	}
}

#Save Members from Hash
sub save_members {
	if ($membersChanged>0) {
		if (fopen(MEMBERSH,">$membersFile")) {
			foreach my $member (keys %members) {
				print MEMBERSH "$members{$member}\n";
			}
			fclose(MEMBERSH);
		} else {
				info("$mcctxt{'2'} $membersFile $mcctxt{'4'} $!")if ($logging);
		}
		$membersChanged=0;
	}
}

#Remove a member
sub remove_member{
	my ($key) = @_;
	if (exists $members{$key}) {
  	addOnExecute("Members",3,$key);
  	delete $members{$key};
  	$membersChanged++;
  	$passwordChanged++;
	}
}

sub find_member{
	my ($member) = @_;
	my @record=();
	if (exists $members{$member}) {                           #Validate if allready there
		@record = split(/\|/, $members{$member});
		return (1,@record);
	} else {
			info("$mcctxt{'3'} $member")if ($logging);
	}
	return (0,@record);
}

sub update_member {
	my (@record) = @_;
	my ($found,@oldrecord) = find_member($record[0]);
	if ($found) {
	  $members{$record[0]} =  join( '|' , @record);
	  $membersChanged++;
	  $passwordChanged++ if ($record[2]!=$oldrecord[2] or $record[3]!=$oldrecord[3]);
    addOnExecute("Members",2,$record[0]);
	} else {
		$members{$record[0]} =  join( '|' , @record);
		$membersChanged++;
		$passwordChanged++;
    addOnExecute("Members",1,$record[0]);
	}
}

#Called when member file should be reloaded
sub reload_members {
	$membersChanged=-1;
	load_members();
}

#Return all keys
sub get_member_keys{
	return (keys %members);
}

#Return a sorted set of keys
sub get_member_sort_keys {
	my ($asc,@columns) =@_;
	my @memberkeys = (keys %members);
	my $listref = manual_member_sort_keys($asc,\@columns,\@memberkeys);
  return (@$listref);
}

1;