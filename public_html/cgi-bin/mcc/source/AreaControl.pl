#AreaControl
#A Area record contains at least the following information
#0=AreaName,1=Active,2=path,3=url,4=Full Name,5=Description
#6=Groups,7=Members,8=Note,9=AreaAction,10=UseScript,11=UseURLpassword,
#12=Hide,13=disableFileCreation,14=SortKey,15=Requestable

%areas=();
$areasChanged=-1;

#load the areas
sub load_areas {
	if ($areasChanged==-1) {
		%areas=();
		if (fopen(areaSH,"$areasFile")) {
			my @l_areas = <areaSH>;
			fclose(areaSH);
			foreach my $line (@l_areas) {
				chomp($line);
				my @record = split(/\|/, $line);
				$areas{$record[0]}=$line;
			}
		} else {
			info("$mcctxt{'1'} $areasFile $mcctxt{'4'} $!") if ($logging);
		}
		$areasChanged=0;
	}
}

#Save the areas
sub save_areas {
	if ($areasChanged>0) {
		if (fopen(areaSH,">$areasFile")) {
			foreach my $area (keys %areas) {
				print areaSH "$areas{$area}\n";
			}
			fclose(areaSH);
		} else {
			info("$mcctxt{'2'} $areasFile $mcctxt{'4'} $!") if ($logging);
		}
		$areasChanged=0;
	}
}

#Remove a area
sub remove_area{
	my ($key) = @_;
	my ($found,@record) = find_area($key);
	if ($found) {
  	addOnExecute("Areas",3,$key);
   	#remove files before deleting
		unlink "$record[2]/.htaccess", "$record[2]/.htpasswd" if (!$record[13]);
  	delete $areas{$key};
  	$areasChanged++;
  	$passwordChanged++;
	}
}

#Find a area
sub find_area{
	my ($area) = @_;
	my @record=();
	if (exists $areas{$area}) {
		@record = split(/\|/, $areas{$area});
		return (1,@record);
	} else {
			info("$mcctxt{'3'} $area") if ($logging);
	}
	return (0,@record);
}

#Update a area
sub update_area {
	my (@record) = @_;
	my ($found,@oldrecord) = find_area($record[0]);
	if ($found) {
	  $areas{$record[0]} =  join( '|' , @record);
	  $areasChanged++;
	  $passwordChanged++ ;
    addOnExecute("Areas",2,$record[0]);
	} else {
		$areas{$record[0]} =  join( '|' , @record);
		$areasChanged++;
		$passwordChanged++;
    addOnExecute("Areas",1,$record[0]);
	}
}

sub reload_areas {
	$areasChanged=-1;
	load_areas();
}

sub get_area_keys {
	return keys %areas;
}

sub get_area_sort_keys {
	my ($asc,@columns) =@_;
	my @sortlist = (@columns , 0);
	my @list = sort {
		my @ra = get_area_info($a);
		my @rb = get_area_info($b);
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
	 } keys %areas;
	 return (@list);
}

1;