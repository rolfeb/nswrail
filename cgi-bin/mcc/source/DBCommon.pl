#Common Functions used to retieve data from Member, Group or Area records

$passwordChanged=0;

############## DEPRECIATED DB COMMON FUNCTIONS ############################
#These functions should not longer be used,

#Return the area record
sub get_area_info {
	my  ($area) = @_;
	my ($found,@record) = find_area($area);
	return @record;
}

# Return member info
sub get_member_info {
	my ($member) = @_;
	my ($found,@record) = find_member($member);
	return @record;
}

# Return group info
sub get_group_info {
	my ($group) = @_;
	my ($found,@record) = find_group($group);
	return @record;
}

#Return a sorted set of keys
sub manual_member_sort_keys {
	my ($asc,$columnsref,$memberkeysref) =@_;
	my @sortlist = (@$columnsref , 0);
	#performacetuning
	my @list =();
	if ($sortlist[0]==0) {
		@list = (sort @$memberkeysref);
		return (\@list);
	}
	@list = sort {
		my @ra = get_member_info($a);
		my @rb = get_member_info($b);
		for (my $i=0;$i<@sortlist;$i++) {
			#Special sort date or count
			if (abs($sortlist[$i])==5) {
			  my ($adate,$acount,$alastdate) = split(/ - /,$ra[5]);
			  my ($bdate,$bcount,$blastdate) = split(/ - /,$rb[5]);
			  my ($va) = $sortlist[$i]>0 ? get_date_value($adate) : $acount;
			  my ($vb) = $sortlist[$i]>0 ? get_date_value($bdate) : $bcount;
			  #We reverse sort so normal last is first
				if (!$asc) {
					 return (1) if ($va > $vb);
					 return (-1) if ($va < $vb);
					}  else {
					 return (-1) if ($va > $vb);
					 return (1) if ($va < $vb);
 				}
			}
			#Normal sort
			if ($asc) {
			 return (1) if ($ra[$sortlist[$i]] gt $rb[$sortlist[$i]]);
			 return (-1) if ($ra[$sortlist[$i]] lt $rb[$sortlist[$i]]);
		  }  else {
			 return (-1) if ($ra[$sortlist[$i]] gt $rb[$sortlist[$i]]);
			 return (1) if ($ra[$sortlist[$i]] lt $rb[$sortlist[$i]]);
			}
		}
		return (0);
	 } @$memberkeysref;
	 return (\@list);
}


############## GROUP COMMON FUNCTIONS ############################

sub set_group_info {
	my  ($group,@others) = @_;
	my ($found,@record) = find_group($group);
	if ($found) {
		update_group($record[0], $record[1], @others);
	} else {
		update_group($group,1, @others);
	}
}

sub set_group_state {
	my ($group,$state)= @_;
	my ($found,@record) = find_group($group);
	if ($found && $record[1] != $state) {
		$record[1]= $state;
		update_group(@record);
	}
}

sub delete_groups {
	my (@groupList) = @_;
	foreach my $group (@groupList) {
		remove_group($group);
	}
}

sub add_group_members {
	my ($group,@memberlist) = @_;
	my ($found,@record) = find_group($group);
	if ($found) {
	  my %includes = include_index($record[4]);
 		foreach my $key (@memberlist) {
			if (!exists $includes{$key}) {
				$includes{$key}=1;
	      $passwordChanged++;
  	  }
	  }
    $record[4] = join( '+' , keys %includes);
    update_group(@record);
  }
}

sub remove_member_from_group {
	my ($member) = @_;
	foreach my $key (keys %groups) {
		remove_group_members($key,$member);
  }
}

sub remove_group_members {
	my ($group,@memberlist) = @_;
	my ($found,@record) = find_group($group);
	if ($found) {
	  my %includes = include_index($record[4]);
 		foreach my $key (@memberlist) {
			if (exists $includes{$key}) {
				delete $includes{$key};
	      $passwordChanged++;
		  }
	  }
    $record[4] = join( '+' , keys %includes);
    update_group(@record);
  }
}

