#GroupEdit
require "$sourcedir/ListIndex.pl";
sub ModifyGroup {
	my ($add_mode) = @_;
	is_admin();
  my $group = $INFO{'group'};
	my @groupsettings = get_group_info($group);
  #make description multi line
  my $checked = $groupsettings[1]==0 ? '' : 'CHECKED';
  info($checked,$groupsettings[1]) if ($logging);
  my $mode = $add_mode==1 ? "text" : "hidden";

  $subsscripts .= qq~
     $listbox_script~;

	$subsmain .= qq~
		<script type="text/javascript"> <!--
			function confirmDel() {
				if (confirm("$txt{'775'} $mcctxt{'41'} $INFO{'group'}")) {
					return true;
				} else {
					return false;
				}
			}
			function checkPath() {
				if ("$massmember" != "1") {
					document.creator.members.value = getSelectedString(document.creator.memberitems);
				}
  			return true;
  		}
		// --> </script>

		<form name="creator" action="$cgi;action=editgroup2" method="post">
		<table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
			<tr>
				<td class="titlebg" bgcolor="$color{'titlebg'}" height="30">
				&nbsp;<img src="$imagesdir/group.gif" alt="" border="0">&nbsp;
				<font size=2 class="text1" color="$color{'titletext'}"><b>$mcctxt{'38'}</b></font></td>
			</tr><tr>
				<td class="windowbg" bgcolor="$color{'windowbg'}" height="25"><BR><font size="1">$mcctxt{'41'}</font><BR><BR></td>
			</tr><tr>
				<td class="catbg" bgcolor="$color{'catbg'}" height="25"><font size=2><b>$txt{'517'}</b></font></td>
			</tr><tr>
				<td class="windowbg" bgcolor="$color{'windowbg'}">
				<font size=2>
				<table border=0 width="100%" cellpadding="3">
					<tr>
						<td width="320"><font size=2><b>$mcctxt{'39'}: </b></font></td>
						<td><font size=2><input type="$mode" name="group" value="$group">$group</font></td>
					</tr><tr>
						<td width="320"><font size=2><b>$mcctxt{'23'}: </b></font><BR>
						<td><input type="checkbox" name="active" value="1" $checked ></td>
					</tr><tr>
						<td width="320"><font size=2><b>$mcctxt{'15'}: </b></font><BR>
						<td><input type="text" name="fullname" size="80" value="$groupsettings[2]"></td>
					</tr><tr>
						<td width="320"><font size=2><b>$mcctxt{'40'}: </b></font></td>
						<td><textarea name="description" cols="80" rows='4'>$groupsettings[3]</textarea></td>
					</tr>
				</table>
				</font>
				<table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
				<tr><td>
				 	<input type="hidden" name="members">
			  	<input type="hidden" name="areas">
			  	<input type="hidden" name="massmember" value="$massmember"></td>
			~;
			#Add Member list
			my $active ='';
			my $free = '';
			my @list = ();
			my %hash = ();
			if (!$massmember) {
				@list = split(/\+/,$groupsettings[4]);

				foreach my $key (sort @list) {
					if (defined get_member_info($key)) {
						$active .= qq~<option value="$key">$key</option>~;
						$hash{$key}=$key;
					}
				}
				foreach my $key (get_member_sort_keys(1,0)) {
					if (!exists $hash{$key}) {
						 $free .= qq~<option value="$key">$key</option>~;
					}
				}
				$subsmain .= qq~
						<td>
						<table border=0 with="100%" cellpadding=1 cellspacing=4>
							<tr>
								<td colspan="4" class="windowbg" bgcolor="$color{'windowbg'}" height="15">
								<font size=2 ><b>$mcctxt{'34'}</b></font>
							</tr><tr>
								<td valign="top">$mcctxt{'36'}<br>
									<select multiple name="memberitems" size="8">$active</select>
								</td>
								<td align="center">
									<input type="button" name="addbtn" value="    >    "
										onclick="addItems(this.form.memberitems, this.form.membersfree); removeItems(this.form.memberitems);">
									<br>
									<br>
									<input type="button"name="removebtn" value="    <    "
										onclick="addItems(this.form.membersfree, this.form.memberitems); removeItems(this.form.membersfree);">
									<br>
								</td>
								<td valign="top">$mcctxt{'37'}<br>
									<select multiple name="membersfree" size="8">$free</select>
								</td>
								</tr>
						</table>
					</td>
				~;
			}
	$subsmain .= qq~
		 </tr>
		 </table>
		<table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
			<tr>
				<td class="catbg" bgcolor="$color{'catbg'}" height="25" align="center"><font size=2><BR>
					<input type=submit name=moda value="$mcctxt{'42'}" onClick="return checkPath()">
    ~;
    if ($add_mode==0) {
			$subsmain .= qq~
				<input type=submit name=moda value="$mcctxt{'43'}" onClick="return confirmDel()"><BR>
			~;
		}
    $subsmain .=qq~
    </font></td>
  </tr>
  </table>
</table>
</form>
<center><BR><a href="javascript:history.go(-1)">$txt{'250'}</a></center>
~;
	$substitle = $mcctxt{'38'};
	&template;
}

sub ModifyGroup2 {
	is_admin();
	my( %item, $key, $value);
	while( ($key,$value) = each(%FORM) ) {
			$value =~ s~\A\s+~~;
			$value =~ s~\s+\Z~~;
			$value =~ s~[\n\r]~~g;
			$item{$key} = $value;
	}
	&fatal_error("$txt{'75'}") if($item{'group'} eq '');
	&fatal_error("$txt{'568'}") if(length($item{'group'}) > 30);
	if($item{'moda'} eq $mcctxt{'42'}) {
		if ($item{'massmember'}) {
			my ($found,@record) = find_group($item{'group'});
			$item{'members'} = $record[4] if ($found);
		}
		set_group_info($item{'group'},$item{'fullname'},$item{'description'},$item{'members'});
		set_group_state($item{'group'},$item{'active'});
  } elsif ($item{'moda'} eq $mcctxt{'43'}) {
    delete_groups($item{'group'});
  }
	commit_database();

	#Go back to mangegroups
	my $page = page_location($item{'group'},get_group_sort_keys(1,0));
	redirectexit("$cgi;action=managegroups;page=${page}");
}

1;