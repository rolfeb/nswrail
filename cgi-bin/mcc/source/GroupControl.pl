#GroupControl
#A group consists of
#0=Name,1=Active,2=FullName,3=Description,4=Members
%groups=();
$groupsChanged=-1;

sub load_groups {
	if ($groupsChanged==-1) {
		%groups=();
		if (fopen(groupSH,"$groupsFile")) {
			my @l_groups = <groupSH>;
			fclose(groupSH);
			foreach my $line (@l_groups) {
				chomp($line);
				my @record = split(/\|/, $line);
				$groups{$record[0]}=$line;
			}
		} else {
			info("$mcctxt{'1'} $groupsFile $mcctxt{'4'} $!")if ($logging);
		}
		$groupsChanged=0;
	}
}

sub save_groups {
	if ($groupsChanged>0) {
		if (fopen(groupSH,">$groupsFile")) {
			foreach my $group (keys %groups) {
				print groupSH "$groups{$group}\n";
			}
			fclose(groupSH);
		} else {
			info("$mcctxt{'2'} $groupsFile $mcctxt{'4'} $!")if ($logging);
		}
		$groupsChanged=0;
	}
}

#Remove a group
sub remove_group{
	my ($key) = @_;
	my ($found,@record) = find_group($key);
	if ($found) {
  	addOnExecute("Group",3,$key);
  	delete $groups{$key};
  	$groupsChanged++;
  	$passwordChanged++;
	}
}

#Find a group
sub find_group{
	my ($group) = @_;
	my @record=();
	if (exists $groups{$group}) {
		@record = split(/\|/, $groups{$group});
		return (1,@record);
	} else {
			info("$mcctxt{'3'} $group")if ($logging);
	}
	return (0,@record);
}

#Update a group
sub update_group {
	my (@record) = @_;
	my ($found,@oldrecord) = find_group($record[0]);
	if ($found) {
	  $groups{$record[0]} =  join( '|' , @record);
	  $groupsChanged++;
	  $passwordChanged++ ;
    addOnExecute("Groups",2,$record[0]);
	} else {
		$groups{$record[0]} =  join( '|' , @record);
		$groupsChanged++;
		$passwordChanged++;
    addOnExecute("Groups",1,$record[0]);
	}
}

sub reload_groups {
	$groupsChanged=-1;
	load_groups();
}

sub get_group_keys {
	return keys %groups;
}

sub get_group_sort_keys {
	my ($asc,@columns)= @_;
	my @sortlist = (@columns , 0);
	my @list = sort {
		my @ra = get_group_info($a);
		my @rb = get_group_info($b);
		for (my $i=0;$i<@sortlist;$i++) {
			if ($asc) {
			 return (1) if ($ra[$sortlist[$i]] gt $rb[$sortlist[$i]]);
			 return (-1) if ($ra[$sortlist[$i]] lt $rb[$sortlist[$i]]);
		  }  else {
			 return (-1) if ($ra[$sortlist[$i]] gt $rb[$sortlist[$i]]);
			 return (1) if ($ra[$sortlist[$i]] lt $rb[$sortlist[$i]]);
			}
		}
		return (0);
	 } keys %groups;
	 return @list;
}

1;