############## AREA COMMON FUNCTIONS ############################

sub set_area_info {
	my  ($area,@others) = @_;
	my ($found,@record) = find_area($area);
	if ($found) {
		update_area($record[0], $record[1], @others);
	} else {
		update_area($area,1, @others);
	}
}

sub set_area_state {
	my ($area,$state)= @_;
	my ($found,@record) = find_area($area);
	if ($found && $record[1] != $state) {
		$record[1]= $state;
		update_area(@record);
	}
}

sub delete_areas {
	my (@areaList) = @_;
	foreach my $area (@areaList) {
		remove_area($area);
	}
}

sub add_area_members {
	my ($area,@memberlist) = @_;
	my ($found,@record) = find_area($area);
	if ($found) {
	  my %includes = include_index($record[7]);
 		foreach my $key (@memberlist) {
			if (!exists $includes{$key}) {
				$includes{$key}=1;
        $passwordChanged++;
		  }
	  }
    $record[7] = join( '+' , keys %includes);
    update_area(@record);
  }
}

sub remove_area_members {
	my ($area,@memberlist) = @_;
	my ($found,@record) = find_area($area);
	if ($found) {
    my %includes = include_index($record[7]);
 		foreach my $key (@memberlist) {
			if (exists $includes{$key}) {
				delete $includes{$key};
        $passwordChanged++;
		  }
	  }
    $record[7] = join( '+' , keys %includes);
    update_area(@record);
  }
}

sub add_area_groups {
	my ($area,@grouplist) = @_;
	my ($found,@record) = find_area($area);
	if ($found) {
    my %includes = include_index($record[6]);
 		foreach my $key (@grouplist) {
			if (!exists $includes{$key}) {
				$includes{$key}=1;
        $passwordChanged++;
	    }
	  }
    $record[6] = join( '+' , keys %includes);
    update_area(@record);
  }
}

############## MEMBER COMMON FUNCTIONS ############################

#set the member to 1 if allowed to do all
sub set_admin_site {
	my ($member,$onoff)= @_;
	my ($found,@record) = find_member($member);
	if ($found) {
			$record[7] = $onoff;
			update_member(@record);
	}
}

#Validate member password
sub validate_member {
  my ($member,$password,$iscrypted) = @_;
  my ($found,@record) = find_member($member);
	if ($found) {
		my $pwd =($iscrypted==1) ? crypt($record[1],substr($password,0,2)): $record[1];
		return @record if ($password eq $pwd);
		info("$mcctxt{'5'} $member") if ($logging);
  }
  return ();
}



#Change the password of a member
sub regenerate_password {
	my ($member) = @_;
	my ($found,@record) = find_member($member);
	if ($found) {
		$record[2]= createpwd($record[0],$record[1]);
		update_member(@record);
  }
}

#Change the password of a member
sub change_password {
	my ($member,$oldpwd,$newpwd1,$newpwd2,$allways) = @_;
	my ($found,@record) = find_member($member);
	if ($found) {
		if ($allways==1 or ($newpwd1 eq $newpwd2 and $oldpwd eq $record[1])) {
			$record[1]= $newpwd1;
			$record[2]= createpwd($record[0],$newpwd1);
			update_member(@record);
			$passwordChanged++;
		}
	}
}

#Maintain the member information
sub set_member_info {
	my  ($member,$password,@others) = @_;
	my ($found,@record) = find_member($member);
	if ($found) {
		update_member($record[0], $record[1], $record[2], $record[3], @others);
	} else {
		$others[4]=$member if ($others[4] eq '');
		$others[0]= salt(8) if ($defaultmemberstate==0);
		update_member($member,$password,createpwd($member,$password),
																		$defaultmemberstate, @others);
	}
}

