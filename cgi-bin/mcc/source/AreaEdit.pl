#AreaEdit
require "$sourcedir/ListIndex.pl";
sub ModifyArea {
	my ($add_mode) = @_;
	is_admin();
  my $area = $INFO{'area'};
	my @areasettings = get_area_info($area);
  #make description multi line
  my $mode = $add_mode==1 ? "text" : "hidden";
  my $defaultPath = GetDirPath();
  if ($add_mode) {
		$areasettings[3]=GetMccURL();
		$areasettings[2]=$defaultPath;
		$areasettings[9]="popup";
  }
  my %checked=(0=>'',1=>' checked ','0'=>'','1','checked');

  $subsscripts .= qq~$listbox_script~;

	$subsmain .= qq~
		<form action="$cgi;action=editarea2" method="post" name="creator">
			<table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
			<tr>
				<td class="titlebg" bgcolor="$color{'titlebg'}" height="30">
					&nbsp;<img src="$imagesdir/area.gif" alt="" border="0">&nbsp;
					<font size=2 class="text1" color="$color{'titletext'}"><b>$mcctxt{'53'}</b></font>
				</td>
			</tr><tr>
				<td class="windowbg" bgcolor="$color{'windowbg'}" height="25">
					<BR><font size="1">$mcctxt{'54'}</font><BR><BR></td>
			</tr><tr>
				<td class="catbg" bgcolor="$color{'catbg'}" height="25">
					<font size=2><b>$mcctxt{'517'}</b></font></td>
			</tr><tr>
				<td class="windowbg" bgcolor="$color{'windowbg'}">
					<font size=2>
						<table border=0 width="100%" cellpadding="3">
							<tr>
								<td width="320"><font size=2><b>$mcctxt{'39'}: </b></font></td>
								<td><font size=2><input type="$mode" name="area" value="$area">$area</font></td>
							</tr><tr>
								<td width="320"><font size=2><b>$mcctxt{'132'}: </b></font></td>
								<td><font size=2><input type="text" name="sortkey" value="$areasettings[14]"></font></td>
							</tr><tr>
								<td width="320"><font size=2><b>$mcctxt{'23'}: </b></font><BR>
								<td><input type="checkbox" name="active" value="1" $checked{$areasettings[1]} ></td>
							</tr><tr>
								<td width="320"><font size=2><b>$mcctxt{'15'}: </b></font><BR>
								<td><input type="text" name="fullname" value="$areasettings[4]" size=80></td>
							</tr><tr>
								<td width="320"><font size=2><b>$mcctxt{'46'}: </b></font><BR>
								<td><input type="text" name="url" value="$areasettings[3]" size=80></td>
							</tr><tr>
								<td width="320"><font size=2><b>$mcctxt{'47'}: </b></font><BR>
								<td><input type="text" name="path" value="$areasettings[2]" size=70>
								<input type=button name=browsepath
						        onclick="window.open('$cgi;action=browsedir;initdir=1;field=creator.path;dir='+this.form.elements['path'].value,
								            '', 'dependent=1,directories=0,height=500,width=400,location=0')"
                    value="&nbsp;&nbsp;Browse...&nbsp;&nbsp;">
								</td>
							</tr><tr>
								<td width="320"><font size=2><b>$mcctxt{'40'}: </b></font></td>
								<td><textarea name="description" cols="80" rows='4'>$areasettings[5]</textarea></td>
							</tr><tr>
								<td width="320"><font size=2><b>$mcctxt{'70'}: </b></font><BR>
								<td><textarea name="note" cols="80" rows='4'>$areasettings[8]</textarea></td>
							</tr><tr>
								<td width="320"><font size="2">$mcctxt{'57'}</font></td>
								<td><input type=checkbox name="useurlpassword" $checked{$areasettings[11]}></td>
							</tr><tr>
								<td width="320"><font size="2">$mcctxt{'68'}</font></td>
								<td><input type=checkbox name="usescript" $checked{$areasettings[10]}></td>
							</tr><tr>
								<td width="320"><font size="2">$mcctxt{'91'}</font></td>
								<td><input type=input name="scriptaction" value="$areasettings[9]"></td>
							</tr><tr>
								<td width="320"><font size="2">$mcctxt{'130'}</font></td>
								<td><input type=checkbox name="hidearea" $checked{$areasettings[12]}></td>
							</tr><tr>
								<td width="320"><font size="2">$mcctxt{'131'}</font></td>
								<td><input type=checkbox name="disablefile" $checked{$areasettings[13]}></td>
								</tr><tr>
								<td width="320"><font size="2">$mcctxt{'142'}</font></td>
								<td><input type=checkbox name="isrequestable" $checked{$areasettings[15]}></td>
							</tr>
						</table>
					</font>
				  <table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
					  <tr><td>
					  <input type="hidden" name="members">
			 		  <input type="hidden" name="groups">
			 		  <input type="hidden" name="massmember" value="$massmember"></td>
			~;
			#Add Member list
			my $active ='';
			my $free = '';
			my %hash = ();
      my @list = ();
      if (!$massmember) {
				@list = split(/\+/,$areasettings[7]);
				%hash = ();
				foreach my $key (sort @list) {
					if (defined get_member_info($key)) {
						$active .= qq~<option value="$key">$key</option>~;
						$hash{$key}=$key;
					}
				}
				foreach my $key ((get_member_sort_keys(1,0))) {
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
								<select multiple name="memberitems" size="8">
										$active
								</select>
							</td>
							<td align="center">	<input type="button" name="addbtn" value="    >    "
										onclick="addItems(this.form.memberitems, this.form.membersfree); removeItems(this.form.memberitems);">
								<br><br>
								<input type="button"
										name="removebtn"
										value="    <    "
										onclick="addItems(this.form.membersfree, this.form.memberitems); removeItems(this.form.membersfree);">
								<br>
							</td>
							<td valign="top">
								$mcctxt{'37'}<br>
								<select multiple name="membersfree" size="8">
									$free
								</select>
							</td>
						</tr>
					</table>
					</td>
				~;
			}
			#Add Area list
			$active ='';
			$free = '';
			@list = split(/\+/,$areasettings[6]);
			my %hash = ();
			foreach my $key (sort @list) {
				if (defined get_group_info($key)) {
				  $active .= qq~<option value="$key">$key</option>~;
				  $hash{$key}=$key;
			  }
		  }
		  foreach my $key (get_group_sort_keys(1,0)) {
				if (!exists $hash{$key}) {
				   $free .= qq~<option value="$key">$key</option>~;
			  }
		  }
			$subsmain .= qq~
			  <td>
				<table border=0 with="100%" cellpadding=1 cellspacing=4>
					<tr>
					<td colspan="4" class="windowbg" bgcolor="$color{'windowbg'}" height="15">
	          <font size=2 ><b>$mcctxt{'25'}</b></font>
					</tr><tr>
						<td valign="top">$mcctxt{'36'}<br>
							<select multiple name="groupitems" size="8">
									$active
							</select>
						</td>
						<td align="center">
							<input type="button"
									name="addbtn2" value="    >    "
									onclick="addItems(this.form.groupitems, this.form.groupsfree); removeItems(this.form.groupitems);">
							<br><br>
							<input type="button"
									name="removebtn2"
									value="    <    "
									onclick="addItems(this.form.groupsfree, this.form.groupitems); removeItems(this.form.groupsfree);">
							<br>
						</td>
						<td valign="top">
							$mcctxt{'37'}<br>
							<select multiple name="groupsfree" size="8">
								$free
							</select>
						</td>
					</tr>
				</table>
				</td>
				</tr>
				</table>
      ~;
	$subsmain .= qq~
			<table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
				<tr>
					<td class="catbg" bgcolor="$color{'catbg'}" height="25" align="center"><font size=2><BR>
						<script language="JavaScript"> <!--
							function confirmDel() {if (confirm("$txt{'775'} $mcctxt{'41'} $INFO{'group'}")) {return true;} else {return false;}}
							function checkPath() {
								if (document.creator.path.value == "$defaultPath") {
									if (!confirm("$mcctxt{'149'}")) {return false;}
								}
								if ("$massmember" != "1") {
							    document.creator.members.value = getSelectedString(document.creator.memberitems);
								}
							  document.creator.groups.value = getSelectedString(document.creator.groupitems);
							  return true;
							}
								// --> </script>
						<input type=submit name=moda value="$mcctxt{'48'}" onClick="return checkPath()">
   ~;
    if ($add_mode==0) {
			$subsmain .= qq~
				<input type=submit name=moda value="$mcctxt{'49'}" onClick="return confirmDel()"><BR>
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
	$substitle = $mcctxt{'53'};
	&template;
}

sub ModifyArea2 {
	is_admin();
	my( %item, $key, $value);
	while( ($key,$value) = each(%FORM) ) {
			$value =~ s~\A\s+~~;
			$value =~ s~\s+\Z~~;
			$value =~ s~[\n\r]~<BR>~g;
			$item{$key} = $value;
	}
	my @onoff = qw/ useurlpassword usescript hidearea disablefile isrequestable/;
	my $fi;
	map { $fi = lc $_; ${$_} = $FORM{$fi} eq 'on' ? 1 : 0; } @onoff;

	&fatal_error("$txt{'75'}") if($item{'area'} eq '');
	&fatal_error("$txt{'568'}") if(length($item{'area'}) > 30);
	if($item{'moda'} eq $mcctxt{'48'}) {
		if ($item{'massmember'}) {
		  my ($found,@record) = find_area($item{'area'});
		  $item{'members'} = $record[7] if ($found);
		}
		set_area_info($item{'area'},$item{'path'},$item{'url'},$item{'fullname'},
		           $item{'description'},$item{'groups'},$item{'members'},$item{'note'},
		           $item{'scriptaction'},$usescript,$useurlpassword,$hidearea,
		           $disablefile,$item{'sortkey'},$isrequestable);
		set_area_state($item{'area'},$item{'active'});
  } elsif($item{'moda'} eq $mcctxt{'49'}) {
    delete_areas($item{'area'});
  }
	commit_database();

	#Go back to mangegroups
	my $page = page_location($item{'area'},get_area_sort_keys(1,0));
	redirectexit("$cgi;action=manageareas;page=$page");
}

1;