#Maintain the memer information
sub set_profile_info {
	my  ($member,@others) = @_;
	my ($found,@record) = find_member($member);
	update_member($record[0], $record[1], $record[2], $record[3],
							 $record[4], $record[5], $record[6], $record[7], @others) if ($found);
}

#Set member state
sub set_member_state {
	my ($member,$state)= @_;
	my ($found,@record) = find_member($member);
	if ($found && $record[3] != $state) {
		$record[3]= $state;
		update_member(@record);
	}
}

sub is_admin {
	if($memberData[7]==0) { &fatal_error($txt{'1'}); }
}


#Delete members
sub delete_members {
	my (@memberList) = @_;
	foreach my $member (@memberList) {
		remove_member($member);
	}
}

sub set_member_last_visit {
	my ($member,$date)= @_;
	my ($found,@record) = find_member($member);
	if ($found) {
			$record[5] = $date;
			update_member(@record);
	}
}

sub get_member_last_visit {
	my ($member)= @_;
	my $date='';
	my ($found,@record) = find_member($member);
	$date = $record[5] if ($found);
	return $date;
}


sub set_activation_code {
	my ($member,$onoff)= @_;
	my ($found,@record) = find_member($member);
	if ($found) {
			$record[4] = $onoff;
			update_member(@record);
	}
}

sub set_areas_request {
	my ($member,@list)= @_;
	my ($found,@record) = find_member($member);
	if ($found) {
		$record[6] = join( '+' , @list);
		update_member(@record);
	}
}

sub toggle_area_request {
	my ($member,@list)= @_;
	my ($found,@record) = find_member($member);
	if ($found) {
		my %includes = include_index($record[6]);
		foreach my $key (@list) {
			if (exists $includes{$key}) {
				delete $includes{$key};
			} else {
				$includes{$key}=1;
			}
		}
		$record[6]= join('+',keys %includes);
		update_member(@record);
	}
}

#Return the requested areas
sub get_areas_request {
	my ($member)= @_;
	my @list=();
	my ($found,@record) = find_member($member);
	@list= split(/\+/, $record[6]) if ($found);
	return @list;
}

#Return 1 if member is allowed to do all
sub get_admin_site {
	my ($member)= @_;
	my $onoff=0;
	my ($found,@record) = find_member($member);
	$onoff=$record[7] if ($found);                          #Validate if there
	return $onoff;
}

#check if e-mail allready used.
sub exists_email {
	my ($email,$member)=@_;
	return 0 if ($allowdoubleemail);
	foreach my $key (keys %members) {
		my ($found,@record) = find_member($key);
		if ($record[9] eq $email and $record[0] ne $member) {
		  return 1;
	  }
  }
  return 0;
}

sub remove_inactive {
	my ($days) = @_;
	if (! defined $days) {$days = 0;}
	my ($curdate) = get_date_value(get_date()) - $days;
	foreach my $key (keys %members) {
			my ($found,@record) = find_member($key);
			my $mdate = get_date_value($record[5]);
			if ($record[3]!=1 and $mdate <= $curdate) {
				remove_member($key);
		  }
  }
  commit_database();
}


sub get_email_member_info{
 my ($email,$isMember) = @_;
 $email =~ s/^\s+//;
 $email =~ s/\s+$//;
 my @record = ();
 if ($isMember) {
	($found,@record) = find_member($email);
 } else {
	 if (!$mailmembersloaded){
		%mailmemberinfo=();
		$mailmembersloaded=1;
		@memberkeys = get_member_keys();
		foreach my $member (@memberkeys) {
			($found,@record) = find_member($member);
			if ($found) {$mailmemberinfo{lc($record[9])} = $member;}
		}
	 }
	 ($found,@record) = find_member($mailmemberinfo{lc($email)});
 }
 #push record global
 for(my $i=0;$i<@record;$i++) {${"member_field_$i"} = $record[$i];}
 return @record;
}

1;
